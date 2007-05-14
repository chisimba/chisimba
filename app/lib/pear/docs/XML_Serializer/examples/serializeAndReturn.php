<?PHP
/**
 * Example that uses the returnResult option to directly return the serialized
 * XML document in the serialize() method.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);

require_once 'XML/Serializer.php';

$options = array(
                    XML_SERIALIZER_OPTION_INDENT        => '    ',
                    XML_SERIALIZER_OPTION_RETURN_RESULT => true
                );

$serializer = &new XML_Serializer($options);

$foo = PEAR::raiseError('Just a test', 1234);

$result = $serializer->serialize($foo);

echo '<pre>';
echo htmlspecialchars($result);
echo '</pre>';
?>