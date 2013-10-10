<?php

/**
*   Result renderer interface that any result renderer needs to implement.
*   A result renderer converts results into a
*   - for the user - usable result format, e.g. php arrays, xml, json and
*   so on.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
interface SparqlEngine_ResultRenderer
{
    /**
    *   Converts the database results into the desired output format
    *   and returns the result.
    *
    *   @param array $arVartable    Variable table
    *   @param Query $query         SPARQL query object
    *   @param SparqlEngine $engine Sparql Engine to query the database
    *   @return mixed   The result as rendered by the result renderers.
    */
    public function convertFromResult($arVartable, Query $query, SparqlEngine $engine);

}//interface SparqlEngine_ResultRenderer

?>