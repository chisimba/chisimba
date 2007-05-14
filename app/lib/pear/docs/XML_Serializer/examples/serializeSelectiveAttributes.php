<?PHP
/**
 * This shows that XML_Serializer is able to work with
 * empty arrays
 *
 * @author Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);
require_once 'XML/Serializer.php';

$data = array(
                array('name' => 'Superman', 'age' => 34, 'realname' => 'Clark Kent'),
                array('name' => 'Batman', 'age' => 32, 'realname' => 'Bruce Wayne'),
                'villain' => array('name' => 'Professor Zoom', 'age' => 'unknown', 'realname' => 'Hunter Zolomon')
            );

$serializer = new XML_Serializer();
$serializer->setOption(XML_SERIALIZER_OPTION_INDENT, '    ');
$serializer->setOption(XML_SERIALIZER_OPTION_DEFAULT_TAG, 'hero');

$serializer->serialize($data);
echo '<pre>';
echo "Default behaviour:\n";
echo htmlspecialchars($serializer->getSerializedData());
echo '</pre>';

$serializer->setOption(XML_SERIALIZER_OPTION_SCALAR_AS_ATTRIBUTES, true);

$serializer->serialize($data);
echo '<pre>';
echo "XML_SERIALIZER_OPTION_SCALAR_AS_ATTRIBUTES = true:\n";
echo htmlspecialchars($serializer->getSerializedData());
echo '</pre>';

$serializer->setOption(XML_SERIALIZER_OPTION_SCALAR_AS_ATTRIBUTES, array(
                                                                       'hero'    => array('name', 'age'),
                                                                       'villain' => array('realname')
                                                                       )
                      );

$serializer->serialize($data);
echo '<pre>';
echo "XML_SERIALIZER_OPTION_SCALAR_AS_ATTRIBUTES is an array:\n";
echo htmlspecialchars($serializer->getSerializedData());
echo '</pre>';
?>