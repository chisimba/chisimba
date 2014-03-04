<?php

// ----------------------------------------------------------------------------------
// Class: RdqlDbEngine
// ----------------------------------------------------------------------------------

/**
 * This class performs as RDQL query on a DbModel.
 *
 * Provided an rdql query parsed into an array of php variables and constraints
 * at first the engine generates an sql statement and queries the database for
 * tuples matching all patterns from the WHERE clause of the given RDQL query.
 * Subsequently the result set is is filtered with evaluated boolean expressions
 * from the AND clause of the given RDQL query.
 *
 * @version  $Id: RdqlDbEngine.php 559 2008-02-29 15:21:35Z cax $
 * @author   Radoslaw Oldakowski <radol@gmx.de>
 *
 * @package rdql
 * @access public
 */

Class RdqlDbEngine extends RdqlEngine {


/**
 * Parsed query variables and constraints.
 *
 * @var     array   ['selectVars'][] = ?VARNAME
 *                  ['sources'][] = URI
 *                  ['patterns'][]['subject']['value'] = VARorURI
 *                                ['predicate']['value'] = VARorURI
 *                                ['object']['value'] = VARorURIorLiterl
 *                                          ['is_literal'] = boolean
 *                                          ['l_lang'] = string
 *                                          ['l_dtype'] = string
 *                  ['filters'][]['string'] = string
 *                               ['evalFilterStr'] = string
 *                               ['reqexEqExprs'][]['var'] = ?VARNAME
 *                                                 ['operator'] = (eq | ne)
 *                                                 ['regex'] = string
 *                               ['strEqExprs'][]['var'] = ?VARNAME
 *                                               ['operator'] = (eq | ne)
 *                                               ['value'] = string
 *                                               ['value_type'] = ('variable' | 'URI' | 'Literal')
 *                                               ['value_lang'] = string
 *                                               ['value_dtype'] = string
 *                               ['numExpr']['vars'][] = ?VARNAME
 *                         ( [] stands for an integer index - 0..N )
 * @access	private
 */
 var $parsedQuery;


/**
 * When an RDQL query is performed on a DbModel, in first step the engine searches
 * in database for triples matching the Rdql-WHERE clause. A recordSet is returned.
 * $rsIndexes maps select and filter variables to their corresponding indexes
 * in the returned recordSet.
 *
 * @var     array [?VARNAME]['value'] = integer
 *                          ['nType'] = integer
 *                          ['l_lang'] = integer
 *                          ['l_dtype'] = integer
 * @access	private
 */
 var $rsIndexes;


 /**
 * Perform an RDQL Query on the given DbModel.
 *
 * @param   object  DbModel $dbModel
 * @param   array   &$parsedQuery  (the same format as $this->parsedQuery)
 * @param   boolean $returnNodes
 * @return  array   [][?VARNAME] = object Node  (if $returnNodes = TRUE)
 *      OR  array   [][?VARNAME] = string
 * @access  public
 */
 function & queryModel(&$dbModel, &$parsedQuery, $returnNodes = TRUE) {

   $this->parsedQuery = &$parsedQuery;

   $sql = $this->generateSql($dbModel->modelID);
   $recordSet =& $dbModel->dbConn->execute($sql);
   $queryResult = $this->filterQueryResult($recordSet);

   if ($returnNodes)
      $ret = $this->toNodes($queryResult);
   else
      $ret = $this->toString($queryResult);
   return $ret;
 }


 /**
 * Generate an SQL string to query the database for tuples matching all patterns
 * of $parsedQuery.
 *
 * @param   integer $modelID
 * @return  string
 * @access	private
 */
 function generateSql($modelID) {

   $sql  = $this->generateSql_SelectClause();
   $sql .= $this->generateSql_FromClause();
   $sql .= $this->generateSql_WhereClause($modelID);
   return $sql;
 }


/**
 * Generate SQL SELECT clause.
 *
 * @return  string
 * @throws  PHPError
 * @access	private
 */
 function generateSql_SelectClause() {

   $sql_select = 'SELECT';
   $index = 0;
   $this->rsIndexes = array();

   foreach ($this->parsedQuery['selectVars'] as $var)
     $sql_select .= $this->_generateSql_SelectVar($var, $index);

   if (isset($this->parsedQuery['filters'])) {
       foreach ($this->parsedQuery['filters'] as $n => $filter) {

         // variables from numeric expressions
         foreach ($filter['numExprVars'] as $numVar)
           $sql_select .= $this->_generateSql_SelectVar($numVar, $index);

         // variables from regex equality expressions
         foreach ($filter['regexEqExprs'] as $regexEqExpr)
           $sql_select .= $this->_generateSql_SelectVar($regexEqExpr['var'], $index);

         // variables from string equality expressions
         foreach ($filter['strEqExprs'] as $strEqVar)
             $sql_select .= $this->_generateSql_SelectVar($strEqVar['var'], $index);
       }
   }

   return rtrim($sql_select, " , ");
 }


/**
 * Generate SQL FROM clause
 *
 * @return  string
 * @access	private
 */
 function generateSql_FromClause() {

   $sql_from = ' FROM';
   foreach ($this->parsedQuery['patterns'] as $n => $v)
     $sql_from .= ' statements s' .($n+1) .' , ';

   return rtrim($sql_from, ' , ');
 }


/**
 * Generate an SQL WHERE clause
 *
 * @param   integer $modelID
 * @return  string
 * @access	private
 */
 function generateSql_WhereClause($modelID) {

   $sql_where = ' WHERE';
   $count_patterns = count($this->parsedQuery['patterns']);
   foreach ($this->parsedQuery['patterns'] as $n => $pattern) {
     $sql_where .= ' s' .($n+1) .'.modelID=' .$modelID .' AND';
     foreach ($pattern as $key => $val_1)
       if ($val_1['value'] && $val_1['value']{0}=='?') {
         $sql_tmp = ' s' .($n+1) .'.' .$key .'=';
         // find internal bindings
         switch ($key) {
           case 'subject':
                    if ($pattern['subject']['value'] == $pattern['predicate']['value'])
                       $sql_where .= $sql_tmp .'s' .($n+1) .'.predicate AND';
                    elseif ($pattern['subject']['value'] == $pattern['object']['value'])
                       $sql_where .= $sql_tmp .'s' .($n+1) .'.object AND';
                    break;
           case 'predicate':
                    if ($pattern['predicate']['value'] == $pattern['object']['value'])
                       $sql_where .= $sql_tmp .'s' .($n+1) .'.object AND';
         }
         // find external bindings
         for ($i=$n+1; $i<$count_patterns; $i++)
             foreach ($this->parsedQuery['patterns'][$i] as $key2 => $val_2)
               if ($val_1['value']==$val_2['value']) {
                  $sql_where .= $sql_tmp .'s' .($i+1) .'.' .$key2 .' AND';
                  break 2;
               }
       }else {
          $sql_where .= ' s' .($n+1) .'.' .$key ."='" .$val_1['value'] ."' AND";
          if ($key == 'object' && isset($val_1['is_literal'])) {
              $sql_where .= ' s' .($n+1) .".object_is='l' AND";
              $sql_where .= ' s' .($n+1) .".l_datatype='" .$val_1['l_dtype'] ."' AND";
              $sql_where .= ' s' .($n+1) .".l_language='" .$val_1['l_lang'] ."' AND";
          }
       }
   }
   return rtrim($sql_where, ' AND');
 }


/**
 * Filter tuples containing variables matching all patterns from the WHERE clause
 * of an RDQL query. As a result of a database query using ADOdb these tuples
 * are returned as an ADORecordSet object, which is then passed to this function.
 *
 * @param   object ADORecordSet &$recordSet
 * @return  array  [][?VARNAME]['value']   = string
 *                             ['nType']   = string
 *                             ['l_lang']  = string
 *                             ['l_dtype'] = string
 * @access	private
 */
 function filterQueryResult(&$recordSet) {
   $queryResult=array();

   if (isset($this->parsedQuery['filters'])) {

       while (!$recordSet->EOF) {

         foreach ($this->parsedQuery['filters'] as $filter) {

           $evalFilterStr = $filter['evalFilterStr'];

           // evaluate regex equality expressions of each filter
           foreach ($filter['regexEqExprs'] as $i => $expr) {
               preg_match($expr['regex'], $recordSet->fields[$this->rsIndexes[$expr['var']]['value']], $match);
               $op = substr($expr['operator'], 0,1);
               if (($op != '!' && !isset($match[0])) || ($op == '!' && isset($match[0])))
                  $evalFilterStr = str_replace("##RegEx_$i##", 'FALSE', $evalFilterStr);
               else
                  $evalFilterStr = str_replace("##RegEx_$i##", 'TRUE', $evalFilterStr);
           }

           // evaluate string equality expressions
           foreach ($filter['strEqExprs'] as $i => $expr) {

             $exprBoolVal = 'FALSE';

             switch ($expr['value_type']) {

               case 'variable':
                    if (($recordSet->fields[$this->rsIndexes[$expr['var']]['value']] ==
                           $recordSet->fields[$this->rsIndexes[$expr['value']]['value']] &&
                         $expr['operator'] == 'eq') ||
                        ($recordSet->fields[$this->rsIndexes[$expr['var']]['value']] !=
                           $recordSet->fields[$this->rsIndexes[$expr['value']]['value']] &&
                         $expr['operator'] == 'ne'))

                       $exprBoolVal = 'TRUE';
                    break;

               case 'URI':

                      if (isset($this->rsIndexes[$expr['var']]['nType']) &&
                           $recordSet->fields[$this->rsIndexes[$expr['var']]['nType']] == 'l') {

                         if ($expr['operator'] == 'ne')
                            $exprBoolVal = 'TRUE';
                         break;
                      }

                    if (($recordSet->fields[$this->rsIndexes[$expr['var']]['value']] ==
                           $expr['value'] && $expr['operator'] == 'eq') ||
                        ($recordSet->fields[$this->rsIndexes[$expr['var']]['value']] !=
                           $expr['value'] && $expr['operator'] == 'ne'))
                       $exprBoolVal = 'TRUE';
                    break;

               case 'Literal':

                    if (!isset($this->rsIndexes[$expr['var']]['nType']) ||
                           $recordSet->fields[$this->rsIndexes[$expr['var']]['nType']] != 'l') {

                       if ($expr['operator'] == 'ne')
                          $exprBoolVal = 'TRUE';
                       break;
                    }

                    $filterLiteral= new Literal($expr['value'],$expr['value_lang']);
                    $filterLiteral->setDatatype($expr['value_dtype']);

                    $resultLiteral=new Literal($recordSet->fields[$this->rsIndexes[$expr['var']]['value']]);
                    $resultLiteral->setDatatype($recordSet->fields[$this->rsIndexes[$expr['var']]['l_dtype']]);
                    $resultLiteral->setLanguage($recordSet->fields[$this->rsIndexes[$expr['var']]['l_lang']]);

                    $equal=$resultLiteral->equals($filterLiteral);

                    if (($equal && $expr['operator'] == 'eq') ||
                        (!$equal && $expr['operator'] == 'ne'))
                       $exprBoolVal = 'TRUE';
                    else
                       $exprBoolVal = 'FALSE';

             }

             $evalFilterStr = str_replace("##strEqExpr_$i##", $exprBoolVal, $evalFilterStr);
          }

          // evaluate numerical expressions
          foreach ($filter['numExprVars'] as $varName) {
            $varValue = "'" .$recordSet->fields[$this->rsIndexes[$varName]['value']] ."'";
            $evalFilterStr = str_replace($varName, $varValue, $evalFilterStr);
          }

          eval("\$filterBoolVal = $evalFilterStr; \$eval_filter_ok = TRUE;");
          if (!isset($eval_filter_ok))
             trigger_error(RDQL_AND_ERR ."'" .htmlspecialchars($filter['string']) ."'", E_USER_ERROR);

          if (!$filterBoolVal) {
             $recordSet->MoveNext();
             continue 2;
          }

        }
        $queryResult[] = $this->_convertRsRowToQueryResultRow($recordSet->fields);
        $recordSet->MoveNext();
      }

   }else
      while (!$recordSet->EOF) {
        $queryResult[] = $this->_convertRsRowToQueryResultRow($recordSet->fields);
        $recordSet->MoveNext();
      }
   return $queryResult;
 }


/**
 * Serialize variable values of $queryResult to string.
 *
 * @param   array  &$queryResult [][?VARNAME]['value']   = string
 *                                           ['nType']   = string
 *                                           ['l_lang']  = string
 *                                           ['l_dtype'] = string
 * @return  array  [][?VARNAME] = string
 * @access	private
 */
 function toString(&$queryResult) {

   // if a result set is empty return only variable sames
   if (count($queryResult) == 0) {
      foreach ($this->parsedQuery['selectVars'] as $selectVar)
         $res[0][$selectVar] = NULL;
      return $res;
   }

   $res = array();
   foreach ($queryResult as $n => $var)
     foreach ($var as $varname => $varProperties)
       if ($varProperties['nType'] == 'r' || $varProperties['nType'] == 'b')
          $res[$n][$varname] = '<' .$varProperties['value'] .'>';
       else {
          $res[$n][$varname] = '"' .$varProperties['value'] .'"';
          if ($varProperties['l_lang'] != NULL)
             $res[$n][$varname] .= ' (xml:lang="' .$varProperties['l_lang'] .'")';
          if ($varProperties['l_dtype'] != NULL)
             $res[$n][$varname] .= ' (rdf:datatype="' .$varProperties['l_dtype'] .'")';
       }
   return $res;
 }


/**
 * Convert variable values of $queryResult to objects (Node).
 *
 * @param   array  &$queryResult [][?VARNAME]['value']   = string
 *                                           ['nType']   = string
 *                                           ['l_lang']  = string
 *                                           ['l_dtype'] = string
 * @return  array  [][?VARNAME] = object Node
 * @access	private
 */
 function toNodes(&$queryResult) {

   // if a result set is empty return only variable sames
   if (count($queryResult) == 0) {
      foreach ($this->parsedQuery['selectVars'] as $selectVar)
         $res[0][$selectVar] = NULL;
      return $res;
   }

   $res = array();
   foreach ($queryResult as $n => $var)
     foreach ($var as $varname => $varProperties)
       if ($varProperties['nType'] == 'r')
          $res[$n][$varname] = new Resource($varProperties['value']);
       elseif ($varProperties['nType'] == 'b')
          $res[$n][$varname] = new BlankNode($varProperties['value']);
       else {
          $res[$n][$varname] = new Literal($varProperties['value'], $varProperties['l_lang']);
          if ($varProperties['l_dtype'] != NULL)
             $res[$n][$varname]->setDataType($varProperties['l_dtype']);
       }
   return $res;
 }


/**
 * Generate a piece of an sql select statement for a variable.
 * Look first if the given variable is defined as a pattern object.
 * (So you can select the node type, literal lang and dtype)
 * If not found - look for subjects and select node label and type.
 * If there is no result either go to predicates.
 * Predicates are always resources therefore select only the node label.
 *
 * @param   string $varName
 * @return  string
 * @access	private
 */
function _generateSql_SelectVar ($varName, &$index) {

  $sql_select = '';

  if (array_key_exists($varName, $this->rsIndexes))
     return NULL;

  foreach ($this->parsedQuery['patterns'] as $n => $pattern)
    if ($varName == $pattern['object']['value']) {

       // select the object label
       $sql_select .= " s" .++$n .".object as _" .ltrim($varName, "?") ." , ";
       $this->rsIndexes[$varName]['value'] = $index++;
       // select the node type
       $sql_select .= " s" .$n .".object_is , ";
       $this->rsIndexes[$varName]['nType'] = $index++;
       // select the object language
       $sql_select .= " s" .$n .".l_language , ";
       $this->rsIndexes[$varName]['l_lang'] = $index++;
       // select the object dtype
       $sql_select .= " s" .$n .".l_datatype , ";
       $this->rsIndexes[$varName]['l_dtype'] = $index++;

       return $sql_select;
    }

  foreach ($this->parsedQuery['patterns'] as $n => $pattern)
    if ($varName == $pattern['subject']['value']) {

       // select the object label
       $sql_select .= " s" .++$n .".subject as _" .ltrim($varName, "?") ." , ";
       $this->rsIndexes[$varName]['value'] = $index++;
       // select the node type
       $sql_select .= " s" .$n .".subject_is , ";
       $this->rsIndexes[$varName]['nType'] = $index++;

       return $sql_select;
    }

  foreach ($this->parsedQuery['patterns'] as $n => $pattern)
    if ($varName == $pattern['predicate']['value']) {

       // select the object label
       $sql_select .= " s" .++$n .".predicate as _" .ltrim($varName, "?") ." , ";
       $this->rsIndexes[$varName]['value'] = $index++;

       return $sql_select;
    }
 }


/**
 * Converts a single row of ADORecordSet->fields array to the format of
 * $queryResult array using pointers to indexes ($this->rsIndexes) in RecordSet->fields.
 *
 * @param   array  &$record [] = string
 * @return  array  [?VARNAME]['value']   = string
 *                           ['nType']   = string
 *                           ['l_lang']  = string
 *                           ['l_dtype'] = string
 * @access	private
 */
 function _convertRsRowToQueryResultRow(&$record) {

   // return only select variables (without conditional variables from the AND clause)
   foreach ($this->parsedQuery['selectVars'] as $selectVar) {
     $resultRow[$selectVar]['value'] = $record[$this->rsIndexes[$selectVar]['value']];
     if (isset($this->rsIndexes[$selectVar]['nType']))
        $resultRow[$selectVar]['nType'] = $record[$this->rsIndexes[$selectVar]['nType']];
     // is a predicate then
     else
        $resultRow[$selectVar]['nType'] = 'r';

     if ($resultRow[$selectVar]['nType'] == 'l') {
        $resultRow[$selectVar]['l_lang'] = $record[$this->rsIndexes[$selectVar]['l_lang']];
        $resultRow[$selectVar]['l_dtype'] = $record[$this->rsIndexes[$selectVar]['l_dtype']];
     }
   }
   return $resultRow;
 }

} // end: Class RdqlDbEngine

?>