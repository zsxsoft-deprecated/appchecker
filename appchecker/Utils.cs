using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Media;

namespace AppChecker
{
    class Utils
    {
        public static SolidColorBrush ConvertColor(int waitRet)
        {
            byte[] bytes = BitConverter.GetBytes(waitRet);
            return new SolidColorBrush(Color.FromArgb(bytes[0], bytes[1], bytes[2], bytes[3]));
        }
    }
}
