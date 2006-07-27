<?php
/**
* Wrapper to Get Id3
*
* This class is a wrapper to GetId3 which is a media analyser script
* It provides information on media files such as width, height,
* compression, codecs, bitrates, play length, etc.
*
* @author Tohir Solomons
*/
include ('modules/files/resources/getid3/getid3.php');
class getid3analyzer extends object
{
    
    /**
    * Get Id3 Object
    * @var $_objGetID3
    */
    private $_objGetID3;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->_objGetID3 = new getid3();
    }
    
    /**
    * Method to Analyze a Media File
    * @param string $file Path to File
    * @return array Media Information
    */
    public function analyze($file)
    {
        return $this->_objGetID3->analyze($file);
    }
}

?>