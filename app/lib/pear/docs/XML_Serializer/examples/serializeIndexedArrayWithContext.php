<?PHP
/**
 * This example demonstrates the use of
 * mode => simplexml
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
                        XML_SERIALIZER_OPTION_INDENT       => '    ',
                        XML_SERIALIZER_OPTION_LINEBREAKS   => "\n",
                        XML_SERIALIZER_OPTION_DEFAULT_TAG  => array(),
                        XML_SERIALIZER_OPTION_DEFAULT_TAG  => array('foos' => 'foo', 'bars' => 'bar')
                    );
    
    $serializer = new XML_Serializer($options);
    
    $data = array(
                    'foos' => array('one foo', 'two foos', 'three foos'),
                    'bars' => array('one bar', 'two bars', 'three bars'),
                );

    $result = $serializer->serialize($data);
    
    if( $result === true ) {
        echo    '<pre>';
        echo    htmlentities($serializer->getSerializedData());
        echo    '</pre>';
    }
?>