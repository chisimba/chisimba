<?PHP
/**
 * This is just a basic example that shows
 * how objects can be serialized so they can
 * be fully restored later.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';

    $options = array(
                        XML_SERIALIZER_OPTION_INDENT      => '    ',
                        XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
                        XML_SERIALIZER_OPTION_COMMENT_KEY => 'comment'
                    );
    
    $foo = new stdClass;
    $foo->comment = 'This is a comment';
    $foo->value   = 'My value';
    
    $foo->bar     = new stdClass();
    $foo->bar->value   = 'Another value';
    $foo->bar->comment = 'Another comment';
    
    $foo->tomato          = new stdClass();
    $foo->tomato->comment = 'And a last comment';
    
    $serializer = &new XML_Serializer($options);
    
    $result = $serializer->serialize($foo);
    
    if ($result === true) {
		$xml = $serializer->getSerializedData();
    }

    echo '<pre>';
    print_r(htmlspecialchars($xml));
    echo '</pre>';
?>