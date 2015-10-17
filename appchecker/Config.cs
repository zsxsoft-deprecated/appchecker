using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
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
        public event PropertyChangedEventHandler PropertyChanged;

        private void OnPropertyChanged(PropertyChangedEventArgs e)
        {
            PropertyChangedEventHandler h = PropertyChanged;
            if (h != null)
                h(this, e);
        }
    }

    public class Config
    {
        public static CheckerInfo Data = new CheckerInfo();
        public static bool Load()
        {
            DataContractJsonSerializer Serializer = new DataContractJsonSerializer(typeof(CheckerInfo));
            FileStream Fs = File.Open("Config.json", FileMode.Open);
            Data = (CheckerInfo)Serializer.ReadObject(Fs);
            Fs.Close();
            return true;
        }

        public static bool Save()
        {
            DataContractJsonSerializer Serializer = new DataContractJsonSerializer(typeof(CheckerInfo));
            FileStream Fs = File.Open("Config.json", FileMode.Create, FileAccess.Write);
            Serializer.WriteObject(Fs, Data);
            Fs.Close();
            return true;
        }
    }
}