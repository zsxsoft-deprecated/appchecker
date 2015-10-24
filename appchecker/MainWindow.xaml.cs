using System;
using System.ComponentModel;
using System.Runtime.InteropServices;
using System.Windows;
using System.IO;
using System.Diagnostics;
using System.Threading;
using System.Text;

namespace AppChecker
{

    /// <summary>
    /// MainWindow.xaml 的交互逻辑
    /// </summary>
    public partial class MainWindow : Window
    {
        Log WindowLog = new Log();
        public MainWindow()
        {
            InitializeComponent();
            Config.Load();
            txtPHPPath.DataContext = Config.Data;
            txtZBPPath.DataContext = Config.Data;
            txtAppID.DataContext = Config.Data;
            txtWebsiteUrl.DataContext = Config.Data;

            WindowLog.Show();
        }


        private void btnSubmit_Click(object sender, RoutedEventArgs e)
        {
            Config.Save();
            Thread Caller = new Thread((ThreadStart)delegate
            {
                WindowLog.Clear();
                Process p = new Process();
                p.StartInfo = new ProcessStartInfo();
                p.StartInfo.FileName = Config.Data.PHPPath;
                p.StartInfo.WorkingDirectory = Directory.GetCurrentDirectory();
                p.StartInfo.StandardOutputEncoding = Encoding.GetEncoding(65001); //Utils.GetCodePage());
                p.StartInfo.EnvironmentVariables["ZBP_PATH"] = Config.Data.ZBPPath;
                p.StartInfo.EnvironmentVariables["ConEmuANSI"] = "ON";
                p.StartInfo.EnvironmentVariables["APPCHECKER_GUI_CHARSET"] = "UTF-8";
                p.StartInfo.Arguments = " checker run " + Config.Data.AppId + " --bloghost=" + Config.Data.WebsiteUrl;
                p.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                p.StartInfo.RedirectStandardOutput = true;
                p.StartInfo.UseShellExecute = false;
                p.StartInfo.CreateNoWindow = true;
                p.Start();
                p.OutputDataReceived += (eventSender, args) => WindowLog.WriteLine(args.Data);
                p.BeginOutputReadLine();
                p.WaitForExit();
            });
            Caller.Start();
            
        }
    

        private void btnBrowse_Click(object sender, RoutedEventArgs e)
        {
            Microsoft.Win32.OpenFileDialog ofd = new Microsoft.Win32.OpenFileDialog();
            ofd.DefaultExt = ".exe";
            ofd.Filter = "PHP Execution File|php.exe";
            if (ofd.ShowDialog() == true)
            {
                if (ofd.FileName != "")
                {
                    Config.Data.PHPPath = ofd.FileName;
                }
            }

        }

        private void btnBrowseZBP_Click(object sender, RoutedEventArgs e)
        {
            System.Windows.Forms.FolderBrowserDialog ofd = new System.Windows.Forms.FolderBrowserDialog();
            if (ofd.ShowDialog() == System.Windows.Forms.DialogResult.OK)
            {
                if (ofd.SelectedPath != "")
                {
                    Config.Data.ZBPPath = ofd.SelectedPath;
                }
            }
        }
    }
}
