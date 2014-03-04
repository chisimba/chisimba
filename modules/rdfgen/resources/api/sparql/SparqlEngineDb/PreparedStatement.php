<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngine/PreparedStatement.php';;

/**
*   A prepared statement that can be execute()d later multiple
*   times with different variable values.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_PreparedStatement extends SparqlEngine_PreparedStatement
{
    protected $sparqlEngine = null;


    public function __construct(SparqlEngineDb $sparqlEngine)
    {
        $this->sparqlEngine = $sparqlEngine;
    }//public function __construct(SparqlEngineDb $sparqlEngine)



    /**
    *   Execute the prepared statement and returns the result.
    *
    *   @param array $arVariables   Array of sparql query variables => values
    *   @param string $resultform   Which result form you need
    *   @return mixed   Anything a sparql query can return
    */
    public function execute($arVariables, $resultform = false)
    {
        return $this->sparqlEngine->execute($arVariables, $resultform);
    }//public function execute($arVariables, $resultform)

}//class SparqlEngineDb_PreparedStatement extends SparqlEngine_PreparedStatement
?>