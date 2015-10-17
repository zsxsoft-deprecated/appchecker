using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;

namespace AppChecker
{
    /// <summary>
    /// Log.xaml 的交互逻辑
    /// </summary>
    public partial class Log : Window
    {
        public SolidColorBrush[] Colors;

        public Log()
        {
            InitializeComponent();
            Colors = new SolidColorBrush[100];
            Colors[32] = Brushes.Green;
            Colors[33] = Brushes.YellowGreen;
        }

        public void Clear()
        {
            Application.Current.Dispatcher.Invoke((Action)delegate
            {
                richTextBox.SelectAll();
                richTextBox.Selection.Text = "";
            });
        }
        public void WriteLine(string Text)
        {
            Application.Current.Dispatcher.Invoke((Action)delegate
            {
                Regex r = new Regex("\\[(\\d+)m(.*)\\[39m", RegexOptions.IgnoreCase);
                int lastIndex = 0;
                if (Text == null) return;
                // Match the regular expression pattern against a text string.
                Match m = r.Match(Text);
                TextRange rangeOfWord;
                while (m.Success)
                {
                    rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                    rangeOfWord.Text = m.Groups[2].Value;

                    rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, Colors[Convert.ToInt16(m.Groups[1].Value)]);
                    lastIndex = m.Groups[2].Index + m.Groups[2].Length + @"[39m".Length;

                    m = m.NextMatch();
                }
                rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                rangeOfWord.Text = Text.Substring(lastIndex);
                rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, Brushes.White);

                richTextBox.AppendText("\u2028");
                richTextBox.ScrollToEnd();
            });
        }
    }
}
