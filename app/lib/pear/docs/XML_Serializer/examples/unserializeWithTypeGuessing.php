<?PHP
/**
 * Example that shows how to use XML_Unserializer to
 * automatically adjust the type of the data
 *
 * @author      Stephan Schmidt <schst@php.net>
 * @category    XML
 * @package     XML_Serializer
 * @subpackage  Examples
 */
error_reporting(E_ALL);

/**
 * uses XML_Unserializer
 */
require_once '../Unserializer.php';

$xml = <<<EOT
<root>
   <string>Just a string...</string>
   <booleanValue>true</booleanValue>
   <foo>-563</foo>
   <bar>4.73736</bar>
   <array foo="false" bar="12">true</array>
</root>
EOT;

$unserializer = &new XML_Unserializer();
$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
$unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
$unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);

$data = $unserializer->unserialize($xml);    
echo '<pre>';
var_dump($data);
echo '</pre>';
?>