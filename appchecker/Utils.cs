using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Media;

namespace AppChecker
{
    class Utils
    {

        [System.Runtime.InteropServices.DllImport("kernel32.dll")]
        public static extern int GetSystemDefaultLCID();
        public static SolidColorBrush ConvertColor(long waitRet)
        {
            byte[] bytes = BitConverter.GetBytes(waitRet); 
            return new SolidColorBrush(Color.FromRgb(bytes[1], bytes[2], bytes[0])); 
        }
        public static SolidColorBrush ConvertColor(Color ColorData)
        {
            return new SolidColorBrush(ColorData);
        }
        public static int GetCodePage()
        {
            int lcid = GetSystemDefaultLCID();
            System.Globalization.CultureInfo ci = System.Globalization.CultureInfo.GetCultureInfo(lcid);
            return  ci.TextInfo.OEMCodePage;
        }
    }

}