<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Util
// ----------------------------------------------------------------------------------
/**
 * Useful utility methods.
 * Static class.
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>, Daniel Westphal <dawe@gmx.de>
 * @package util
 * @access public
 */
class RDF_Util extends RDF_Object
{
    /**
     * Extracts the namespace prefix out of a URI.
     *
     * @param String $uri
     * @return string
     * @access public
     */
    function guessNamespace($uri)
    {
        $l = RDF_Util::getNamespaceEnd($uri);
        return $l > 1 ? substr($uri , 0, $l) : "";
    }

    /**
     * Delivers the name out of the URI (without the namespace prefix).
     *
     * @param String $uri
     * @return string
     * @access public
     */
    function guessName($uri)
    {
        return substr($uri, RDF_Util::getNamespaceEnd($uri));
    }

    /**
     * Extracts the namespace prefix out of the URI of a Resource.
     *
     * @param Object Resource $resource
     * @return string
     * @access public
     */
    function getNamespace($resource)
    {
        return RDF_Util::guessNamespace($resource->getURI());
    }

    /**
     * Delivers the Localname (without the namespace prefix) out of the URI of a Resource.
     *
     * @param Object Resource $resource
     * @return string
     * @access public
     */
    function getLocalName($resource)
    {
        return RDF_Util::guessName($resource->getURI());
    }

    /**
     * Position of the namespace end
     * Method looks for # : and /
     *
     * @param String $uri
     * @access private
     */
    function getNamespaceEnd($uri)
    {
        $l = strlen($uri)-1;
        do {
            $c = substr($uri, $l, 1);
            if ($c == '#' || $c == ':' || $c == '/') {
                break;
            }
            $l--;
        } while ($l >= 0);
        $l++;
        return $l;
    }

    /**
     * Tests if the URI of a resource belongs to the RDF syntax/model namespace.
     *
     * @param Object Resource $resource
     * @return boolean
     * @access public
     */
    function isRDF($resource)
    {
        return ($resource != null && RDF_Util::getNamespace($resource) == RDF_NAMESPACE_URI);
    }

    /**
     * Escapes < > and &
     *
     * @param String $textValue
     * @return String
     * @access public
     */
    function escapeValue($textValue)
    {
        $textValue = str_replace('<', '&lt;', $textValue);
        $textValue = str_replace('>', '&gt;', $textValue);
        $textValue = str_replace('&', '&amp;', $textValue);

        return $textValue;
    }

    /**
     * Converts an ordinal RDF resource to an integer.
     * e.g. Resource(RDF:_1) => 1
     *
     * @param object Resource $resource
     * @return Integer
     * @access public
     */
    function getOrd($resource)
    {
        if ($resource == null || !is_a($resource, 'RDF_Resource')
            || !RDF_Util::isRDF($resource)
        ) {
            return -1;
        }
        $name = RDF_Util::getLocalName($resource);
        echo substr($name, 1) . ' ' . RDF_Util::getLocalName($resource);
        $n = substr($name, 1);
        // noch rein : checken ob $n Nummer ist !!!!!!!!!!!!!!!!!!!!!!if ($n)
        return $n;
        return -1;
    }

    /**
     * Creates ordinal RDF resource out of an integer.
     *
     * @param Integer $num
     * @return object Resource
     * @access public
     */
    function createOrd($num)
    {
        return RDF_Resource::factory(RDF_NAMESPACE_URI . '_' . $num);
    }

    /**
     * Prints a Model_Memory as HTML table.
     * You can change the colors in the configuration file.
     *
     * @param object Model_Memory     &$model
     * @access public
     */
    function writeHTMLTable(&$model)
    {
        echo '<table border="1" cellpadding="3" cellspacing="0" width="100%">' . RDF_LINEFEED;
        echo RDF_INDENTATION . '<tr bgcolor="' . RDF_HTML_TABLE_HEADER_COLOR . '">' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td td width="68%" colspan="3">';
        echo '<p><b>Base URI:</b> ' . $model->getBaseURI() . '</p></td>' . RDF_LINEFEED;
        echo RDF_INDENTATION . RDF_INDENTATION . '<td width="32%"><p><b>Size:</b> ' . $model->size() . '</p></td>' . RDF_LINEFEED . RDF_INDENTATION . '</tr>';
        echo RDF_INDENTATION . '<tr bgcolor="' . RDF_HTML_TABLE_HEADER_COLOR . '">' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td width="4%"><p align=center><b>No.</b></p></td>' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td width="32%"><p><b>Subject</b></p></td>' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td width="32%"><p><b>Predicate</b></p></td>' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td width="32%"><p><b>Object</b></p></td>' . RDF_LINEFEED . RDF_INDENTATION . '</tr>' . RDF_LINEFEED;

        $i = 1;
        foreach($model->triples as $statement) {
            echo RDF_INDENTATION . '<tr valign="top">' . RDF_LINEFEED . RDF_INDENTATION . RDF_INDENTATION . '<td><p align=center>' . $i . '.</p></td>' . RDF_LINEFEED;
            // subject
            echo RDF_INDENTATION . RDF_INDENTATION . '<td bgcolor="';
            echo RDF_Util::chooseColor($statement->getSubject());
            echo '">';
            echo '<p>' . RDF_Util::getNodeTypeName($statement->getSubject()) . $statement->subj->getLabel() . '</p></td>' . RDF_LINEFEED;
            // predicate
            echo RDF_INDENTATION . RDF_INDENTATION . '<td bgcolor="';
            echo RDF_Util::chooseColor($statement->getPredicate());
            echo '">';
            echo '<p>' . RDF_Util::getNodeTypeName($statement->getPredicate()) . $statement->pred->getLabel() . '</p></td>' . RDF_LINEFEED;
            // object
            echo RDF_INDENTATION . RDF_INDENTATION . '<td bgcolor="';
            echo RDF_Util::chooseColor($statement->getObject());
            echo '">';
            echo '<p>';
            if (is_a($statement->getObject(), 'RDF_Literal')) {
                if ($statement->obj->getLanguage() != null) {
                    $lang = ' <b>(xml:lang="' . $statement->obj->getLanguage() . '") </b> ';
                } else $lang = '';
                if ($statement->obj->getDatatype() != null) {
                    $dtype = ' <b>(rdf:datatype="' . $statement->obj->getDatatype() . '") </b> ';
                } else $dtype = '';
            } else {
                $lang = '';
                $dtype = '';
            }
            echo RDF_Util::getNodeTypeName($statement->getObject())
             . nl2br(htmlspecialchars($statement->obj->getLabel())) . $lang . $dtype;

            echo '</p></td>' . RDF_LINEFEED;
            echo RDF_INDENTATION . '</tr>' . RDF_LINEFEED;
            $i++;
        }
        echo '</table>' . RDF_LINEFEED;
    }

    /**
     * Chooses a node color.
     * Used by RDF_Util::writeHTMLTable()
     *
     * @param object Node   $node
     * @return object Resource
     * @access private
     */
    function chooseColor($node)
    {
        if (is_a($node, 'RDF_BlankNode')) {
            return RDF_HTML_TABLE_BNODE_COLOR;
        } elseif (is_a($node, 'RDF_Literal')) {
            return RDF_HTML_TABLE_LITERAL_COLOR;
        } else {
            if (RDF_Util::getNamespace($node) == RDF_NAMESPACE_URI
                || RDF_Util::getNamespace($node) == RDF_SCHEMA_URI
            ) {
                return RDF_HTML_TABLE_RDF_NS_COLOR;
            }
        }
        return RDF_HTML_TABLE_RESOURCE_COLOR;
    }

    /**
     * Get Node Type.
     * Used by RDF_Util::writeHTMLTable()
     *
     * @param object Node   $node
     * @return object Resource
     * @access private
     */
    function getNodeTypeName($node)
    {
        if (is_a($node, 'RDF_BlankNode')) {
            return 'Blank Node: ';
        } elseif (is_a($node, 'RDF_Literal')) {
            return 'Literal: ';
        } else {
            if (RDF_Util::getNamespace($node) == RDF_NAMESPACE_URI
                || RDF_Util::getNamespace($node) == RDF_SCHEMA_URI
            ) {
                return 'RDF Node: ';
            }
        }
        return 'Resource: ';
    }
} // end: RDF_Util

?>