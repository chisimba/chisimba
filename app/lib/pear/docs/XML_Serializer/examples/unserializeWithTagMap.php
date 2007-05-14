<?PHP
/**
 * This example shows how to use the tagMap option.
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);

// this is a simple XML document
$xml = '<root>' .
       '  <foo>FOO</foo>' .
       '  <bar>BAR</bar>' .
       '</root>';

require_once 'XML/Unserializer.php';

// complex structures are arrays, the key is the attribute 'handle' or 'name', if handle is not present
$options = array(
                 XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'array',
                 XML_UNSERIALIZER_OPTION_TAG_MAP     => array(
                                                            'foo' => 'bar',
                                                            'bar' => 'foo'
                                                           )
                );

//  be careful to always use the ampersand in front of the new operator 
$unserializer = &new XML_Unserializer($options);

// userialize the document
$status = $unserializer->unserialize($xml, false);    

if (PEAR::isError($status)) {
    echo 'Error: ' . $status->getMessage();
} else {
    $data = $unserializer->getUnserializedData();
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// a tad more complex
$xml = '<root>' .
       '  <foo>'.
       '    <tomato>45</tomato>'.
       '  </foo>'.
       '  <bar>'.
       '    <tomato>31</tomato>'.
       '  </bar>'.
       '</root>';

// userialize the document
$status = $unserializer->unserialize($xml, false);    

if (PEAR::isError($status)) {
    echo 'Error: ' . $status->getMessage();
} else {
    $data = $unserializer->getUnserializedData();
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
?>