﻿using System;
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
        // From ConEmu
        // https://github.com/Maximus5/ConEmu/blob/e1f0c20cf546f01585d1dcb6c3115535fff12ced/src/ConEmuHk/Ansi.cpp
        static int[] ClrMap = { 0, 4, 2, 6, 1, 5, 3, 7 };
        // XTerm-256 colors
        static long[] RgbMap = {0,4,2,6,1,5,3,7, 8+0,8+4,8+2,8+6,8+1,8+5,8+3,8+7, // System Ansi colors
			/*16*/0x000000, /*17*/0x5f0000, /*18*/0x870000, /*19*/0xaf0000, /*20*/0xd70000, /*21*/0xff0000, /*22*/0x005f00, /*23*/0x5f5f00, /*24*/0x875f00, /*25*/0xaf5f00, /*26*/0xd75f00, /*27*/0xff5f00,
			/*28*/0x008700, /*29*/0x5f8700, /*30*/0x878700, /*31*/0xaf8700, /*32*/0xd78700, /*33*/0xff8700, /*34*/0x00af00, /*35*/0x5faf00, /*36*/0x87af00, /*37*/0xafaf00, /*38*/0xd7af00, /*39*/0xffaf00,
			/*40*/0x00d700, /*41*/0x5fd700, /*42*/0x87d700, /*43*/0xafd700, /*44*/0xd7d700, /*45*/0xffd700, /*46*/0x00ff00, /*47*/0x5fff00, /*48*/0x87ff00, /*49*/0xafff00, /*50*/0xd7ff00, /*51*/0xffff00,
			/*52*/0x00005f, /*53*/0x5f005f, /*54*/0x87005f, /*55*/0xaf005f, /*56*/0xd7005f, /*57*/0xff005f, /*58*/0x005f5f, /*59*/0x5f5f5f, /*60*/0x875f5f, /*61*/0xaf5f5f, /*62*/0xd75f5f, /*63*/0xff5f5f,
			/*64*/0x00875f, /*65*/0x5f875f, /*66*/0x87875f, /*67*/0xaf875f, /*68*/0xd7875f, /*69*/0xff875f, /*70*/0x00af5f, /*71*/0x5faf5f, /*72*/0x87af5f, /*73*/0xafaf5f, /*74*/0xd7af5f, /*75*/0xffaf5f,
			/*76*/0x00d75f, /*77*/0x5fd75f, /*78*/0x87d75f, /*79*/0xafd75f, /*80*/0xd7d75f, /*81*/0xffd75f, /*82*/0x00ff5f, /*83*/0x5fff5f, /*84*/0x87ff5f, /*85*/0xafff5f, /*86*/0xd7ff5f, /*87*/0xffff5f,
			/*88*/0x000087, /*89*/0x5f0087, /*90*/0x870087, /*91*/0xaf0087, /*92*/0xd70087, /*93*/0xff0087, /*94*/0x005f87, /*95*/0x5f5f87, /*96*/0x875f87, /*97*/0xaf5f87, /*98*/0xd75f87, /*99*/0xff5f87,
			/*100*/0x008787, /*101*/0x5f8787, /*102*/0x878787, /*103*/0xaf8787, /*104*/0xd78787, /*105*/0xff8787, /*106*/0x00af87, /*107*/0x5faf87, /*108*/0x87af87, /*109*/0xafaf87, /*110*/0xd7af87, /*111*/0xffaf87,
			/*112*/0x00d787, /*113*/0x5fd787, /*114*/0x87d787, /*115*/0xafd787, /*116*/0xd7d787, /*117*/0xffd787, /*118*/0x00ff87, /*119*/0x5fff87, /*120*/0x87ff87, /*121*/0xafff87, /*122*/0xd7ff87, /*123*/0xffff87,
			/*124*/0x0000af, /*125*/0x5f00af, /*126*/0x8700af, /*127*/0xaf00af, /*128*/0xd700af, /*129*/0xff00af, /*130*/0x005faf, /*131*/0x5f5faf, /*132*/0x875faf, /*133*/0xaf5faf, /*134*/0xd75faf, /*135*/0xff5faf,
			/*136*/0x0087af, /*137*/0x5f87af, /*138*/0x8787af, /*139*/0xaf87af, /*140*/0xd787af, /*141*/0xff87af, /*142*/0x00afaf, /*143*/0x5fafaf, /*144*/0x87afaf, /*145*/0xafafaf, /*146*/0xd7afaf, /*147*/0xffafaf,
			/*148*/0x00d7af, /*149*/0x5fd7af, /*150*/0x87d7af, /*151*/0xafd7af, /*152*/0xd7d7af, /*153*/0xffd7af, /*154*/0x00ffaf, /*155*/0x5fffaf, /*156*/0x87ffaf, /*157*/0xafffaf, /*158*/0xd7ffaf, /*159*/0xffffaf,
			/*160*/0x0000d7, /*161*/0x5f00d7, /*162*/0x8700d7, /*163*/0xaf00d7, /*164*/0xd700d7, /*165*/0xff00d7, /*166*/0x005fd7, /*167*/0x5f5fd7, /*168*/0x875fd7, /*169*/0xaf5fd7, /*170*/0xd75fd7, /*171*/0xff5fd7,
			/*172*/0x0087d7, /*173*/0x5f87d7, /*174*/0x8787d7, /*175*/0xaf87d7, /*176*/0xd787d7, /*177*/0xff87d7, /*178*/0x00afd7, /*179*/0x5fafd7, /*180*/0x87afd7, /*181*/0xafafd7, /*182*/0xd7afd7, /*183*/0xffafd7,
			/*184*/0x00d7d7, /*185*/0x5fd7d7, /*186*/0x87d7d7, /*187*/0xafd7d7, /*188*/0xd7d7d7, /*189*/0xffd7d7, /*190*/0x00ffd7, /*191*/0x5fffd7, /*192*/0x87ffd7, /*193*/0xafffd7, /*194*/0xd7ffd7, /*195*/0xffffd7,
			/*196*/0x0000ff, /*197*/0x5f00ff, /*198*/0x8700ff, /*199*/0xaf00ff, /*200*/0xd700ff, /*201*/0xff00ff, /*202*/0x005fff, /*203*/0x5f5fff, /*204*/0x875fff, /*205*/0xaf5fff, /*206*/0xd75fff, /*207*/0xff5fff,
			/*208*/0x0087ff, /*209*/0x5f87ff, /*210*/0x8787ff, /*211*/0xaf87ff, /*212*/0xd787ff, /*213*/0xff87ff, /*214*/0x00afff, /*215*/0x5fafff, /*216*/0x87afff, /*217*/0xafafff, /*218*/0xd7afff, /*219*/0xffafff,
			/*220*/0x00d7ff, /*221*/0x5fd7ff, /*222*/0x87d7ff, /*223*/0xafd7ff, /*224*/0xd7d7ff, /*225*/0xffd7ff, /*226*/0x00ffff, /*227*/0x5fffff, /*228*/0x87ffff, /*229*/0xafffff, /*230*/0xd7ffff, /*231*/0xffffff,
			/*232*/0x080808, /*233*/0x121212, /*234*/0x1c1c1c, /*235*/0x262626, /*236*/0x303030, /*237*/0x3a3a3a, /*238*/0x444444, /*239*/0x4e4e4e, /*240*/0x585858, /*241*/0x626262, /*242*/0x6c6c6c, /*243*/0x767676,
			/*244*/0x808080, /*245*/0x8a8a8a, /*246*/0x949494, /*247*/0x9e9e9e, /*248*/0xa8a8a8, /*249*/0xb2b2b2, /*250*/0xbcbcbc, /*251*/0xc6c6c6, /*252*/0xd0d0d0, /*253*/0xdadada, /*254*/0xe4e4e4, /*255*/0xeeeeee
        };
        public Log()
        {
            InitializeComponent();
        }

        public void Clear()
        {
            Application.Current.Dispatcher.Invoke((Action)delegate
            {
                richTextBox.SelectAll();
                richTextBox.Selection.Text = "";
            });
        }

        [System.Runtime.InteropServices.DllImport("kernel32")]
        static extern bool AllocConsole();
        /// <summary>
        /// Writes the line.
        /// </summary>
        /// <param name="Text">The text.</param>
        public void WriteLine(string Text)
        {
            if (Text == null) return;
            byte[] buffer = Encoding.GetEncoding("GBK").GetBytes(Text);
            Text = Encoding.UTF8.GetString(buffer);
            //AllocConsole();
            Application.Current.Dispatcher.Invoke((Action)delegate
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
                    rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                    n = m.NextMatch();
                    lastIndex = m.Index + m.Length;
                    if (n.Success)
                    {
                        rangeOfWord.Text = Text.Substring(lastIndex, n.Index - lastIndex);
                    } else
                    {
                        rangeOfWord.Text = Text.Substring(lastIndex);
                    }
                    rangeOfWord.ApplyPropertyValue(TextElement.BackgroundProperty, (Utils.ConvertColor(RgbMap[(BackgroundColor & 0xF0) >> 4])));
                    rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, (Utils.ConvertColor(RgbMap[ForegroundColor & 0xFF])));
                    rangeOfWord.ApplyPropertyValue(TextElement.FontWeightProperty, FontWeights.Regular);
                    Console.WriteLine("ORIG =" + Text);
                    Console.WriteLine("bg=" + BackgroundColor + ",  fr= " + ForegroundColor + ",  text=" + rangeOfWord.Text);
                    
                    m = n;
                }
                rangeOfWord = new TextRange(richTextBox.Document.ContentEnd, richTextBox.Document.ContentEnd);
                rangeOfWord.Text = Text.Substring(lastIndex) + "\u2028";
                rangeOfWord.ApplyPropertyValue(TextElement.ForegroundProperty, Brushes.White);
                rangeOfWord.ApplyPropertyValue(TextElement.BackgroundProperty, Brushes.Black);
                richTextBox.ScrollToEnd();
            });
        }
    }
}
