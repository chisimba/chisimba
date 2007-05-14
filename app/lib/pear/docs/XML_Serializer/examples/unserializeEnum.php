<?PHP
/**
 * This example shows how to use the XML_UNSERIALIZER_OPTION_FORCE_ENUM
 * option
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);

require_once 'XML/Unserializer.php';

$xml1 = '<root>
   <item>
     <name>schst</name>
   </item>
   <item>
     <name>luckec</name>
   </item>
 </root>';
    
$xml2 = '<root>
   <item>
     <name>schst</name>
   </item>
 </root>';
    
$options = array(
                  XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('item')
                );

                
//  be careful to always use the ampersand in front of the new operator 
$unserializer = &new XML_Unserializer($options);

// userialize the document
$status = $unserializer->unserialize($xml1);

if (PEAR::isError($status)) {
    echo 'Error: ' . $status->getMessage();
} else {
    $data = $unserializer->getUnserializedData();
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// userialize the document
$status = $unserializer->unserialize($xml2);

if (PEAR::isError($status)) {
    echo 'Error: ' . $status->getMessage();
} else {
    $data = $unserializer->getUnserializedData();
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
?>