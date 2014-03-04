<?php

// ----------------------------------------------------------------------------------
// Class: RDFUtil
// ----------------------------------------------------------------------------------

/**
* Useful utility methods.
* Static class.
*
* @version  $Id: RdfUtil.php 295 2006-06-23 06:45:53Z tgauss $
* @author Chris Bizer <chris@bizer.de>, Daniel Westphal <dawe@gmx.de>
* @author   Anton Köstlbacher <anton1@koestlbacher.de>
* @package utility
* @access	public
**/
class RDFUtil extends Object {

	/**
	* Extracts the namespace prefix out of a URI.
	*
	* @param	String	$uri
	* @return	string
	* @access	public
	*/
	function guessNamespace($uri) {
		$l = RDFUtil::getNamespaceEnd($uri);
		return $l > 1 ? substr($uri ,0, $l) : "";
	}

	/**
	* Delivers the name out of the URI (without the namespace prefix).
	*
	* @param	String	$uri
	* @return	string
	* @access	public
	*/
	function guessName($uri) {
		return substr($uri,RDFUtil::getNamespaceEnd($uri));
	}

	/**
	* Extracts the namespace prefix out of the URI of a Resource.
	*
	* @param	Object Resource	$resource
	* @return	string
	* @access	public
	*/
	function getNamespace($resource) {
		return RDFUtil::guessNamespace($resource->getURI());
	}

	/**
	* Delivers the Localname (without the namespace prefix) out of the URI of a Resource.
	*
	* @param	Object Resource	$resource
	* @return	string
	* @access	public
	*/
	function getLocalName($resource) {
		return RDFUtil::guessName($resource->getURI());
	}

	/**
	* Position of the namespace end
	* Method looks for # : and /
	* @param	String	$uri
	* @access	private
	*/
	function getNamespaceEnd($uri) {
		$l = strlen($uri)-1;
		do {
			$c = substr($uri, $l, 1);
			if($c == '#' || $c == ':' || $c == '/')
			break;
			$l--;
		} while ($l >= 0);
		$l++;
		return $l;
	}

		/**
	* Short Prefix for known Namespaces by given URI
	* @param	String	$uri
	* @access	public
	*/
	function getPrefix($uri) {
            switch (RDFUtil::guessNamespace($uri))
	    {
                case RDF_NAMESPACE_URI:
                $prefix = RDF_NAMESPACE_PREFIX;
                break;

                case RDF_SCHEMA_URI:
                $short_p = RDF_SCHEMA_PREFIX;
                break;

                case OWL_URI:
                $short_p = OWL_PREFIX;
                break;

                default:
                $short_p = $statement->getLabelPredicate();
            }
            return $short_p;
	}


	/**
	* Tests if the URI of a resource belongs to the RDF syntax/model namespace.
	*
	* @param	Object Resource	$resource
	* @return	boolean
	* @access	public
	*/
	function isRDF($resource) {
		return ($resource != NULL && RDFUtil::getNamespace($resource) == RDF_NAMESPACE_URI);
	}

	/**
	* Escapes < > and &
	*
	* @param	String	$textValue
	* @return	String
	* @access	public
	*/
	function escapeValue($textValue) {

		$textValue = str_replace('<', '&lt;', $textValue);
		$textValue = str_replace('>', '&gt;', $textValue);
		$textValue = str_replace('&', '&amp;', $textValue);

		return $textValue;
	}

	/**
	* Converts an ordinal RDF resource to an integer.
	* e.g. Resource(RDF:_1) => 1
	*
	* @param	object Resource	$resource
	* @return	Integer
	* @access	public
	*/
	function getOrd($resource)  {
		if($resource == NULL || !is_a($resource, 'Resource') || !RDFUtil::isRDF($resource))
		return -1;
		$name = RDFUtil::getLocalName($resource);
		echo substr($name, 1).' '.RDFUtil::getLocalName($resource);
		$n = substr($name, 1);
		//noch rein : chekcen ob $n Nummer ist !!!!!!!!!!!!!!!!!!!!!!if($n)
		return $n;
		return -1;
	}

	/**
	* Creates ordinal RDF resource out of an integer.
	*
	* @param	Integer	$num
	* @return	object Resource
	* @access	public
	*/
	function createOrd($num)  {
		return new Resource(RDF_NAMESPACE_URI . '_' . $num);
	}

	/**
	* Prints a MemModel as HTML table.
	* You can change the colors in the configuration file.
	*
	* @param	object MemModel 	&$model
	* @access	public
	*/
	function writeHTMLTable(&$model)  {
		$nms = $model->getParsedNamespaces();
		$names = '';
		$pre = '';


		echo '<table border="1" cellpadding="3" cellspacing="0" width="100%">' . LINEFEED;
		echo INDENTATION . '<tr bgcolor="' . HTML_TABLE_HEADER_COLOR . '">' . LINEFEED . INDENTATION . INDENTATION . '<td td width="68%" colspan="3">';
		echo '<p><b>Base URI:</b> ' . $model->getBaseURI() . '</p></td>' . LINEFEED;
		echo INDENTATION . INDENTATION . '<td width="32%"><p><b>Size:</b> ' . $model->size() . '</p></td>' . LINEFEED . INDENTATION . '</tr>';

		echo '<tr><td><b>Prefix:</b>'.'<br/></td><td colspan="3"><b>Namespace:</b>'.'<br/></td></tr>';
		$i=0;
		if($nms != false){
			foreach($nms as $namespace => $prefix){
				if($i==0){
					$col = HTML_TABLE_NS_ROW_COLOR0;
				}else{
					$col = HTML_TABLE_NS_ROW_COLOR1;
				}
				echo '<tr bgcolor="'.$col.'"><td>'.$prefix.'</td><td colspan="3">'.$namespace.'</td></tr>';
				$i++;
				$i%=2;
			}
		}else{
			echo '<tr><td>-</td><td colspan="3">-</td></tr>';
		}




		echo INDENTATION . '<tr bgcolor="' . HTML_TABLE_HEADER_COLOR . '">' . LINEFEED . INDENTATION . INDENTATION . '<td width="4%"><p align=center><b>No.</b></p></td>' . LINEFEED . INDENTATION . INDENTATION . '<td width="32%"><p><b>Subject</b></p></td>' . LINEFEED . INDENTATION . INDENTATION . '<td width="32%"><p><b>Predicate</b></p></td>' . LINEFEED . INDENTATION . INDENTATION . '<td width="32%"><p><b>Object</b></p></td>' . LINEFEED . INDENTATION . '</tr>' . LINEFEED;

		$i = 1;
		foreach($model->triples as $key => $statement) {
			$infered='';
			if (is_a($statement,'InfStatement')) $infered='<small>(infered)</small>';
			echo INDENTATION . '<tr valign="top">' . LINEFEED . INDENTATION . INDENTATION . '<td><p align=center>' . $i . '.<BR>'.$infered.'</p></td>' . LINEFEED;
			// subject
			echo INDENTATION . INDENTATION . '<td bgcolor="';
			echo RDFUtil::chooseColor($statement->getSubject());
			echo '">';
			echo '<p>' .  RDFUtil::getNodeTypeName($statement->getSubject());
			if(is_a($statement->subj,'Resource')){
				$ns = $statement->subj->getNamespace();
				if(isset($nms[$ns])){
					echo $nms[$ns].':'.RDFUtil::getLocalName($statement->subj);
				}else{
					echo $statement->subj->getLabel();
				}
			}
			echo '</p></td>' .  LINEFEED;
			// predicate
			echo INDENTATION . INDENTATION . '<td bgcolor="';
			echo RDFUtil::chooseColor($statement->getPredicate());
			echo '">';
			echo '<p>' . RDFUtil::getNodeTypeName($statement->getPredicate());
			if(is_a($statement->pred,'Resource')){
				$ns = $statement->pred->getNamespace();
				if(isset($nms[$ns])){
					echo $nms[$ns].':'.RDFUtil::getLocalName($statement->pred);
				}else{
					echo $statement->pred->getLabel();
				}
			}
			echo '</p></td>' .  LINEFEED;
			// object
			echo INDENTATION . INDENTATION . '<td bgcolor="';
			echo RDFUtil::chooseColor($statement->getObject());
			echo '">';
			echo '<p>';
			if (is_a($statement->getObject(), 'Literal')) {
				if ($statement->obj->getLanguage() != null) {
					$lang = ' <b>(xml:lang="' . $statement->obj->getLanguage() . '") </b> ';
				} ELSE $lang = '';
				if ($statement->obj->getDatatype() != null) {
					$dtype = ' <b>(rdf:datatype="' . $statement->obj->getDatatype() . '") </b> ';
				} ELSE $dtype = '';
			} else {
				$lang = '';
				$dtype = '';
			}
			$label = $statement->obj->getLabel();
			if(is_a($statement->obj,'Resource')){
				$ns = $statement->obj->getNamespace();
				if(isset($nms[$ns])){
					$label = $nms[$ns].':'.RDFUtil::getLocalName($statement->obj);
				}else{
					$label = $statement->obj->getLabel();
				}
			}

			echo  RDFUtil::getNodeTypeName($statement->getObject())
			.nl2br(htmlspecialchars($label)) . $lang . $dtype;

			echo '</p></td>' . LINEFEED;
			echo INDENTATION . '</tr>' . LINEFEED;
			$i++;
		}
		echo '</table>' . LINEFEED;
	}

	/**
	* Chooses a node color.
	* Used by RDFUtil::writeHTMLTable()
	*
	* @param	object Node	$node
	* @return	object Resource
	* @access	private
	*/
	function chooseColor($node)  {
		if (is_a($node, 'BlankNode'))
		return HTML_TABLE_BNODE_COLOR;
		elseif (is_a($node, 'Literal'))
		return HTML_TABLE_LITERAL_COLOR;
		else {
			if (RDFUtil::getNamespace($node) == RDF_NAMESPACE_URI ||
			RDFUtil::getNamespace($node) == RDF_SCHEMA_URI ||
			RDFUtil::getNamespace($node) == OWL_URI
			)

			return HTML_TABLE_RDF_NS_COLOR;
		}
		return HTML_TABLE_RESOURCE_COLOR;

	}

	/**
	* Get Node Type.
	* Used by RDFUtil::writeHTMLTable()
	*
	* @param	object Node	$node
	* @return	object Resource
	* @access	private
	*/
	function getNodeTypeName($node)  {
		if (is_a($node, "BlankNode"))
		return 'Blank Node: ';
		elseif (is_a($node, 'Literal'))
		return 'Literal: ';
		else {
			if (RDFUtil::getNamespace($node) == RDF_NAMESPACE_URI ||
			RDFUtil::getNamespace($node) == RDF_SCHEMA_URI ||
			RDFUtil::getNamespace($node) == OWL_URI)
			return 'RDF Node: ';
		}
		return 'Resource: ';

	}


 /**
 * Short Prefix for known and/or parsed Namespaces by given URI and Model
 * Uses $default_prefixes defined in constants.php and getParsedNamespaces()
 * Returns FALSE if no matching prefix is found
 *
 * @author   Anton Köstlbacher <anton1@koestlbacher.de>
 * @param    string  $uri
 * @param    object $model
 * @return   string, boolean
 * @access   public
 * @throws   PhpError
 */

 function guessPrefix($uri, &$model)
 {
     global $default_prefixes;
     $namespace = RDFUtil::guessNamespace($uri);
     $par_nms   = $model->getParsedNamespaces();
     if (isset($par_nms[$namespace]))
     {
         $prefix = $par_nms[$namespace];
     }
     else
     {
         $prefix = array_search($namespace, $default_prefixes);
     }
     if($prefix !== false)
     {
        return $prefix;
     }
     else
     {
        return false;
     }
 }


 /**
 * Generates a dot-file for drawing graphical output with the
 * graphviz-application which can be downloaded at http://www.graphviz.org
 * If the graphviz-application is installed and its path is set to the
 * correct value in constants.php we can directly generate any
 * file format graphviz supports, e.g. SVG, PNG
 * Parameters: model to visualize, output format, use prefixes
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * WARNING: Graphviz can be slow with large models.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Anton Köstlbacher <anton1@koestlbacher.de>
 * @param    object  Model
 * @param    string  $format
 * @param    boolean $short_prefix
 * @return   string, binary
 * @access   public
 * @throws   PhpError
 */

 function visualizeGraph(&$model, $format = "input_dot", $short_prefix = TRUE)
 {
     global $graphviz_param;
     $i = 0;

     foreach ($model->triples as $key => $statement)
     {
         $subject   = $statement->getLabelSubject();
         $predicate = $statement->getLabelPredicate();
         $object    = $statement->getLabelObject();

         // format subject
         if (!isset($attrib[$subject]))
         {
             if (is_a($statement->subject(),'BlankNode'))
             {
                 $attrib[$subject] = $graphviz_param['BLANKNODE_STYLE'];
             }
             else
             {
                 if ($short_prefix == TRUE && RDFUtil::guessPrefix($subject, $model) != FALSE)
                 {
                     $prefix = RDFUtil::guessPrefix($subject, $model);
                     $subject_label = $prefix.":".RDFUtil::guessName($subject);
                     $attrib[$subject] = "label=\"".$subject_label."\" ";
                     if(!isset($prefix_array[$prefix]))
                     {
                         $prefix_array[$prefix] = RDFUtil::guessNamespace($subject);
                     }
                 }
                 if (GRAPHVIZ_URI == TRUE)
                 {
                     $attrib[$subject] .= "URL=\"".$subject."\"";
                 }
             }
         }

         // format predicate
         if ($short_prefix == TRUE && RDFUtil::guessPrefix($predicate, $model) != FALSE)
         {
             $prefix = RDFUtil::guessPrefix($predicate, $model);
             $predicate_label = "label=\"".$prefix.":".RDFUtil::guessName($predicate)."\"";
             if(!isset($prefix_array[$prefix]))
             {
                 $prefix_array[$prefix] = RDFUtil::guessNamespace($predicate);
             }
         }
         else
         {
             $predicate_label = "label=\"".$predicate."\"";
         }

	if (is_a($statement,'InfStatement'))
         {
             $predicate_label .= " ".$graphviz_param['INFERRED_STYLE'];
         }
         else
         {
             if (GRAPHVIZ_URI == TRUE)
             {
                 $predicate_label .= "URL=\"".$predicate."\"";
             }
         }

         // format object
         if (!isset($attrib[$object]))
         {
             if (is_a($statement->object(),'BlankNode'))
             {
                 $attrib[$object] = $graphviz_param['BLANKNODE_STYLE'];
             }
             elseif (is_a($statement->object(),'Literal'))
             {
                 $object_label = $object;
                 $object = "literal".$i;
                 $i++;
                 $attrib[$object] = "label=\"$object_label\" ".$graphviz_param['LITERAL_STYLE'];
             }
             else
             {
                 if ($short_prefix == TRUE && RDFUtil::guessPrefix($object, $model) != FALSE)
                 {
                     $prefix = RDFUtil::guessPrefix($object, $model);
                     $object_label = $prefix.":".RDFUtil::guessName($object);
                     $attrib[$object] = "label=\"".$object_label."\" ";
                     if(!isset($prefix_array[$prefix]))
                     {
                         $prefix_array[$prefix] = RDFUtil::guessNamespace($object);
                     }
                 }
                 if (GRAPHVIZ_URI == TRUE)
                 {
                     $attrib[$object] .= "URL=\"".$object."\"";
                 }
             }
         }

         // fill graph array
         $graph[] = "\"".$subject."\" -> \"".$object."\" [".$predicate_label."];";
     }

     //generate DOT-file
     $dot = "digraph G { ".$graphviz_param['GRAPH_STYLE']."\n edge [".$graphviz_param['PREDICATE_STYLE']."]\n node [".$graphviz_param['RESOURCE_STYLE']."]\n";
     if (isset($attrib))
     {
         foreach ($attrib AS $key => $value)
         {
             $dot .= "\"$key\" [$value];\n";
         }
     }
     if (!isset($graph))
     {
         $dot .= "error [shape=box,label=\"No Statements found!\"]";
     }
     else
     {
         $dot .= implode("\n", $graph);
     }


     if (GRAPHVIZ_STAT == TRUE)
     {
     	$stat_label = "| ".$model->size()." Statements drawn";
     }
     if ((strstr($graphviz_param['GRAPH_STYLE'], 'rankdir="LR"') === FALSE) && (strstr($graphviz_param['GRAPH_STYLE'], 'rankdir=LR') === FALSE))
     {
         $sep1 = "}";
         $sep2 = "";
     }
     else
     {
         $sep1 = "";
         $sep2 = "}";
     }

     if ($short_prefix == TRUE && isset($prefix_array))
     {
         $struct_label = "{ { Base URI: ".$model->getBaseURI()." $sep1 | { { ".implode("|", array_keys($prefix_array))." } | { ".implode("|", $prefix_array)." } } $stat_label } $sep2";
     }
     else
     {
         $struct_label = "{ { Base URI: ".$model->getBaseURI()."$sep1 ".$stat_label." } }";
     }

     $dot .= "\n struct [shape=Mrecord,label=\"$struct_label\",".$graphviz_param['BOX_STYLE']."];\n";
     $dot .= " vocabulary [style=invis];\n struct -> vocabulary [style=invis];\n}";

     // if needed call dot.exe
     if (($format != "input_dot") && (defined('GRAPHVIZ_PATH')) && (strstr(GRAPHVIZ_FORMAT, $format) !== FALSE))
     {
         mt_srand((double)microtime()*1000000);
         $filename=GRAPHVIZ_TEMP.md5(uniqid(mt_rand())).".dot";
         $file_handle = @fopen($filename, 'w');
         if ($file_handle)
         {
             fwrite($file_handle, $dot);
             fclose($file_handle);
         }
         $dotinput = " -T".$format." ".$filename;

         ob_start();
	passthru(GRAPHVIZ_PATH.$dotinput);
         $output = ob_get_contents();
	ob_end_clean();
         unlink($filename);
         echo $output;
         return TRUE;
     }
     elseif ($format == "input_dot")
     {
         echo $dot;
         return TRUE;
     }
     else
     {
         return FALSE;
     }
 }

} // end: RDfUtil

?>