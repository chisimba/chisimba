<?php

// ----------------------------------------------------------------------------------
// Class: RdfParser
// ----------------------------------------------------------------------------------


/**
 * An RDF paser.
 * This class reads RDF data from files or URIs and generates models out of it. All valid
 * RDF XML syntaxes defined by the W3C in RDF/XML Syntax Specification (Revised)
 * - W3C Working Draft 10 October 2003
 * (http://www.w3.org/TR/2003/WD-rdf-syntax-grammar-20031010/) are supported.
 * The parser is based on the PHP version of repat
 * (http://phpxmlclasses.sourceforge.net/show_doc.php?class=class_rdf_parser.html)
 * by Luis Argerich (lrargerich@yahoo.com).
 *
 * @version  $Id: RdfParser.php 493 2007-08-12 17:43:07Z cweiske $
 * @author Luis Argerich <lrargerich@yahoo.com>,
 *         Chris Bizer <chris@bizer.de>,
 *         Radoslaw Oldakowski <radol@gmx.de>
 *		   Daniel Westphal <mail@d-westphal.de>
 * @package syntax
 * @access	public
 *
 */

class RdfParser extends Object {

var $rdf_parser;
var $model;

/* Private Methods */


/**
* converts a string to its unicode NFC form (e.g. \uHHHH or \UHHHHHHHH).
*
* @param String $str
* @return String
* @access private
*
*/
function str2unicode_nfc($str=""){
	$result="";
	/* try to detect encoding */
	$tmp=str_replace("?", "", $str);
	if(strpos(utf8_decode($tmp), "?")===false){
		$str=utf8_decode($str);
	}
	for($i=0,$i_max=strlen($str);$i<$i_max;$i++){
		$nr=0;/* unicode dec nr */
		/* char */
		$char=$str[$i];
		/* utf8 binary */
		$utf8_char=utf8_encode($char);
		$bytes=strlen($utf8_char);
		if($bytes==1){
			/* 0####### (0-127) */
			$nr=ord($utf8_char);
		}
		elseif($bytes==2){
			/* 110##### 10###### = 192+x 128+x */
			$nr=((ord($utf8_char[0])-192)*64) + (ord($utf8_char[1])-128);
		}
		elseif($bytes==3){
			/* 1110#### 10###### 10###### = 224+x 128+x 128+x */
			$nr=((ord($utf8_char[0])-224)*4096) + ((ord($utf8_char[1])-128)*64) + (ord($utf8_char[2])-128);
		}
		elseif($bytes==4){
			/* 1111#### 10###### 10###### 10###### = 240+x 128+x 128+x 128+x */
			$nr=((ord($utf8_char[0])-240)*262144) + ((ord($utf8_char[1])-128)*4096) + ((ord($utf8_char[2])-128)*64) + (ord($utf8_char[3])-128);
		}
		/* result (see http://www.w3.org/TR/rdf-testcases/#ntrip_strings) */
		if($nr<9){/* #x0-#x8 (0-8) */
			$result.="\\u".sprintf("%04X",$nr);
		}
		elseif($nr==9){/* #x9 (9) */
			$result.='\t';
		}
		elseif($nr==10){/* #xA (10) */
			$result.='\n';
		}
		elseif($nr<13){/* #xB-#xC (11-12) */
			$result.="\\u".sprintf("%04X",$nr);
		}
		elseif($nr==13){/* #xD (13) */
			$result.='\t';
		}
		elseif($nr<32){/* #xE-#x1F (14-31) */
			$result.="\\u".sprintf("%04X",$nr);
		}
		elseif($nr<34){/* #x20-#x21 (32-33) */
			$result.=$char;
		}
		elseif($nr==34){/* #x22 (34) */
			$result.='\"';
		}
		elseif($nr<92){/* #x23-#x5B (35-91) */
			$result.=$char;
		}
		elseif($nr==92){/* #x5C (92) */
			$result.='\\';
		}
		elseif($nr<127){/* #x5D-#x7E (93-126) */
			$result.=$char;
		}
		elseif($nr<65536){/* #x7F-#xFFFF (128-65535) */
			$result.="\\u".sprintf("%04X",$nr);
		}
		elseif($nr<1114112){/* #x10000-#x10FFFF (65536-1114111) */
			$result.="\\U".sprintf("%08X",$nr);
		}
		else{
			/* other chars are not defined => ignore */
		}
	}
	return $result;
}




/**
 * @access	private
 */
function _new_element()
{
    $e['parent']=Array();  // Parent is a blank Array
    $e['state']=0;
    $e['has_property_atributes']=0;
    $e['has_member_attributes']=0;
    $e['subject_type']=0;
    $e['subject']='';
    $e['predicate']='';
    $e['ordinal']=0;
    $e['members']=0;
    $e['data']='';
    $e['xml_lang']='';
    $e['bag_id']='';
    $e['statements']=0;
    $e['statement_id']='';
    $e['datatype']='';
    $e['element_base_uri'] = '';

    return $e;
}

/**

 * @param string $source
 * @param string &$destination
 *
 * @access	private
 */
function _copy_element($source, &$destination )
{
    if( $source )
    {
        $destination['parent'] = $source;
        $destination['state'] = $source['state'];
        $destination['xml_lang'] = $source['xml_lang'];
    	$destination['element_base_uri'] = $source['element_base_uri'];
    }
}

  /**
   * @param string &$e
   * @access	private
   */
function _clear_element(&$e)
{
        $e['subject']='';
        $e['predicate']='';
        $e['data']='';
        $e['bag_id']='';
        $e['statement_id']='';

        if(isset($e['parent'])) {
          if( $e['parent'] )
          {
              if( $e['parent']['xml_lang'] != $e['xml_lang'] )
              {
                  $e['xml_lang']='';
              }
          }
          else
          {
              $e['xml_lang']='';
          }
        } else {
            $e['xml_lang']='';
        }

        $e['parent']=Array();
        $e['state']=0;
        $e['has_property_attributes']=0;
        $e['has_member_attributes']=0;
        $e['subject_type']=0;
        $e['subject']='';
        $e['predicate']='';
        $e['ordinal']=0;
        $e['members']=0;
        $e['data']='';
        $e['xml_lang']='';
        $e['bag_id']='';
        $e['statements']=0;
        $e['statement_id']='';
		$e['datatype']='';
    	$e['element_base_uri'] = '';
}

  /**
   * @access	private
   */
function _push_element()
{
    if(!isset($this->rdf_parser['free'])) {
        $this->rdf_parser['free']=Array();
    }
    if(count($this->rdf_parser['free'])>0)
    {
        $e = $this->rdf_parser['free'];
        if(isset($e['parent'])) {
          $this->rdf_parser['free'] = $e['parent'];
        } else {
          $this->rdf_parser['free']=$this->_new_element();
        }
    }
    else
    {
        $e = $this->_new_element();
    }
    if(!isset($this->rdf_parser['top'])) {
      $this->rdf_parser['top']=Array();
    }
    $this->_copy_element( $this->rdf_parser['top'], $e );
    $this->rdf_parser['top'] = $e;
}

  /**
   * @access	private
   */
function _pop_element()
{
    $e = $this->rdf_parser['top'];
    $this->rdf_parser['top'] = $e['parent'];
    $this->_clear_element( $e );
    $this->rdf_parser['free'] = $e;
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_property_attribute_resource($local_name )
{
    return ( $local_name == RDF_TYPE );
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_property_attribute_literal($local_name )
{
    return ( $local_name == RDF_VALUE )
		|| ( $local_name == RDF_BAG )
		|| ( $local_name == RDF_SEQ )
		|| ( $local_name == RDF_ALT )
		|| ( $local_name == RDF_STATEMENT )
		|| ( $local_name == RDF_PROPERTY )
		|| ( $local_name == RDF_LIST );
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_ordinal( $local_name )
{
    $ordinal = -1;

    if( $local_name{0} ==  '_'  )
    {
        $ordinal =  substr($local_name,1) + 1 ;
    }

    return ( $ordinal > 0 ) ? $ordinal : 0;
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_property_attribute( $local_name )
{
    return $this->_is_rdf_property_attribute_resource( $local_name )
        || $this->_is_rdf_property_attribute_literal( $local_name );
}

function _is_forbidden_rdf_property_attribute($local_name)
{
	return ( $local_name == RDF_RDF )
		|| ( $local_name == RDF_DESCRIPTION)
        || ( $local_name == RDF_ID)
        || ( $local_name == RDF_ABOUT )
        || ( $local_name == RDF_BAG_ID )
        || ( $local_name == RDF_PARSE_TYPE )
        || ( $local_name == RDF_RESOURCE )
        || ( $local_name == RDF_NODEID )
        || ( $local_name == RDF_LI )
        || ( $local_name == RDF_ABOUT_EACH )
		|| ( $local_name == RDF_ABOUT_EACH_PREFIX )
		|| ( $local_name == RDF_DATATYPE );
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_property_element( $local_name )
{
    return (  $local_name == RDF_TYPE )
        || (  $local_name == RDF_SUBJECT )
        || (  $local_name == RDF_PREDICATE )
        || (  $local_name == RDF_OBJECT )
        || (  $local_name == RDF_VALUE )
        || (  $local_name == RDF_LI )
        || (  $local_name == RDF_SEEALSO )
		|| ( $local_name == RDF_BAG )
		|| ( $local_name == RDF_SEQ )
		|| ( $local_name == RDF_ALT )
		|| ( $local_name == RDF_STATEMENT )
		|| ( $local_name == RDF_PROPERTY )
		|| ( $local_name == RDF_LIST )
		|| ( $local_name == RDF_FIRST )
		|| ( $local_name == RDF_REST )
        || (  $local_name{0} == '_'  );
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_forbidden_rdf_property_element ($local_name)
{
	return ( $local_name == RDF_RDF )
		|| ( $local_name == RDF_DESCRIPTION)
        || ( $local_name == RDF_ID)
        || ( $local_name == RDF_ABOUT )
        || ( $local_name == RDF_BAG_ID )
        || ( $local_name == RDF_PARSE_TYPE )
        || ( $local_name == RDF_RESOURCE )
        || ( $local_name == RDF_NODEID )
        || ( $local_name == RDF_ABOUT_EACH )
		|| ( $local_name == RDF_ABOUT_EACH_PREFIX )
		|| ( $local_name == RDF_DATATYPE );
}


  /**
   * @param string $local_name
   * @access	private
   */
function _is_rdf_node_element( $local_name )
{
    return ( $local_name == RDF_DESCRIPTION )
        || ( $local_name == RDF_STATEMENT )
        || ( $local_name == RDF_SUBJECT )
        || ( $local_name == RDF_PREDICATE )
        || ( $local_name == RDF_OBJECT )
        || ( $local_name == RDF_PROPERTY )
		|| ( $local_name == RDF_TYPE )
        || ( $local_name == RDF_VALUE )
		|| ( $local_name == RDF_BAG )
		|| ( $local_name == RDF_SEQ )
		|| ( $local_name == RDF_ALT )
        || ( $local_name == RDF_SEEALSO )
		|| ( $local_name == RDF_LIST )
		|| ( $local_name == RDF_FIRST )
		|| ( $local_name == RDF_REST )
		|| ( $local_name == RDF_NIL )
        || ( $local_name{0} == '_'  );
}

  /**
   * @param string $local_name
   * @access	private
   */
function _is_forbidden_rdf_node_element ($local_name)
{
	return ( $local_name == RDF_RDF )
        || ( $local_name == RDF_ID)
        || ( $local_name == RDF_ABOUT )
        || ( $local_name == RDF_BAG_ID )
        || ( $local_name == RDF_PARSE_TYPE )
        || ( $local_name == RDF_RESOURCE )
        || ( $local_name == RDF_NODEID )
		|| ( $local_name == RDF_LI )
        || ( $local_name == RDF_ABOUT_EACH )
		|| ( $local_name == RDF_ABOUT_EACH_PREFIX )
		|| ( $local_name == RDF_DATATYPE );
}

  /**
   * @param string $val
   * @access	private
   */
function _istalnum($val) {
  return ereg("[A-Za-z0-9]",$val);
}
  /**
   * @param string $val
   * @access	private
   */
function _istalpha($val) {
  return ereg("[A-Za-z]",$val);
}

  /**
   * @param string $uri
   * @access	private
   */
function _is_absolute_uri($uri )
{
    $result = false;
        $uri_p=0;
    if( $uri && $this->_istalpha( $uri{$uri_p} ) )
    {
        ++$uri_p;

        while( ($uri_p<strlen($uri))
            && ( $this->_istalnum( $uri{$uri_p} )
                || ( $uri{$uri_p} ==  '+'  )
                || ( $uri{$uri_p} == '-'  )
                || ( $uri{$uri_p} ==  '.'  ) ) )
        {
                ++$uri_p;
        }

        $result = ( $uri{$uri_p} == ':'  );
    }
    return $result;
}


/*
   * This function returns an associative array returning any of the various components of the URL that are present. This includes the
   * $arr=parse_url($url)
   * scheme - e.g. http
   * host
   * port
   * user
   * pass
   * path
   * query - after the question mark ?
   * fragment - after the hashmark #
   *
   * @param string $uri
   * @param string $buffer
   * @param string &$scheme
   * @param string &$authority
   * @param string &$path
   * @param string &$query
   * @param string &$fragment
   * @access	private
*/
function _parse_uri($uri,$buffer,&$scheme,&$authority,&$path,&$query,&$fragment ) {

  $parsed=parse_url($uri);
  if(isset($parsed['scheme'])) {
    $scheme=$parsed['scheme'];
  } else {
    $scheme='';
  }
  if(isset($parsed['host'])) {
    $host=$parsed['host'];
  } else {
    $host='';
  }
  if(isset($parsed['host'])) {
    $authority=$parsed['host'];
  } else {
    $authority='';
  }
  if(isset($parsed['path'])) {
    $path=$parsed['path'];
  } else {
    $path='';
  }
  if(isset($parsed['query'])) {
    $query=$parsed['query'];
  } else {
    $query='';
  }
  if(isset($parsed['fragment'])) {
    $fragment=$parsed['fragment'];
  } else {
    $fragment='';
  }

}

/**
   * @param string $base_uri
   * @param string $reference_uri
   * @param string &$buffer
   * @access	private
*/
function _resolve_uri_reference($base_uri,$reference_uri,&$buffer )
{
	if ($reference_uri == '')
		return ($buffer = preg_replace("/\#[^\/\\\]*$/", '', $base_uri));

    $base_buffer='';
    $reference_buffer='';
    $path_buffer='';

    $buffer = '';

    $this->_parse_uri($reference_uri,
    				  $reference_buffer,
    				  $reference_scheme,
    				  $reference_authority,
                      $reference_path,
                      $reference_query,
                      $reference_fragment );

    $this->_parse_uri($base_uri,
            		  $base_buffer,
            		  $base_scheme,
            		  $base_authority,
            		  $base_path,
            		  $base_query,
            	      $base_fragment );

    if( $reference_scheme == ''
        && $reference_authority == ''
        && $reference_path == ''
        && $reference_query == '' )
    {
        $buffer=$base_uri;

        if( $reference_fragment != '' )
        {
			if ($base_path == '' || $base_path == '/' || $base_path == "\\") {
			   $buffer = $this->rdf_parser['document_base_uri'];
			}
			else
			{
				$buffer = preg_replace("/\#[^\/\\\]*$/", '', $base_uri);
			}

            // CB: Changed for base URI
			$c = substr($buffer, strlen($buffer)-1 ,1);
	        if (!($c=='#' || $c==':' || $c=='/' || $c=="\\"))
			       $buffer.= '#' ;
            $buffer.=$reference_fragment;

        }
    }
    else if( $reference_scheme != '' )
    {
        $buffer=$reference_uri;
    }
    else
    {
        $result_scheme = $base_scheme;
        $result_path = '';

        if( $reference_authority != '' )
        {
            $result_authority = $reference_authority;
        }
        else
        {
            $result_authority = $base_authority;

            if ($reference_path != '')
            {
            	if ($reference_path{0} == '/' || $reference_path{0} == "\\")
            	{
            		if ($reference_path{1} == '/' || $reference_path{1} == "\\")
            		{
						$result_authority = '';
						$result_path = $reference_path;
            		}
            		else
            			$result_path = $reference_path;
            	}
            	elseif (substr($reference_path, 0, 3) == '../' ||
            	        substr($reference_path, 0, 3) == '..\\')
            	{
            		$slash = $reference_path{2};
            		while($base_path != '' && ( substr($reference_path, 0, 3) == '../'
            	                             || substr($reference_path, 0, 3) == '..\\'))
            	    {
            		  	$base_path = preg_replace("/((\/)|(\\\))[^\/\\\]*$/", '', $base_path);
            		  	if ($base_path != '') {
            		  	   $base_path = preg_replace("/((\/)|(\\\))[^\/\\\]*$/", '', $base_path);
            		  	   $reference_path = substr($reference_path, 3);
            		  	}
            		}

                    $result_path = $base_path .$slash .$reference_path;
            	}
            	else
            	{
            		if ($base_path)
            			$result_path = preg_replace("/[^\/\\\]*$/", $reference_path, $base_path, 1);
					else
					    $result_path = '/' .$reference_path;
            	}
            }

        }

        if( $result_scheme != '' )
        {
            $buffer=$result_scheme;
            $buffer.=':';
        }

        if( $result_authority != '' )
        {
            $buffer.="//";
            $buffer.=$result_authority;
        }

        if( $result_path != '' )
        {
            $buffer.=$result_path;
        }

        if( $reference_query != '' )
        {
            $buffer.='?';
            $buffer.=$reference_query;
        }

        if( $reference_fragment != '' )
        {
            $buffer.='#';
            $buffer.=$reference_fragment;
        }
    }
}


/**
   * IDs which contain CombiningChars or Extenders
   * (see http://www.w3.org/TR/REC-xml-names/#NT-NCName) are assumed to be invalid.
   * If you want to use IDs containing these characters you can turn off
   * the validating by setting the constant VALIDATE_IDS to FALSE (see constants.php).
   *
   * @param string $id
   * @access	private
*/
function is_valid_id($id )
{
	if (!VALIDATE_IDS)
		return TRUE;

    $result = FALSE;

    if( $id )
    {
        if( $this->_istalpha($id{0}) || $id{0} == '_')
        {
            $result = TRUE;
            $i=0;
            $len = strlen($id);
			while( $result != FALSE && ++$i < $len )
            {
                if( !($this->_istalnum( $id{$i})
                      || $id{$i} == '.'
                      || $id{$i} == '-'
                      || $id{$i} == '_'))
                {
                   $result = FALSE;
                }
            }
        }
    }

    if (!$result)
    	$this->_report_error('illegal ID, nodeID or bagID attribute value');
    else
    	return TRUE;
}

/**
   * @param string $id
   * @param string &$buffer
   * @access	private
*/
function _resolve_id($id,&$buffer )
{
    $id_buffer='';

    if( $this->is_valid_id($id) )
    {
        $id_buffer="#$id";
    }

    $this->_resolve_uri_reference( $this->rdf_get_base(), $id_buffer, $buffer );

}

/**
   * @param string $name
   * @param string &$buffer
   * @param string &$namespace_uri
   * @param string &$local_name
   * @access	private
*/
function _split_name($name, &$buffer, &$namespace_uri, &$local_name )
{
    static $nul = 0;
    $buffer=$name;

        if(  strstr( $buffer, NAMESPACE_SEPARATOR_CHAR ) )
        {
            $cosas=explode(NAMESPACE_SEPARATOR_CHAR,$buffer);
            $namespace_uri = $cosas[0];
            $local_name = $cosas[1];
        }
        else
        {
            if( ( $buffer{ 0 } ==  'x'  )
                && ( $buffer{ 1 } ==  'm'  )
                && ( $buffer{ 2 } ==  'l'  )
                && ( $buffer{ 3 } ==  ':'  ) )
            {
                $namespace_uri = XML_NAMESPACE_URI;
                $local_name = substr($buffer,4);
            }
            else
            {
                $namespace_uri = '';
                $local_name = $buffer;
            }
        }
}
/**
   * @param string &$buf
   * @access	private
*/
function _generate_anonymous_uri(&$buf )
{
    $id='';
    if(!isset($this->rdf_parser['anonymous_id'])) {
      $this->rdf_parser['anonymous_id']=0;
    }
    $this->rdf_parser['anonymous_id']++;

    $buf= BNODE_PREFIX . $this->rdf_parser['anonymous_id'];

}
/**
   * @param string $subject_type
   * @param string $subject
   * @param string $predicate
   * @param string $ordinal
   * @param string $object_type
   * @param string $object
   * @param string $xml_lang
   * @param string $bag_id
   * @param string $statements
   * @param string $statement_id
   * @access	private
*/
function _report_statement( $subject_type, $subject, $predicate, $ordinal, $object_type,  $object, $xml_lang, $bag_id, $statements, $statement_id, $datatype )
{
    $statement_id_type = RDF_SUBJECT_TYPE_URI;
    $statement_id_buffer='';
    $predicate_buffer='';
    if (!$xml_lang && $object_type == RDF_OBJECT_TYPE_LITERAL && isset($this->rdf_parser['document_xml_lang']))
    	$xml_lang = $this->rdf_parser['document_xml_lang'];

	 // call add statement
	 $this->add_statement_to_model($this->rdf_parser['user_data'],$subject_type,$subject,$predicate,$ordinal,$object_type,$object,$xml_lang, $datatype );

        if( $bag_id )
        {
            if( $statements == '' )
            {
                $this->_report_statement(RDF_SUBJECT_TYPE_URI,
                    $bag_id,
                    RDF_NAMESPACE_URI.RDF_TYPE,
                    0,
                    RDF_OBJECT_TYPE_RESOURCE,
                    RDF_NAMESPACE_URI.RDF_BAG,
                    '',
                    '',
                    '',
                    '',
					$datatype );
            }

            if( ! $statement_id )
            {
                $statement_id_type = RDF_SUBJECT_TYPE_BNODE;
                $this->_generate_anonymous_uri( $statement_id_buffer );
                $statement_id = $statement_id_buffer;
            }
            $statements++;
            $predicate_buffer='RDF_NAMESPACE_URI_'.$statements;

            $this->_report_statement(
                RDF_SUBJECT_TYPE_URI,
                $bag_id,
                $predicate_buffer,
                $statements,
                RDF_OBJECT_TYPE_BNODE,
                $statement_id,
                '',
                '',
                '',
                '',
				$datatype );
        }

        if( $statement_id )
        {
            // rdf:type = rdf:Statement
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDF_NAMESPACE_URI.RDF_TYPE,
                0,
                RDF_OBJECT_TYPE_RESOURCE,
                RDF_NAMESPACE_URI.RDF_STATEMENT,
                '',
                '',
                '',
                '',
				$datatype );

			if ($subject_type == RDF_SUBJECT_TYPE_BNODE)
			   $obj_type = RDF_OBJECT_TYPE_BNODE;
			else
			   $obj_type = RDF_OBJECT_TYPE_RESOURCE;


            // rdf:subject
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDF_NAMESPACE_URI.RDF_SUBJECT,
                0,
				$obj_type,
                $subject,
                '',
                '',
                '',
                '',
				$datatype );

            // rdf:predicate
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDF_NAMESPACE_URI.RDF_PREDICATE,
                0,
                RDF_OBJECT_TYPE_RESOURCE,
                $predicate,
                '',
                '',
                '',
                '',
				$datatype );

            // rdf:object
            $this->_report_statement(
                $statement_id_type,
                $statement_id,
                RDF_NAMESPACE_URI.RDF_OBJECT,
                0,
                $object_type,
                $object,
                '',
                '',
                '',
                '',
				$datatype );
        }
}
/**
   * @param string $subject_type
   * @param string $subject
   * @param string $attributes
   * @param string $xml_lang
   * @param string $bag_id
   * @param string $statements
 * @access	private
*/
function _handle_property_attributes($subject_type, $subject, $attributes, $xml_lang, $bag_id, $statements )
{
    $i=0;

    $attribute='';
    $predicate='';

    $attribute_namespace_uri='';
    $attribute_local_name='';
    $attribute_value='';

    $ordinal=0;

    for( $i = 0; isset($attributes[ $i ]); $i += 2 )
    {
        $this->_split_name(
            $attributes[ $i ],
            $attribute,
            $attribute_namespace_uri,
            $attribute_local_name );

        $attribute_value = $attributes[ $i + 1 ];

        $predicate=$attribute_namespace_uri;
        $predicate.=$attribute_local_name;

        if( RDF_NAMESPACE_URI == $attribute_namespace_uri )
        {
            if( $this->_is_rdf_property_attribute_literal( $attribute_local_name ) )
            {
				$this->_report_statement(
                    $subject_type,
                    $subject,
                    $predicate,
                    0,
                    RDF_OBJECT_TYPE_LITERAL,
                    $attribute_value,
                    $xml_lang,
                    $bag_id,
                    $statements,
                    '',
					'');
            }
            else if( $this->_is_rdf_property_attribute_resource( $attribute_local_name ) )
            {
				$this->_report_statement(
                    $subject_type,
                    $subject,
                    $predicate,
                    0,
                    RDF_OBJECT_TYPE_RESOURCE,
                    $attribute_value,
                    '',
                    $bag_id,
                    $statements,
                    '',
					'');
            }
            else if( ( $ordinal = $this->_is_rdf_ordinal( $attribute_local_name ) ) != 0 )
            {
				$this->_report_statement(
                    $subject_type,
                    $subject,
                    $predicate,
                    $ordinal,
                    RDF_OBJECT_TYPE_LITERAL,
                    $attribute_value,
                    $xml_lang,
                    $bag_id,
                    $statements,
                    '',
					'' );
            }
            else if (   ($attribute_local_name != RDF_ABOUT)
                     && ($attribute_local_name != RDF_RDF)
                     && ($attribute_local_name != RDF_DESCRIPTION)
                     && ($attribute_local_name != RDF_ID)
                     && ($attribute_local_name != RDF_ABOUT_EACH)
                     && ($attribute_local_name != RDF_ABOUT_EACH_PREFIX)
                     && ($attribute_local_name != RDF_BAG_ID)
                     && ($attribute_local_name != RDF_RESOURCE)
                     && ($attribute_local_name != RDF_PARSE_TYPE)
                     && ($attribute_local_name != RDF_PARSE_TYPE_LITERAL)
                     && ($attribute_local_name != RDF_PARSE_TYPE_RESOURCE)
                     && ($attribute_local_name != RDF_LI)
                     && ($attribute_local_name != RDF_SUBJECT)
                     && ($attribute_local_name != RDF_PREDICATE)
                     && ($attribute_local_name != RDF_OBJECT)
                     && ($attribute_local_name != RDF_NODEID)
                     && ($attribute_local_name != RDF_DATATYPE)
                     && ($attribute_local_name != RDF_SEEALSO)
                     && ($attribute_local_name != RDF_NIL)
                     && ($attribute_local_name != RDF_REST)
                     && ($attribute_local_name != RDF_FIRST)
                    )
            {
            	$this->_report_statement(
                	$subject_type,
                	$subject,
                	$predicate,
                	0,
                	RDF_OBJECT_TYPE_LITERAL,
                	$attribute_value,
                	$xml_lang,
                	$bag_id,
                	$statements,
                	'',
					'' );
            }
        }
        else if( XML_NAMESPACE_URI == $attribute_namespace_uri )
        {
			if ($attribute_local_name == 'base')
			{
    			$this->rdf_parser['top']['element_base_uri'] = $attribute_value;
			}
        }
        else if( $attribute_namespace_uri )
        {
            // is it required that property attributes be in an explicit namespace?

			$this->_report_statement(
                $subject_type,
                $subject,
                $predicate,
                0,
                RDF_OBJECT_TYPE_LITERAL,
                $attribute_value,
                $xml_lang,
                $bag_id,
                $statements,
                '',
				'' );
        }
    }
}



/**
   * @param string $warning
 * @access	private
*/
function _report_warning($warning)
{
	$errmsg = RDFAPI_ERROR . '(class: parser): ' . $warning .'.';
	trigger_error($errmsg, E_USER_WARNING);
}


function _report_error($error)
{
	$errmsg = RDFAPI_ERROR . '(class: parser): ' . $error .'.';
	trigger_error($errmsg, E_USER_ERROR);
}


/**
   * @param string $namespace_uri
   * @param string $local_name
   * @param string $attributes
   * @param string $parent
 * @access	private
*/
function _handle_resource_element( $namespace_uri, $local_name, $attributes, $parent )
{
    $subjects_found = 0;
    $aux=$attributes;
    $aux2=Array();
    foreach($attributes as $atkey=>$atvalue) {
      $aux2[]=$atkey;
      $aux2[]=$atvalue;
    }
    $attributes=$aux2;
    $id = '';
    $about = '';

    $bag_id = '';
    $node_id = '';
    $datatype = '';

    $i=0;

    $attribute='';

    $attribute_namespace_uri='';
    $attribute_local_name='';
    $attribute_value='';

    $id_buffer='';

    $type='';

    $this->rdf_parser['top']['has_property_attributes'] = false;
    $this->rdf_parser['top']['has_member_attributes'] = false;


    if( $namespace_uri == RDF_NAMESPACE_URI )
    {
       if( ! $this->_is_rdf_node_element( $local_name ) )
       {
		  $msg = 'unknown or out of context rdf node element: '.$local_name;

		  if ($this->_is_forbidden_rdf_node_element($local_name))
		  	 $this->_report_error($msg);
		  else
		  	 $this->_report_warning($msg);
       }
    }

    // examine each attribute for the standard RDF "keywords"
    for( $i = 0; isset($attributes[$i]); $i += 2 )
    {
        $this->_split_name(
            $attributes[ $i ],
            $attribute,
            $attribute_namespace_uri,
            $attribute_local_name );

        $attribute_value = $attributes[ $i + 1 ];

        // if the attribute is not in any namespace
        //   or the attribute is in the RDF namespace
        if( ( $attribute_namespace_uri == '' )
            || (  $attribute_namespace_uri == RDF_NAMESPACE_URI  ))
        {
            if( $attribute_local_name == RDF_ID )
            {
                $id = $attribute_value;
                ++$subjects_found;
            } else if( $attribute_local_name == RDF_ABOUT ) {
				$about = '_'.$attribute_value;
                ++$subjects_found;
            } else if( $attribute_local_name == RDF_NODEID) {
                $node_id = $attribute_value;
				++$subjects_found;
            } else if(  $attribute_local_name == RDF_ABOUT_EACH ) {
				$error = 'aboutEach has been removed from the RDF specifications';
				$this->_report_error($error);
            } else if( $attribute_local_name == RDF_ABOUT_EACH_PREFIX ) {
				$error = 'aboutEachPrefix has been removed from the RDF specifications';
				$this->_report_error($error);
            } else if( $attribute_local_name == RDF_BAG_ID) {
                $bag_id = $attribute_value;
            } else if( $attribute_local_name == RDF_DATATYPE) {
                $datatype = $attribute_value;
            } else if( $this->_is_rdf_property_attribute( $attribute_local_name )) {
                $this->rdf_parser['top']['has_property_attributes'] = true;
            } else if( $this->_is_rdf_ordinal( $attribute_local_name )) {
                $this->rdf_parser['top']['has_property_attributes'] = true;
                $this->rdf_parser['top']['has_member_attributes'] = true;
            } else {
				$this->rdf_parser['top']['has_property_attributes'] = true;
				$msg = 'unknown or out of context rdf attribute: '.$attribute_local_name;

		  		if ($this->_is_forbidden_rdf_property_attribute($attribute_local_name))
		  			$this->_report_error($msg);
		  		else
		  			$this->_report_warning($msg);
            }
        }
        else if(  $attribute_namespace_uri == XML_NAMESPACE_URI )
        {
            if( $attribute_local_name == XML_LANG )
            {
                $this->rdf_parser['top']['xml_lang'] = $attribute_value;
            }
    		elseif ($attribute_local_name == 'base')
    		{
    			$this->rdf_parser['top']['element_base_uri'] = $attribute_value;
    		}
        }
        else if( $attribute_namespace_uri )
        {
            $this->rdf_parser['top']['has_property_attributes'] = true;
        }
    }

    // if no subjects were found, generate one.
    if( $subjects_found == 0 )
    {
        $this->_generate_anonymous_uri( $id_buffer );
        $this->rdf_parser['top']['subject']=$id_buffer;
		$this->rdf_parser['top']['subject_type'] = RDF_SUBJECT_TYPE_BNODE;
    }
    else if( $subjects_found > 1 )
    {
		 $this->_report_error('ID, about and nodeID are mutually exclusive');
    }
    else if( $id )
    {
        $this->_resolve_id( $id, $id_buffer );
        $this->rdf_parser['top']['subject_type'] = RDF_SUBJECT_TYPE_URI;
        $this->rdf_parser['top']['subject']=$id_buffer;
    }
    else if( $about )
    {
		$this->_resolve_uri_reference( $this->rdf_get_base(), substr($about,1), $id_buffer );
        $this->rdf_parser['top']['subject_type'] = RDF_SUBJECT_TYPE_URI;
        $this->rdf_parser['top']['subject']=$id_buffer;
    }
    else if( $node_id )
    {
    	$this->is_valid_id($node_id);
        $this->rdf_parser['top']['subject_type'] = RDF_SUBJECT_TYPE_BNODE;
        $this->rdf_parser['top']['subject']=$node_id;
    }

    // if the subject is empty, assign it the document uri
    if( $this->rdf_parser['top']['subject'] == '' )
    {
		$this->rdf_parser['top']['subject']=$this->rdf_get_base();
    }

    if( $bag_id )
    {
        $this->_resolve_id( $bag_id, $id_buffer );
        $this->rdf_parser['top']['bag_id']=$id_buffer;
    }

    // only report the type for non-rdf:Description elements.
    if( ($local_name != RDF_DESCRIPTION )
        || ( $namespace_uri != RDF_NAMESPACE_URI ) )
    {
        $type=$namespace_uri;
        $type.=$local_name;

		$this->_report_statement(
            $this->rdf_parser['top']['subject_type'],
            $this->rdf_parser['top']['subject'],
            RDF_NAMESPACE_URI.RDF_TYPE,
            0,
            RDF_OBJECT_TYPE_RESOURCE,
            $type,
            '',
            $this->rdf_parser['top']['bag_id'],
            $this->rdf_parser['top']['statements'],
            '',
			$datatype);

    }

    // if this element is the child of some property,
    //   report the appropriate statement.
    if( $parent )
    {
        if ($this->rdf_parser['top']['subject_type'] == RDF_SUBJECT_TYPE_BNODE)
        	$objtype = RDF_OBJECT_TYPE_BNODE;
        else
        	$objtype = RDF_OBJECT_TYPE_RESOURCE;

    	$this->_report_statement(
            $parent['parent']['subject_type'],
            $parent['parent']['subject'],
            $parent['predicate'],
            $parent['ordinal'],
            $objtype,
            $this->rdf_parser['top']['subject'],
            '',
            $parent['parent']['bag_id'],
            $parent['parent']['statements'],
            $parent['statement_id'],
			$parent['datatype']);

    }

    if( $this->rdf_parser['top']['has_property_attributes'] )
    {
        $this->_handle_property_attributes(
            $this->rdf_parser['top']['subject_type'],
            $this->rdf_parser['top']['subject'],
            $attributes,
            $this->rdf_parser['top']['xml_lang'],
            $this->rdf_parser['top']['bag_id'],
            $this->rdf_parser['top']['statements'] );
    }
}

/**
   * @param string &$namespace_uri
   * @param string &$local_name
   * @param string &$attributes
 * @access	private
*/
function _handle_property_element( &$namespace_uri, &$local_name, &$attributes )
{
    $buffer='';

    $i=0;

    $aux=$attributes;
    $aux2=Array();
    foreach($attributes as $atkey=>$atvalue) {
      $aux2[]=$atkey;
      $aux2[]=$atvalue;
    }
    $attributes=$aux2;

    $attribute_namespace_uri='';
    $attribute_local_name='';
    $attribute_value = '';

    $resource = NULL;
    $statement_id = '';
    $bag_id = '';
    $parse_type = '';
	$node_id = '';
	$datatype = '';

    $this->rdf_parser['top']['ordinal'] = 0;

    if( $namespace_uri == RDF_NAMESPACE_URI )
    {
       if( ! $this->_is_rdf_property_element( $local_name ) )
        {
			$msg = 'unknown or out of context rdf property element: '.$local_name;

		  	if ($this->_is_forbidden_rdf_property_element($local_name))
		  		$this->_report_error($msg);
		  	else
		  		$this->_report_warning($msg);
        }

    }

    $buffer=$namespace_uri;

    if( ( $namespace_uri == RDF_NAMESPACE_URI )
        &&  ( $local_name == RDF_LI ) )
    {
        $this->rdf_parser['top']['parent']['members']++;
        $this->rdf_parser['top']['ordinal'] = $this->rdf_parser['top']['parent']['members'];

        $this->rdf_parser['top']['ordinal']=$this->rdf_parser['top']['ordinal'];

        $buffer.='_'.$this->rdf_parser['top']['ordinal'];

    }
    else
    {
        $buffer.=$local_name;
    }

    $this->rdf_parser['top']['predicate']=$buffer;

    $this->rdf_parser['top']['has_property_attributes'] = false;
    $this->rdf_parser['top']['has_member_attributes'] = false;


    for( $i = 0; isset($attributes[$i]); $i += 2 )
    {
        $this->_split_name(
            $attributes[$i],
            $buffer,
            $attribute_namespace_uri,
            $attribute_local_name );

        $attribute_value = $attributes[$i + 1];

        // if the attribute is not in any namespace
        //   or the attribute is in the RDF namespace
        if( ( $attribute_namespace_uri == '' )
            || (  $attribute_namespace_uri == RDF_NAMESPACE_URI ) )
        {
            if( ( $attribute_local_name == RDF_ID )  )
            {
                $statement_id = $attribute_value;
            }
            else if( $attribute_local_name == RDF_PARSE_TYPE )
            {
                $parse_type = $attribute_value;
            }
            else if(  $attribute_local_name == RDF_RESOURCE )
            {
                $resource = $attribute_value;
            }
			else if(  $attribute_local_name == RDF_NODEID )
            {
				$node_id = $attribute_value;
            }
            else if(  $attribute_local_name == RDF_BAG_ID )
            {
                $bag_id = $attribute_value;
            }
			else if(  $attribute_local_name == RDF_DATATYPE )
            {
				$datatype = $attribute_value;
				$this->rdf_parser['top']['datatype'] = $attribute_value;
            }
            else if( $this->_is_rdf_property_attribute( $attribute_local_name ) )
            {
                $this->rdf_parser['top']['has_property_attributes'] = true;
            }
            else
            {
                $this->_report_warning('unknown rdf attribute: '.$attribute_local_name );
                return;
            }
        }
        else if(  $attribute_namespace_uri == XML_NAMESPACE_URI  )
        {
            if( $attribute_local_name == XML_LANG  )
            {
                $this->rdf_parser['top']['xml_lang'] = $attribute_value;
            }
    		elseif ($attribute_local_name == 'base')
    		{
    			$this->rdf_parser['top']['element_base_uri'] = $attribute_value;
    		}
        }
        else if( $attribute_namespace_uri )
        {
            $this->rdf_parser['top']['has_property_attributes'] = true;
        }
    }

    if( $statement_id )
    {
	    $this->_resolve_id($statement_id, $buffer );
        $this->rdf_parser['top']['statement_id']=$buffer;
    }

	if ($node_id)
	{
		$this->is_valid_id($node_id);

		if ($resource)
		{
			$this->_report_error('nodeID and resource are mutually exclusive');
		}
       	if ($statement_id)
       	{
       		// reify statement
       		$this->_report_statement(
        		$this->rdf_parser['top']['parent']['subject_type'],
        		$this->rdf_parser['top']['parent']['subject'],
        		$this->rdf_parser['top']['predicate'],
        		$this->rdf_parser['top']['ordinal'],
        		RDF_OBJECT_TYPE_BNODE,
        		$node_id,
        		'',
        		$this->rdf_parser['top']['parent']['bag_id'],
        		$this->rdf_parser['top']['parent']['statements'],
        		$this->rdf_parser['top']['statement_id'],
				'');
			$statement_id = '';
       	}
       	else
       	{
			$this->_report_statement(
	           $this->rdf_parser['top']['parent']['subject_type'],
	           $this->rdf_parser['top']['parent']['subject'],
	           $this->rdf_parser['top']['predicate'],
	           $this->rdf_parser['top']['ordinal'],
	           RDF_OBJECT_TYPE_BNODE,
	           $node_id,
	           '',
	           $this->rdf_parser['top']['parent']['bag_id'],
	           $this->rdf_parser['top']['parent']['statements'],
	           '',
			   $datatype );
       	}

       	$this->rdf_parser['top']['state'] = IN_PROPERTY_EMPTY_RESOURCE;
	}

    if( $parse_type )
	{
        if( $resource ) {
			$this->_report_error('property elements with rdf:parseType do not allow rdf:resource' );
        }

        if( $bag_id ) {
            $this->_report_warning('property elements with rdf:parseType do not allow rdf:bagID' );
            return;
        }

        if( $this->rdf_parser['top']['has_property_attributes'] )
        {
				$this->_report_error('property elements with rdf:parseType do not allow property attributes');
            return;
        }

        if(  $attribute_value == RDF_PARSE_TYPE_RESOURCE )
        {
            $this->_generate_anonymous_uri( $buffer );
            // since we are sure that this is now a resource property we can report it
            $this->_report_statement(
                $this->rdf_parser['top']['parent']['subject_type'],
                $this->rdf_parser['top']['parent']['subject'],
                $this->rdf_parser['top']['predicate'],
                0,
                RDF_OBJECT_TYPE_BNODE,
                $buffer,
                '',
                $this->rdf_parser['top']['parent']['bag_id'],
                $this->rdf_parser['top']['parent']['statements'],
				$this->rdf_parser['top']['statement_id'],
				$datatype );

            $this->_push_element( );

            $this->rdf_parser['top']['state'] = IN_PROPERTY_PARSE_TYPE_RESOURCE;
            $this->rdf_parser['top']['subject_type'] = RDF_SUBJECT_TYPE_BNODE;
            $this->rdf_parser['top']['subject']=$buffer;
            $this->rdf_parser['top']['bag_id']='';
			$this->rdf_parser['top']['datatype']= $datatype;

        }
       	elseif (  $attribute_value == RDF_PARSE_TYPE_LITERAL )
       	{
       		$this->rdf_parser['top']['state'] = IN_PROPERTY_PARSE_TYPE_LITERAL;
       		$this->rdf_parser['top']['datatype']= RDF_NAMESPACE_URI .RDF_XMLLITERAL;
       		$this->rdf_parser['xml_literal']['buffer'] = '';
       		$this->rdf_parser['xml_literal']['depth'] = 0;
       	}
		elseif ($attribute_value == RDF_PARSE_TYPE_COLLECTION)
		{
			$this->_generate_anonymous_uri( $buffer );
            $this->_report_statement(
                $this->rdf_parser['top']['parent']['subject_type'],
                $this->rdf_parser['top']['parent']['subject'],
                $this->rdf_parser['top']['predicate'],
                0,
                RDF_OBJECT_TYPE_BNODE,
                $buffer,
                '',
                $this->rdf_parser['top']['parent']['bag_id'],
                $this->rdf_parser['top']['parent']['statements'],
				$this->rdf_parser['top']['statement_id'],
				$datatype );

			$this->rdf_parser['top']['state'] = IN_PROPERTY_PARSE_TYPE_COLLECTION;
			$this->rdf_parser['top']['collection']['first_blank_node_id'] = $buffer;
		}

        else
        {

			$this->_report_statement(
                $this->rdf_parser['top']['parent']['subject_type'],
                $this->rdf_parser['top']['parent']['subject'],
                $this->rdf_parser['top']['predicate'],
                0,
                RDF_OBJECT_TYPE_XML,
                '',
                '',
                $this->rdf_parser['top']['parent']['bag_id'],
                $this->rdf_parser['top']['parent']['statements'],
				$this->rdf_parser['top']['statement_id'],
    			$datatype );

            $this->rdf_parser['top']['state'] = IN_PROPERTY_PARSE_TYPE_LITERAL;
        }
    }
    else if( $resource !== NULL || $bag_id || $this->rdf_parser['top']['has_property_attributes'] )
    	{
        if( $resource !== NULL )
        {
            $subject_type = RDF_SUBJECT_TYPE_URI;
			$this->_resolve_uri_reference( $this->rdf_get_base(), $resource, $buffer );
        	$object_type=RDF_OBJECT_TYPE_RESOURCE;
        }
        else
        {
			$subject_type = RDF_SUBJECT_TYPE_BNODE;
            $this->_generate_anonymous_uri( $buffer );
            $object_type=RDF_OBJECT_TYPE_BNODE;
        }
        $this->rdf_parser['top']['state'] = IN_PROPERTY_EMPTY_RESOURCE;

        // since we are sure that this is now a resource property we can report it.
		$this->_report_statement(
            $this->rdf_parser['top']['parent']['subject_type'],
            $this->rdf_parser['top']['parent']['subject'],
            $this->rdf_parser['top']['predicate'],
            $this->rdf_parser['top']['ordinal'],
            $object_type,
            $buffer,
            '',
            $this->rdf_parser['top']['parent']['bag_id'],
            $this->rdf_parser['top']['parent']['statements'],
			$this->rdf_parser['top']['statement_id'],
			$datatype ); // should we allow IDs?

        if( $bag_id )
        {
            $this->_resolve_id( $bag_id, $buffer );
            $this->rdf_parser['top']['bag_id']=$buffer;
        }

        if( $this->rdf_parser['top']['has_property_attributes'] )
        {
            $this->_handle_property_attributes(
                $subject_type,
                $buffer,
                $attributes,
                $this->rdf_parser['top']['xml_lang'],
                $this->rdf_parser['top']['bag_id'],
                $this->rdf_parser['top']['statements'] );
        }
    }
}

/**
   * @param string &$namespace_uri
   * @param string &$local_name
   * @param string &$attributes
   * @access	private
*/
function _handle_collection_element(&$namespace_uri, &$local_name, &$attributes)
{
	$aux2=Array();
    foreach($attributes as $atkey=>$atvalue) {
      $aux2[]=$atkey;
      $aux2[]=$atvalue;
    }
    $attributes=$aux2;
/* collection construction site
// old:
	if (   ($namespace_uri == RDF_NAMESPACE_URI || $namespace_uri == '')
	    && ($local_name == RDF_DESCRIPTION || $local_name == RDF_LI) )
	{
		for( $i = 0; isset($attributes[$i]); $i += 2 )
    	{
			$this->_split_name(
            	$attributes[ $i ],
            	$attribute,
            	$attribute_namespace_uri,
            	$attribute_local_name );

        	$attribute_value = $attributes[ $i + 1 ];

        	if( $attribute_namespace_uri == '' || $attribute_namespace_uri == RDF_NAMESPACE_URI  )
	        {
        	    if( $attribute_local_name == RDF_ABOUT ||
        	        $attribute_local_name == RDF_RESOURCE)
        	    {
					$this->rdf_parser['top']['parent']['collection']['object_type'][] = RDF_OBJECT_TYPE_RESOURCE;
        	    }
        	    elseif ( $attribute_local_name == RDF_NODEID ) {
        	    	$this->rdf_parser['top']['parent']['collection']['object_type'][] = RDF_OBJECT_TYPE_BNODE;
        	    }
        	    $this->rdf_parser['top']['parent']['collection']['object_label'][] = $attribute_value;
    		}
    	}
	}
*/
// new

		for( $i = 0; isset($attributes[$i]); $i += 2 )
    	{
			$this->_split_name(
            	$attributes[ $i ],
            	$attribute,
            	$attribute_namespace_uri,
            	$attribute_local_name );

        	$attribute_value = $attributes[ $i + 1 ];

        	if( $attribute_namespace_uri == '' || $attribute_namespace_uri == RDF_NAMESPACE_URI  )
	        {
	        	$tmp_subject_type = RDF_SUBJECT_TYPE_URI;
        	    if( $attribute_local_name == RDF_ABOUT ||
        	        $attribute_local_name == RDF_RESOURCE)
        	    {
					$this->rdf_parser['top']['parent']['collection']['object_type'][] = RDF_OBJECT_TYPE_RESOURCE;
        	    }
        	    elseif ( $attribute_local_name == RDF_NODEID ) {
        	    	$this->rdf_parser['top']['parent']['collection']['object_type'][] = RDF_OBJECT_TYPE_BNODE;
        	    	$tmp_subject_type = RDF_SUBJECT_TYPE_BNODE;
        	    }
        	    $id_buffer = '';
        	    $this->_resolve_uri_reference( $this->rdf_get_base(), $attribute_value, $id_buffer );
        	    $this->rdf_parser['top']['parent']['collection']['object_label'][] = $id_buffer;

       	    	if (!(   ($namespace_uri == RDF_NAMESPACE_URI || $namespace_uri == '')
	 			      && ($local_name == RDF_DESCRIPTION || $local_name == RDF_LI) ))
				   {
					  $this->_report_statement(
							$tmp_subject_type,
							$id_buffer,
							RDF_NAMESPACE_URI.RDF_TYPE,
							'',
							RDF_OBJECT_TYPE_RESOURCE,
							$namespace_uri.$local_name,
							'',
							'',
							'',
							'',
							'');
				   }
    		}
    	}


// collection construction site
}

/**
   * @param string &$namespace_uri
   * @param string &$local_name
   * @param string &$attributes
   * @access	private
*/
function _handle_xml_start_element(&$namespace_uri, &$local_name, &$attributes)
{
	$aux2=Array();
    foreach($attributes as $atkey=>$atvalue) {
      $aux2[]=$atkey;
      $aux2[]=$atvalue;
    }
    $attributes=$aux2;

    $element = '<' .$this->_join_name_and_declare_prefix($namespace_uri, $local_name);

    for( $i = 0; isset($attributes[$i]); $i += 2 )
    	{
			$this->_split_name(
            	$attributes[ $i ],
            	$attribute,
            	$attribute_namespace_uri,
            	$attribute_local_name );

        	$attribute_value = $attributes[ $i + 1 ];

			$element .= ' ' .$this->_join_name_and_declare_prefix($attribute_namespace_uri, $attribute_local_name);
			$element .= '=\"' .$attribute_value .'\"';
    	}
    $element .= '>';

    $this->rdf_parser['xml_literal']['buffer'] .= $element;
}

/**
   * @param string $name
   * @access	private
*/
function _handle_xml_end_element($name)
{
	$buffer='';
	$namespace_uri='';
    $local_name='';

	$this->_split_name(
        $name,
       	$buffer,
       	$namespace_uri,
       	$local_name );

    $element = '</';

	if ($namespace_uri && isset($this->rdf_parser['default_namespace'])
	    &&$namespace_uri != $this->rdf_parser['default_namespace'])
	{
 		$element .= $this->rdf_parser['namespaces'][$namespace_uri] .':';
	}

    $element .= $local_name .'>';

    $this->rdf_parser['xml_literal']['buffer'] .= $element;
    $depth = $this->rdf_parser['xml_literal']['depth']--;

    if (isset($this->rdf_parser['xml_literal']['declared_ns']))
      	foreach ($this->rdf_parser['xml_literal']['declared_ns'] as $prefix => $_depth)
    {
    	if ($depth == $_depth)
        	unset($this->rdf_parser['xml_literal']['declared_ns'][$prefix]);
    }
}

/**
   * @param string $namespace_uri
   * @param string $local_name
   * @access	private
*/
function _join_name_and_declare_prefix($namespace_uri, $local_name) {

	$name = '';

	if ($namespace_uri)
	{
		if (isset($this->rdf_parser['default_namespace'])
		    && $namespace_uri == $this->rdf_parser['default_namespace'])
		{
			$name .= $local_name;

			if (!isset($this->rdf_parser['xml_literal']['declared_ns']['_DEFAULT_'])
				&& $namespace_uri != XML_NAMESPACE_URI)
			{
				$name .= ' xmlns=' . '\"' .$namespace_uri .'\"';

				$this->rdf_parser['xml_literal']['declared_ns']['_DEFAULT_']
					= $this->rdf_parser['xml_literal']['depth'];
			}
		}
		else
		{
			$ns_prefix = $this->rdf_parser['namespaces'][$namespace_uri];
			$name .= $ns_prefix .':' .$local_name;

			if (!isset($this->rdf_parser['xml_literal']['declared_ns'][$ns_prefix])
				&& $namespace_uri != XML_NAMESPACE_URI)
			{
				$name .= " xmlns:$ns_prefix=" . '\"' .$namespace_uri .'\"';

				$this->rdf_parser['xml_literal']['declared_ns'][$ns_prefix]
					= $this->rdf_parser['xml_literal']['depth'];
			}
		}

	}
	else
		$name .= $local_name;

	return $name;

}

/**
  * @access	private
*/
function _end_collection() {

	if (isset($this->rdf_parser['top']['collection']))
	{

		$subject = $this->rdf_parser['top']['collection']['first_blank_node_id'];

		for ($i=0; isset($this->rdf_parser['top']['collection']['object_label'][$i]); $i++)
		{

			$this->_report_statement(
				RDF_SUBJECT_TYPE_BNODE,
				$subject,
				RDF_NAMESPACE_URI.RDF_FIRST,
				'',
				$this->rdf_parser['top']['collection']['object_type'][$i],
				$this->rdf_parser['top']['collection']['object_label'][$i],
				'',
				'',
				'',
				'',
				'');

			if (!isset($this->rdf_parser['top']['collection']['object_label'][$i+1]))
			{
				$obj_type_2 = RDF_OBJECT_TYPE_RESOURCE;
				$object_2 = RDF_NAMESPACE_URI.RDF_NIL;
			}
			else
			{
				$obj_type_2= RDF_OBJECT_TYPE_BNODE;
				$this->_generate_anonymous_uri($object_2);
			}


			$this->_report_statement(
				RDF_SUBJECT_TYPE_BNODE,
				$subject,
				RDF_NAMESPACE_URI.RDF_REST,
				'',
				$obj_type_2,
				$object_2,
				'',
				'',
				'',
				'',
				'');

			$subject = $object_2;
		}
	}
}

/**
   * @param string $parser
   * @param string $name
   * @param string $attributes
   * @access	private
*/
function _start_element_handler($parser, $name, $attributes )
{
    $buffer='';

    $namespace_uri='';
    $local_name='';

    $this->_push_element();


    $this->_split_name(
        $name,
        $buffer,
        $namespace_uri,
        $local_name );

    switch( $this->rdf_parser['top']['state'] )
    {
    case IN_TOP_LEVEL:
		// set base_uri, if possible
		foreach ($attributes as $key => $value) {
	         if($key == XML_NAMESPACE_URI . NAMESPACE_SEPARATOR_CHAR . 'base') {
			 		$this->rdf_parser['base_uri'] = $value;
					$this->rdf_parser['document_base_uri'] = $value;

					$c = substr($value, strlen($value)-1 ,1);
					if (!($c=='#' || $c==':' || $c=='/' || $c=="\\"))
						 $this->rdf_parser['normalized_base_uri'] = $value . '#';
					else
						$this->rdf_parser['normalized_base_uri'] = $value;

			  }
			  elseif ($key == XML_NAMESPACE_URI . NAMESPACE_SEPARATOR_CHAR .'lang')
			  	$this->rdf_parser['document_xml_lang'] = $value;
echo "";
		}


		if( RDF_NAMESPACE_URI.NAMESPACE_SEPARATOR_STRING.RDF_RDF == $name )
        {
			$this->rdf_parser['top']['state'] = IN_RDF;

			break;
        }
    case IN_RDF:
        $this->rdf_parser['top']['state'] = IN_DESCRIPTION;
        $this->_handle_resource_element( $namespace_uri, $local_name, $attributes, '' );
        break;
    case IN_DESCRIPTION:
    case IN_PROPERTY_PARSE_TYPE_RESOURCE:
 		$this->rdf_parser['top']['state'] = IN_PROPERTY_UNKNOWN_OBJECT;
        $this->_handle_property_element( $namespace_uri, $local_name, $attributes );
        break;
   	case IN_PROPERTY_PARSE_TYPE_COLLECTION:
		$this->_handle_collection_element($namespace_uri, $local_name, $attributes);
		break;
    case IN_PROPERTY_UNKNOWN_OBJECT:
        /* if we're in a property with an unknown object type and we encounter
           an element, the object must be a resource, */
        $this->rdf_parser['top']['data']='';
        $this->rdf_parser['top']['parent']['state'] = IN_PROPERTY_RESOURCE;
        $this->rdf_parser['top']['state'] = IN_DESCRIPTION;
        $this->_handle_resource_element(
            $namespace_uri,
            $local_name,
            $attributes,
            $this->rdf_parser['top']['parent'] );
        break;
    case IN_PROPERTY_LITERAL:
        $this->_report_warning( 'no markup allowed in literals' );
        break;
    case IN_PROPERTY_PARSE_TYPE_LITERAL:
    	$this->rdf_parser['top']['state'] = IN_XML;
        /* fall through */
    case IN_XML:
		$this->rdf_parser['xml_literal']['depth']++;
		$this->_handle_xml_start_element($namespace_uri, $local_name, $attributes);
        break;
    case IN_PROPERTY_RESOURCE:
        $this->_report_warning(
                     'only one element allowed inside a property element' );
        break;
    case IN_PROPERTY_EMPTY_RESOURCE:
        $this->_report_warning(
                    'no content allowed in property with rdf:resource, rdf:bagID, or property attributes' );
        break;
    case IN_UNKNOWN:
        break;
    }
}

/**
    property elements with text only as content set the state to
    IN_PROPERTY_LITERAL. as character data is received from expat,
    it is saved in a buffer and reported when the end tag is
    received.
  * @access	private
*/
function _end_literal_property()
{
    if(!isset($this->rdf_parser['top']['statement_id'])) {
      $this->rdf_parser['top']['statement_id']='';
    }
    if(!isset($this->rdf_parser['top']['parent']['subject_type'])) {
      $this->rdf_parser['top']['parent']['subject_type']='';
    }
    if(!isset($this->rdf_parser['top']['parent']['subject'])) {
      $this->rdf_parser['top']['parent']['subject']='';
    }
    if(!isset($this->rdf_parser['top']['parent']['bag_id'])) {
      $this->rdf_parser['top']['parent']['bag_id']='';
    }
    if(!isset($this->rdf_parser['top']['parent']['statements'])) {
      $this->rdf_parser['top']['parent']['statements']=0;
    }
    if(!isset($this->rdf_parser['top']['predicate'])) {
      $this->rdf_parser['top']['predicate']='';
    }
    if(!isset($this->rdf_parser['top']['datatype'])) {
      $this->rdf_parser['top']['datatype']='';
    }
    if(!isset($this->rdf_parser['top']['ordinal'])) {
      $this->rdf_parser['top']['ordinal']=0;
    }
    $this->_report_statement(
        $this->rdf_parser['top']['parent']['subject_type'],
        $this->rdf_parser['top']['parent']['subject'],
        $this->rdf_parser['top']['predicate'],
        $this->rdf_parser['top']['ordinal'],
        RDF_OBJECT_TYPE_LITERAL,
        $this->rdf_parser['top']['data'],
        $this->rdf_parser['top']['xml_lang'],
        $this->rdf_parser['top']['parent']['bag_id'],
        $this->rdf_parser['top']['parent']['statements'],
        $this->rdf_parser['top']['statement_id'],
		$this->rdf_parser['top']['datatype']);

}

/**
   * @param string $parser
   * @param string $name
   * @access	private
*/
function _end_element_handler( $parser, $name )
{
    switch( $this->rdf_parser['top']['state'] )
    {
    case IN_TOP_LEVEL:
		break;
    case IN_XML:
		$this->_handle_xml_end_element($name);
        break;
    case IN_PROPERTY_UNKNOWN_OBJECT:
    case IN_PROPERTY_LITERAL:
        $this->_end_literal_property( );
        break;
    case IN_PROPERTY_PARSE_TYPE_RESOURCE:
        $this->_pop_element(  );
        break;
    case IN_PROPERTY_PARSE_TYPE_LITERAL:
//		$search =  array((0) => chr(10), (1) => chr(13), (2) => chr(9));
//		$replace = array((0) => '\n'   , (1) => '\r'   , (2) => '\t');
//		$this->rdf_parser["xml_literal"]["buffer"]
//			= str_replace($search, $replace, $this->rdf_parser["xml_literal"]["buffer"]);

		$this->rdf_parser['top']['data'] = $this->rdf_parser['xml_literal']['buffer'];
		$this->_end_literal_property();
		$this->rdf_parser['xml_literal']['buffer'] = '';

        break;
	case IN_PROPERTY_PARSE_TYPE_COLLECTION:
	    $this->_end_collection();
		break;
    case IN_RDF:
    case IN_DESCRIPTION:
    case IN_PROPERTY_RESOURCE:
    case IN_PROPERTY_EMPTY_RESOURCE:
    case IN_UNKNOWN:
        break;
    }

    $this->_pop_element();
}

/**
   * @param string $parser
   * @param string $s
   * @access	private
*/
function _character_data_handler( $parser,$s)
{
    $len=strlen($s);
    switch( $this->rdf_parser['top']['state'] )
    {
    case IN_PROPERTY_LITERAL:
    case IN_PROPERTY_UNKNOWN_OBJECT:
        if( isset($this->rdf_parser['top']['data']) )
        {
            $n = strlen( $this->rdf_parser['top']['data'] );
            $this->rdf_parser['top']['data'].= $s;

        }
        else
        {
            $this->rdf_parser['top']['data']=$s;
        }

        if( $this->rdf_parser['top']['state'] == IN_PROPERTY_UNKNOWN_OBJECT )
        {
            /* look for non-whitespace */
            for( $i = 0; (( $i < $len ) && (  ereg(" |\n|\t",$s{ $i }) )); $i++ );
            /* if we found non-whitespace, this is a literal */
            if( $i < $len )
            {
                $this->rdf_parser['top']['state'] = IN_PROPERTY_LITERAL;
            }
        }

        break;
    case IN_TOP_LEVEL:
    	break;
    case IN_PROPERTY_PARSE_TYPE_LITERAL:
    case IN_XML:
        $this->rdf_parser['xml_literal']['buffer'] .= $s;
        break;
    case IN_RDF:
    case IN_DESCRIPTION:
    case IN_PROPERTY_RESOURCE:
    case IN_PROPERTY_EMPTY_RESOURCE:
    case IN_PROPERTY_PARSE_TYPE_RESOURCE:
    case IN_UNKNOWN:
        break;
    }
}


  /**
   * Adds a new statement to the model
   * This method is called by generateModel().
   *
   * @access	private
   * @param	string	&$user_data
   * @param	string	$subject_type
   * @param	string	$subject
   * @param	string	$predicate
   * @param	string	$ordinal
   * @param	string	$object_type
   * @param	string	$object
   * @param	string	$xml_lang )
   * @return	object MemModel
   */
function add_statement_to_model(
	&$user_data,
	$subject_type,
	$subject,
	$predicate,
	$ordinal,
	$object_type,
	$object,
	$xml_lang,
	$datatype )
{

	// ParseUnicode
	if(UNIC_RDF){
		$subject=$this->str2unicode_nfc($subject);
		$predicate=$this->str2unicode_nfc($predicate);
		$object=$this->str2unicode_nfc($object);
	}

	//create subject
	if ($subject_type == RDF_SUBJECT_TYPE_BNODE)
			$objsub = new BlankNode($subject);
	    else
			$objsub = new Resource($subject);

	// create predicate
	$objpred = new Resource($predicate);

	// create object
	if (($object_type == RDF_OBJECT_TYPE_RESOURCE) || ($object_type == RDF_OBJECT_TYPE_BNODE)) {
			if ($object_type == RDF_OBJECT_TYPE_BNODE)
					$objobj = new BlankNode($object);
				else
					$objobj = new Resource($object);
	} else {

		$objobj = new Literal($object);
		if ($datatype != '') {
			$objobj->setDatatype($datatype);
		}
		elseif ($xml_lang !='') {
			$objobj->setLanguage($xml_lang);
		}
	}

	// create statement
	$statement = new Statement($objsub, $objpred, $objobj);

	// add statement to model
	if(CREATE_MODEL_WITHOUT_DUPLICATES == TRUE){
		$this->model->addWithoutDuplicates($statement);
	}else
		$this->model->add($statement);
	}


/* public functions */

  /**
   * Generates a new MemModel from a URI, a file or from memory.
   * If you want to parse an RDF document, pass the URI or location in the filesystem
   * of the RDF document. You can also pass RDF code direct to the function. If you pass
   * RDF code directly to the parser and there is no xml:base included, you should set
   * the base URI manually using the optional second parameter $rdfBaseURI.
   * Make sure that here are proper namespace declarations in your input document.
   *
   * @access	public
   * @param		string	 $base
   * @param     boolean  $rdfBaseURI
   * @return	object MemModel
   */
function & generateModel($base,$rdfBaseURI = false, $model = false) {

	// Check if $base is a URI or filename or a string containing RDF code.
	if (substr(ltrim($base),0 ,1) != '<') {

	// $base is URL or filename
	$this->model = $model?$model:new MemModel($base);

	$input = fopen($base,'r') or die("RDF Parser: Could not open File: $base. Stopped parsing.");
	$this->rdf_parser_create( NULL );
	$this->rdf_set_base($base);
	$done=false;
	while(!$done)
	{
	  $buf = fread( $input, 512 );
	  $done = feof($input);

	  if ( ! $this->rdf_parse( $buf, feof($input) ) )
	  {
	    $err_code = xml_get_error_code( $this->rdf_get_xml_parser());
		$line = xml_get_current_line_number($this->rdf_get_xml_parser() );
	    $errmsg = RDFAPI_ERROR . '(class: parser; method: generateModel): XML-parser-error ' . $err_code .' in Line ' . $line .' of input document.';
	    trigger_error($errmsg, E_USER_ERROR);
	  }
	}
	/* close file. */
	fclose( $input );

	} else {
	// $base is RDF string
    $this->model = $model?$model:new MemModel($base);

	$this->rdf_parser_create( NULL );

	if ($rdfBaseURI!==false)
	{
		$this->rdf_set_base($rdfBaseURI);
	} else
	{
		$this->rdf_set_base( NULL );
	}

	  if ( ! $this->rdf_parse( $base, TRUE ) )
	  {
	    $err_code = xml_get_error_code( $this->rdf_get_xml_parser());
		$line = xml_get_current_line_number($this->rdf_get_xml_parser() );
	    $errmsg = RDFAPI_ERROR . '(class: parser; method: generateModel): XML-parser-error ' . $err_code .' in Line ' . $line .' of input document.';
	    trigger_error($errmsg, E_USER_ERROR);
	  }
	}
	// base_uri could have changed while parsing
	$this->model->setBaseURI($this->rdf_parser['base_uri']);

	if(isset($this->rdf_parser['namespaces'])){
		$this->model->addParsedNamespaces($this->rdf_parser['namespaces']);
	}

	$this->rdf_parser_free();

	return $this->model;

}

/**
 * @param		string	 $encoding

 * @access	private
*/
function rdf_parser_create( $encoding )
{

    $parser = xml_parser_create_ns( $encoding, NAMESPACE_SEPARATOR_CHAR );

    xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
    $this->rdf_parser['xml_parser'] = $parser;

    xml_set_object($this->rdf_parser['xml_parser'], $this);
    xml_set_element_handler( $this->rdf_parser['xml_parser'], '_start_element_handler', '_end_element_handler' );
    xml_set_character_data_handler( $this->rdf_parser['xml_parser'], '_character_data_handler' );
	xml_set_start_namespace_decl_handler($this->rdf_parser['xml_parser'], '_start_ns_declaration_handler');

    return $this->rdf_parser;
}

/**
 * @param	resource	&$parser
 * @param   string		$ns_prefix
 * @param	string		$ns_uri
 * @access	private
*/
function _start_ns_declaration_handler(&$parser, $ns_prefix, $ns_uri)
{
	if (!$ns_prefix)
		$this->rdf_parser['default_namespace'] = $ns_uri;
	else
		$this->rdf_parser['namespaces'][$ns_uri] = $ns_prefix;
}


/**
 * @access	private
*/
function rdf_parser_free( )
{
    $z=3;

    $this->rdf_parser['base_uri']='';
	$this->rdf_parser['document_base_uri'] = '';

    unset( $this->rdf_parser );
}

/**
   * @param		string	 $s
   * @param		string	 $is_final
 * @access	private
*/
function rdf_parse( $s, $is_final )
{
    return XML_Parse( $this->rdf_parser['xml_parser'], $s, $is_final );
}

/**
 * @access	private
*/
function rdf_get_xml_parser()
{
    return ( $this->rdf_parser['xml_parser']);
}

/**
   * @param		string	 $base
 * @access	private
*/
function rdf_set_base($base )
{

    $this->rdf_parser['base_uri']=$base;

	$c = substr($base, strlen($base)-1 ,1);
	if (!($c=='#' || $c==':' || $c=='/' || $c=="\\"))
		 $this->rdf_parser['normalized_base_uri'] = $base . '#';
	else
		$this->rdf_parser['normalized_base_uri'] = $base;

    return 0;
}

/**
 * @access	private
*/
function rdf_get_base()
{
		if ($this->rdf_parser['top']['element_base_uri'])
			return $this->rdf_parser['top']['element_base_uri'];
		else
			return $this->rdf_parser['base_uri'];
}


} // end: rdf_parser

?>