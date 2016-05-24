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
                RichTextBox.SelectAll();
                RichTextBox.Selection.Text = "";
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
                Regex MatchRegex = new Regex("\\[(\\d+)(;(\\d+))?m", RegexOptions.IgnoreCase);
                int lastIndex = 0;

                // Match the regular expression pattern against a text string.
                Match TextMatch = MatchRegex.Match(Text);
                Match NextMatch;
                TextRange RangeForNewText = new TextRange(RichTextBox.Document.ContentEnd, RichTextBox.Document.ContentEnd);
                int BackgroundColor = 255;
                int ForegroundColor = 0;
                Console.WriteLine(Text);
                if (TextMatch.Success)
                {
                    RangeForNewText.Text = Text.Substring(0, TextMatch.Index);
                }
                while (TextMatch.Success)
                {
                    BackgroundColor = Convert.ToInt32(TextMatch.Groups[3].Value != "" ? TextMatch.Groups[3].Value : "0"); // ;number
                    ForegroundColor = Convert.ToInt32(TextMatch.Groups[1].Value);
                    BackgroundColor = BackgroundColor == 0 ? BackgroundColor : Math.Abs((BackgroundColor - 40) % 7);
                    ForegroundColor = ForegroundColor == 0 ? ForegroundColor : Math.Abs((ForegroundColor - 30) % 7);
                    RangeForNewText = new TextRange(RichTextBox.Document.ContentEnd, RichTextBox.Document.ContentEnd);
                    NextMatch = TextMatch.NextMatch();
                    lastIndex = TextMatch.Index + TextMatch.Length;
                    RangeForNewText.Text = NextMatch.Success ? Text.Substring(lastIndex, NextMatch.Index - lastIndex) : Text.Substring(lastIndex);
                    RangeForNewText.ApplyPropertyValue(TextElement.BackgroundProperty, (ColorMap[BackgroundColor]));
                    RangeForNewText.ApplyPropertyValue(TextElement.ForegroundProperty, (ColorMap[ForegroundColor]));
                    //rangeOfWord.ApplyPropertyValue(TextElement.FontWeightProperty, FontWeights.Regular);
                    Console.WriteLine("ORIG =" + Text);
                    Console.WriteLine("bg =" + BackgroundColor + ",  fr = " + ForegroundColor + ",  text =" + RangeForNewText.Text);
                    TextMatch = NextMatch;
                }
                RangeForNewText = new TextRange(RichTextBox.Document.ContentEnd, RichTextBox.Document.ContentEnd)
                {
                    Text = Text.Substring(lastIndex) + "\u2028"//System.Environment.NewLine
                };
                RangeForNewText.ApplyPropertyValue(TextElement.ForegroundProperty, Brushes.White);
                RangeForNewText.ApplyPropertyValue(TextElement.BackgroundProperty, Brushes.Black);
                RichTextBox.ScrollToEnd();
            }));
        }
    }
}
