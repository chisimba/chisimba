<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

 /**
 * Class for reading and writing arrays in XML
 * The Serialize and Unserialize classes extend PEAR
 * This is a wrapper class for them, that extends object
 *
 
 * @category  Chisimba
 * @package   utilities
 * @author James Scoble
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

require_once('XML/Unserializer.php');

class xmlserial extends object
{
    /**
    * Basic Object init function
    */
    function init()
    {
    }

    /**
    * Method to read in XML, and return an array
    * by calling functions in the Unserialize class
    * @param string $file the XML to read
    * @param string $type whether its a filename or a string
    * @returns array $data
    */
    function readXML($file,$type=TRUE)
    {
        $objUnserializer = &new XML_Unserializer();
        $objUnserializer->unserialize($file,$type);
        $data = $objUnserializer->getUnserializedData();
        return $data;
    }

    /**
    * Method to export an array as XML
    * by calling functions in the Serialize class
    * @param array $data
    * @returns string $xmldata
    */
    function writeXML($data)
    {
        require_once('XML/Serializer.php');
        $objSerializer = &new XML_Serializer(NULL);
        $objSerializer->serialize($data);
        $xmldata=$objSerializer->getSerializedData();
        return $xmldata;
    }

}
?>