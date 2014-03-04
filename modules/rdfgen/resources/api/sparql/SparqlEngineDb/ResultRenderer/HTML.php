<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   Sparql DB HTML result renderer.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_ResultRenderer_HTML implements SparqlEngineDb_ResultRenderer
{
    /**
    *   If the result HTML should be wrapped in a div
    *   @var boolean
    */
    protected $bWrap = true;

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
                $strCode = $this->createTableFromRecords($arRecordSets);
                break;

            case 'construct':
            case 'describe':
                throw new Exception(
                    'Construct and describe are currently not supported by the'
                    . ' HTML renderer'
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
                    $strCode = 'There were results.';
                } else {
                    $strCode = 'There are ' . $nCount . ' results.';
                }
                break;

            default:
                throw new Exception('Unsupported result form: ' . $strResultForm);
        }

        return $this->wrapCode($strCode);
    }//public function convertFromDbResults($arRecordSets, Query $query, SparqlEngineDb $engine)



    protected function wrapCode($strCode)
    {
        if (!$this->bWrap) {
            return $strCode;
        }

        return
            '<div class="SparqlEngineDb_ResultRenderer_HTML_result">' . "\n"
            . $strCode . "\n"
            . "</div>\n";
    }//protected function wrapCode($strCode)



    protected function createTableFromRecords($arRecordSets)
    {
        $arResultVars = $this->query->getResultVars();

        if (in_array('*', $arResultVars)) {
            $arResultVars   = array_keys($this->sg->arVarAssignments);
        }

        $arResult = array();
        foreach ($arRecordSets as $dbRecordSet) {
            //work around bug in adodb:
            // ADORecordSet_empty does not implement php5 iterators
            if ($dbRecordSet->RowCount() <= 0) {
                return array();
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
                        list($strCode, $strColor) = $this->$strMethod($dbRecordSet, $arVarSettings[0], $strVar);
                        $arResultRow[$strVarName] = '<td style="background-color: '
                            . $strColor . '">' . $strCode . '</td>';
                    }
                }
                $arResult[] = $arResultRow;
            }
        }

        //I always wanted to to this :)
        return
            "<table border='1'>\n"
            . " <caption>SPARQL result with " . count($arResult) . " rows</caption>\n"
            . " <thead><th>"
                . implode('</th><th>', array_keys(reset($arResult)))
            . "</th></thead>\n"
            . " <tbody>\n  <tr>"
            . implode(
                "</tr>\n  <tr>",
                array_map(
                    create_function(
                        '$ar',
                        'return implode("", $ar);'
                    ),
                    $arResult
                )
              )
            . "</tr>\n </tbody>\n"
            . "</table>\n";
    }//protected function createTableFromRecords($arRecordSets)



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
            return $this->getHtmlNull();
        }
        if ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']] == 'r'
            //null should be predicate which is always a resource
         || $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']] === null
        ) {
            return $this->getHtmlResource($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
        } else {
            return $this->getHtmlBlank($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
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
        if ($dbRecordSet->fields[$strVarBase .  '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']] === null) {
            return $this->getHtmlNull();
        }

        return $this->getHtmlResource($dbRecordSet->fields[$strVarBase .  '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
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
            return $this->getHtmlNull();
        }
        switch ($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_is']]) {
            case 'r':
                return $this->getHtmlResource($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
                break;
            case 'b':
                return $this->getHtmlBlank($dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']]);
                break;
            default:
                return $this->getHtmlLiteral(
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_value']],
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_lang']],
                    $dbRecordSet->fields[$strVarBase . '.' . $this->sg->arVarAssignments[$strVarName]['sql_type']]
                );
        }
    }//protected function createObjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVar)



    protected function getHtmlNull()
    {
        return array('<pre>NULL</pre>', '#FFF');
    }//protected function getHtmlNull()



    protected function getHtmlBlank($value)
    {
        return array('<i>Blank node</i>', HTML_TABLE_BNODE_COLOR);
    }//protected function getHtmlBlank($value)



    protected function getHtmlResource($value)
    {
        return array(
            htmlspecialchars($value),
            HTML_TABLE_RESOURCE_COLOR
        );
    }//protected function getHtmlResource($value)



    protected function getHtmlLiteral($value, $language, $datatype)
    {
        $strCode = htmlspecialchars($value);
        if ($language) {
            $strCode . ' <i>xml:lang</i>=' . $language;
        }
        if ($datatype) {
            $strCode . ' <i>rdf:type</i>=' . $datatype;
        }
        return array($strCode, HTML_TABLE_LITERAL_COLOR);
    }//protected function getHtmlLiteral($value, $language, $datatype)


}//class SparqlEngineDb_ResultRenderer_HTML implements SparqlEngineDb_ResultRenderer

?>