<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   Sparql DB XML result renderer as defined by
*   http://www.w3.org/TR/rdf-sparql-XMLres/
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_ResultRenderer_XML implements SparqlEngineDb_ResultRenderer
{

    /**
    *   Defines the methods needed to create the types
    *   in $arVarAssignments.
    *   Key is the type (e.g. "s" for subject), and
    *   value the method's name.
    *
    *   @see $arVarAssignments
    *
    *   @var array
    */
    protected $arCreationMethods = array(
        's' => 'createSubjectFromDbRecordSetPart',
        'p' => 'createPredicateFromDbRecordSetPart',
        'o' => 'createObjectFromDbRecordSetPart'
    );



    /**
    *   Converts the database results into nice HTML.
    *
    *   @param array $arRecordSets  Array of (possibly several) SQL query results.
    *   @param Query $query     SPARQL query object
    *   @param SparqlEngineDb $engine   Sparql Engine to query the database
    *   @return mixed   HTML code
    */
    public function convertFromDbResults($arRecordSets, Query $query, SparqlEngineDb $engine)
    {
        $this->query = $query;
        $this->sg    = $engine->getSqlGenerator();
        $strCode     = '';

        $strResultForm = $query->getResultForm();
        switch ($strResultForm) {
            case 'select':
            case 'select distinct':
                $strCode = $this->createFromRecords($arRecordSets, $strResultForm);
                break;

            case 'construct':
            case 'describe':
                throw new Exception(
                    'Construct and describe are not supported by the'
                    . ' XML renderer'
                );

            case 'count':
            case 'ask':
                if (count($arRecordSets) > 1) {
                    throw new Exception(
                        'More than one result set for a '
                        . $strResultForm . ' query!'
                    );
                }

                $nCount = 0;
                $dbRecordSet = reset($arRecordSets);
                foreach ($dbRecordSet as $row) {
                    $nCount += intval($row['count']);
                    break;
                }

                if ($strResultForm == 'ask') {
                    $strCode = $this->getHead()
                        . '  <boolean>'
                        . self::getSpokenBoolean($nCount > 0)
                        . '</boolean>';
                } else {
                    $strCode = $this->getHead()
                        . '  <int>'
                        . $nCount
                        . '</int>';
                }
                break;

            default:
                throw new Exception('Unsupported result form: ' . $strResultForm);
        }

        return $this->wrapCode($strCode);
    }//public function convertFromDbResults($arRecordSets, Query $query, SparqlEngineDb $engine)



    protected function wrapCode($strCode)
    {
        return <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">

EOT
            . $strCode . "\n"
            . "</sparql>\n";
    }//protected function wrapCode($strCode)



    protected function getHead($strXml = '')
    {
        return "  <head>\n"
            . $strXml
            . "  </head>\n";
    }//protected function getHead($strXml = '')



    protected function createFromRecords($arRecordSets, $strResultForm)
    {
        $arResultVars = $this->query->getResultVars();

        if (in_array('*', $arResultVars)) {
            $arResultVars   = array_keys($this->sg->arVarAssignments);
        }
		
        $strVarXML = '';
        foreach ($arResultVars as $var) {
        	$strVarXML .= "    <variable name=\"" . substr((string)$var,1) . "\"/>\n";
        }
        
        $strHeadXml = $this->getHead($strVarXML);

        $arResult = array();
        foreach ($arRecordSets as $dbRecordSet) {
            //work around bug in adodb:
            // ADORecordSet_empty does not implement php5 iterators
            if ($dbRecordSet->RowCount() <= 0) {
                
            	return 
            		$strHeadXml
            		. '  <results ordered="'
                	. self::getSpokenBoolean($arSM['order by'] !== null)
					. '" distinct="'
                	. self::getSpokenBoolean($strResultForm == 'select distinct')
                	. '">' . "\n"
            		. '    <!-- empty result -->' . PHP_EOL 
            		. "  </results>\n";
            }

            foreach ($dbRecordSet as $row) {
                $arResultRow = array();
                foreach ($arResultVars as $strVar) {
                    $strVarName = (string)$strVar;
                    if (!isset($this->sg->arVarAssignments[$strVarName])) {
                        //variable is in select, but not in result (test: q-select-2)
                        $arResultRow[$strVarName] = '';
                    } else {
                        $arVarSettings  = $this->sg->arVarAssignments[$strVarName];
                        $strMethod      = $this->arCreationMethods[$arVarSettings[1]];
                        $arResultRow[$strVarName] = $this->$strMethod($dbRecordSet, $arVarSettings[0], $strVar);
                    }
                }
                $arResult[] = $arResultRow;
            }
        }


        $arSM = $this->query->getSolutionModifier();

        return
            $strHeadXml
            . '  <results ordered="'
                . self::getSpokenBoolean($arSM['order by'] !== null)
                . '" distinct="'
                . self::getSpokenBoolean($strResultForm == 'select distinct')
                . '">' . "\n"
            . $this->getResultXml($arResult)
            . "  </results>\n";
    }//protected function createFromRecords($arRecordSets)



    protected function getResultXml($arResult)
    {
        $strCode = '';
        foreach ($arResult as $arSet) {
            $strCode .= "    <result>\n";
            foreach ($arSet as $strVarName => $strValue) {
                if ($strValue !== null) {
                    $strCode .= '      <binding name="' . substr($strVarName,1) . '">'
                        . $strValue
                        . "</binding>\n";
                }
            }
            $strCode .= "    </result>\n";
        }
        return $strCode;
    }//protected function getResultXml($arResult)



    protected static function getSpokenBoolean($b)
    {
        return $b ? 'true' : 'false';
    }//protected static function getSpokenBoolean($b)



    /**
    *   Creates an RDF subject object
    *   contained in the given $dbRecordSet object.
    *
    *   @see convertFromDbResult() to understand $strVarBase necessity
    *
    *   @param ADORecordSet $dbRecordSet    Record set returned from ADOConnection::Execute()
    *   @param string       $strVarBase     Prefix of the columns the recordset fields have.
    *
    *   @return string HTML code
    */
    protected function createSubjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)
    {
        $strVarName = (string)$strVar;
        if ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']] === null) {
            return $this->getXmlNull();
        }
        if ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']] == 'r'
            //null should be predicate which is always a resource
         || $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']] === null
        ) {
            return $this->getXmlResource($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
        } else {
            return $this->getXmlBlank($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
        }
    }//protected function createSubjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)



    /**
    *   Creates an RDF predicate object
    *   contained in the given $dbRecordSet object.
    *
    *   @see convertFromDbResult() to understand $strVarBase necessity
    *
    *   @param ADORecordSet $dbRecordSet    Record set returned from ADOConnection::Execute()
    *   @param string       $strVarBase     Prefix of the columns the recordset fields have.
    *
    *   @return string HTML code
    */
    protected function createPredicateFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)
    {
        $strVarName = (string)$strVar;
        if ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']] === null) {
            return $this->getXmlNull();
        }

        return $this->getXmlResource($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
    }//protected function createPredicateFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)



    /**
    *   Creates an RDF object object
    *   contained in the given $dbRecordSet object.
    *
    *   @see convertFromDbResult() to understand $strVarBase necessity
    *
    *   @param ADORecordSet $dbRecordSet    Record set returned from ADOConnection::Execute()
    *   @param string       $strVarBase     Prefix of the columns the recordset fields have.
    *
    *   @return string HTML code
    */
    protected function createObjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)
    {
        $strVarName = (string)$strVar;
        if ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']] === null) {
            return $this->getXmlNull();
        }
        switch ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']]) {
            case 'r':
                return $this->getXmlResource($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
                break;
            case 'b':
                return $this->getXmlBlank($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
                break;
            default:
                return $this->getXmlLiteral(
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']],
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_lang']],
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_type']]
                );
        }
    }//protected function createObjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)



    protected function getXmlNull()
    {
        return null;
    }//protected function getHtmlNull()



    protected function getXmlBlank($value)
    {
        return '<bnode>' . $value . '</bnode>';
    }//protected function getHtmlBlank($value)



    protected function getXmlResource($value)
    {
        return '<uri>' . htmlspecialchars($value) . '</uri>';
    }//protected function getHtmlResource($value)



    protected function getXmlLiteral($value, $language, $datatype)
    {
        $strCode = '<literal';
        if ($language) {
            $strCode . ' xml:lang="' . $language . '"';
        }
        if ($datatype) {
            $strCode . ' datatype="' . $datatype . '"';
        }
        $strCode .= '>' . htmlspecialchars($value) . '</literal>';
        return $strCode;
    }//protected function getHtmlLiteral($value, $language, $datatype)

}//class SparqlEngineDb_ResultRenderer_XML implements SparqlEngineDb_ResultRenderer

?>