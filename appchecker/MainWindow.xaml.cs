using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.InteropServices;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace appchecker
{
    /// <summary>
    /// MainWindow.xaml 的交互逻辑
    /// </summary>
    public partial class MainWindow : Window
    {
        /// <summary>
        /// 启动控制台
        /// </summary>
        [DllImport("kernel32.dll")]
        public static extern bool AllocConsole();

        /// <summary>
        /// 释放控制台
        /// </summary>
        [DllImport("kernel32.dll")]
        public static extern bool FreeConsole();
        public MainWindow()
        {
            InitializeComponent();
        }

        private void btnSubmit_Click(object sender, RoutedEventArgs e)
        {
            AllocConsole();
            Console.WriteLine("测试");
        }
    }
}
