<?php
/**
 * this class contains utilities for converting xml documents to array and
 * vice versa
 */
class xmlutil extends dbtable {
    public function init() {
        parent::init("tbl_witsstudentonlineapplication_localcache");
        $this->objUser  = $this->getObject("user","security");
    }
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXML( $data, $rootNodeName = 'ResultSet', $xml=null ) {

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
        if ( is_null($xml) )
            $xml =  simplexml_load_string( "" );

        // loop through the data passed in.
        foreach( $data as $key => $value ) {

            // no numeric keys in our xml please!
            if ( is_numeric( $key ) ) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if ( is_array( $value ) ) {
                $node = xmlutil::is_assoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

                // recrusive call.
                if ( $numeric ) $key = 'anon';
                xmlutil::toXml( $value, $key, $node );
            }
            else {

                // add single node.
                $value = htmlentities( $value );
                $xml->addChild( $key, $value );
            }
        }

        // pass back as XML
        return $xml->asXML();

        // if you want the XML to be formatted, use the below instead to return the XML
        //$doc = new DOMDocument('1.0');
        //$doc->preserveWhiteSpace = false;
        //$doc->loadXML( $xml->asXML() );
        //$doc->formatOutput = true;
        //return $doc->saveXML();
    }


    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
     *
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function toArray( $xml ) {
        if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
        $children = $xml->children();
        if ( !$children ) return (string) $xml;
        $arr = array();
        foreach ( $children as $key => $node ) {
            $node = xmlutil::toArray( $node );

            // support for 'anon' non-associative arrays
            if ( $key == 'anon' ) $key = count( $arr );

            // if the node is already set, put it into an array
            if ( isset( $arr[$key] ) ) {
                if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
                $arr[$key][] = $node;
            }
            else {
                $arr[$key] = $node;
            }
        }
        return $arr;
    }

    // determine if a variable is an associative array
    public static function is_assoc( $array ) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

    /**
     * converts an array to xml
     */
    function array_to_xml($array, $level=1) {
        $xml = '';
        if ($level==1) {
            $xml .= '<?xml version="1.0" encoding="ISO-8859-1"?>'.
                    "\n<forms>\n";
        }
        foreach ($array as $key=>$value) {
            $key = strtolower($key);
            if (is_array($value)) {
                $multi_tags = false;
                foreach($value as $key2=>$value2) {
                    if (is_array($value2)) {
                        $xml .= str_repeat("\t",$level)."<$key>\n";
                        $xml .= $this->array_to_xml($value2, $level+1);
                        $xml .= str_repeat("\t",$level)."</$key>\n";
                        $multi_tags = true;
                    }
                    else {
                        if (trim($value2)!='') {
                            if (htmlspecialchars($value2)!=$value2) {
                                $xml .= str_repeat("\t",$level).
                                        "<$key><![CDATA[$value2]]>".
                                        "</$key>\n";
                            }
                            else {
                                $xml .= str_repeat("\t",$level).
                                        "<$key>$value2</$key>\n";
                            }
                        }
                        $multi_tags = true;
                    }
                }
                if (!$multi_tags and count($value)>0) {
                    $xml .= str_repeat("\t",$level)."<$key>\n";
                    $xml .= array_to_xml($value, $level+1);
                    $xml .= str_repeat("\t",$level)."</$key>\n";
                }
            }
            else {
                if (trim($value)!='') {
                    if (htmlspecialchars($value)!=$value) {
                        $xml .= str_repeat("\t",$level)."<$key>".
                                "<![CDATA[$value]]></$key>\n";
                    }
                    else {
                        $xml .= str_repeat("\t",$level).
                                "<$key>$value</$key>\n";
                    }
                }
            }
        }
        if ($level==1) {
            $xml .= "</forms>\n";
        }
        return $xml;
    }

    public function saveXML($data,$formname) {
        $userid=$this->objUser->userid();

        $existingdata=$this->getAll("where userid ='$userid' and formname='$formname'");
        if(count($existingdata) > 0) {
            $xml=$existingdata[0]['content'];
            $forms=$this->toArray($xml);
            $forms[$formname]=$data;
            $xml=$this->array_to_xml($forms);
            $updatedata=array(
                    "content"=>$xml,"formname"=>$formname);
            return $this->update('id',$existingdata[0]['id'],$updatedata);
        }
        else {
            $forms=array();
            $forms[$formname]=$data;
            $xml=$this->array_to_xml($forms);

            $insertdata=array(
                    "userid"=>$userid,
                    "content"=>$xml,
                    "formname"=>$formname);
            return $this->insert($insertdata);
        }
    }

    function getXML($name) {
        $userid=$this->objUser->userid();
        $existingdata=$this->getAll("where userid ='$userid' and formname='$name'");
        if(count($existingdata) > 0) {

            $xml=$existingdata[0]['content'];
            $forms=$this->toArray($xml);
            return $forms[$name];
        }

        return array();
    }
}
?>
