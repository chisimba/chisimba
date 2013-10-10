<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlVariable.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/TypeSorter.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/FilterGenerator.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/SqlGeneratorException.php';

/**
*   Creates sql statements from a Query object
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_SqlGenerator
{
    public $query = null;

    /**
    *   Determines which variables can be found
    *   in which SQL result column.
    *   @example
    *   array(
    *       '?person' => array('t1', 's'),
    '       '?p'      => array('t1', 'p'),
    *       '?mbox'   => array('t2', 'o')
    *   )
    *   Would express that variable ?person is the subject
    *   in result table t1, and ?mbox is the object in
    *   table t2.
    *
    *   @see $arCreationMethods
    *
    *   @internal The array is created in createSql()
    *   and used in convertFromDbResult().
    *
    *   @var array
    */
    public $arVarAssignments = array();

    /**
    *   Array of variable name => table.col assignments
    *   for all variables used in the query not only
    *   the ones that shall be returned.
    *
    *   @example
    *   array(
    *       '?person'   => 't0.subject'
    *   )
    *
    *   @var array
    */
    public $arUsedVarAssignments = array();

    /**
    *   Array of arrays that contain all variable names
    *   which are to be found in the result of
    *   an sql statement in a union.
    *
    *   @example
    *   array(
    *       0 => array(
    *           '?person' => 's',
    *           '?p' => 'o'
    *       ),
    *       1 => array(
    *           '?person' => 's',
    *           '?mbox' => 'o'
    *       )
    *   )
    *
    *   @var array
    */
    public $arUnionVarAssignments = array();

    /**
    *   Which variables have been used as which type?
    *   key is variable name, value is an array of
    *   max. three keys (s, p, o)
    *
    *   @example
    *   array(
    *       '?person' => array(
    *           's' => true
    *       ),
    *       '?mbox' => array(
    *           'o' => true
    *       )
    *   )
    *
    *   @var array
    */
    protected $arUsedVarTypes = array();

    /**
    *   Array with placeholders of prepared statements variables.
    *   key is the variable name (without "??"), value is the
    *   placeholder.
    *   @var array
    */
    protected $arPlaceholders = array();

    /**
    *   Column names for subjects, predicates and
    *   objects for easy access via their character
    *   names (spo).
    *
    *   @var array
    */
    public static $arTableColumnNames = array(
        's' => array(
            'value' => 'subject',
            'is'    => 'subject_is'
        ),
        'p' => array(
            'value' => 'predicate'
        ),
        'o' => array(
            'value' => 'object',
            'is'    => 'object_is'
        )
    );

    /**
    *   Current UNION part number
    *   @var int
    */
    protected $nUnionCount = 0;

    protected $nSqlVariableNameCount = 0;

    /**
    *   Name of the statements table
    */
    protected $tblStatements = 'statements';



    public function __construct(Query $query, ADOConnection $dbConn, $arModelIds)
    {
        $this->query      = $query;
        $this->dbConn     = $dbConn;
        $this->arModelIds = $arModelIds;
    }//public function __construct(Query $query, ADOConnection $dbConn, $arModelIds)



    /**
    *   Creates an SQL query string from the given Sparql query object.
    *
    *   @internal uses $query variable
    *
    *   @return array       Array of arrays of SQL query string parts: select, from and where
    *
    *   @throws SparqlEngineDb_SqlGeneratorException   If there is no variable in the result set.
    */
    function createSql()
    {
//var_dump($this->query);
        $arSelect   = array();
        $arFrom     = array();
        $arWhere    = array();

        $strResultForm = $this->query->getResultForm();
        $filterGen     = new SparqlEngineDb_FilterGenerator($this);
        switch ($strResultForm) {
            case 'construct':
                $arResultVars = $this->query->getConstructPatternVariables();
                break;
            default:
                $arResultVars = $this->query->getResultVars();
                break;
        }

        $this->nTableId                 = 0;
        $this->nGraphPatternCount       = 0;
        $this->nUnionCount              = 0;
        $this->nUnionTriplePatternCount = 0;
        $this->arUnionVarAssignments[0] = array();

        foreach ($this->query->getResultPart() as $graphPattern) {
            if ($graphPattern->isEmpty()) {
                continue;
            }
            if ($graphPattern->getUnion() !== null) {
                ++$this->nUnionCount;
                $this->nTableId                 = 0;
                $this->nUnionTriplePatternCount = 0;
                $this->nGraphPatternCount = 0;
                $this->arUnionVarAssignments[$this->nUnionCount] = array();
            }
            $this->nTriplePatternCount = 0;
            $arTriplePattern = $graphPattern->getTriplePatterns();
            if ($arTriplePattern != null) {
                foreach ($arTriplePattern as $triplePattern) {
                    list (
                        $arSelect[$this->nUnionCount][],
                        $arFrom  [$this->nUnionCount][],
                        $arWhere [$this->nUnionCount][]
                    ) =
                        $this->getTripleSql(
                            $triplePattern,
                            $graphPattern,
                            $arResultVars
                        );
                    ++$this->nTableId;
                    ++$this->nTriplePatternCount;
                    ++$this->nUnionTriplePatternCount;
                }
            }
            ++$this->nGraphPatternCount;

        }

        //constraints extra. needed, since OPTIONAL parts are put after
        // the current pattern while the constraint already refers to variables
        // defined in there
        $this->nGraphPatternCount       = 0;
        $this->nUnionCount              = 0;
        foreach ($this->query->getResultPart() as $graphPattern) {
            if ($graphPattern->getUnion() !== null) {
                ++$this->nUnionCount;
            }
            $arConstraints = $graphPattern->getConstraints();
            if ($arConstraints != null) {
                foreach ($arConstraints as $constraint) {
                    $arWhere[$this->nUnionCount][count($arWhere[$this->nUnionCount]) - 1]
                     .= $filterGen->createFilterSql(
                        $constraint->getTree(),
                        $graphPattern->getOptional() !== null,
                        $this->nUnionCount
                    );
                }
            }
            ++$this->nGraphPatternCount;
        }

        $arSelect    = $this->createEqualSelects($arSelect);
        $arStrSelect = array();

        switch ($strResultForm) {
            case 'construct':
            case 'describe':
                $strSelectType = 'SELECT';
            case 'select':
            case 'select distinct':
                if (!isset($strSelectType)) {
                    $strSelectType = $strResultForm;
                }
                foreach ($arSelect as $nUnionCount => $arSelectPart) {
                    $arSelectPart = self::removeNull($arSelectPart);
                    if (count($arSelectPart) == 0
                    || (count($arSelectPart) == 1 && $arSelectPart[0] == '')) {
                        //test "test-1-07" suggests we return no rows in this case
                        //throw new SparqlEngineDb_SqlGeneratorException('No variable that could be returned.');
                    } else {
                        $arStrSelect[$nUnionCount] = strtoupper($strSelectType) . ' ' . implode(', '   , $arSelectPart);
                    }
                }
                break;

            case 'ask':
            case 'count':
                $arStrSelect = array('SELECT COUNT(*) as count');
                break;

            default:
                throw new SparqlEngineDb_SqlGeneratorException('Unsupported query type "' . $strResultForm . '"');
                break;
        }

        $arSqls = array();
        foreach ($arStrSelect as $nUnionCount => $arSelectPart) {
            $arSqls[] = array(
                'select'    => $arStrSelect[$nUnionCount],
                'from'      => ' FROM '  . implode(' '    , self::removeNull($arFrom[$nUnionCount])),
                'where'     => ' WHERE ' . self::fixWhere(
                            implode(' '  , self::removeNull($arWhere[$nUnionCount]))
                )
            );
        }
        return $arSqls;
    }//function createSql()



    /**
    *   Creates some SQL statements from the given triple pattern
    *   array.
    *
    *   @param QueryTriple  $triple                 Array containing subject, predicate and object
    *   @param GraphPattern $graphPattern           Graph pattern object
    *
    *   @return array   Array consisting of on array and two string values:
    *                   SELECT, FROM and WHERE part
    */
    function getTripleSql(QueryTriple $triple, GraphPattern $graphPattern, $arResultVars)
    {
        $arSelect  = array();
        $strFrom    = null;
        $strWhere   = null;
        $strWhereEquality        = '';
        $bWhereEqualitySubject   = false;
        $bWhereEqualityPredicate = false;
        $bWhereEqualityObject    = false;

        $subject    = $triple->getSubject();
        $predicate  = $triple->getPredicate();
        $object     = $triple->getObject();

        $arRefVars      = array();
        $strTablePrefix = 't' . $this->nTableId;

        /**
        *   SELECT part
        *   We do select only the columns we need for variables
        */
        if (SparqlVariable::isVariable($subject)) {
            if (isset($this->arUnionVarAssignments[$this->nUnionCount][$subject])) {
                //already selected -> add equality check
                $bWhereEqualitySubject = true;
                $this->arUsedVarTypes[$subject]['s'] = true;
            } else {
                if (isset($this->arVarAssignments[$subject][0])) {
                    $strTablePrefix = $this->arVarAssignments[$subject][0];
                }
                $this->arVarAssignments[$subject] = array($strTablePrefix, 's');
                $this->arUnionVarAssignments[$this->nUnionCount][$subject] = array($strTablePrefix, 's');
                $this->arUsedVarTypes[$subject]['s'] = true;
                if (self::isResultVar($subject, $arResultVars)) {
                    //new variable that needs to be selected
                    $arSelect[$subject] = $this->createVariableSelectArray(
                        's', $subject, $strTablePrefix
                    );
                    if (isset($this->arUsedVarAssignments[$subject])) {
                        $arRefVars[$subject] = $strTablePrefix . '.subject';
                    } else {
                        $this->arUsedVarAssignments[$subject] = $strTablePrefix . '.subject';
                    }
                }
            }
        }

        if (SparqlVariable::isVariable($predicate)) {
            if (isset($this->arUnionVarAssignments[$this->nUnionCount][$predicate])) {
                //already selected -> add equality check
                $bWhereEqualityPredicate = true;
                $this->arUsedVarTypes[$predicate]['p'] = true;
            } else {
                if (isset($this->arVarAssignments[$predicate][0])) {
                    $strTablePrefix = $this->arVarAssignments[$predicate][0];
                }
                $this->arVarAssignments[$predicate] = array($strTablePrefix, 'p');
                $this->arUnionVarAssignments[$this->nUnionCount][$predicate] = array($strTablePrefix, 'p');
                $this->arUsedVarTypes[$predicate]['p'] = true;
                if (self::isResultVar($predicate, $arResultVars)) {
                    $arSelect[$predicate] = $this->createVariableSelectArray(
                        'p', $predicate, $strTablePrefix
                    );
                    if (isset($this->arUsedVarAssignments[$predicate])) {
                        $arRefVars[$predicate] = $strTablePrefix . '.predicate';
                    } else {
                        $this->arUsedVarAssignments[$predicate] = $strTablePrefix . '.predicate';
                    }
                }
            }
        }

        if (SparqlVariable::isVariable($object)) {
            if (isset($this->arUnionVarAssignments[$this->nUnionCount][$object])) {
                //already selected -> add equality check
                $bWhereEqualityObject = true;
                $this->arUsedVarTypes[$object]['o'] = true;
            } else {
                if (isset($this->arVarAssignments[$object][0])) {
                    $strTablePrefix = $this->arVarAssignments[$object][0];
                }
                $this->arVarAssignments[$object] = array($strTablePrefix, 'o');
                $this->arUnionVarAssignments[$this->nUnionCount][$object] = array($strTablePrefix, 'o');
                $this->arUsedVarTypes[$object]['o'] = true;
                if (self::isResultVar($object, $arResultVars)) {
                    $arSelect[$object] = $this->createVariableSelectArray(
                        'o', $object, $strTablePrefix
                    );
                    if (isset($this->arUsedVarAssignments[$object])) {
                        $arRefVars[$object] = $strTablePrefix . '.object';
                    } else {
                        $this->arUsedVarAssignments[$object] = $strTablePrefix . '.object';
                    }
                }
                if (isset($this->query->varLanguages[$object])
                 && $this->query->varLanguages[$object] !== null
                ) {
                    $strWhereEquality .=
                        ' AND ' . $strTablePrefix . '.l_language = "'
                        . addslashes($this->query->varLanguages[$object]) . '"';
                }
                if (isset($this->query->varDatatypes[$object])
                 && $this->query->varDatatypes[$object] !== null
                ) {
                    $strWhereEquality .=
                        ' AND ' . $strTablePrefix . '.l_datatype = "'
                        . addslashes($this->query->varDatatypes[$object]) . '"';
                }
            }
        }

        /**
        * WhereEquality - needs to be done now because strTablePrefix may change
        */
        if ($bWhereEqualitySubject) {
            $strWhereEquality .= ' AND ' . self::getSqlEqualityCondition(
                            array($strTablePrefix, 's'),
                            $this->arVarAssignments[$subject]
                        );
        }
        if ($bWhereEqualityPredicate) {
            $strWhereEquality .= ' AND ' . self::getSqlEqualityCondition(
                            array($strTablePrefix, 'p'),
                            $this->arVarAssignments[$predicate]
                        );
        }
        if ($bWhereEqualityObject) {
            $strWhereEquality .= ' AND ' . self::getSqlEqualityCondition(
                            array($strTablePrefix, 'o'),
                            $this->arVarAssignments[$object]
                        );
        }


        /**
        *   FROM part
        */
        if ($this->nUnionTriplePatternCount == 0) {
            //first FROM
            $strFrom    = $this->tblStatements . ' as ' . $strTablePrefix;
        } else {
            //normal join
            if (count($this->arModelIds) == 1) {
                $strFrom    = 'LEFT JOIN ' . $this->tblStatements . ' as ' . $strTablePrefix
                            . ' ON t0.modelID = ' . $strTablePrefix . '.modelID';
            } else if (count($this->arModelIds) > 1) {
                $arIDs     = array();
                foreach ($this->arModelIds as $nId) {
                    $arIDs[] = $strTablePrefix . '.modelID = ' . intval($nId);
                }
                $strFrom  = 'LEFT JOIN ' . $this->tblStatements . ' as ' . $strTablePrefix
                          . ' ON (' . implode(' OR ', $arIDs) . ')';
            } else {
                $strFrom    = 'LEFT JOIN ' . $this->tblStatements . ' as ' . $strTablePrefix
                            . ' ON t0.modelID = ' . $strTablePrefix . '.modelID';
            }

            foreach ($arRefVars as $strRefVar => $strSqlVar) {
                $strFrom .= ' AND ' . $this->arUsedVarAssignments[$strRefVar] . ' = ' . $strSqlVar;
            }

            if ($graphPattern->getOptional() !== null) {
                $strFrom .=  $this->getSqlCondition($subject  , $strTablePrefix, 'subject')
                           . $this->getSqlCondition($predicate, $strTablePrefix, 'predicate')
                           . $this->getSqlCondition($object   , $strTablePrefix, 'object')
                           . $strWhereEquality;
            }
        }


        /**
        *   WHERE part
        */
        if ($this->nUnionTriplePatternCount == 0) {
            if (count($this->arModelIds) == 1) {
                $strWhere  = $strTablePrefix . '.modelID = ' . intval(reset($this->arModelIds));
            } else if (count($this->arModelIds) > 1) {
                $arIDs     = array();
                foreach ($this->arModelIds as $nId) {
                    $arIDs[] = $strTablePrefix . '.modelID = ' . intval($nId);
                }
                $strWhere  = '(' . implode(' OR ', $arIDs) . ')';
            } else {
                //so that we can append an AND
                $strWhere = '1';
            }
        }
        if ($graphPattern->getOptional() === null || $this->nGraphPatternCount == 0) {
            $strWhere .=  $this->getSqlCondition($subject  , $strTablePrefix, 'subject')
                        . $this->getSqlCondition($predicate, $strTablePrefix, 'predicate')
                        . $this->getSqlCondition($object   , $strTablePrefix, 'object')
                        . $strWhereEquality;
        }

        return array($arSelect, $strFrom, $strWhere);
    }//function getTripleSql(QueryTriple $triple)




    protected function createVariableSelectArray($chType, $varname, $strTablePrefix)
    {
        $var = $this->query->getResultVar($varname);
        if ($var !== false) {
            if ((string)$var != $varname) {
                //copy over var assignments
                $this->arVarAssignments[(string)$var] = $this->arVarAssignments[$varname];
            }

            //works on non-* only
            $func = $var->getFunc();
            if ($func != null) {
                if ($func == 'datatype') {
                    if ($chType != 'o') {
                        throw new SparqlEngineDb_SqlGeneratorException(
                            'datatype() works on objects only'
                        );
                    }
                    return array(
                        $strTablePrefix . '.l_datatype as "' . $strTablePrefix . '.' . $this->getSqlVariableNameValue($var) . '"',
                        '"r"' .                      ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameIs($var) . '"',
                        '""' .                       ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameLanguage($var) . '"',
                        '""' .                       ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameDatatype($var) . '"',
                    );
                } else if ($func == 'lang') {
                    if ($chType != 'o') {
                        throw new SparqlEngineDb_SqlGeneratorException(
                            'lang() works on objects only'
                        );
                    }
                    return array(
                        $strTablePrefix . '.l_language as "' . $strTablePrefix . '.' . $this->getSqlVariableNameValue($var) . '"',
                        '"l"' .                      ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameIs($var) . '"',
                        '""' .                       ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameLanguage($var) . '"',
                        '""' .                       ' as "' . $strTablePrefix . '.' . $this->getSqlVariableNameDatatype($var) . '"',
                    );
                } else {
                    throw new SparqlEngineDb_SqlGeneratorException(
                        'Unsupported function for select "' . $func . '"'
                    );
                }
            }
        }

        switch ($chType) {
            case 's':
                return array(
                    $strTablePrefix . '.subject as "'    . $strTablePrefix . '.' . $this->getSqlVariableNameValue($varname) . '"',
                    $strTablePrefix . '.subject_is as "' . $strTablePrefix . '.' . $this->getSqlVariableNameIs($varname) . '"'
                );
            case 'p':
                return array(
                    $strTablePrefix . '.predicate as "' . $strTablePrefix . '.' . $this->getSqlVariableNameValue($varname) . '"'
                );
            case 'o':
                return array(
                    $strTablePrefix . '.object as "'     . $strTablePrefix . '.' . $this->getSqlVariableNameValue($varname) . '"',
                    $strTablePrefix . '.object_is as "'  . $strTablePrefix . '.' . $this->getSqlVariableNameIs($varname) . '"',
                    $strTablePrefix . '.l_language as "' . $strTablePrefix . '.' . $this->getSqlVariableNameLanguage($varname) . '"',
                    $strTablePrefix . '.l_datatype as "' . $strTablePrefix . '.' . $this->getSqlVariableNameDatatype($varname) . '"',
                );
            default:
                throw new SparqlEngineDb_SqlGeneratorException(
                    'Unknown sentence type "' . $chType . "', one of (s,p,o) expected"
                );
        }
    }//protected function createVariableSelectArray($chType, $value, $strTablePrefix)



    /**
    *   Creates SELECT statements that have the same number of columns.
    *   Needed for UNIONs.
    *
    *   @param array $arSelect  Array of arrays.
    *       array(
    *           //for each union part one
    *           0 => array(
    *               //foreach triple pattern
    *               0 => array(
    *                   '?person'   => array(
    *                       't0.subject as "t0.subject"'
    *                   )
    *               )
    *           )
    *       )
    *   @return array Array of SELECT strings
    */
    protected function createEqualSelects($arSelect)
    {
        $arNewSelect = array();
        if (count($arSelect) == 1) {
            if ($arSelect[0] == array(array())) {
                //ASK and COUNT
                return array(array(''));
            }

            foreach ($arSelect[0] as $arTripleVars) {
                $ar = array();
                foreach ($arTripleVars as $arVarParts) {
                    $ar[] = implode(', ', $arVarParts);
                }
                if (count($ar) > 0) {
                    $arNewSelect[0][] = implode(', ', $ar);
                }
            }
            return $arNewSelect;
        }

        $arVars = array();
         foreach ($arSelect as $arUnionVars) {
            foreach ($arUnionVars as $arTripleVars) {
                $arVars = array_merge($arVars, array_keys($arTripleVars));
            }
        }
        $arVars = array_unique($arVars);

        foreach ($arSelect as $nUnionCount => $arUnionVars) {
            $arSelectVars = array();
            foreach ($arUnionVars as $arTripleVars) {
                foreach ($arTripleVars as $strVar => $arVarParts) {
                    $arSelectVars[$strVar] = $arVarParts;
                }
            }

            $ars = array();
            foreach ($arVars as $strVar) {
                if (isset($arSelectVars[$strVar])) {
                    $ar     = $arSelectVars[$strVar];
                    $nCount = count($arSelectVars[$strVar]);
                } else {
                    $ar     = array();
                    $nCount = 0;
                }

                if ($nCount == 0) {
                    //nothing of this variable in this union part
                    $ar[] = 'NULL as '
                        . '"' . $this->arVarAssignments[$strVar][0] . '.' . $this->arVarAssignments[$strVar]['sql_value'] . '"';
                }
                if ((
                    isset($this->arUsedVarTypes[$strVar]['o'])
                    || isset($this->arUsedVarTypes[$strVar]['s'])
                    ) && $nCount < 2
                ) {
                    //it's a subject or object, but we don't want the type
                    $ar[] = 'NULL as '
                        . '"' . $this->arVarAssignments[$strVar][0] . '.' . $this->arVarAssignments[$strVar]['sql_is'] . '"';
                }
                if (isset($this->arUsedVarTypes[$strVar]['o']) && $nCount < 4) {
                    //it's a subject or object, but we don't want the type
                    if (isset($this->arVarAssignments[$strVar]['sql_lang'])) {
                        $strColLanguage = $this->arVarAssignments[$strVar]['sql_lang'];
                    } else {
                        $strColLanguage = 'dummyLang';
                    }
                    if (isset($this->arVarAssignments[$strVar]['sql_type'])) {
                        $strColDatatype = $this->arVarAssignments[$strVar]['sql_type'];
                    } else {
                        $strColDatatype = 'dummyType';
                    }
                    $ar[] = 'NULL as '
                        . '"' . $this->arVarAssignments[$strVar][0] . '.' . $strColLanguage . '"';
                    $ar[] = 'NULL as '
                        . '"' . $this->arVarAssignments[$strVar][0] . '.' . $strColDatatype . '"';
                }
                $ars[] = implode(', ', $ar);
            }
            $arNewSelect[$nUnionCount] = $ars;
        }

        return $arNewSelect;
    }//protected function createEqualSelects($arSelect)



    /**
    *   Creates an SQL statement that checks for the value
    *   of some subject/predicate/object
    *
    *   @param mixed    $bject          subject|predicate|object
    *   @param string   $strTablePrefix Table prefix (e.g. "t0")
    *   @param string   $strType        Type of $bject ('subject'|'predicate'|'object')
    *   @return string  Part of the SQL query (prefixed with AND)
    */
    function getSqlCondition($bject, $strTablePrefix, $strType)
    {
        if (is_string($bject)) {
            if (SparqlVariable::isVariable($bject)) {
                //variable?
                if (self::isPreparedVariable($bject)) {
                    //no, not really
                    $value = $this->getPreparedVariablePlaceholder($bject);
                } else {
                    //yes
                    return null;
                }
            } else {
                $value = $this->dbConn->qstr($bject);
            }
            //literal
            return ' AND ' . $strTablePrefix . '.' . $strType . ' = ' . $value;
        }

        if ($bject instanceof BlankNode) {
            //Blank node
            throw new SparqlEngineDb_SqlGeneratorException(
                'FIXME: Querying for blank nodes not supported'
            );

        } else if ($bject instanceof Resource) {
            //Resource
            $r = ' AND ' . $strTablePrefix . '.' . $strType . ' = '
                . $this->dbConn->qstr($bject->getURI());
            if ($strType !== 'predicate') {
                $r .= ' AND ' . $strTablePrefix . '.' . $strType . '_is ='
                . ' "r"';
            }
            return $r;

        } else if ($bject instanceof Literal) {
            //Literal
            //I'm doubling Filter code here, but what the hell
            $strColDatatype = $strTablePrefix . '.l_datatype';
            if ($bject->dtype == 'http://www.w3.org/2001/XMLSchema#integer'
             || $bject->dtype == 'http://www.w3.org/2001/XMLSchema#double'
            ) {
                $strVariable = 'CAST(' . $strTablePrefix . '.' . $strType . ' AS DECIMAL(15,10))';
                $strValue    = $bject->getLabel();
            } else {
                $strVariable = $strTablePrefix . '.' . $strType;
                $strValue    = $this->dbConn->qstr($bject->getLabel());
            }
            $r = ' AND ' . $strVariable . ' = ' . $strValue;
            if ($strType !== 'predicate') {
                $r .= ' AND ' . $strTablePrefix . '.' . $strType . '_is ='
                . ' "l"';
            }

            if ($strType == 'object') {
                if ($bject->dtype == '' || $bject->dtype == 'http://www.w3.org/2001/XMLSchema#string') {
                    //string
                    $r .= ' AND ('
                        . $strColDatatype . ' = ""'
                        . ' OR ' . $strColDatatype . ' = "http://www.w3.org/2001/XMLSchema#string"'
                        . ')';
                } else {
                    $r .= ' AND ' . $strColDatatype . ' = "'
                        . $bject->dtype
                        . '"';
                }
            }

            if ($bject->lang != '') {
                $strColLanguage = $strTablePrefix . '.l_language';
                $r .= ' AND ' . $strColLanguage . ' = '
                   . $this->dbConn->qstr($bject->lang);
            }
            return $r;

        } else {
            throw new SparqlEngineDb_SqlGeneratorException(
                'Unsupported sentence part: ' . get_class($bject)
            );
        }
    }//function getSqlCondition($bject, $strTablePrefix, $strType)



    /**
    *   Checks if the sentence part (subject, predicate or object) in
    *   $arNew has the same content as $arOld.
    *   Required for queries like ":x ?a ?a" where predicate and object
    *   need to have the same value
    *
    *   @param array    $arNew  array($strTablePrefix, $strType = s|p|o)
    *   @param array    $arOld  array($strTablePrefix, $strType = s|p|o)
    *   @return string
    */
    protected static function getSqlEqualityCondition($arNew, $arOld)
    {
        $chTypeNew         = $arNew[1]; $chTypeOld         = $arOld[1];
        $strTablePrefixNew = $arNew[0]; $strTablePrefixOld = $arOld[0];

        if ($chTypeNew == 'p' || $chTypeOld == 'p') {
            //just check value
            //FIXME: it might be I need to check for resource type in object and subject
            return
                  $strTablePrefixNew . '.' . self::$arTableColumnNames[$chTypeNew]['value']
                . ' = '
                . $strTablePrefixOld . '.' . self::$arTableColumnNames[$chTypeOld]['value']
                ;
        } else if ($chTypeNew == 's' || $chTypeOld == 's') {
            //check value and type
            return
                  $strTablePrefixNew . '.' . self::$arTableColumnNames[$chTypeNew]['value']
                . ' = '
                . $strTablePrefixOld . '.' . self::$arTableColumnNames[$chTypeOld]['value']
                . ' AND '
                . $strTablePrefixNew . '.' . self::$arTableColumnNames[$chTypeNew]['is']
                . ' = '
                . $strTablePrefixOld . '.' . self::$arTableColumnNames[$chTypeOld]['is']
                ;
        } else {
            //two objects -> check everything
            return
                  $strTablePrefixNew . '.object = '     . $strTablePrefixOld . '.object'
                . ' AND '
                . $strTablePrefixNew . '.object_is = '  . $strTablePrefixOld . '.object_is'
                . ' AND '
                . $strTablePrefixNew . '.l_language = ' . $strTablePrefixOld . '.l_language'
                . ' AND '
                . $strTablePrefixNew . '.l_datatype = ' . $strTablePrefixOld . '.l_datatype'
                ;
        }
    }//protected static function getSqlEqualityCondition($arNew, $arOld)



    /**
    *   Checks if the given variable name is part of the result
    *   variables list.
    *   Needed since $arResultVars may contain "*" that captures all variables.
    *
    *   @param string   $strVar         Variable name (e.g. "?p")
    *   @param array    $arResultVars   Array with result variables
    *   @return boolean     true if it is a result variable
    */
    protected static function isResultVar($strVar, &$arResultVars)
    {
        foreach ($arResultVars as $var) {
            if ($var == '*') {
                return true;
            } else if ((is_string($var) && $var == $strVar)
                || (is_object($var) && $var->getVariable() == $strVar)) {
                return true;
            }
        }
        return false;
    }//protected static function isResultVar($strVar, &$arResultVars)



    /**
    *   Checks if the given variable is a replacement
    *   for a prepared statement.
    *
    *   @return boolean
    */
    public static function isPreparedVariable($bject)
    {
        return is_string($bject) && strlen($bject) >= 3
             && ($bject[0] == '?' || $bject[0] == '$')
             && ($bject[1] == '?' || $bject[1] == '$')
        ;
    }//public static function isPreparedVariable($bject)



    /**
    *   Returns a placeholder to be included in the sql statement.
    *   It will be replaced with a real prepared statement variable later on.
    *   Also adds it to the internal placeholder array.
    *
    *   @param string $strVariable  The variable to get a placeholder for
    *   @return string placeholder
    */
    protected function getPreparedVariablePlaceholder($strVariable)
    {
        $strName = substr($strVariable, 2);
        if (!isset($this->arPlaceholders[$strName])) {
            $this->arPlaceholders[$strName] = '@$%_PLACEHOLDER_'
                . count($this->arPlaceholders) . '_%$@';
        }
        return $this->arPlaceholders[$strName];
    }//protected function getPreparedVariablePlaceholder($strVariable)



    public function getPlaceholders()
    {
        return $this->arPlaceholders;
    }//public function getPlaceholders()



    public function getVarAssignments()
    {
        return $this->arVarAssignments;
    }//public function getVarAssignments()



    public function getUsedVarAssignments()
    {
        return $this->arUsedVarAssignments;
    }//public function getUsedVarAssignments()



    public function getUsedVarTypes()
    {
        return $this->arUsedVarTypes;
    }//public function getUsedVarTypes()



    /**
    *   Removes all NULL values from an array and returns it.
    *
    *   @param array $array     Some array
    *   @return array $array without the NULL values.
    */
    protected static function removeNull($array)
    {
        foreach ($array as $key => &$value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }
        return $array;
    }//protected static function removeNull($array)



    /**
    *   Removes a leading AND from the where clause which would render
    *   the sql illegal.
    */
    protected function fixWhere($strWhere)
    {
        $strWhere = ltrim($strWhere);
        if (substr($strWhere, 0, 4) == 'AND ') {
            $strWhere = substr($strWhere, 4);
        }
        return $strWhere;
    }//protected function fixWhere($strWhere)



    protected function getSqlVariableName($var)
    {
        $strSparqlVar = (string)$var;
        if (!isset($this->arVarAssignments[$strSparqlVar]['sqlname'])) {
            if (preg_match('/[a-zA-Z0-9]+/', substr($strSparqlVar, 1))) {
                $strName = 'v_' . substr($strSparqlVar, 1);
            } else {
                $strName = 'va_' . $this->nSqlVariableNameCount++;
            }
            $this->arVarAssignments[$strSparqlVar]['sqlname'] = $strName;
        }
        return $this->arVarAssignments[$strSparqlVar]['sqlname'];
    }//protected function getSqlVariableName($var)



    protected function getSqlVariableNameValue($var)
    {
        $strSparqlVar = (string)$var;
        $this->arVarAssignments[$strSparqlVar]['sql_value'] =
            'value_' . $this->getSqlVariableName($var);
        return $this->arVarAssignments[$strSparqlVar]['sql_value'];
    }//protected function getSqlVariableNameValue($var)



    protected function getSqlVariableNameIs($var)
    {
        $strSparqlVar = (string)$var;
        $this->arVarAssignments[$strSparqlVar]['sql_is'] =
            'is_' . $this->getSqlVariableName($var);
        return $this->arVarAssignments[$strSparqlVar]['sql_is'];
    }//protected function getSqlVariableNameIs($var)



    protected function getSqlVariableNameLanguage($var)
    {
        $strSparqlVar = (string)$var;
        $this->arVarAssignments[$strSparqlVar]['sql_lang'] =
            'lang_' . $this->getSqlVariableName($var);
        return $this->arVarAssignments[$strSparqlVar]['sql_lang'];
    }//protected function getSqlVariableNameLanguage($var)



    protected function getSqlVariableNameDatatype($var)
    {
        $strSparqlVar = (string)$var;
        $this->arVarAssignments[$strSparqlVar]['sql_type'] =
            'type_' . $this->getSqlVariableName($var);
        return $this->arVarAssignments[$strSparqlVar]['sql_type'];
    }//protected function getSqlVariableNameDatatype($var)



    public function setStatementsTable($tblStatements)
    {
        $this->tblStatements = $tblStatements;
    }//public function setStatementsTable($tblStatements)

}//class SparqlEngineDb_SqlGenerator
?>