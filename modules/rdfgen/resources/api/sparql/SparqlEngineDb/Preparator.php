<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/Offsetter.php';

/**
*   This class takes care of prepared statements:
*   Preparing them in the database, replacing
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_Preparator
{



    public function __construct(Query $query, ADOConnection $dbConn)
    {
        $this->query        = $query;
        $this->dbConn       = $dbConn;
        $this->arPrefixes   = $this->query->getPrefixes();
    }//public function __construct(Query $query, ADOConnection $dbConn)



    /**
    *   Converts the given queries into sql prepared statments,
    *   calls the prepare command in the database and returns
    *   an array consisting of subarrays. They contain
    *   the db's prepared statement as first value, and an array
    *   of variable positions as second value (key is the position,
    *   the sparql variable is the value).
    *
    *   @param array $arQueries      Array of sql queries part arrays
    *   @param array $arPlaceholders Array of sparql (variable name =>
    *                                placeholder name) pairs
    *   @return array  Array of (prepared statment, variable positions) pairs
    */
    public function prepareInDb($arQueries, $arPlaceholders)
    {
        $arDbStatements = array();

        foreach ($arQueries as $arQuery) {
            list($strPrepared, $arVariablePositions) = $this->replacePlaceholders(
                implode('', $arQuery),
                $arPlaceholders
            );
            if (count($arQueries) == 1) {
                //I currently haven't seens one case in which the count was > 1
                //if that happens, we will need to add a fix
                $strPrepared .= SparqlEngineDb_Offsetter::getLimitSql(
                    $this->query,
                    $this->dbConn
                );
            }
            $stmt = $this->dbConn->Prepare($strPrepared);
            $arDbStatements[] = array(
                $stmt,
                $arVariablePositions
            );
        }

        return $arDbStatements;
    }//public function prepareInDb($arQueries, $arPlaceholders)



    /**
    *   Replaces the placeholders in the given SQL statement with real
    *   SQL prepared statements placeholders.
    *
    *   @param string $strQuery SQL query with placeholders
    *   @param array $arPlaceholders Array of sparql (variable name =>
    *                                placeholder name) pairs
    *   @return array (prepared sql query string, variable positions) pair
    */
    protected function replacePlaceholders($strQuery, $arPlaceholders)
    {
        $arVariablePositions = array();
        $this->arTmpVariablePositions = array();
        $this->arTmpPlaceholders      = $arPlaceholders;
        $strQuery = preg_replace_callback(
            '/@\\$%_PLACEHOLDER_[0-9]+_%\\$@/',
            array($this, 'replacePlaceholdersCb'),
            $strQuery
        );
        return array($strQuery, $this->arTmpVariablePositions);
    }//protected function replacePlaceholders($strQuery, $arPlaceholders)



    /**
    *   Callback method internally used by replacePlaceholders() method.
    */
    protected function replacePlaceholdersCb($matches)
    {
        $strPlaceholder     = $matches[0];
        $strSparqlVariable  = array_search(
            $strPlaceholder,
            $this->arTmpPlaceholders
        );
        $strDbPlaceholder   = $this->dbConn->Param($strSparqlVariable);
        $this->arTmpVariablePositions[] = $strSparqlVariable;

        return $strDbPlaceholder;
    }//protected function replacePlaceholdersCb($matches)



    /**
    *   Executes the given prepared statments, filling the placeholders
    *   with the given values.
    *
    *   @param array $arDbStatements    Return value of prepareInDb()
    *   @param array $arVariableValues      Array of (variable name, value) pairs
    *
    *   @return array Array of database results as returned by Execute()
    */
    public function execute($arDbStatements, $arVariableValues)
    {
        $arResults = array();
        $oldmode = $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);

        foreach ($arDbStatements as $arStatement) {
            list($stmt, $arVariablePositions) = $arStatement;
            $arVariables = $this->createVariableArray(
                $arVariablePositions,
                $arVariableValues
            );
            $arResults[] = $this->dbConn->Execute($stmt, $arVariables);
        }

        $this->dbConn->SetFetchMode($oldmode);

        return $arResults;
    }//public function execute($arDbStatements, $arVariableValues)



    /**
    *   Creates an array full of variables to be passed to the Execute() method
    *   of the database connection object.
    *   Uses the variable positions array to get the positions of the variables
    *   in the result array, and the variable value array to get the actual
    *   values for the prepared statement.
    *
    *   @param array $arVariablePositions   Positions of the variables as returned
    *                                       by replacePlaceholders().
    *   @param array $arVariableValues      Array of (variable name, value) pairs
    *
    *   @return array Array of variable values
    */
    protected function createVariableArray($arVariablePositions, $arVariableValues)
    {
        $arVariables = array();

        foreach ($arVariablePositions as $nPos => $strVariable) {
            if (!isset($arVariableValues[$strVariable])) {
                throw new Exception('No value for variable "' . $strVariable . '" in prepared statement');
            }

            $strValue = self::replacePrefix(
                $arVariableValues[$strVariable],
                $this->arPrefixes
            );

            $arVariables[$nPos] = $strValue;
        }

        return $arVariables;
    }//protected function createVariableArray($arVariablePositions, $arVariableValues)



    /**
    *   Replaces all placeholders with their actual values.
    */
    public function replacePlaceholdersWithVariables($strQuery, $arPlaceholders, $arVariableValues)
    {
        $this->arTmpPlaceholders   = $arPlaceholders;
        $this->arTmpVariableValues = $arVariableValues;

        $strQuery = preg_replace_callback(
            '/@\\$%_PLACEHOLDER_[0-9]+_%\\$@/',
            array($this, 'replacePlaceholdersWithVariablesCb'),
            $strQuery
        );

        return $strQuery;
    }//public function replacePlaceholdersWithVariables($strQuery, $arPlaceholders, $arVariableValues)



    /**
    *   Callback method internally used by
    *   replacePlaceholdersWithVariables() method.
    */
    protected function replacePlaceholdersWithVariablesCb($matches)
    {
        $strPlaceholder     = $matches[0];
        $strSparqlVariable  = array_search(
            $strPlaceholder,
            $this->arTmpPlaceholders
        );
        return $this->dbConn->qstr(
            self::replacePrefix(
                $this->arTmpVariableValues[$strSparqlVariable],
                $this->arPrefixes
            )
        );
    }//protected function replacePlaceholdersWithVariablesCb($matches)



    protected static function replacePrefix($strValue, $arPrefixes)
    {
        //replace prefixes?
        if (count($arParts = explode(':', $strValue, 2)) == 2) {
            if (isset($arPrefixes[$arParts[0]])) {
                $strValue = $arPrefixes[$arParts[0]] . $arParts[1];
            }
        }
        return $strValue;
    }//protected static function replacePrefix($strValue, $arPrefixes)

}//class SparqlEngineDb_Preparator

?>