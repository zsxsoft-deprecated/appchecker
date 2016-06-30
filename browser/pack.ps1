$script:arch = $null;
$script:target = '1.2.5';
$script:platform = $null;

Function DeleteUselessFiles() {
    Get-ChildItem ./out | ForEach-Object -Process {
        if ($_ -is [System.IO.DirectoryInfo]) {
            Remove-Item -Path ./out/$_/locales -Recurse
#            Remove-Item -Path ./out/$_/pdf.dll
            Remove-Item -Path ./out/$_/version
            Remove-Item -Path ./out/$_/LICENSE
#            Remove-Item -Path ./out/$_/xinput1_3.dll
#            Remove-Item -Path ./out/$_/d3dcompiler_47.dll
#            Remove-Item -Path ./out/$_/vccorlib120.dll
#            Copy-Item -Path ./config.js -Destination ./out/$_/
        }
    }
}

Function BuildElectron($platform, $arch) {
    $script:platform = $platform
    $script:arch = $arch
    electron-packager ./ browser `
        --asar `
        --overwrite `
        --icon=danmu.ico `
        --app-version=0.0.1 `
        --out="./out" `
        --ignore="""\.(pdb|exp|lib|map|obj|tlog|vcxproj|gypi|sln|md|log|bin)$|out|node-gyp|nw|nw-.*|.git""" `
        --arch=$script:arch --platform=$script:platform --version=$script:target `
        --version-string.ProductName="AppChecker - Electron Module" `
        --version-string.CompanyName="zsx (http://www.zsxsoft.com)" `
        --version-string.OriginalFilename="browser.exe" `
        --version-string.FileVersion="0.0.1" `
        --version-string.InternalName="AppChecker" `
        --version-string.FileDescription="Electron module for AppChecker" `
        --version-string.LegalCopyright="https://github.com/zsxsoft/appchecker/" 
}

Function RebuildModule($module) {
    Write-Host 'Building' $module 
    cd node_modules/$module
    node-gyp rebuild --arch=$script:arch --platform=$script:platform --target=$script:target --dist-url=https://atom.io/download/atom-shell --msvs_version=2015
    cd ../../
    Write-Host $module "successful built!"
}

BuildElectron 'win32' 'ia32'
DeleteUselessFiles