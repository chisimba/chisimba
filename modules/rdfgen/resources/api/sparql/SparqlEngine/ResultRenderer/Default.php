<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   Default result renderer for SparqlEngine
*
*   @author Tobias Gauß <tobias.gauss@web.de>
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngine_ResultRenderer_Default implements SparqlEngine_ResultRenderer
{
    /**
    *   Converts the database results into the output format
    *   and returns the result.
    *
    *   @param array $arVartable    Variable table
    *   @param Query $query         SPARQL query object
    *   @param SparqlEngine $engine Sparql Engine to query the database
    *   @return mixed               Most likely an array
    */
    public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)
    {
        $this->query   = $query;
        $this->engine  = $engine;
        $this->dataset = $engine->getDataset();

        $result = false;
        $qrf    = $this->query->getResultForm();

        if ($arVartable != null) {
            switch ($qrf) {
                case 'ask':
                    if (count($arVartable) > 0) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                    break;
                case 'count':
                    $result = count($arVartable);
                    break;
                case 'construct':
                    $result = $this->constructGraph(
                        $arVartable,
                        $this->query->getConstructPattern()
                    );
                    break;
                case 'describe':
                    $result = $this->describeGraph($arVartable);
                    break;
                default:
                    $result = $arVartable;
                    break;
            }
        } else if ($qrf == 'describe'){
            $result = $this->describeGraph(null);
        } else if ($qrf == 'construct'){
            $result = $this->constructGraph(
                false,
                $this->query->getConstructPattern()
            );
        }

        return $result;
    }//public function convertFromResult($arVartable, Query $query, SparqlEngine $engine)



    /**
    * Constructs a result graph.
    *
    * @param  array         $arVartable       A table containing the result vars and their bindings
    * @param  GraphPattern  $constructPattern The CONSTRUCT pattern
    * @return MemModel      The result graph which matches the CONSTRUCT pattern
    */
    function constructGraph($arVartable, $constructPattern)
    {
        $resultGraph = new MemModel();

        if (!$arVartable) {
            return $resultGraph;
        }

        $tp = $constructPattern->getTriplePatterns();

        $bnode = 0;
        foreach ($arVartable as $value) {
            foreach ($tp as $triple) {
                $sub  = $triple->getSubject();
                $pred = $triple->getPredicate();
                $obj  = $triple->getObject();

                if (is_string($sub)  && $sub{1} == '_') {
                    $sub  = new BlankNode("_bN".$bnode);
                }
                if (is_string($pred) && $pred{1} == '_') {
                    $pred = new BlankNode("_bN".$bnode);
                }
                if (is_string($obj)  && $obj{1} == '_') {
                    $obj  = new BlankNode("_bN".$bnode);
                }


                if (is_string($sub)) {
                    $sub  = $value[$sub];
                }
                if (is_string($pred)) {
                    $pred = $value[$pred];
                }
                if (is_string($obj)) {
                    $obj  = $value[$obj];
                }

                if ($sub !== "" && $pred !== "" && $obj !== "") {
                    $resultGraph->add(new Statement($sub,$pred,$obj));
                }
            }
            $bnode++;
        }
        return $resultGraph;
    }//function constructGraph($arVartable, $constructPattern)



    /**
    * Builds a describing named graph. To define an attribute list for a
    * several rdf:type look at constants.php
    *
    * @param  array      $arVartable
    * @return MemModel
    */
    function describeGraph($arVartable)
    {
        // build empty named graph
        $resultGraph = new MemModel();
        // if no where clause fill $arVartable
        $vars = $this->query->getResultVars();
        if ($arVartable == null) {
            if ($vars) {
                $arVartable[0] = array('?x' => new Resource(substr($vars[0],1,-1)));
                $vars[0] = '?x';
            }
        }
        // fetch attribute list from constants.php
        global $sparql_describe;
        // for each resultset
        foreach ($arVartable as $resultset) {
            foreach ($vars as $varname) {
                $varvalue = $resultset[$varname];
                // try to determine rdf:type of the variable
                $type = $this->_determineType($varvalue, $resultGraph);
                // search attribute list defined in constants.php
                $list = null;
                if ($type) {
                    $strLuri = strtolower($type->getUri());
                    if (isset($sparql_describe[$strLuri])) {
                        $list = $sparql_describe[$strLuri] ;
                    }
                }
                // search in dataset
                $this->_getAttributes($list, $resultGraph, $varvalue);
            }
        }

        return $resultGraph;
    }//function describeGraph($arVartable)



    /**
    * Tries to determine the rdf:type of the variable.
    *
    * @param  Node       $var The variable
    * @param  MemModel   $resultGraph The result graph which describes the Resource
    * @return String     Uri of the rdf:type
    */
    protected function _determineType($var, $resultGraph)
    {
        $type = null;
        // find in namedGraphs
        if (!$var instanceof Literal) {
            $iter = $this->dataset->findInNamedGraphs(
                null,
                $var,
                new Resource(RDF_NAMESPACE_URI.'type'),
                null,
                true
            );
            while ($iter->valid()) {
                $statement = $iter->current();
                $type = $statement->getObject();
                $resultGraph->add($iter->current());
                break;
            }
        }
        // if no type information found find in default graph
        if (!$type) {
            if (!$var instanceof Literal) {
                $iter1 = $this->dataset->findInDefaultGraph(
                    $var,
                    new Resource(RDF_NAMESPACE_URI.'type'),
                    null
                );
                $type = null;
                while ($iter1->valid()) {
                    $statement = $iter1->current();
                    $type      = $statement->getObject();
                    $resultGraph->add($iter1->current());
                    break;
                }
            }
        }
        return $type;
    }//protected function _determineType($var, $resultGraph)



    /**
    * Search the attributes listed in $list in the dataset.
    * Modifies $resultGraph
    *
    * @param Array      $list List containing the attributes
    * @param MemModel   $resultGraph The result graph which describes the Resource
    * @return void
    */
    protected function _getAttributes($list, $resultGraph, $varvalue)
    {
        if ($list){
            foreach ($list as $attribute) {
                if (!$varvalue instanceof Literal) {
                    $iter2 = $this->dataset->findInNamedGraphs(
                        null,
                        $varvalue,
                        new Resource($attribute),
                        null,
                        true
                    );
                    while ($iter2->valid()) {
                        $resultGraph->add($iter2->current());
                        $iter2->next();
                    }
                    $iter3 = $this->dataset->findInDefaultGraph(
                        $varvalue,
                        new Resource($attribute),
                        null
                    );
                    while ($iter3->valid()) {
                        $resultGraph->add($iter3->current());
                        $iter3->next();
                    }
                }
            }
        }
    }//protected function _getAttributes($list, $resultGraph, $varvalue)


}//class SparqlEngine_ResultRenderer_Default implements SparqlEngine_ResultRenderer

?>