<?php

/**
* Document Converter
*
* This class is a wrapper to PyODConverter / JODConverter and uses OpenOffice as
* a service.
*
* Website: http://www.artofsolving.com/opensource
*
* To run this class, you need:
* 1) either python or java installed
* 2) OpenOffice started as a service: soffice -headless -nofirststartwizard -accept="socket,port=8100;urp;"&
*
* @author Tohir Solomons
*/
class remoteconversion extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');

        require_once($this->getPearResource('XML/RPC.php'));
    }

    /**
    * Method to convert a document from one format to the other
    * @param string $inputFilename Absolute Path to the file
    * @param string $destination Absolute Path of the destination
    * @param string $type Type of conversion either openoffice or swftools
    *
    * Extension of destination file determines the type of conversions
    * For the list of supported formats, see: http://www.artofsolving.com/node/17
    *
    * Destination directory exists
    */
    public function convert($inputFilename, $destination, $type='openoffice')
    {
        if (!file_exists($inputFilename)) {
            return FALSE;
        }

        $filetosend = file_get_contents($inputFilename);
        $filetosend = base64_encode($filetosend);


        $remotePassword = $this->objSysconfig->getValue('REMOTEPASSWORD', 'documentconverter');

        // Params sometimes produce an error in htmlentities(), so suppress them.
        @$params = array(new XML_RPC_Value(basename($remotePassword), 'string'),new XML_RPC_Value(basename($inputFilename), 'string'), new XML_RPC_Value($filetosend, 'string'), new XML_RPC_Value(basename($destination), 'string'), new XML_RPC_Value("var4", 'string'));

        // Construct the method call (message).
        if ($type == 'swftools') {
            $msg = new XML_RPC_Message('document.convertPDF2SWF', $params);
        } else {
            $msg = new XML_RPC_Message('document.convertFile', $params);
        }

        $remoteServer = $this->objSysconfig->getValue('REMOTESERVER', 'documentconverter');
        $remoteAPIScript = $this->objSysconfig->getValue('REMOTESERVERAPISCRIPT', 'documentconverter');

        // The server is the 2nd arg, the path to the API module is the 1st.
        $cli = new XML_RPC_Client($remoteAPIScript, $remoteServer);

        // set the debug level to 0 for no debug, 1 for debug mode...
        //$cli->setDebug(1);

        // bomb off the message to the server
        $resp = $cli->send($msg);

        if (0 == $resp) {
            log_debug('Communication error: ' . $cli->errstr);
            return FALSE;
        } else if (0 != $resp->faultCode()) {
            /*
             * Display problems that have been gracefully caught and
             * reported by the Chisimba api.
             */
            log_debug( 'Fault Code: ' . $resp->faultCode());
            log_debug( 'Fault Reason: ' . $resp->faultString());

            return FALSE;
        } else {
            $val = $resp->value();
            $val = $val->scalarval();

            $code = substr($val, 0, 1);
            $contents = substr($val, 1);

            if ($code == 0) {
                return FALSE;
            } else if ($code == 1) {
                $this->putSingleFileContents($destination, $contents);
            } else if ($code == 2) {
                $this->putZippedFileContents($destination, $contents);
            }

            return TRUE;
        }
    }

    /**
     * Method to retrieve the converted file as a single file
     * @param string $destination Destination Filename
     * @param string $contents Contents of the file
     */
    private function putSingleFileContents($destination, $contents)
    {
        file_put_contents($destination, base64_decode($contents));
    }

    /**
     * Method to retrieve the converted file as a zipped file
     *
     * In this instance, the zipped file first needs to be extracted
     *
     * @param string $destination Destination Filename
     * @param string $contents Contents of the file
     */
    private function putZippedFileContents($destination, $contents)
    {
        //file_put_contents($destination, base64_decode($contents));

        // Create Temp Directory
        $dirName = md5($destination.time());

        // Full Path to Temp Directory
        $dirToSave = $this->objConfig->getContentBasePath().'/remoteconversion/'.$dirName;

        // Load Classes
        $objMkdir = $this->getObject('mkdir', 'files');
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');

        // Clean Paths
        $dirToSave = $objCleanUrl->cleanUpUrl($dirToSave);
        // Make Directory
        $objMkdir->mkdirs($dirToSave);

        // Store Zipped File Contents
        file_put_contents($dirToSave.'/contents.zip', base64_decode($contents));

        // Load Zip Class
        $objZip = $this->getObject('wzip', 'utilities');

        // Extract files
        $objZip->unZipArchive($dirToSave.'/contents.zip', dirname($destination));

        // Cleanup
        @unlink($dirToSave.'/contents.zip');
        @rmdir($dirToSave);

    }

}
?>