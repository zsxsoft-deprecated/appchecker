using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Win32;
using System.Runtime.InteropServices;

namespace AppChecker
{

    /// <summary>
    /// <see cref="http://mel-green.com/2009/04/c-set-file-type-association/"/>
    /// </summary>
    public class FileAssociation
    {
        /// <summary>
        /// Associate file extension with progID, description, icon and application
        /// </summary>
        /// <param name="extension"></param>
        /// <param name="progID"></param>
        /// <param name="description"></param>
        /// <param name="icon"></param>
        /// <param name="application"></param>
        public static void Associate(string extension,
               string progID, string description, string icon, string application)
        {
            Registry.ClassesRoot.CreateSubKey(extension).SetValue("", progID);
            if (progID != null && progID.Length > 0)
                using (RegistryKey key = Registry.ClassesRoot.CreateSubKey(progID))
                {
                    if (description != null)
                        key.SetValue("", description);
                    if (icon != null)
                        key.CreateSubKey("DefaultIcon").SetValue("", ToShortPathName(icon));
                    if (application != null)
                        key.CreateSubKey(@"Shell\Open\Command").SetValue("",
                                    ToShortPathName(application) + " \"%1\"");
                }
        }

        /// <summary>
        /// Unassociate file   
        /// </summary>
        /// <param name="extension">The extension.</param>
        public static void Unassociate(string extension)
        {
            Registry.ClassesRoot.DeleteSubKeyTree(extension);
        }

        /// <summary>
        /// Determines whether the extension already associated in registry        
        /// </summary>
        /// <param name="extension">The extension.</param>
        /// <returns></returns>
        public static bool IsAssociated(string extension)
        {
            return (Registry.ClassesRoot.OpenSubKey(extension, false) != null);
        }

        /// <summary>
        /// Gets the short name of the path.
        /// </summary>
        /// <param name="lpszLongPath">The LPSZ long path.</param>
        /// <param name="lpszShortPath">The LPSZ short path.</param>
        /// <param name="cchBuffer">The CCH buffer.</param>
        /// <returns></returns>
        [DllImport("Kernel32.dll")]
        private static extern uint GetShortPathName(string lpszLongPath,
            [Out] StringBuilder lpszShortPath, uint cchBuffer);

        /// <summary>
        /// Return short path format of a file name   
        /// </summary>
        /// <param name="longName">The long name.</param>
        /// <returns></returns>
        private static string ToShortPathName(string longName)
        {
            StringBuilder s = new StringBuilder(1000);
            uint iSize = (uint)s.Capacity;
            uint iRet = GetShortPathName(longName, s, iSize);
            return s.ToString();
        }
    }
}
