<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlVariable.php';

/**
*   Generates SQL from Sparql FILTER parts
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_FilterGenerator
{
    /**
    *   SQL Generator
    *   @var SparqlEngineDb_SqlGenerator
    */
    protected $sg = null;

    /**
    *   If the filter is in an optional statement
    *   @var boolean
    */
    protected $bOptional    = false;

    /**
    *   Number of parameters for the functions supported.
    *   First value is minimum, second is maximum.
    *   @var array
    */
    protected static $arFuncParamNumbers = array(
        'bound'         => array(1, 1),
        'datatype'      => array(1, 1),
        'isblank'       => array(1, 1),
        'isiri'         => array(1, 1),
        'isliteral'     => array(1, 1),
        'isuri'         => array(1, 1),
        'lang'          => array(1, 1),
        'langmatches'   => array(2, 2),
        'regex'         => array(2, 3),
        'sameterm'      => array(2, 2),
        'str'           => array(1, 1),
        'xsd:datetime'  => array(1, 1),
    );

    /**
    *   List of operators and their counterpart if operands are switched.
    *   (a > b) => (b < a)
    *   @var array
    */
    protected static $arOperatorSwitches = array(
        '>'     => '<',
        '<'     => '>',
        '>='    => '<=',
        '<='    => '>=',
    );

    protected static $arDumbOperators = array(
        '&&', '||'
    );

    protected static $typeXsdBoolean  = 'http://www.w3.org/2001/XMLSchema#boolean';
    protected static $typeXsdDateTime = 'http://www.w3.org/2001/XMLSchema#dateTime';
    protected static $typeXsdDouble   = 'http://www.w3.org/2001/XMLSchema#double';
    protected static $typeXsdInteger  = 'http://www.w3.org/2001/XMLSchema#integer';
    protected static $typeXsdString   = 'http://www.w3.org/2001/XMLSchema#string';
    protected static $typeVariable    = 'variable';



    public function __construct(SparqlEngineDb_SqlGenerator $sg)
    {
        $this->sg = $sg;
    }//public function __construct(SparqlEngineDb_SqlGenerator $sqlgen)



    /**
    *   Creates the SQL representing a Sparql FILTER.
    *
    *   @param array $tree  Filter element tree as returned by
    *                       SparqlParser::parseConstraintTree()
    *   @param boolean $bOptional   If the filter is in an optional statement
    *   @return string  SQL WHERE part with prefixed AND
    */
    public function createFilterSql($tree, $bOptional, $nUnionCount)
    {
        if (count($tree) == 0) {
            //empty filter pattern?
            return '';
        }

        $this->bOptional = $bOptional;
        $this->nUnionCount = $nUnionCount;

        return ' AND ' . $this->createTreeSql($tree, null);
    }//public function createFilterSql($tree)



    /**
    *
    */
    protected function createTreeSql($tree, $parent)
    {
        switch ($tree['type']) {
            case 'equation':
                $sql = $this->createEquation($tree);
                break;
            case 'value':
                if ($parent != null) {
                    $bDumbParent = $parent['type'] == 'equation'
                        && in_array($parent['operator'], self::$arDumbOperators);
                } else {
                    $bDumbParent = true;
                }
                $sql = $this->createValue($tree, $bDumbParent);
                break;
            case 'function':
                $sql = $this->createFunction($tree);
                break;
            default:
                //var_dump($tree);
                throw new SparqlEngineDb_SqlGeneratorException('Unsupported tree type: ' . $tree['type']);
                break;
        }

        if (isset($tree['negated'])) {
            $sql = '!(' . $sql . ')';
        }

        return $sql;
    }//protected function createTreeSql($tree, $strParentType)



    /**
    *   Creates the sql for an element of type "value".
    *
    *   @param array $tree  Element
    *   @param boolean $bDumbParent True if the parent is a boolean equation or null.
    */
    protected function createValue($tree, $bDumbParent)
    {
        $strValue = stripslashes($tree['value']);
        if (SparqlVariable::isVariable($strValue)) {
            if (!isset($this->sg->arUnionVarAssignments[$this->nUnionCount][$strValue])) {
                throw new SparqlEngineDb_SqlGeneratorException(
                    'Unknown variable in filter: ' . $strValue
                );
            }

            if ($bDumbParent) {
                return $this->createBooleanValue($tree);
            } else if ($this->isObject($tree)) {
                //convert datetime to datetime if necessary
                return self::mkVal(
                    '(CASE'
                    . ' WHEN ' . $this->getDatatypeCol($tree) . ' = "' . self::$typeXsdBoolean . '"'
                        . ' THEN IF(LOWER(' . $this->getValueCol($tree) . ') = "true", TRUE, FALSE)'
                    . ' ELSE ' . $this->getValueCol($tree)
                    . ' END)',
                    self::$typeVariable
                );
            } else {
                return self::mkVal(
                    $this->getValueCol($tree),
                    self::$typeVariable
                );
            }
        }

        if ($this->isNumber($tree)) {
            return $strValue;
        } else if ($tree['quoted'] === false &&
          (!isset($tree['datatype']) || $tree['datatype'] != self::$typeXsdBoolean)
        ) {
            $strValueNew = $this->sg->query->getFullUri($strValue);
            if ($strValueNew === false) {
                if ($strValue[0] == '<' && substr($strValue, -1) == '>') {
                    $strValue = substr($strValue, 1, -1);
                } else {
var_dump($tree);
                    throw new SparqlEngineDb_SqlGeneratorException(
                        'Unexpected value "' . $strValue . '" (expected datatype)'
                    );
                }
            } else {
                $strValue = $strValueNew;
            }
        } else if (isset($tree['datatype'])) {
            switch ($tree['datatype']) {
                case self::$typeXsdBoolean:
                    if (strtolower($strValue) == 'false') {
                        //fix: (bool)"false" === true
                        $strValue = false;
                    }
                    return self::mkVal(
                        (bool)$strValue ? 'TRUE' : 'FALSE',
                        self::$typeXsdBoolean
                    );

                default:
                    throw new SparqlEngineDb_SqlGeneratorException(
                        'Unsupported datatype "' . $tree['datatype']
                    );
            }
        }

        return self::mkVal(
            '"' . addslashes($strValue) . '"',
            self::$typeXsdString
        );
    }//protected function createValue($tree)



    protected function createBooleanValue($tree)
    {
        list($strTable, $chType) = $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']];
        if (!$this->isObject($tree)) {
            //Maybe needs a fix
            $strSqlCol = SparqlEngineDb_SqlGenerator::$arTableColumnNames[$chType]['value'];
            return self::mkVal(
                $strTable . '.' . $strSqlCol . ' != ""',
                self::$typeXsdBoolean
            );
        }

        $cType  = $strTable . '.l_datatype';
        $cValue = $strTable . '.object';
        $xsd    = 'http://www.w3.org/2001/XMLSchema';

        return self::mkVal(
            '('
            . "(($cType = '' || $cType = '$xsd#string') AND $cValue != '')"
            . " OR ($cType = '$xsd#boolean' AND $cValue != 'false')"
            . " OR (($cType = '$xsd#integer' || $cType = '$xsd#double') AND CAST($cValue AS DECIMAL(15,10)) != 0)"
            //plain check for all unknown datatypes
            . " OR ($cType != '' AND $cType != '$xsd#string' AND $cType != '$xsd#boolean' AND $cType != '$xsd#integer' AND $cType != '$xsd#double' AND $cValue != '')"
            . ')',
            self::$typeXsdBoolean
        );
    }//protected function createBooleanValue($tree)



    protected function createFunction($tree)
    {
        $strFuncName = strtolower($tree['name']);
        if (!isset(self::$arFuncParamNumbers[$strFuncName])) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'Unsupported FILTER function: ' . $strFuncName
            );
        }

        $nParams     = count($tree['parameter']);
        if ($nParams < self::$arFuncParamNumbers[$strFuncName][0]
         || $nParams > self::$arFuncParamNumbers[$strFuncName][1]
        ) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'Wrong parameter count for FILTER function: ' . $strFuncName
                . ' (got ' . $nParams . ', expected '
                . self::$arFuncParamNumbers[$strFuncName][0]
                . '-' . self::$arFuncParamNumbers[$strFuncName][1]
            );
        }

        $strThisFunc = 'createFunction_' . str_replace(':', '_', $strFuncName);
        return $this->$strThisFunc($tree);
    }//protected function createFunction($tree)



    protected function createEquation($tree)
    {
        $strExtra       = '';
        $strIsNull      = '';
        $strIsNullEnd   = '';

        if (($this->isNumber($tree['operand1']) || $this->isPlainString($tree['operand1']))
            && $this->isObject($tree['operand2'])
        ) {
            //switch operands and operator
            $tmp = $tree['operand1'];
            $tree['operand1'] = $tree['operand2'];
            $tree['operand2'] = $tmp;
            $tree['operator'] = self::switchOperator($tree['operator']);
        }

        $val1 = $this->createTreeSql($tree['operand1'], $tree);
        $val2 = $this->createTreeSql($tree['operand2'], $tree);

        if ($this->isObject($tree['operand1'])) {
            if ($this->bOptional) {
                $strIsNull    = '(';
                $strIsNullEnd = ' OR ' . $val1 . ' IS NULL)';
            }
            if (isset($tree['operand2']['language'])) {
                $strColLanguage = $this->getLangCol($tree['operand1']);
                $strExtra .= ' AND ' . $strColLanguage . ' = "'
                    . addslashes($tree['operand2']['language'])
                    . '"';
            }

            if ($this->isNumber($tree['operand2'])) {
                $strColDatatype = $this->getDatatypeCol($tree['operand1']);
                $strExtra .= ' AND (' . $strColDatatype . '  = "' . self::$typeXsdDouble . '"'
                    . ' OR ' . $strColDatatype . '  = "' . self::$typeXsdInteger . '"'
                    . ')';
                return $strIsNull . '('
                    . 'CAST(' . $val1 . ' AS DECIMAL(15,10))'
                    . ' ' . $tree['operator'] . ' '
                    . $val2
                    . $strExtra
                    . ')' . $strIsNullEnd;
            }
        }

        //I don't check the operator since it is already checked in the parser
        return $strIsNull . '('
            . '('
                . $val1
                . ' ' . $tree['operator'] . ' '
                . $val2
                . $strExtra
            . ')'
            . $this->createTypeEquation(
                $tree['operator'], $val1, $val2,
                $tree['operand1'], $tree['operand2']
            )
            . ')' . $strIsNullEnd;
    }//protected function createEquation($tree)



    /**
    *   Generates sql code to make sure the datatypes of the two operands match
    */
    protected function createTypeEquation($operator, $val1, $val2, $tree1, $tree2)
    {
        if (in_array($operator, array('&&', '||'))) {
            return '';
        }

        if ($val1->type != self::$typeVariable && $val2->type != self::$typeVariable) {
            //both are not variables -> type needs to be the same
            return $val1->type == $val2->type ? '' : ' AND FALSE';
        }
        $so1 = $this->isObjectOrSubject($tree1);
        $so2 = $this->isObjectOrSubject($tree2);
        $o1  = $this->isObject($tree1);
        $o2  = $this->isObject($tree2);

        if ($so1 && $so2) {
            $sql = ' AND ' . $this->getIsCol($tree1) . ' = ' . $this->getIsCol($tree2);
            if ($o1 && $o2) {
                //maybe needs string fix
                $sql .= ' AND ' . $this->getDatatypeCol($tree1) . ' = '
                    . $this->getDatatypeCol($tree2);
            }
            return $sql;
        }

        if ($o1 && $val2->type != self::$typeVariable) {
            return $this->createSingleTypeCheck($val2, $tree1);
        } else if ($o2 && $val1->type != self::$typeVariable) {
            return $this->createSingleTypeCheck($val1, $tree2);
        }

        //fixme
        return '';
    }//protected function createTypeEquation($operator, $val1, $val2, $tree1, $tree2)



    /**
    *   Creates sql to ensure the type between $val and variable $tree
    *   is the same.
    */
    protected function createSingleTypeCheck($val, $tree)
    {
        if ($val->type == self::$typeXsdString) {
            //string can be empty type or xsd type
            return ' AND ('
                . $this->getDatatypeCol($tree) . ' = "' . $val->type . '"'
                . ' OR '
                . $this->getDatatypeCol($tree) . ' = ""'
                . ')';
        }
        return ' AND ' . $this->getDatatypeCol($tree) . ' = "' . $val->type . '"';
    }//protected function createSingleTypeCheck($val, $tree)



    /*
    *   SQL generation functions
    */



    /**
    *   Creates an sql statement that returns if the element is a blank node
    */
    protected function createFunction_bound($tree)
    {
        if ($this->isVariable($tree['parameter'][0])) {
            return self::mkVal(
                $this->getValueCol($tree['parameter'][0]) . ' IS NOT NULL',
                self::$typeXsdBoolean
            );
        }

        //We'll see which other cases occur here
        return self::mkVal('TRUE', self::$typeXsdBoolean);
    }//protected function createFunction_bound($tree)



    protected function createFunction_datatype($tree)
    {
        if (!$this->isObject($tree['parameter'][0])) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'datatype\'s first parameter needs to be an object'
            );
        }

        return self::mkVal(
            $this->getDatatypeCol($tree['parameter'][0]),
            self::$typeXsdString
        );
    }//protected function createFunction_datatype($tree)



    /**
    *   Creates an sql statement that returns if the element is a blank node
    */
    protected function createFunction_isblank($tree)
    {
        if (!$this->isObjectOrSubject($tree['parameter'][0])) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'isBlank\'s first parameter needs to be an object or subject'
            );
        }

        return self::mkVal(
            $this->getIsCol($tree['parameter'][0]) . ' = "b"',
            self::$typeXsdBoolean
        );
    }//protected function createFunction_isblank($tree)



    protected function createFunction_isiri($tree)
    {
        return $this->createFunction_isuri($tree);
    }//protected function createFunction_isiri($tree)



    /**
    *   Creates an sql statement that returns the language of the object
    */
    protected function createFunction_isliteral($tree)
    {
        if ($this->isObjectOrSubject($tree['parameter'][0])) {
            return $this->getIsCol($tree['parameter'][0]) . ' = "l"';
        }

        //This does not take combined functions into account (subfunctions)
        return self::mkVal(
            $this->isPlainString($tree) ? 'TRUE' : 'FALSE',
            self::$typeXsdBoolean
        );
    }//protected function createFunction_isliteral($tree)



    protected function createFunction_isuri($tree)
    {
        if ($this->isObjectOrSubject($tree['parameter'][0])) {
            return self::mkVal(
                $this->getIsCol($tree['parameter'][0]) . ' = "r"',
                self::$typeXsdBoolean
            );
        } else {
            //predicates are iris
            return self::mkVal('TRUE', self::$typeXsdBoolean);
        }
    }//protected function createFunction_isuri($tree)



    /**
    *   Creates an sql statement that returns the language of the object
    */
    protected function createFunction_lang($tree)
    {
        if (!$this->isObject($tree['parameter'][0])) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'lang\'s first parameter needs to be an object'
            );
        }
        return self::mkVal(
            $this->getLangCol($tree['parameter'][0]),
            self::$typeXsdString
        );
    }//protected function createFunction_lang($tree)



    /**
    *   Creates an sql statement that checks if the variable
    *   matches a given language
    */
    protected function createFunction_langmatches($tree)
    {
        //those two restrictions are needed until we have a mysql-only
        // langmatches function
        if ($tree['parameter'][0]['type'] != 'function'
         || $tree['parameter'][0]['name'] != 'lang'
        ) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'langMatches\' first parameter needs to be a lang() function'
            );
        }

        if (!$this->isPlainString($tree['parameter'][1])) {
            throw new SparqlEngineDb_SqlGeneratorException(
                'langMatches\' second parameter needs to be a string'
            );
        }

        $lang  = $tree['parameter'][1]['value'];
        $col   = $this->createTreeSql($tree['parameter'][0], $tree);

        switch ($lang) {
            case '*':
                //anything but nothing
                $sql = $col . ' != ""';
                break;
            case '':
                //nothing
                $sql = $col . ' = ""';
                break;
            default:
                //language, maybe with different subcode
                // en -> en, en-US
                $sql = '(' . $col . ' = "' . addslashes($lang) . '" OR '
                    . $col . ' LIKE "' . addslashes($lang) . '-%")';
                break;
        }

        return self::mkVal($sql, self::$typeXsdBoolean);
    }//protected function createFunction_isuri($tree)



    /**
    *   Creates an sql statement that checks if the given part matches
    *   an regex
    */
    protected function createFunction_regex($tree)
    {
        $strVar   = $this->createTreeSql($tree['parameter'][0], $tree);
        $strRegex = $this->createTreeSql($tree['parameter'][1], $tree);

        if (isset($tree['parameter'][2])) {
            //is quoted
            $strMod = $this->createTreeSql($tree['parameter'][2], $tree);
            switch ($strMod) {
                case '':
                case '""':
                    $sql = $strVar . ' REGEXP ' . $strRegex;
                    break;

                case '"i"':
                    $sql = 'CAST(' . $strVar . ' AS CHAR) REGEXP ' . $strRegex;
                    break;

                default:
                    throw new SparqlEngineDb_SqlGeneratorException(
                        'Unsupported regex modifier "'
                        . $strMod
                        . '"'
                    );
            }
        } else {
            $sql = $strVar . ' REGEXP ' . $strRegex;
        }

        if ($this->isObject($tree['parameter'][0])) {
            $col = $this->getIsCol($tree['parameter'][0]);
            $sql = "($sql AND $col = 'l')";
        }

        return self::mkVal($sql, self::$typeXsdBoolean);
    }//protected function createFunction_regex($tree)



    /**
    *   Creates an sql statement that checks if both terms are the same
    */
    protected function createFunction_sameterm($tree)
    {
        //FIXME: dead simple implementation that does not cover all cases
        return self::mkVal(
            $this->createTreeSql($tree['parameter'][0], $tree)
                . ' = '
                . $this->createTreeSql($tree['parameter'][1], $tree),
            self::$typeXsdBoolean
        );
    }//protected function createFunction_sameterm($tree)



    /**
    *   Creates an sql statement that returns the string representation
    *   of the given element
    */
    protected function createFunction_str($tree)
    {
        if ($this->isObject($tree['parameter'][0])) {
            return self::mkVal(
                '(CASE ' . $this->getIsCol($tree['parameter'][0])
                . ' WHEN "b" THEN ""'
                . ' ELSE ' . $this->getValueCol($tree['parameter'][0])
                . ' END)',
                self::$typeXsdString
            );
        }

        return $this->createTreeSql($tree['parameter'][0], $tree);
    }//protected function createFunction_str($tree)



    /**
    *   Creates an sql statement that returns the datetime representation
    *   of the given element
    */
    protected function createFunction_xsd_datetime($tree)
    {
        $val = $this->createTreeSql($tree['parameter'][0], $tree);
        if ($val->type == self::$typeXsdDateTime
         || $val->type == self::$typeVariable) {
            //no need to cast again
            return $val;
        }
        return self::mkVal(
            $this->getDateConversionSql($val),
            self::$typeXsdDateTime
        );
    }//protected function createFunction_xsd_datetime($tree)




    /*
    *   Helper methods
    */



    protected static function mkVal($value, $type = null) {
        return new SparqlEngineDb_FilterGenerator_Value($value, $type);
    }



    protected function getCol($tree, $type)
    {
        list($strTable, $chType) = $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']];
        if (!isset(SparqlEngineDb_SqlGenerator::$arTableColumnNames[$chType][$type])) {
            return false;
        }
        $strSqlCol = SparqlEngineDb_SqlGenerator::$arTableColumnNames[$chType][$type];
        return $strTable . '.' . $strSqlCol;
    }



    protected function getDatatypeCol($tree)
    {
        list($strTable, $chType) = $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']];
        return $strTable . '.l_datatype';
    }



    protected function getDateConversionSql($strValue)
    {
        return 'STR_TO_DATE(' . $strValue . ', "%Y-%m-%dT%H:%i:%sZ")';
    }//protected function getDateConversionSql($strValue)



    protected function getIsCol($tree)
    {
        return $this->getCol($tree, 'is');
    }



    protected function getLangCol($tree)
    {
        list($strTable, $chType) = $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']];
        return $strTable . '.l_language';
    }



    protected function getValueCol($tree)
    {
        return $this->getCol($tree, 'value');
    }



    protected function isNumber($tree)
    {
        return $tree['type'] == 'value'
         && $tree['quoted'] === false
         && preg_match('#^[0-9.]+$#', $tree['value'])
         && floatval($tree['value']) == $tree['value'];
    }//protected function isNumber($tree)



    /**
    *   Checks if the given tree element is a variable
    *   and the variable is actually an object in the database.
    *
    *   @param array $tree  Tree element
    *   @return boolean True if the element is an object
    */
    protected function isObject($tree)
    {
        return $this->isVariable($tree)
         && $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']][1] == 'o';
    }//protected function isVariable($tree)



    /**
    *   Checks if the given tree element is a variable
    *   and the variable is actually an object or a subject in the database.
    *
    *   @param array $tree  Tree element
    *   @return boolean True if the element is an object or an subject
    */
    protected function isObjectOrSubject($tree)
    {
        return $this->isVariable($tree)
         && (
            $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']][1] == 'o'
         || $this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']][1] == 's'
        );
    }//protected function isObjectOrSubject($tree)



    /**
    *   Checks if the given tree element is a plain string (no variable)
    *
    *   @param array $tree  Tree element
    *   @return boolean True if the element is a string
    */
    protected function isPlainString($tree)
    {
        return $tree['type'] == 'value'
         && $tree['quoted'] === true;
    }//protected function isPlainString($tree)



    protected function isValueButNotVariableNorString($tree)
    {
        return $tree['type'] == 'value'
         && $tree['type']['quoted'] === false
         && !SparqlVariable::isVariable($tree['value']);
    }//protected function isValueButNotVariableNorString($tree)



    /**
    *   Checks if the given tree element is a variable
    *
    *   @param array $tree  Tree element
    *   @return boolean True if the element is a variable
    */
    protected function isVariable($tree)
    {
        return $tree['type'] == 'value'
         && $tree['quoted'] === false
         && SparqlVariable::isVariable($tree['value'])
         && isset($this->sg->arUnionVarAssignments[$this->nUnionCount][$tree['value']]);
    }//protected function isVariable($tree)



    protected static function switchOperator($op)
    {
        if (!isset(self::$arOperatorSwitches[$op])) {
            return $op;
        } else {
            return self::$arOperatorSwitches[$op];
        }
    }//protected static function switchOperator($op)

}//class SparqlEngineDb_FilterGenerator




/**
*   Value class that holds some arbitrary value
*   and a datatype.
*   Objects of this class can transparently be used in strings since
*   its __toString() returns the value.
*/
class SparqlEngineDb_FilterGenerator_Value
{
    public $value = null;
    public $type  = null;



    public function __construct($value, $type = null)
    {
        $this->value = $value;
        $this->type  = $type;
    }



    public function __toString()
    {
        return $this->value;
    }

}//class SparqlEngineDb_FilterGenerator_Value

?>