<?php

/**
 * Class to dynamically create a zip file (archive) of file(s) and/or directory
 *
 * @author Rochak Chauhan  www.rochakchauhan.com
 * @package CreateZipFile
 * @see Distributed under "General Public License"
 *
 * @version 1.0
 */
class createzipfile extends object{

    public $compressedData = array();
    public $centralDirectory = array(); // central directory
    public $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    public $oldOffset = 0;

    function init(){
        
    }
    /**
     * Function to create the directory where the file(s) will be unzipped
     *
     * @param string $directoryName
     * @access public
     * @return void
     */
    public function addDirectory($directoryName) {
        $directoryName = str_replace("\\", "/", $directoryName);
        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x0a\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x00\x00\x00\x00";
        $feedArrayRow .= pack("V", 0);
        $feedArrayRow .= pack("V", 0);
        $feedArrayRow .= pack("V", 0);
        $feedArrayRow .= pack("v", strlen($directoryName));
        $feedArrayRow .= pack("v", 0);
        $feedArrayRow .= $directoryName;
        $feedArrayRow .= pack("V", 0);
        $feedArrayRow .= pack("V", 0);
        $feedArrayRow .= pack("V", 0);
        $this->compressedData[] = $feedArrayRow;
        $newOffset = strlen(implode("", $this->compressedData));
        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x0a\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V", 0);
        $addCentralRecord .= pack("V", 0);
        $addCentralRecord .= pack("V", 0);
        $addCentralRecord .= pack("v", strlen($directoryName));
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("V", 16);
        $addCentralRecord .= pack("V", $this->oldOffset);
        $this->oldOffset = $newOffset;
        $addCentralRecord .= $directoryName;
        $this->centralDirectory[] = $addCentralRecord;
    }

    /**
     * Function to add file(s) to the specified directory in the archive
     *
     * @param string $directoryName
     * @param string $data
     * @return void
     * @access public
     */
    public function addFile($data, $directoryName) {
        $directoryName = str_replace("\\", "/", $directoryName);
        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x14\x00";
        $feedArrayRow .= "\x00\x00";
        $feedArrayRow .= "\x08\x00";
        $feedArrayRow .= "\x00\x00\x00\x00";
        $uncompressedLength = strlen($data);
        $compression = crc32($data);
        $gzCompressedData = gzcompress($data);
        $gzCompressedData = substr(substr($gzCompressedData, 0, strlen($gzCompressedData) - 4), 2);
        $compressedLength = strlen($gzCompressedData);
        $feedArrayRow .= pack("V", $compression);
        $feedArrayRow .= pack("V", $compressedLength);
        $feedArrayRow .= pack("V", $uncompressedLength);
        $feedArrayRow .= pack("v", strlen($directoryName));
        $feedArrayRow .= pack("v", 0);
        $feedArrayRow .= $directoryName;
        $feedArrayRow .= $gzCompressedData;
        $feedArrayRow .= pack("V", $compression);
        $feedArrayRow .= pack("V", $compressedLength);
        $feedArrayRow .= pack("V", $uncompressedLength);
        $this->compressedData[] = $feedArrayRow;
        $newOffset = strlen(implode("", $this->compressedData));
        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x14\x00";
        $addCentralRecord .="\x00\x00";
        $addCentralRecord .="\x08\x00";
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V", $compression);
        $addCentralRecord .= pack("V", $compressedLength);
        $addCentralRecord .= pack("V", $uncompressedLength);
        $addCentralRecord .= pack("v", strlen($directoryName));
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("v", 0);
        $addCentralRecord .= pack("V", 32);
        $addCentralRecord .= pack("V", $this->oldOffset);
        $this->oldOffset = $newOffset;
        $addCentralRecord .= $directoryName;
        $this->centralDirectory[] = $addCentralRecord;
    }

    /**
     * Function to return the zip file
     *
     * @return zipfile (archive)
     * @access public
     * @return void
     */
    public function getZippedfile() {
        $data = implode("", $this->compressedData);
        $controlDirectory = implode("", $this->centralDirectory);
        return
        $data .
        $controlDirectory .
        $this->endOfCentralDirectory .
        pack("v", sizeof($this->centralDirectory)) .
        pack("v", sizeof($this->centralDirectory)) .
        pack("V", strlen($controlDirectory)) .
        pack("V", strlen($data)) .
        "\x00\x00";
    }

    /**
     *
     * Function to force the download of the archive as soon as it is created
     *
     * @param archiveName string - name of the created archive file
     * @access public
     * @return ZipFile via Header
     */
    public function forceDownload($archiveName) {
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Security checks
        if ($archiveName == "") {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
            exit;
        } elseif (!file_exists($archiveName)) {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
            exit;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . basename($archiveName) . ";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($archiveName));
        readfile("$archiveName");
    }

    /**
     * Function to parse a directory to return all its files and sub directories as array
     *
     * @param string $dir
     * @access protected
     * @return array
     */
    protected function parseDirectory($rootPath, $seperator="/") {
        $fileArray = array();
        $handle = opendir($rootPath);
        while (($file = @readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($rootPath . $seperator . $file)) {
                    $array = $this->parseDirectory($rootPath . $seperator . $file);
                    $fileArray = array_merge($array, $fileArray);
                } else {
                    $fileArray[] = $rootPath . $seperator . $file;
                }
            }
        }
        return $fileArray;
    }

    /**
     * Function to Zip entire directory with all its files and subdirectories
     *
     * @param string $dirName
     * @access public
     * @return void
     */
    public function zipDirectory($dirName, $outputDir) {
        if (!is_dir($dirName)) {
            trigger_error("CreateZipFile FATAL ERROR: Could not locate the specified directory $dirName", E_USER_ERROR);
        }
        $tmp = $this->parseDirectory($dirName);
        $count = count($tmp);
        $this->addDirectory($outputDir);
        for ($i = 0; $i < $count; $i++) {
            $fileToZip = trim($tmp[$i]);
            $newOutputDir = substr($fileToZip, 0, (strrpos($fileToZip, '/') + 1));
            $outputDir = $outputDir . $newOutputDir;
            $fileContents = file_get_contents($fileToZip);
            $this->addFile($fileContents, $fileToZip);
        }
    }

}

?>