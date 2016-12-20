using System;
using System.Windows;
using System.IO;
using System.Diagnostics;
using System.Threading;
using System.Text;
using Microsoft.Win32;

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
            PHPConnection.OnLogReceived += (eventSender, args) => WindowLog.WriteLine(args.Data);

            // Get Environment Line
            var envArg = Environment.GetCommandLineArgs();
            if (envArg.Length > 1)
            {
                Config.Data.AppId = envArg[1];
            }
        }


        private void btnSubmit_Click(object sender, RoutedEventArgs e)
        {
            Config.Save();
            WindowLog.Clear();

            if (Config.Data.AppId.Length >= ".zba".Length) {

                if (Config.Data.AppId.Substring(Config.Data.AppId.Length - 4).ToLower() == ".zba") {
                    PHPConnection.InstallZBA(Config.Data, Config.Data.AppId).Start();
                } else {
                    PHPConnection.RunChecker(Config.Data).Start();
                }
            } else {
                PHPConnection.RunChecker(Config.Data).Start();
            }
            
        }


        private void btnBrowse_Click(object sender, RoutedEventArgs e)
        {
            OpenFileDialog ofd = new OpenFileDialog
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

        private void btnBrowsePHPIni_Click(object sender, RoutedEventArgs e)
        {
            OpenFileDialog ofd = new OpenFileDialog
            {
                DefaultExt = "php.ini",
                Filter = "php.ini|php.ini"
            };
            if (ofd.ShowDialog() == true)
            {
                if (ofd.FileName != "")
                {
                    Config.Data.PHPIniPath = ofd.FileName;
                }
            }
        }

        private void btnBrowseZBA_Click(object sender, RoutedEventArgs e)
        {
            OpenFileDialog ofd = new OpenFileDialog
            {
                DefaultExt = ".zba",
                Filter = "Z-Blog Packed App|*.zba"
            };
            if (ofd.ShowDialog() == true)
            {
                if (ofd.FileName != "")
                {
                    Config.Data.AppId = ofd.FileName;
                }
            }
        }

        private void btnFileAsso_Click(object sender, RoutedEventArgs e)
        {
            //if (!FileAssociation.IsAssociated(".zba"))
            //{
                try {
                    FileAssociation.Associate(".zba", "zblogcn.zba", "Z-Blog Packed App", $"{Utils.ProgramPath}\\Logo.ico", $"{Utils.ProgramPath}\\AppChecker.exe");
                    MessageBox.Show("关联成功", "AppChecker", MessageBoxButton.OK, MessageBoxImage.Information);
                } catch (System.Exception ex)
                {
                    WindowLog.WriteLine(ex.ToString());
                    MessageBox.Show("关联失败\n\n请以管理员权限启动程序再试", "AppChecker", MessageBoxButton.OK, MessageBoxImage.Error);
                }
            //}
        }

        private void btnCancelFileAsso_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                FileAssociation.Unassociate(".zba");
                MessageBox.Show("取消关联成功", "AppChecker", MessageBoxButton.OK, MessageBoxImage.Information);
            }
            catch (System.Exception ex)
            {
                WindowLog.WriteLine(ex.ToString());
                MessageBox.Show("取消关联失败\n\n请以管理员权限启动程序再试", "AppChecker", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void btnSetArticleId_Click(object sender, RoutedEventArgs e)
        {
            string ret = Microsoft.VisualBasic.Interaction.InputBox("文章ID？", "", Config.Data.ArticleId);
            Config.Data.ArticleId = ret;
        }
    }
}
