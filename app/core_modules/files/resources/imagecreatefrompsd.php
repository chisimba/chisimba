<?php
/*
*    PHP PSD reader class, v1.1
*
*    By Tim de Koning
*
*    Kingsquare Information Services, 10 jan 2007
*
*    example use:
*    ------------
*    <?php
*    include_once('classPhpPsdReader.php')
*    print imagejpeg(imagecreatefrompsd('test.psd'));
*    ?>
*
*    More info, bugs or requests, contact info@kingsquare.nl
*
*    Latest version and demo: http://www.kingsquare.nl/phppsdreader
*
*/


class PhpPsdReader {
    var $infoArray;
    var $fp;
    var $fileName;
    var $tempFileName;
    var $colorBytesLength;

    function PhpPsdReader($fileName) {
        set_time_limit(0);
        $this->infoArray = array();
        $this->fileName = $fileName;
        $this->fp = fopen($this->fileName,'r');

        if (fread($this->fp,4)=='8BPS') {
            $this->infoArray['version id'] = $this->_getWord();
            fseek($this->fp,6,SEEK_CUR); // 6 bytes of 0's
            $this->infoArray['channels'] = $this->_getWord();
            $this->infoArray['rows'] = $this->_getLong();
            $this->infoArray['columns'] = $this->_getLong();
            $this->infoArray['colorDepth'] = $this->_getWord();
            $this->infoArray['colorMode'] = $this->_getWord();


            /* COLOR MODE DATA SECTION */ //4bytes Length The length of the following color data.
            $this->infoArray['colorModeDataSectionLength'] = $this->_getLong(true);
            fseek($this->fp,$this->infoArray['colorModeDataSectionLength'],SEEK_CUR); // ignore this snizzle

            /*  IMAGE RESOURCES */
            $this->infoArray['imageResourcesSectionLength'] = $this->_getLong(false);
            fseek($this->fp,$this->infoArray['imageResourcesSectionLength'],SEEK_CUR); // ignore this snizzle

            /*  LAYER AND MASK */
            $this->infoArray['layerMaskDataSectionLength'] = $this->_getLong(false);
            fseek($this->fp,$this->infoArray['layerMaskDataSectionLength'],SEEK_CUR); // ignore this snizzle


            /*  IMAGE DATA */
            $this->infoArray['compressionType'] = $this->_getInteger(2);
            $this->infoArray['oneColorChannelPixelBytes'] = $this->infoArray['colorDepth']/8;
            $this->colorBytesLength = $this->infoArray['rows']*$this->infoArray['columns']*$this->infoArray['oneColorChannelPixelBytes'];
        } else {
            $this->infoArray['error'] = 'invalid or unsupported psd';
            return false;
        }
    }


    function getImage() {
        // decompress image data if required
        switch($this->infoArray['compressionType']) {
            // case 2:, case 3: zip not supported yet..
            case 1:
                // packed bits
                $this->infoArray['scanLinesByteCounts'] = array();
                for ($i=0; $i<($this->infoArray['rows']*$this->infoArray['channels']); $i++) $this->infoArray['scanLinesByteCounts'][] = $this->_getInteger(2);
                $this->tempFileName = tempnam(realpath('/tmp'),'decompressedImageData');
                $tfp = fopen($this->tempFileName,'wb');
                foreach ($this->infoArray['scanLinesByteCounts'] as $scanLinesByteCount) {
                    fwrite($tfp,$this->_getPackedBitsDecoded($this->_getBytes($scanLinesByteCount)));
                }
                fclose($tfp);
                fclose($this->fp);
                $this->fp = fopen($this->tempFileName,'r');
            default:
                // continue with current file handle;
                break;
        }

        // let's write pixel by pixel....
        $image = imagecreatetruecolor($this->infoArray['columns'],$this->infoArray['rows']);

        for ($rowPointer = 0; ($rowPointer < $this->infoArray['rows']); $rowPointer++) {
            for ($columnPointer = 0; ($columnPointer < $this->infoArray['columns']); $columnPointer++) {
                switch ($this->infoArray['channels']) {
                    case 1:
                        $r = $g = $b = $this->_getInteger($this->infoArray['oneColorChannelPixelBytes']);
                        break;

                    case 3:
                    default:
                        $r = $this->_getInteger($this->infoArray['oneColorChannelPixelBytes']);
                        $currentPointerPos = ftell($this->fp);
                        fseek($this->fp,$this->colorBytesLength-1,SEEK_CUR);
                        $g = $this->_getInteger($this->infoArray['oneColorChannelPixelBytes']);
                        fseek($this->fp,$this->colorBytesLength-1,SEEK_CUR);
                        $b =  $this->_getInteger($this->infoArray['oneColorChannelPixelBytes']);
                        fseek($this->fp,$currentPointerPos);
                        break;
                }
                $pixelColor = imagecolorallocate($image,$r,$g,$b);
                imagesetpixel($image,$columnPointer,$rowPointer,$pixelColor);
            }
        }
        fclose($this->fp);
        if (isset($this->tempFileName)) unlink($this->tempFileName);
        return $image;
    }

    /**
     *
     * PRIVATE FUNCTIONS
     *
     */

    function _getPackedBitsDecoded($string) {
        /*
        The PackBits algorithm will precede a block of data with a one byte header n, where n is interpreted as follows:
        n Meaning
        0 to 127 Copy the next n + 1 symbols verbatim
        -127 to -1 Repeat the next symbol 1 - n times
        -128 Do nothing

        Decoding:
        Step 1. Read the block header (n).
        Step 2. If the header is an EOF exit.
        Step 3. If n is non-negative, copy the next n + 1 symbols to the output stream and go to step 1.
        Step 4. If n is negative, write 1 - n copies of the next symbol to the output stream and go to step 1.

        */

        $stringPointer = 0;
        $returnString = '';

        while (1) {
            if (isset($string[$stringPointer])) $headerByteValue = $this->_unsignedToSigned(hexdec(bin2hex($string[$stringPointer])),1);
            else return $returnString;
            $stringPointer++;

            if ($headerByteValue >= 0) {
                for ($i=0; $i <= $headerByteValue; $i++) {
                    $returnString .= $string[$stringPointer];
                    $stringPointer++;
                }
            } else {
                if ($headerByteValue != -128) {
                    $copyByte = $string[$stringPointer];
                    $stringPointer++;

                    for ($i=0; $i < (1-$headerByteValue); $i++) {
                        $returnString .= $copyByte;
                    }
                }
            }
        }
    }

    function _unsignedToSigned($int,$byteSize=1) {
        switch($byteSize) {
            case 1:
                if ($int<128) return $int;
                else return -256+$int;
                break;

            case 2:
                if ($int<32768) return $int;
                else return -65536+$int;

            case 4:
                if ($int<2147483648) return $int;
                else return -4294967296+$int;

            default:
                return $int;
        }
    }

    function _getBytes($byteCount=1) {
        return fread($this->fp,$byteCount);
    }

    function _getWord()    {
        return intval(bin2hex(fread($this->fp,2)));
    }

    function _getLong($bigEndian = false) {
        return $this->_getInteger(4,$bigEndian);
    }

    function _getInteger($byteCount=1,$bigEndian = false) {
        if ($bigEndian) return hexdec($this->_hexReverse(bin2hex(fread($this->fp,$byteCount))));
        else return hexdec(bin2hex(fread($this->fp,$byteCount)));
    }

    function _hexReverse($hex) {
        $hex = preg_replace('![^0-9a-f]!im','',$hex);
        $output = '';
        if (strlen($hex)%2) return false;
        for ($pointer = strlen($hex);$pointer>=0;$pointer-=2) {
            $output .= substr($hex,$pointer,2);
        }
        return $output;
    }

}

/**
* Returns an image identifier representing the image obtained from the given filename, using only GD, returns an empty string on failure
*
* @param string $fileName
* @return image identifier
*/

function imagecreatefrompsd($fileName) {
    $psdReader = new PhpPsdReader($fileName);
    
    if (isset($psdReader->infoArray['error'])) {
        return '';
    } else {
        return $psdReader->getImage();
    }
}
?>