using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;

namespace AppChecker
{
    public delegate void PHPConnectionEventHandler(object sender, PHPConnectionEventArgs e);
    public class PHPConnectionEventArgs : EventArgs
    {
        private readonly string data = "";
    
        public PHPConnectionEventArgs(string Data)
        {
            this.data = Data;
        }

        public string Data
        {
            get { return data; }
        }
        
    }

    class PHPConnection
    {
        public static event PHPConnectionEventHandler OnLogReceived;
        public static void ReceivedLog(string Data)
        {
            var handler = OnLogReceived;
            var args = new PHPConnectionEventArgs(Data);
            if (null != handler) handler(null, args);
        }

        private static ProcessStartInfo InitializeProcessStartInfo(CheckerInfo Data, string Argument)
        {
            return new ProcessStartInfo
            {
                FileName = Data.PHPPath,
                WorkingDirectory = Utils.ProgramPath,
                StandardOutputEncoding = Encoding.GetEncoding(65001),//Utils.GetCodePage());
                Arguments = Argument,
                WindowStyle = ProcessWindowStyle.Hidden,
                RedirectStandardOutput = true,
                UseShellExecute = false,
                CreateNoWindow = true,
                EnvironmentVariables = { { "ZBP_PATH", Data.ZBPPath }, { "ConEmuANSI", "ON" }, { "APPCHECKER_GUI_CHARSET", "UTF-8" } }
            };
        }
        private static Process InitializePHPProcess(CheckerInfo Data, string Argument)
        {
            var p = new Process
            {
                StartInfo = InitializeProcessStartInfo(Data, Argument),
            };
            p.Start();
            p.OutputDataReceived += (eventSender, args) => ReceivedLog(args.Data);
            p.BeginOutputReadLine();
            p.WaitForExit();
            return p;
        }

        public static Thread RunChecker(CheckerInfo Data)
        {
            return new Thread(() => InitializePHPProcess(Data, $"-c \"{Data.PHPIniPath}\" checker run --bloghost=\"{Data.WebsiteUrl}\" {Data.AppId} "));
        }

        public static Thread InstallZBA(CheckerInfo Data, string ZBAPath)
        {
            return new Thread(() => InitializePHPProcess(Data, $"-c \"{Data.PHPIniPath}\" checker install --bloghost=\"{Data.WebsiteUrl}\" \"{ZBAPath}\" "));
        }
    }
}
