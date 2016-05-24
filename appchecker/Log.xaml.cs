using System;
using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Documents;
using System.Windows.Media;

namespace AppChecker
{
    /// <summary>
    /// Log.xaml 的交互逻辑
    /// </summary>
    public partial class Log : Window
    {
        /// <summary>
        /// The color map
        /// <see cref="http://tldp.org/LDP/abs/html/colorizing.html"/>
        /// </summary>
        static SolidColorBrush[] ColorMap =
        {
            Brushes.Black, Brushes.PaleVioletRed, Brushes.LightGreen,
            Brushes.Yellow, Brushes.LightBlue, Brushes.Magenta,
            Brushes.Cyan, Brushes.White
        };

        public Log()
        {
            InitializeComponent();
        }

        public void Clear()
        {
                richTextBox.SelectAll();
                richTextBox.Selection.Text = "";
        }

        [System.Runtime.InteropServices.DllImport("kernel32")]
        static extern bool AllocConsole();
        
        /// <summary>
        /// Writes to the emulator.
        /// </summary>
        /// <param name="Text">The text.</param>
        public void WriteLine(string Text)
        {
            if (string.IsNullOrEmpty(Text)) return;
            Text = Text.Replace("\u001b", "");
            //AllocConsole();
            this.Dispatcher.Invoke(new Action(()=>
            {
                Regex r = new Regex("\\[(\\d+)(;(\\d+))?m", RegexOptions.IgnoreCase);
                int lastIndex = 0;

                // Match the regular expression pattern against a text string.
                Match m = r.Match(Text);
                Match n;
                TextRange rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                int BackgroundColor = 255;
                int ForegroundColor = 0;
                Console.WriteLine(Text);
                if (m.Success)
                {
                    rangeOfWord.Text = Text.Substring(0, m.Index);
                }
                while (m.Success)
                {
                    BackgroundColor = Convert.ToInt32(m.Groups[3].Value != "" ? m.Groups[3].Value : "0"); // ;number
                    ForegroundColor = Convert.ToInt32(m.Groups[1].Value);
                    BackgroundColor = BackgroundColor == 0 ? BackgroundColor : Math.Abs((BackgroundColor - 40) % 7);
                    ForegroundColor = ForegroundColor == 0 ? ForegroundColor : Math.Abs((ForegroundColor - 30) % 7);
                    rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                    n = m.NextMatch();
                    lastIndex = m.Index + m.Length;
                    rangeOfWord.Text = n.Success ? Text.Substring(lastIndex, n.Index - lastIndex) : Text.Substring(lastIndex);
                    rangeOfWord.ApplyPropertyValue(TextElement.BackgroundProperty, (ColorMap[BackgroundColor]));
                    rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, (ColorMap[ForegroundColor]));
                    rangeOfWord.ApplyPropertyValue(TextElement.FontWeightProperty, FontWeights.Regular);
                    Console.WriteLine("ORIG =" + Text);
                    Console.WriteLine("bg=" + BackgroundColor + ",  fr= " + ForegroundColor + ",  text=" + rangeOfWord.Text);
                    m = n;
                }
                rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd)
                {
                    Text = Text.Substring(lastIndex) + System.Environment.NewLine
                };
                rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, Brushes.White);
                rangeOfWord.ApplyPropertyValue(TextElement.BackgroundProperty, Brushes.Black);
                richTextBox.ScrollToEnd();
            }));
        }
    }
}
