﻿using System;
using System.ComponentModel;
using System.Runtime.InteropServices;
using System.Windows;
using System.IO;
using System.Diagnostics;
using System.Threading;

namespace AppChecker
{

    public class CheckerInfo : INotifyPropertyChanged
    {
        private string _AppId;
        /// <summary>
        /// Gets or sets App ID
        /// </summary>
        public string AppId
        {
            get
            {
                return _AppId;
            }
            set
            {
                if (_AppId == value) return;
                _AppId = value;
                OnPropertyChanged(new PropertyChangedEventArgs("AppId"));
            }
        }
        private string _PHPPath;
        /// <summary>
        /// Gets or sets PHP Path.
        /// </summary>
        public string PHPPath
        {
            get
            {
                return _PHPPath;
            }
            set
            {
                if (_PHPPath == value) return;
                _PHPPath = value;
                OnPropertyChanged(new PropertyChangedEventArgs("PHPPath"));
            }
        }
        private string _ZBPPath;
        /// <summary>
        /// Gets or sets Z-BlogPHP Path.
        /// </summary>
        public string ZBPPath
        {
            get
            {
                return _ZBPPath;
            }
            set
            {
                if (_ZBPPath == value) return;
                _ZBPPath = value;
                OnPropertyChanged(new PropertyChangedEventArgs("ZBPPath"));
            }
        }
        public event PropertyChangedEventHandler PropertyChanged;

        private void OnPropertyChanged(PropertyChangedEventArgs e)
        {
            PropertyChangedEventHandler h = PropertyChanged;
            if (h != null)
                h(this, e);
        }
    }
    /// <summary>
    /// MainWindow.xaml 的交互逻辑
    /// </summary>
    public partial class MainWindow : Window
    {
        CheckerInfo Data = new CheckerInfo();
        Log WindowLog = new Log();
        public MainWindow()
        {
            InitializeComponent();
            txtPHPPath.DataContext = Data;
            txtZBPPath.DataContext = Data;
            txtAppID.DataContext = Data;


            WindowLog.Show();
        }


        private void btnSubmit_Click(object sender, RoutedEventArgs e)
        {
            Thread Caller = new Thread((ThreadStart)delegate
            {
                WindowLog.Clear();
                Process p = new Process();
                p.StartInfo = new ProcessStartInfo();
                p.StartInfo.FileName = Data.PHPPath;
                p.StartInfo.WorkingDirectory = Directory.GetCurrentDirectory();
                p.StartInfo.EnvironmentVariables["ZBP_PATH"] = Data.ZBPPath;
                p.StartInfo.EnvironmentVariables["ConEmuANSI"] = "ON";
                p.StartInfo.Arguments = " checker " + Data.AppId;
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
                    Data.PHPPath = ofd.FileName;
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
                    Data.ZBPPath = ofd.SelectedPath;
                }
            }
        }
    }
}