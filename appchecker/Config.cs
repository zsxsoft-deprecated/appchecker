using System.Runtime.Serialization.Json;
using System.Runtime.Serialization;
using System.IO;
using System.ComponentModel;

namespace AppChecker
{
    [DataContract]
    public class CheckerInfo : INotifyPropertyChanged
    {
        private string _AppId;
        /// <summary>
        /// Gets or sets App ID
        /// </summary>
        [DataMember]
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
        [DataMember]
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
        [DataMember]
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
        private string _WebsiteUrl;
        /// <summary>
        /// Gets or sets Z-BlogPHP Path.
        /// </summary>
        [DataMember]
        public string WebsiteUrl
        {
            get
            {
                return _WebsiteUrl;
            }
            set
            {
                if (_WebsiteUrl == value) return;
                _WebsiteUrl = value;
                OnPropertyChanged(new PropertyChangedEventArgs("WebsiteUrl"));
            }
        }

        private string _PHPIniPath;
        /// <summary>
        /// Gets or sets php.ini Path.
        /// </summary>
        [DataMember]
        public string PHPIniPath
        {
            get
            {
                return _PHPIniPath;
            }
            set
            {
                if (_PHPIniPath == value) return;
                _PHPIniPath = value;
                OnPropertyChanged(new PropertyChangedEventArgs("PHPIniPath"));
            }
        }

        public event PropertyChangedEventHandler PropertyChanged;

        private void OnPropertyChanged(PropertyChangedEventArgs e)
        {
            PropertyChanged?.Invoke(this, e);
        }
    }

    public class Config
    {
        public static CheckerInfo Data = new CheckerInfo();
        public static bool Load()
        {
            using (FileStream Fs = File.Open("Config.json", FileMode.Open))
            {
                Data = (CheckerInfo)new DataContractJsonSerializer(typeof(CheckerInfo)).ReadObject(Fs);
            }
            return true;
        }

        public static bool Save()
        {
            using (FileStream Fs = File.Open("Config.json", FileMode.Create, FileAccess.Write))
            {
                new DataContractJsonSerializer(typeof(CheckerInfo)).WriteObject(Fs, Data);
            }
            return true;
        }
    }
}