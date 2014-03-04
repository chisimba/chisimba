<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   XML result renderer for SparqlEngine
*
*   @author Tobias Gauß <tobias.gauss@web.de>
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngine_ResultRenderer_XML implements SparqlEngine_ResultRenderer
{
    /**
    *   Converts the database results into the output format
    *   and returns the result.
    *
    *   @param array $arVartable    Variable table
    *   @param Query $query         SPARQL query object
    *   @param SparqlEngine $engine Sparql Engine to query the database
    *   @return string XML result
    */
    public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)
    {
        $this->query   = $query;
        $this->engine  = $engine;
        $this->dataset = $engine->getDataset();

        if ($arVartable instanceof NamedGraphMem) {
            return $arVartable->writeRdfToString();
        }

        $result = '<sparql xmlns="http://www.w3.org/2005/sparql-results#">';
        $header = '<head>';

        // build header
        if (is_array($arVartable)) {
            $vars = $this->query->getResultVars();
            $header = '<head>';
            foreach ($vars as $value) {
                $header = $header
                        . '<variable name="' . substr($value, 1) . '"/>';
            }
            $header = $header . '</head>';

            // build results
            $solm = $this->query->getSolutionModifier();
            $sel  = $this->query->getResultForm();

            $distinct = 'false';
            if ($sel == 'select distinct') {
                $distinct = 'true';
            }

            $ordered = 'false';
            if ($solm['order by'] != 0) {
                $ordered = 'true';
            }

            $results = '<results ordered="'.$ordered.'" distinct="'.$distinct.'">';
            foreach ($arVartable as $value) {
                $results = $results.'<result>';
                foreach ($value as $varname => $varvalue) {
                    $results = $results
                            . $this->_getBindingString(
                                substr($varname, 1),
                                $varvalue
                            );
                }
                $results = $results . '</result>';
            }
            $results = $results . '</results>';
        } else {
            $results = '</head><boolean>' . $vartable . '</boolean>';
        }

        $result = $result . $header . $results . '</sparql>';
        $result = simplexml_load_string($result);
        return $result->asXML();
    }//public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)



    /**
    * Helper Function for function buildXmlResult($vartable). Generates
    * an xml string for a single variable an their corresponding value.
    *
    * @param  String  $varname The variables name
    * @param  Node    $varvalue The value of the variable
    * @return String  The xml string
    */
    protected function _getBindingString($varname, $varvalue)
    {
        $binding = '<binding name="'.$varname.'">';
        $value = '<unbound/>';

        if ($varvalue instanceof BlankNode) {
            $value = '<bnode>' . $varvalue->getLabel() . '</bnode>';
        } else if ($varvalue instanceof Resource) {
            $value = '<uri>' . $varvalue->getUri() . '</uri>';
        } else if ($varvalue instanceof Literal) {
            $label = htmlspecialchars($varvalue->getLabel());
            $value = '<literal>'.$label.'</literal>';
            if ($varvalue->getDatatype() != null) {
                $value = '<literal datatype="'
                        . $varvalue->getDatatype() . '">'
                        . $label
                        . '</literal>';
            }
            if ($varvalue->getLanguage() != null) {
                $value = '<literal xml:lang="'
                        . $varvalue->getLanguage() . '">'
                        . $label
                        . '</literal>';
            }
        }
        $binding = $binding . $value . '</binding>';

        return $binding;
    }//protected function _getBindingString($varname, $varvalue)

}//class SparqlEngine_ResultRenderer_XML implements SparqlEngine_ResultRenderer

?>