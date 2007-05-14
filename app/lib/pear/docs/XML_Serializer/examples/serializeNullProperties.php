<?PHP
/**
 * This example demonstrates the use of
 * ignoreNull => true
 *
 * It can be used to serialize an indexed array
 * like ext/simplexml does, by using the name
 * of the parent tag, while omitting this tag.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);
    
    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"         => '    ',
                        "linebreak"      => "\n",
                    );
    
    $serializer = new XML_Serializer($options);
    
    $object = new stdClass();
    $object->foo = 'bar';
    $object->bar = null;
    
    $array = array(
                    'foo' => 'bar',
                    'bar' => null
                );
    
    $result = $serializer->serialize($object);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }

    $result = $serializer->serialize($array);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
    
    $serializer->setOption('ignoreNull', true);
    $result = $serializer->serialize($object);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
    
    $result = $serializer->serialize($array);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
?>