<?php
require_once RDFAPI_INCLUDE_DIR . 'util/Object.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Blanknode.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Resource.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Literal.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Statement.php';
require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
require_once RDFAPI_INCLUDE_DIR . 'constants.php';

// ----------------------------------------------------------------------------------
// Class: N3Parser
// ----------------------------------------------------------------------------------


/**
 * PHP Notation3 Parser
 *
 * This parser can parse a subset of n3, reporting triples to a callback function
 * or constructing a RAP Model ( http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi )
 *
 * Supported N3 features:
 * <ul>
 *   <li>Standard things, repeated triples ( ; and , ), blank nodes using [ ], self-reference ('<>')</li>
 *   <li>@prefix mappings</li>
 *   <li>= maps to owl#sameAs</li>
 *   <li>a maps to rdf-syntax-ns#type</li>
 *   <li>Literal datytype- and xmlLanguageTag support
 * </ul>
 * Un-supported N3 Features include:
 * <ul>
 *   <li>Reification using { }</li>
 *   <li>. and ^ operators for tree traversal</li>
 *   <li>Any log operators, like log:forAll etc.</li>
 * </ul>
 *
 * This parser is based on n3.py from Epp released 2nd March, 2002.
 * by Sean B. Palmer
 * ( http://infomesh.net/2002/eep/20020302-013802/n3.py )
 *
 * This parser is released under the GNU GPL license.
 * ( http://www.gnu.org/licenses/gpl.txt )
 *
 *
 *
 * @author Sean B. Palmer <sean@mysterylights.com>
 * @author Gunnar AA. Grimnes <ggrimnes@csd.abdn.ac.uk>
 * @author Daniel Westphal <mail@d-westphal.de>
 * @version $Id: N3Parser.php 517 2007-08-13 16:14:17Z cweiske $
 * @license GPL http://www.gnu.org/licenses/gpl.txt
 * @package syntax
 * @access public
 **/

class N3Parser extends Object {


  /* ==================== Variables ==================== */

  var $Tokens;
  var $bNode;
  var $RDF_NS, $DAML_NS, $OWL_NS;
  var $debug;
  var $parseError;
  var $parsedNamespaces = array();

  /* ==================== Public Methods ==================== */

  /**
   * Constructor
   * @access public
   **/
  function N3Parser() {
    //Regular expressions:
     $Name     = '[A-Za-z0-9_@\.]+[^\.,;\[\]\s\) ]*';
     $URI      = '<[^> ]*>';
     $bNode    = '_:'.$Name;
     $Univar   = '\?'.$Name;
     $QName    = '(?:[A-Za-z][A-Za-z0-9_@\.]*)?:'.$Name;
     $Literal  = '(?:'
               . '"(\\\"|[^"])*"'
               . '|'
               . "'(\\\'|[^'])*'"
               . ')';
               # '"(?:\\"|[^"])*"'
     $Number   = '[-+]?[0-9]+(\\.[0-9]+)?([eE][-+]?[0-9]+)?';
     $Boolean  = '@(?:true|false)';
//   $Literal  = '"[^"\\\\]*(?:\\.\\[^"\\]*)*"'; # '"(?:\\"|[^"])*"'
     $LangTag  = '@[A-Za-z\-]*[^ \^\.\;\,]';
     $Datatype = '(\^\^)[^ ,\.;)]+';
     $Datatype_URI = '(\^\^)'.$URI;
     //     $LLiteral = '"""[^"\\\\]*(?:(?:.|"(?!""))[^"\\\\]*)*"""';
     $LLiteral = '(?:'
                 . '"""[^"\\\\]*(?:(?:\\\\.|"(?!""))[^"\\\\]*)*"""'
                 . '|'
                 . "'''[^'\\\\]*(?:(?:\\\\.|'(?!''))[^\"\\\\]*)*'''"
                 . ')';
     //          '"""[^"\\]*(?:(?:\\.|"(?!""))[^"\\]*)*"""'
     $Comment    = '#.*$';
     $Prefix     = '(?:[A-Za-z][A-Za-z0-9_]*)?:';
     $PrefixDecl = '@prefix';
     $WS         = '[ \t]';
     $this->RDF_NS  = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'; # for 'a' keyword
     $this->DAML_NS = 'http://www.daml.org/2001/03/daml+oil#'; # for '=' keyword
     $this->OWL_NS  = 'http://www.w3.org/2002/07/owl#';

     //     $t = array( $LLiteral, $URI); //, $Literal, $PrefixDecl, $QName, $bNode, $Prefix,
     //	    $Univar, 'a', '{', '}', '\(', '\)', '\[', '\]', ',', ';', '\.', $WS, $Comment);
     $t = array(
            $Datatype_URI, $Datatype, $LLiteral, $URI, $Literal,
            $PrefixDecl, $QName, $Number, $Boolean, $bNode,
            $Prefix, $Univar, 'a','=',
            '{', '}', '\(', '\)', '\[', '\]', ',', ';', '\.',
            $WS, $Comment,$LangTag
     );
     $this->Tokens = "/(".join($t,"|").")/m";

     $this->bNode      = 0;
     $this->debug      = 0;
     $this->bNodeMap   = array();
     $this->FixBnodes  = FIX_BLANKNODES;
     $this->parseError =false;
  }


  /**
   * Sets, if BlankNode labels should be replaced by the generic label from the constants.php file
   * default is "false" -> the used label in n3 is parsed to the model
   * @param boolean
   * @access public
   **/
  function setFixBnodes($set) {

  	if (($set===true) OR ($set===false)) $this->FixBnodes = $set;
  }


  /**
   * This parses a N3 string and prints out the triples
   * @param string $s
   * @access public
   **/
  function parse($s) {
    //   """Get a string, tokenize, create list, convert to Eep store."""
    $stat=$this->n3tolist($s);
    foreach ( $stat as $t) {

      if (count($t)>3) {
        $object=$t[2];

        for ($i = 3; $i < 5; $i++){
          if ($t[$i][0]=='@')$object.=$t[$i];
          if (substr($t[$i],0,2)=='^^')$object.=$t[$i];
        };
      } else {$object=$t[2];};

      print '('.$t[0].', '.$t[1].', '.$object.")\n";

    }
    //   return [[eep.Article(t[0]), eep.Article(t[1]), eep.Article(t[2])]
    //              for t in n3tolist(s)]
  }


  /**
   * This parses a N3 string and calls func($subject, $predicate, $object) with each triple
   * @param string $s
   * @param string $func
   * @access public
   **/
  function uparse($s,$func) {
    //   """Get a string, tokenize, create list, convert to Eep store."""
    $stat=$this->n3tolist($s);
    foreach ( $stat as $t) {

    	if (count($t)>3) {
        $object=$t[2];

        for ($i = 3; $i < 5; $i++){
          if ($t[$i][0]=='@')$object.=$t[$i];
          if (substr($t[$i],0,2)=='^^')$object.=$t[$i];
        };
      } else {$object=$t[2];};
    	//    print "(".$t[0].", ".$t[1].", ".$t[2].")";

      $func($t[0],$t[1],$object);
    }
    //   return [[eep.Article(t[0]), eep.Article(t[1]), eep.Article(t[2])]
    //              for t in n3tolist(s)]
  }


  /**
   * This parses a N3 string and returns a memmodel
   * @param string $s
   * @access public
   * @return object Model
   **/

  function parse2model($s,$model = false) {
	if($model == false){
	    $m=new MemModel();
	}else{
		$m=$model;
	}
    //   """Get a string, tokenize, create list, convert to Eep store."""
    $stat=$this->n3tolist($s);

    foreach ( $stat as $t) {
      $s=$this->toRDFNode($t[0],$t);
      $p=$this->toRDFNode($t[1],$t);
      $o=$this->toRDFNode($t[2],$t);

       $new_statement= new Statement($s,$p,$o);

      $m->add($new_statement);
      //    print "(".$t[0].", ".$t[1].", ".$t[2].")";
    }
    //   return [[eep.Article(t[0]), eep.Article(t[1]), eep.Article(t[2])]
    //              for t in n3tolist(s)]
    $m->addParsedNamespaces($this->parsedNamespaces);
    return $m;
  }

/**
 * Generate a new MemModel from an URI or file.
 *
 * @access	public
 * @param $path
 * @throws PhpError
 * @return object MemModel
 */
  function & generateModel($path,$dummy=false,$model=false) {

    $handle = fopen($path,'r') or die("N3 Parser: Could not open File: '$path' - Stopped parsing.");
	$done=false;
	$input="";
	while(!$done)
	{
	  $input .= fread( $handle, 512 );
	  $done = feof($handle);

	};


    fclose($handle);

    $m = $this->parse2model($input,$model);
    return $m;
  }


  /* ==================== Private Methods from here ==================== */

  //  General list processing functions

/**
 * Returns FALSE if argument is a whitespace character
 * @access private
 * @param string $s
 **/
  function isWS($s) {
    return !preg_match('/^(#.*|\s*)$/', $s);
  }



  /**
   * Returns true if the string is not a comment
   * @access private
   * @param string $s
   * @returns boolean
   **/
  function notComment($s) {
    if ($s=="") return false;
    $N3Comment = '^[ \t]*\#';

    if (ereg($N3Comment,$s)) return false;
    else return true;
  }

  /**
   * Removes all whitespace tokens from list
   * @access private
   * @param array $list
   **/
  function filterWs($list) {
    //    var_dump($list);
    //  """Filter whitespace from a list."""

    return array_filter($list, array($this,"isWS"));
  }

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
   * Gets a slice of an array.
   * Returns the wanted slice, as well as the remainder of the array.
   * e.g. getSpan(['p', 'q', 'r'], 1, 2) gives (['q'], ['p', 'r'])
   * @return array
   * @access private
   * @param array $list
   * @param integer $start
   * @param integer $end
   **/
  function getSpan($list, $start, $end) {

    $pre=array_slice($list, 0, $start);
    $post=array_slice($list, $end);

    return array(array_slice($list, $start,$end-$start),$this->array_concat($pre,$post));
  }


  /**
   * Concatenates two arrays
   * @param array $a
   * @param array $b
   * @returns array
   * @access private
   **/
  function array_concat($a, $b) {
    array_splice($a,count($a),0,$b);
    return $a;
  }

  /**
   * Returns an array with all indexes where item appears in list
   * @param array $list
   * @param string $item
   * @returns array
   * @access private
   **/
  function posns($list, $item) {
    $res=array();
    $i=0;
    foreach ( $list as $k=>$v) {
      if ($v === $item ) $res[]=$i;
      $i++;
    }
    $res[]=$i;
    return $res;
  }


  /* More N3 specific functions */

  /**
   * Returns a list of tokens
   * @param string $s
   * @returns array
   * @access private
   **/
  function toke($s) {

    //    print "$s\n";
    //   """Notation3 tokenizer. Takes in a string, returns a raw token list."""
    if (strlen($s) == 0) die('Document has no content!');

    $s=str_replace("\r\n","\n",$s);
    $s=str_replace("\r","\n",$s);


    //$lines=explode("\n",$s);

    //$reallines=array_filter($lines, array($this, "notComment"));
    //    print "LINES: ".join($reallines, " ")." :LINES\n";
    //array_walk($reallines, array($this, "trimLine"));
    //$res=array();

    //    foreach ($reallines as $l) {
    //preg_match_all($this->Tokens, $l, $newres);
    //$res=$this->array_concat($res,$newres[0]);
    //}

    $res=array();
    preg_match_all($this->Tokens, $s, $newres);
    $res=$this->array_concat($res, array_map('trim', $newres[0]));
//var_dump($newres[0]);
    return $res;
  }

  /**
   * Returns a list with the elements between start and end as one quoted string
   * e.g. listify(["a","b","c","d"],1,2) => ["a","b c", "d"]
   * @param array $list
   * @param integer $start
   * @param integer $end
   * @returns array
   * @access private
   **/
  function listify($list, $start, $end) {

    //Re-form a list, merge elements start->end into one quoted element
    //Start and end are offsets...

    $l=$end-$start;

    $s=array_slice($list, 0, $start);
    $m=array_slice($list, $start,$l);
    $e=array_slice($list, $end);

    //  array_push($s,"\"".join($m," ")."\"");
    array_push($s,$m);

    return $this->array_concat($s,$e);
  }

  /**
   * Returns an array with prefixes=>namespace mappings
   * @param array $list
   * @access private
   * @returns array
   **/
  function getPrefixes($list) {

    $prefixes=array();
    $ns=1;
    $name=2;
    foreach ($list as $l) {
      if ($l=='@prefix') {
	//   while '@prefix' in list {

	$pos=current($list);
	//pos = list.index('@prefix')
	$r = $this->getSpan($list, $pos, ($pos+4)); # processes the prefix tokens
	$binding=$r[0];
	$list=$r[1];
	$prefixes[$binding[$ns]] = substr($binding[$name],1,-1);
	$this->parsedNamespaces[substr($binding[$name],1,-1)] = substr($binding[$ns],0,-1);
      }
    }

	if (count($prefixes)<1) $list= array_slice($list,0);

    return array($prefixes, $list);
  }

  /**
   * Callback function for replacing "a" elements with the right RDF uri.
   * @param string $l
   * @access private
   **/
  function replace_a_type(&$l,$p) {
    if ($l=='a') $l='<'.$this->RDF_NS.'type>';
  }

  /**
   * Callback function for replacing "=" elements with the right DAML+OIL uri.
   * @param string $l
   * @access private
   **/
  function replace_equal(&$l,$p) {
    if ($l=='=') $l='<'.$this->OWL_NS.'sameAs>';
  }

  /**
   * Callback function for replacing "this" elements with the right RDF uri.
   * @param string $l
   * @access private
   **/
  function replace_this($l,$p) {
    if ($l=='this') $l='<urn:urn-n:this>';
  }

    /**
    * Applies stuff :)
    * Expands namespace prefixes etc.
    * @param array $prefixes
    * @param array $list
    * @returns $list
    * @access private
    **/
    function applyStuff($prefixes, $list)
    {
        array_walk($list, array($this, 'replace_a_type'));
        array_walk($list, array($this, 'replace_equal'));
        array_walk($list, array($this, 'replace_this'));

        for ($i = 0; $i < count($list); $i++) {

            if ($list[$i]=='<>') {
                if (!isset($path)) {
                    if (!isset($_SERVER['SERVER_ADDR'])) {
                        $_SERVER['SERVER_ADDR'] = 'localhost';
                    }
                    if (!isset($_SERVER['REQUEST_URI'])) {
                        $_SERVER['REQUEST_URI'] = '/rdfapi-php';
                    }
                    $list[$i] = '<http://'.$_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_URI'].'#generate_timestamp_'.time().'>';
                } else {
                    $list[$i] = '<'.$path.'>';
                };
            };


            if (preg_match('/^[-+]?[0-9]+$/', $list[$i])) {
                //integer
                $list[$i] = intval($list[$i]);
            } else if (is_numeric($list[$i])) {
                //float or decimal
                // After conversion we cannot distinguish between both
                $list[$i] = floatval($list[$i]);
            } else if ((!strstr('<_"\'?.;,{}[]()@', $list[$i]{0}))
             && (substr($list[$i],0,3) != '^^<')
            ) {
                //prefix or unknown
                $_r   = explode(':', $list[$i]);
                $ns   = $_r[0] . ':';
                $name = $_r[1];

                if (isset($prefixes[$ns])) {
                    $list[$i] = '<'.$prefixes[$ns].$name.'>';
                } else if (isset($prefixes[substr($ns, 2)])) {
                    $list[$i] = '^^' . $prefixes[substr($ns, 2)] . $name . '';
                } else {
                    //die('Prefix not declared:'.$ns);
                    $this->parseError = true;
                    trigger_error('Prefix not declared: '.$ns, E_USER_ERROR);
                    break;
                }

            } else {
                if ($list[$i]{0} == '"') {
                    $bLiteral = true;
                    $chBase   = '"';
                } else if ($list[$i]{0} == '\'') {
                    $bLiteral = true;
                    $chBase   = '\'';
                } else {
                    $bLiteral = false;
                }
                if ($bLiteral) {
                    $tripleBase = $chBase . $chBase . $chBase;
                    // Congratulations - it's a literal!
                    if (substr($list[$i], 0, 3) == $tripleBase) {
                        if (substr($list[$i],-3,3) == $tripleBase) {
                            // A big literal...
                            $lit = substr($list[$i],3,-3);
                            //	      print "++$lit++";
                            $lit=str_replace('\n', '\\n',$lit);

                            //$lit=ereg_replace("[^\\]" . $chBase, "\\" . $chBase, $lit);
                            $lit = stripslashes($lit);

                            $list[$i] = $chBase . $lit . $chBase;
                        } else {
                            die ('Incorrect string formatting: '.substr($list[$i],-3,3));
                        }
                    } else {
                        if (strstr($list[$i],"\n")) {
                            die('Newline in literal: ' . $list[$i]);
                        }
                        $list[$i] = stripslashes($list[$i]);
                    }
                }
            }

            if (substr($list[$i],0,2)=='^^') {
                if ($list[$i][2]!='<') {
                    $list[$i] = '^^<' . substr($list[$i], 2) . '>';
                }
            };

        }//foreach list item

        return $list;
    }//function applyStuff($prefixes, $list)



  /**
   * Returns an array of triples extracted from the list of n3 tokens
   * @param array $list
   * @returns array
   * @access private
   **/
  function getStatements($list) {


    $statements = array();

    while (in_array('.', $list)) {
      //  for($i=0;$i<count($list); $i++) {
      //    if ($list[$i]==".") {
      //   while '.' in list {
      $pos=array_search('.',$list);

      $r=$this->getSpan($list, 0, $pos+1);

      $statement=$r[0];
      $list = $r[1];

      array_pop($statement);
      $statements[]=$statement;
    }

    return $statements;
  }

  /**
   * Gets a list of triples with same subject
   * e.g. :Gunnar :firstname "Gunnar" ; :lastname "Grimnes.
   * @param array $list
   * @returns array
   * @acces private
   **/
  function getPovs($list) {
    $povs = array();
    while (in_array(';', $list)) {
      $r=$this->posns($list,';');
      $pos=array_slice($r,0,2);
      $r = $this->getSpan($list, $pos[0], $pos[1]);
      $pov=$r[0];
      $list=$r[1];

      // skip lone semicolons, e.g. "<a> <b> <c> ; ."
      if (count($pov) == 1) continue;

      $povs[]=array_slice($pov,1);
    }

    return array($list, $povs);
  }

  /**
   * Gets a list of triples with same predicate
   * e.g. :Gunnar :likes "Cheese", "Wine".
   * @access private
   * @param array $list
   * @returns array
   **/
  function getObjs($list) {


    $objs = array();
    while (in_array(",",$list)) {
      $pos=array_search(",",$list);
      //  for($i=0;$i<count($list); $i++) {
      //    if ($list[$i]==",") {
      //   while ',' in list {


      $get_array_fields=2;
      if (isset ($list[$pos+2])) {
      	if (@$list[$pos+2][0]=='@') $get_array_fields++;
        if (@$list[$pos+2][0]=='^') $get_array_fields++;
      };
      if (isset ($list[$pos+3])) { if (@$list[$pos+3][0]=='^') $get_array_fields++;};


      $r=$this->getSpan($list, $pos, ($pos+$get_array_fields));

      $obj=$r[0];
      if (!isset($obj[2])) $obj[2]=' ';
      if (!isset($obj[3])) $obj[3]=' ';

      $list=$r[1];


      $objs[]=$obj;


    }

    return array($list, $objs);
  }

  /**
   * Does the real work, returns a list of subject, predicate, object triples.
   * @param array $list
   * @returns array
   * @access private
   **/
  function statementize($list) {

    if (count($list) == 1 && preg_match("/_".BNODE_PREFIX."[0-9]+_/",$list[0])) {
	if ($this->debug) print "Ignored bNode exists statement. $list\n";
	return array();
    }



    if (count($list) == 3) return array($list);
    if (count($list) < 3) {
        throw new Exception(
            'N3 statement too short,'
            . ' only ' . count($list) . ' elements instead of 3:' . "\n"
            . implode("\n", $list)
        );
    }

    //Get all ;
    $r=$this->getPovs($list);
    $spo=$r[0];
    $po=$r[1];
    $all=array();



    //      (spo, po), all = getPovs(list), []
    $subject = $spo[0];
    foreach ($po as $pop) {
      //  for pop in po {
      $r=$this->getObjs($pop);

      $myPo=$r[0];
      $obj=$r[1];
      //myPo, obj = getObjs(pop)

      if (!isset($myPo[2])) $myPo[2]=' ';
      if (!isset($myPo[3])) $myPo[3]=' ';


      $predicate = $myPo[0];
      $all[]=array($subject,$predicate,$myPo[1],$myPo[2],$myPo[3]);
      //    all.append([subject, predicate, myPo[1]])



      foreach ($obj as $o) $all[]=array($subject,$predicate, $o[1],$o[2],$o[3]);
      //         for x in obj: all.append([subject, predicate, x])

    }



    $r = $this->getObjs($spo);
    $spo=$r[0];

    $objs=$r[1];

    //spo, objs = getObjs(spo)
    $subject=$spo[0];
    $predicate=$spo[1];


    if(!isset($spo[3])) $spo[3]=' ';
    if(!isset($spo[4])) $spo[4]=' ';

    $all[]=array($subject, $predicate, $spo[2],$spo[3],$spo[4]);

    foreach ($objs as $obj) $all[]=array($subject, $predicate, $obj[1],$obj[2],$obj[3]);

    return $all;
  }

  /**
   * Makes lists of elements in list into a seperate array element.
   * e.g. doLists(["a","b","[","c","]","d"], "[","]")=> ["a","b", ["c"], "d"]
   * @param array $list
   * @param string $schar
   * @param string $echar
   * @returns array
   * @access private
   **/
  function doLists($list, $schar, $echar) {

    while (in_array($schar, $list)) {
      //   while schar in list {
      $ndict        = array();
      $nestingLevel = 0;
      $biggest      = 0;
      for ($i = 0; $i < count($list); $i++) {
        if ($list[$i] == $schar) {
            $nestingLevel += 1;
            if (!in_array($nestingLevel, array_keys($ndict))) {
              $ndict[$nestingLevel] = array(array($i));
            } else {
              $ndict[$nestingLevel][]=array($i);
            }
        }
        if ($list[$i] == $echar) {
          if (!in_array($nestingLevel, array_keys($ndict))) {
            $ndict[$nestingLevel]=array(array($i));
          } else {
            $ndict[$nestingLevel][count($ndict[$nestingLevel])-1][]=$i;
            $nestingLevel-= 1;
# elif type(list[i]) == type([]) {
#    list[i] = doLists(list[i], schar, echar)
          }
        }
      }
      foreach (array_keys($ndict) as $key) {
        if ($key > $biggest)  $biggest = $key;
      }

      $tol  = $ndict[$biggest][0];
      $list = $this->listify($list, $tol[0], ($tol[1]+1));
    }
    return $list;
  }

  /**
   * Apply doLists for all different types of list.
   * @param array
   * @returns array
   * @access private
   **/
  function listStuff($list) {
# y, z = zip(['[', ']'], ['{', '}'], ['(', ')'])
# return map(doLists, [list, list, list], y, z).pop()
    $list = $this->doLists($list, '[', ']');
    $list = $this->doLists($list, '{', '}');
    return $this->doLists($list, '(', ')');
  }

  /**
   * Generates a new node id.
   * @access private
   * @returns string
   **/
  function bnodeID() {

    $this->bNode++;
    return "_".BNODE_PREFIX.$this->bNode."_";
  }

  /**
   * This makes bNodes out of variables like _:a etc.
   * @access private
   * @param array $list
   * @returns array
   **/
  function fixAnon($list) {
//    $map=array();
    for($i=0;$i<count($list);$i++) {
      $l=$list[$i];
      if (substr($l,0,2)=="_:") {
	  if (!isset($this->bNodeMap[$l])) {
	  $a=$this->bnodeID();
	  $this->bNodeMap[$l]=$a;
	} else $a=$this->bNodeMap[$l];
	$list[$i]=$a;
      }
    }
    return $list;
  }

  /**
   * This makes [ ] lists into bnodes.
   * @access private
   * @param array $list
   * @return array
   **/
  function expandLists($list) {

    for($i=0;$i<count($list);$i++) {
      if (is_array($list[$i])) {
	if ( $list[$i][0]=='[' ) {
	  $bnode=$this->bnodeID();
	  $prop=$list[$i];
	  $list[$i]=$bnode;
	  $list[]=$bnode;
	  $list=$this->array_concat($list, array_slice($prop,1,-1));
	  $list[]='.';
	}elseif($list[$i][0]=='(') {

	    $rdfNil = '<'. RDF_NAMESPACE_URI . RDF_NIL .'>';
	    $rdfFirst = '<'. RDF_NAMESPACE_URI . RDF_FIRST .'>';
	    $rdfRest = '<'. RDF_NAMESPACE_URI . RDF_REST .'>';

	    // local copy of list without "(" and ")"
	    $t_list = array_slice($list[$i], 1, -1);

	    //prepare bnodes
	    $fromBnode = $this->bnodeID();
	    $toBnode = $this->bnodeID();

	    //link first bnode into graph
	    $list[$i] = $fromBnode;

	    $count = count($t_list);

	    //loop through list, convert to RDF linked list
	    for ($idx = 0; $idx < $count; $idx++){

	        // set rdf:first
	        $list[] = $fromBnode;
	        $list[] = $rdfFirst;
	        $list[] = $t_list[$idx];
	        $list[] = '.';

	        // set rdf:rest (nil or next bnode)
	        if ($idx == $count - 1) {
	            $list[] = $fromBnode;
	            $list[] = $rdfRest;
	            $list[] = $rdfNil;
	            $list[] = '.';
	        }
	        else {
	            $list[] = $fromBnode;
	            $list[] = $rdfRest;
	            $list[] = $toBnode;
	            $list[] = '.';

	            $fromBnode = $toBnode;
	            $toBnode = $this->bnodeID();
	        }
	    }
	}
	else {
	    die('Only [ ] and () lists are supported!');
	}
    }


    }
    return $list;
  }

  /**
   * Main work-horse function. This converts a N3 string to a list of statements
   * @param string $s
   * @returns array
   * @access private
   **/
  function n3tolist($s) {

    //   """Convert an N3 string into a list of triples as strings."""
    $result = array();

   $t = $this->filterWs($this->toke($s)); # tokenize the stream, and filter whitespace tokens

    if ($this->debug) {
      print "Filter WS:\n";
      var_dump($t);
    }
    $r=$this->getPrefixes($t); # get the prefix directives, and add to a dict
    $prefixes=$r[0];
    $t=$r[1];
    if ($this->debug) {
      print "Prefixes:\n";
      var_dump($prefixes);
      print "***\n";
      var_dump($t);
    }
    $t=$this->applyStuff($prefixes, $t);#apply prefixes, keywords, and string formatting
    if ($this->debug) {
      print "Stuff applied:\n";
      var_dump($t);
    }

    $t=$this->fixAnon($t); # fix _:a anons
    if ($this->debug) {
      print "Fix anon:\n";
      var_dump($t);
    }

    $t = $this->listStuff($t); # apply list stuff: todo
    if ($this->debug) {
      print "Lists done:\n";
      var_dump($t);
    }
    $t=$this->expandLists($t);
    if ($this->debug) {
      print "Lists applied:\n";
      var_dump($t);
    }
    $t = $this->getStatements($t); # get all of the "statements" from the stream

    foreach ($t as $stat) {
      $stats = $this->statementize($stat);

      foreach ($stats as $y) {
        $result[]=$y;
      }
    }

    //   for x in [statementize(stat) for stat in t] {
    //      for y in x: result.append(y)
    return $result;
  }



    /**
     * Constructs a RAP RDFNode from URI/Literal/Bnode
     * @access private
     * @param string $s
     * @returns object RDFNode
     **/
    function toRDFNode($s, $state)
    {
        $ins = substr($s, 1, -1);
        if ($s{0} == '"' || $s{0} == '\'') {
            $lang = NULL;

            if (count($state)>3) {
                for ($i = 3; $i < count($state); $i++) {
                    if ($state[$i][0]=='@') {
                        $lang = substr($state[3], 1);
                    }
                    if (substr($state[$i],0,2) == '^^') {
                        $dtype = substr($state[$i],2);
                        if ($dtype[0]=='<') {
                            $dtype = substr($dtype,1,-1);
                        }
                    }
                }
            }


            if (UNIC_RDF) {
                $ins = $this->str2unicode_nfc($ins);
            }
            $new_Literal = new Literal($ins, $lang);
            if (isset($dtype)) {
                $new_Literal->setDatatype($dtype);
            }
            return  $new_Literal;
        } else if (is_int($s)) {
            $value = new Literal($s);
            $value->setDatatype(XML_SCHEMA . 'integer');
            return $value;
        } else if (is_float($s)) {
            $value = new Literal($s);
            $value->setDatatype(XML_SCHEMA . 'double');
            return $value;
        } else if ($s == '@true') {
            $value = new Literal(true);
            $value->setDatatype(XML_SCHEMA . 'boolean');
            return $value;
        } else if ($s == '@false') {
            $value = new Literal(false);
            $value->setDatatype(XML_SCHEMA . 'boolean');
            return $value;
        }

        if (strstr($s, '_' . BNODE_PREFIX)) {
            if (($this->FixBnodes) || (!array_search($s,$this->bNodeMap))) {
                return new BlankNode($ins);
            } else {
                return new BlankNode(
                    trim(
                        substr(
                            array_search($s, $this->bNodeMap),
                            2
                        )
                    )
                );
            };
        }

        return new Resource($ins);
    }//function toRDFNode($s, $state)




} //end: N3Parser

?>
