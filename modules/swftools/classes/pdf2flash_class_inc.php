<?php
/**
 *
 * Convert PDF to Flash
 *
 * This class uses SWFTools to convert a document from PDF to Flash
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   swftools
 * @author    Tohir Solomons _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: pdf2flash_class_inc.php 11970 2008-12-29 21:43:22Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Convert PDF to Flash
*
* @author Tohir Solomons
* @package swftools
*
*/
class pdf2flash extends object
{
    
    /**
     * Method to indicate whether to use a custom viewport or not
     */
    public $useCustomViewPort = TRUE;
    public $customViewPort = '';
    public $debug = FALSE;
    
    /**
    *
    * Intialiser for the pdf2flash class
    * @access public
    *
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objMkdir = $this->getObject('mkdir', 'files');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        
        $this->objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->convertLocation = $this->objSysconfig->getValue('CONVERTLOCATION', 'documentconverter');
        
        $this->customViewPort = $this->getResourcePath('a4final.swf');
    }
    
    /**
     * Method to convert a PDF to Flash
     * @param string $pdfFilePath Full Path to PDF File
     * @param string $destination Destination + filename.swf
     *      It will automatically append usrfiles/ to the destination
     *      Has to end in .swf
     *
     * This function intercepts and will either do the conversion
     * locally or remotely
     *      
     * @return boolean Whether file has been created or not
     */
    
    public function convert2SWF($inputFilename, $destination)
    {
        if ($this->convertLocation == 'remote') {
            $objRemote = $this->getObject('remoteconversion', 'documentconverter');
            
            return $objRemote->convert($inputFilename, $destination, 'swftools');
        } else {
            return $this->localConvert2SWF($inputFilename, $destination);
        }
    }
    
    /**
     * Method to convert a PDF to Flash
     * @param string $pdfFilePath Full Path to PDF File
     * @param string $destination Destination + filename.swf
     *      It will automatically append usrfiles/ to the destination
     *      Has to end in .swf
     * @return boolean Whether file has been created or not
     */
    public function localConvert2SWF($pdfFilePath, $destination)
    {
        // Create var for destination directory
        $path = dirname('/'.$destination);
        // Clean up file name
        $path = $this->objCleanUrl->cleanUpUrl($path);
        
        // Create var for destination file
        //$filepath = $this->objConfig->getcontentBasePath().'/'.$destination;
        // Clean Up file name
        $filepath = $this->objCleanUrl->cleanUpUrl($destination);
        
        // Get full path to viewer. This is the file with the prev/next buttons
        $viewport = $this->customViewPort;
        
        // Create Directory
        $this->objMkdir->mkdirs($path, 0777);
        
        if ($this->useCustomViewPort) {
            /*
             List of Original Coummand
             pdf2swf -t -o tmp.swf fsiu_elearn.pdf
             swfcombine -o flashfile.swf myviewport.swf viewport=tmp.swf 
             swfcombine --dummy `swfdump -XY tmp.swf` flashfile.swf -o flashfile.swf
            */
            
            // First Create SWF from PDF
            $command = 'pdf2swf -t -o '.$filepath.'1'.' '.$pdfFilePath;
            
            if ($this->debug) {
                echo $command;
            }
            
            log_debug($command);
            log_debug(shell_exec($command));
            
            if (!file_exists($filepath.'1')) {
                return FALSE;
            }
            
            // Then include the navigation
            $command = 'swfcombine -o '.$filepath.' '.$viewport.' viewport='.$filepath.'1';
            
            if ($this->debug) {
                echo $command;
            }
            
            log_debug($command);
            log_debug(shell_exec($command));
            
            if (!file_exists($filepath)) {
                return FALSE;
            }
            
            // Then fix the resolution
            $command = 'swfcombine --dummy `swfdump -XY '.$filepath.'1'.'` '.$filepath.' -o '.$filepath;
            
            if ($this->debug) {
                echo $command;
            }
            
            log_debug($command);
            log_debug(shell_exec($command));
            
            // Delete temp file
            unlink($filepath.'1');
            
            if (!file_exists($filepath)) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $command = "pdf2swf -bl -o $filepath {$pdfFilePath}";
            
            log_debug(shell_exec($command));
            if (file_exists($filepath)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
    
    public function generateHTMLWrapper($absolutePath, $relativePath, $destination)
    {
        $width = substr(shell_exec('swfdump -X '.$absolutePath), 3);
        $height = substr(shell_exec('swfdump -Y '.$absolutePath), 3);
        
        $width = trim ($width)+30;
        $height = trim ($height);
        
        $content = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="[-WIDTH-]" height="[-HEIGHT-]">
  <param name="movie" value="[-SOURCE-]">
  <param name="quality" value="high">
  <embed src="[-SOURCE-]" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="[-WIDTH-]" height="[-HEIGHT-]"></embed>
</object>';
        
        $content = str_replace('[-SOURCE-]', $relativePath, $content);
        $content = str_replace('[-HEIGHT-]', $height, $content);
        $content = str_replace('[-WIDTH-]', $width, $content);
        
        $handle = fopen($destination, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }

}
?>