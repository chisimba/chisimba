<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/SqlMerger.php';

/**
*   Determines the offset in a row of sql queries.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_Offsetter
{
    public function __construct(ADOConnection $dbConn, Query $query)
    {
        $this->dbConn   = $dbConn;
        $this->query    = $query;
    }//public function __construct(ADOConnection $dbConn, Query $query)



    /**
    *   Determines the offset in the sqls, the position to start form.
    *
    *   @param array $arSqls    Array of SQL query parts as returned by
    *                           SparqlEngine_TypeSorter::getOrderifiedSqls()
    *   @return array   Array of two values: The first determines the
    *                   index of the sql query to begin with, the second
    *                   is the row offset that should be used in the final
    *                   SQL query.
    */
    public function determineOffset($arSqls)
    {
        $arSM = $this->query->getSolutionModifier();
        if ($arSM['offset'] === null) {
            return array(0, 0);
        }

        $nCount = 0;
        foreach ($arSqls as $nId => $arSql) {
            $nCurrentCount = $this->getCount($arSql);
            if ($nCurrentCount + $nCount > $arSM['offset']) {
                return array($nId, $arSM['offset'] - $nCount);
            }
            $nCount += $nCurrentCount;
        }
        //nothing found - no results for this offset
        return array(count($arSqls), 0);
    }//public function determineOffset($arSql)



    /**
    *   Returns the number of rows that the given query will return.
    *
    *   @param array $arSql Array with sql parts and at least keys
    *                'from' and 'where' set.
    *   @return int     Number of rows returned.
    */
    protected function getCount($arSql)
    {
        $sql = SparqlEngineDb_SqlMerger::getCount($this->query, $arSql);
        $dbResult = $this->dbConn->execute($sql);

        $nCount = 0;
        foreach ($dbResult as $row) {
            $nCount = intval($row[0]);
            break;
        }
        return $nCount;
    }//protected function getCount($arSql)



    /**
    *   Creates a sql LIMIT statement if the sparql query needs one.
    *   This method is needed because AdoDb does not support limits with
    *   prepared statements. It's a pity.
    *
    *   @return string  SQL command to be appended to a query, to limit
    *                   the number of result rows returned.
    */
    public static function getLimitSql(Query $query, ADOConnection $dbConn)
    {
        $arSM = $query->getSolutionModifier();
        if ($arSM['limit'] === null && $arSM['offset'] === null) {
            return '';
        }
        //this here is mysql syntax. if anyone has problems, write it
        //dependent on $dbConn's type
        if ($arSM['offset'] === null) {
            return ' LIMIT ' . $arSM['limit'];
        } else if ($arSM['limit'] === null) {
            return ' LIMIT ' . $arSM['offset'] . ', 18446744073709551615';
        } else {
            return ' LIMIT ' . $arSM['offset'] . ', ' . $arSM['limit'];
        }
    }//public static function getLimitSql(Query $query, ADOConnection $dbConn)

}//class SparqlEngineDb_Offsetter

?>