<?PHP
/**
 * XML Serializer example
 *
 * This example makes use of the CData sections option
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);
require_once 'XML/Serializer.php';

$serializer = new XML_Serializer();
$serializer->setOption(XML_SERIALIZER_OPTION_INDENT, '    ');
$serializer->setOption(XML_SERIALIZER_OPTION_DEFAULT_TAG, 'item');
$serializer->setOption(XML_SERIALIZER_OPTION_CDATA_SECTIONS, true);

$data = array(
           'foo' => 'This is some text...',
           'bar' => '& even more text...',
           'test' => array('Foo', 'Foo & bar')
			);

$result = $serializer->serialize($data);

if( $result === true ) {
	$xml = $serializer->getSerializedData();
    echo '<pre>';
    echo htmlspecialchars($xml);
    echo '</pre>';
} else {
    $result->getMessage();
    exit();
}
?>