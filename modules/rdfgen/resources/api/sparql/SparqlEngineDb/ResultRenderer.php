<?php

/**
*   Result renderer interface that any result renderer needs to implement.
*   A result renderer converts the raw database results into a
*   - for the user - usable result format, e.g. php arrays, xml, json and
*   so on.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
interface SparqlEngineDb_ResultRenderer
{
    /**
    *   Converts the database results into the desired output format
    *   and returns the result.
    *
    *   @param array $arRecordSets  Array of (possibly several) SQL query results.
    *   @param Query $query     SPARQL query object
    *   @param SparqlEngineDb $engine   Sparql Engine to query the database
    *   @return mixed   The result as rendered by the result renderers.
    */
    public function convertFromDbResults($arRecordSets, Query $query, SparqlEngineDb $engine);

}//interface SparqlEngineDb_ResultRenderer

?>