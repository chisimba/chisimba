<?PHP
/**
 * This example shows how to set a different target encoding
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);

require_once 'XML/Unserializer.php';

$xml = '<xml>
   <string>
   
    This XML
    document
    contains
    line breaks.

   </string>
 </xml>';


//  be careful to always use the ampersand in front of the new operator 
$unserializer = &new XML_Unserializer();
$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);

echo '<pre>';

echo "Default behavior (XML_UNSERIALIZER_WHITESPACE_TRIM)\n";
$data = $unserializer->unserialize($xml);
var_dump($data);
echo "\n\n";

echo "Normalize whitespace (XML_UNSERIALIZER_WHITESPACE_NORMALIZE)\n";
$unserializer->setOption(XML_UNSERIALIZER_OPTION_WHITESPACE, XML_UNSERIALIZER_WHITESPACE_NORMALIZE);
$data = $unserializer->unserialize($xml);
var_dump($data);
echo "\n\n";

echo "Keep whitespace (XML_UNSERIALIZER_WHITESPACE_KEEP)\n";
$unserializer->setOption(XML_UNSERIALIZER_OPTION_WHITESPACE, XML_UNSERIALIZER_WHITESPACE_KEEP);
$data = $unserializer->unserialize($xml);
var_dump($data);

echo '</pre>';
?>