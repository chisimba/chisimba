<?PHP
/**
 * Example to demonstrate the encodeFunction and decodeFunction
 * options.
 *
 * This allows you to apply callbacks to your data that is called
 * on all strings while serializing and unserializing.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';
    require_once 'XML/Unserializer.php';

    $options = array(
                      XML_SERIALIZER_OPTION_INDENT               => '    ',
                      XML_SERIALIZER_OPTION_LINEBREAKS           => "\n",
                      XML_SERIALIZER_OPTION_SCALAR_AS_ATTRIBUTES => true,
                      XML_SERIALIZER_OPTION_ENCODE_FUNC          => 'strtoupper'
                    );
    
    $foo = new stdClass();
    $foo->bar = new stdClass();
    $foo->bar->test  = 'This is a test.';
    $foo->bar->value = 'This is a value.';
    
    $serializer = &new XML_Serializer($options);
    
    $result = $serializer->serialize($foo);
    
    if ($result === true) {
		$xml = $serializer->getSerializedData();
    }

    echo	"<pre>";
    print_r( htmlspecialchars($xml) );
    echo	"</pre>";

    $unserializer = &new XML_Unserializer();
    $unserializer->setOption('parseAttributes', true);
    $unserializer->setOption('decodeFunction', 'strtolower');

    $result = $unserializer->unserialize($xml);
    
    echo '<pre>';
    print_r($unserializer->getUnserializedData());
    echo '</pre>';
?>