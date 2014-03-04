<?php

/**
 * Document Converter
 *
 * This class is a wrapper to PyODConverter / JODConverter and uses OpenOffice as
 * a service.
 *
 * Website: http://www.artofsolving.com/opensource
 * Website: http://code.google.com/p/jodconverter/
 *
 * To run this class, you need:
 * 1) either python or java installed
 * 2a) For Linux, OpenOffice started as a service:
 *   soffice -headless -nofirststartwizard -accept="socket,port=8100;urp;"&
 *   soffice -headless -nofirststartwizard -accept="socket,host=localhost,port=8100;urp;StarOffice.Service"
 * Check if it is running:
 *   netstat -an | grep '8100'
 *   ps aux | grep 'soffice'
 * 2b) For Windows, open a command prompt, and
 * navigate to the OpenOffice.org program
 * folder e.g.
 *   C:\Program Files\OpenOffice.org 2.3\program
 * or
 *   C:\Program Files (x86)\OpenOffice.org 3\program
 * then run:
 *   soffice.exe -headless -nologo -norestore -accept=socket,host=localhost,port=8100;urp;StarOffice.ServiceManager
 *   soffice.exe -headless -nofirststartwizard -accept="socket,host=localhost,port=8100;urp;StarOffice.Service"
 * You can check if the service is running by
 * typing the following command at the command
 * prompt:
 *   netstat -anp tcp
 * The following line should appear in the
 * displayed results:
 *   TCP 127.0.0.1:8100 0.0.0.0:0 LISTENING
 *
 * @author Tohir Solomons
 * @author Jeremy O'Connor
 */
class convertdoc extends object {

    /**
     * Constructor
     */
    public function init() {
        //system('soffice -headless -accept="socket,port=8100;urp;"');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysconfig = $this->getObject('dbsysconfig', 'sysconfig');

        $this->convertLocation = $this->objSysconfig->getValue('CONVERTLOCATION', 'documentconverter');
    }

    /**
     * Method to convert a document from one format to the other
     * @param string $inputFilename Absolute Path to the file
     * @param string $destination Absolute Path of the destination
     *
     * This function intercepts and will either do the conversion locally or remotely
     */
    public function convert($inputFilename, $destination) {
        if ($this->convertLocation == 'remote') {
            $objRemote = $this->getObject('remoteconversion');

            return $objRemote->convert($inputFilename, $destination);
        } else {
            return $this->localConvert($inputFilename, $destination);
        }
    }

    /**
     * Method to convert a document from one format to the other
     * @param string $inputFilename Absolute Path to the file
     * @param string $destination Absolute Path of the destination
     *
     * Extension of destination file determines the type of conversions
     * For the list of supported formats, see: http://www.artofsolving.com/node/17
     *
     * Destination directory exists
     */
    public function localConvert($inputFilename, $destination) {
        if (!file_exists($inputFilename)) {
            return 'inputfiledoesnotexist';
        }

        // At the moment, only the java version is supported
        // Todo: Make it configurable to do it via python
        $result = $this->javaConvert($inputFilename, $destination);

        return $result;
    }

    /**
     * Make an OS dependant path that can be pasent to shell_exec().
     *
     * @param string The OS independant path
     * @return string The OS dependant path
     */
    private function makeOSPath($path) {
        //return $path;
        return escapeshellarg($path);
        /*
        $isWindowsOS = strtoupper(substr(php_uname('s'), 0, 7)) === 'WINDOWS';
        //return $isWindowsOS?('"'.str_replace('/', '\\', $path).'"'):$path;
        return $isWindowsOS?escapeshellarg($path):$path;
        */
    }

    /**
     * Method to convert a document using Java
     *
     * @param string $inputFilename Absolute Path to the file
     * @param string $destination Absolute Path of the destination
     * @return string Conversion Result
     */
    private function javaConvert($inputFilename, $destination) {


        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $oolocation = $objSysConfig->getValue('OO_LOCATION', 'documentconverter');

        $command = 'java  -jar ' . escapeshellarg($this->getResourcePath('jodconverter-core-3.0-beta-4/lib/jodconverter-core-3.0-beta-4.jar')) . ' ' . escapeshellarg($inputFilename) . ' ' . escapeshellarg($destination);
        log_debug($command);
        $results = shell_exec($command);
        log_debug($results);

        if (file_exists($destination)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to convert a document using Java
     *
     * @param string $inputFilename Absolute Path to the file
     * @param string $destination Absolute Path of the destination
     * @return string Conversion Result
     */
    private function pythonConvert($inputFilename, $destination) {
        $command = 'python ' . $this->getResourcePath('pyodconverter-0.9/DocumentConverter.py') . '  ' . $inputFilename . ' ' . $destination;

        //echo $command;

        return system($command);
    }

}

?>
