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
            Grid.DataContext = Config.Data;
            WindowLog.Show();
        }


        private void btnSubmit_Click(object sender, RoutedEventArgs e)
        {
            Config.Save();
            new Thread(() =>
            {
                WindowLog.Clear();
                Process p = new Process
                {
                    StartInfo = new ProcessStartInfo
                    {
                        FileName = Config.Data.PHPPath,
                        WorkingDirectory = Directory.GetCurrentDirectory(),
                        StandardOutputEncoding = Encoding.GetEncoding(65001),//Utils.GetCodePage());
                        Arguments = $" checker run {Config.Data.AppId} --bloghost={Config.Data.WebsiteUrl}",
                        WindowStyle = ProcessWindowStyle.Hidden,
                        RedirectStandardOutput = true,
                        UseShellExecute = false,
                        CreateNoWindow = true,
                        EnvironmentVariables = { { "ZBP_PATH", Config.Data.ZBPPath }, { "ConEmuANSI", "ON" }, { "APPCHECKER_GUI_CHARSET", "UTF-8" } }
                    }
                };
                p.Start();
                p.OutputDataReceived += (eventSender, args) => WindowLog.WriteLine(args.Data);
                p.BeginOutputReadLine();
                p.WaitForExit();
            }).Start();           
        }


        private void btnBrowse_Click(object sender, RoutedEventArgs e)
        {
            Microsoft.Win32.OpenFileDialog ofd = new Microsoft.Win32.OpenFileDialog
            {
                DefaultExt = ".exe",
                Filter = "PHP Execution File|php.exe"
            };
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
