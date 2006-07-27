<?
 /**
 * Class for reading and writing arrays in XML
 * The Serialize and Unserialize classes extend PEAR
 * This is a wrapper class for them, that extends object
 *
 * @author James Scoble
 */

require_once('xml/Unserializer.php');

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
        require_once('xml/Serializer.php');
        $objSerializer = &new XML_Serializer(NULL);
        $objSerializer->serialize($data);
        $xmldata=$objSerializer->getSerializedData();
        return $xmldata;
    }
    
}
?>