<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';
require_once RDFAPI_INCLUDE_DIR . 'util/RdfUtil.php';

/**
*   XML result renderer for SparqlEngine
*
*   @author Tobias Gauß <tobias.gauss@web.de>
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngine_ResultRenderer_HTML implements SparqlEngine_ResultRenderer
{
    /**
    *   If the result HTML should be wrapped in a div
    *   @var boolean
    */
    protected $bWrap = true;



    /**
    *   Converts the database results into the output format
    *   and returns the result.
    *
    *   @param array $arVartable    Variable table
    *   @param Query $query         SPARQL query object
    *   @param SparqlEngine $engine Sparql Engine to query the database
    *   @return string HTML result
    */
    public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)
    {
        $this->query   = $query;
        $this->engine  = $engine;
        $this->dataset = $engine->getDataset();

        $strCode     = '';

        $strResultForm = $query->getResultForm();
        switch ($strResultForm) {
            case 'select':
            case 'select distinct':
                $strCode = $this->createTableFromRecords($arVartable);
                break;

            case 'construct':
            case 'describe':
                throw new Exception(
                    'Construct and describe are currently not supported by the'
                    . ' HTML renderer'
                );

            case 'count':
            case 'ask':
                $nCount = count($arVartable);

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
    }//public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)



    protected function wrapCode($strCode)
    {
        if (!$this->bWrap) {
            return $strCode;
        }

        return
            '<div class="SparqlEngine_ResultRenderer_HTML_result">' . "\n"
            . $strCode . "\n"
            . "</div>\n";
    }//protected function wrapCode($strCode)



    protected function createTableFromRecords($arVartable)
    {
        if (count($arVartable) == 0) {
            return 'No result rows.';
        }

        $arResult = array();
        foreach ($arVartable as $row) {
            $arResultRow = array();
            foreach ($row as $strVarName => $value) {
                $arResultRow[$strVarName] = $this->createValue($value);
            }
            $arResult[] = $arResultRow;
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
    protected function createValue($value)
    {
        if ($value === null) {
            $strCode = $this->getHtmlNull();
        }
        if ($value instanceof Literal) {
            $strCode = $this->getHtmlLiteral(
                $value->getLabel(),
                $value->getLanguage(),
                $value->getDatatype()
            );
        } else if ($value instanceof Resource) {
            $strCode = $this->getHtmlResource($value->getURI());
        } else {
            $strCode = $this->getHtmlBlank();
        }

        return '<td style="background-color: '
               . RdfUtil::chooseColor($value) . '">'
               . $strCode
               . '</td>';
    }//protected function createObjectFromDbRecordSetPart(ADORecordSet $dbRecordSet, $strVarBase, $strVarName)



    protected function getHtmlNull()
    {
        return '<pre>NULL</pre>';
    }//protected function getHtmlNull()



    protected function getHtmlBlank($value)
    {
        return '<i>Blank node</i>';
    }//protected function getHtmlBlank($value)



    protected function getHtmlResource($value)
    {
        return htmlspecialchars($value);
    }//protected function getHtmlResource($value)



    protected function getHtmlLiteral($value, $language, $datatype)
    {
        $strCode = htmlspecialchars($value);
        if ($language) {
            $strCode .= '<br/>&nbsp;&nbsp;<i>xml:lang</i>=' . $language;
        }
        if ($datatype) {
            $strCode .= '<br/>&nbsp;&nbsp;<i>rdf:type</i>=' . $datatype;
        }
        return $strCode;
    }//protected function getHtmlLiteral($value, $language, $datatype)

}//class SparqlEngine_ResultRenderer_HTML implements SparqlEngine_ResultRenderer

?>