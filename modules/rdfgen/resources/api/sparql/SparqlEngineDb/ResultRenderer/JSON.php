<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   Sparql DB JSON Renderer. Roughly follows http://www.w3.org/2001/sw/DataAccess/json-sparql/;
*
*   @author Christoph Rieß
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_ResultRenderer_JSON implements SparqlEngineDb_ResultRenderer
{

    /**
    *   Converts the database results into JSON Format
    *
    *   @param array $arRecordSets  Array of (possibly several) SQL query results.
    *   @param Query $query     SPARQL query object
    *   @param SparqlEngineDb $engine   Sparql Engine to query the database
    *   @return mixed   HTML code
    */
    public function convertFromDbResults($arRecordSets, Query $query, SparqlEngineDb $engine) {
    	
        $this->query = $query;
        $this->sg    = $engine->getSqlGenerator();
        $strResultForm = $query->getResultForm();
        
        foreach ($this->getResultVars() as $var)
        	$ResultVarsTemp[] = substr($var,1);
        
        switch ($strResultForm) {
            case 'select':
            case 'select distinct':
                $results = $this->createFromRecords($arRecordSets, $strResultForm);
				$strCode = json_encode(array(
				'head' => array('vars'=>$ResultVarsTemp),
				'results'=>array('bindings'=>$results))
				);
				//$strCode = str_replace(',{',','.PHP_EOL.'{',$strCode);
                break;
			case 'construct':
            case 'describe':
                throw new Exception(
                    'Construct and describe are not supported by the'
                    . ' JSON renderer'
                );

            case 'count':
            case 'ask':
                if (count($arRecordSets) > 1) {
                    throw new Exception(
                        'More than one result set for a '
                        . $strResultForm . ' query not supported by JSON Renderer'
                    );
                }

                $nCount = 0;
                $dbRecordSet = reset($arRecordSets);
                foreach ($dbRecordSet as $row) {
                    $nCount += intval($row['count']);
                    break;
                }

                if ($strResultForm == 'ask') {
                    $strcode = json_encode(array('boolean' => ($nCount > 0)));
                } else {
                    $strcode = json_encode(array('int' => $nCount ));
                }
                break;
            default:
            	throw new Exception('Error');
        }
        
        return $strCode;
        

    }
    /**
     * Method to create from record with specific resultform (not used yet)
     *
     * @param array $arRecordSets  Array of (possibly several) SQL query results.
     * @param unknown_type $strResultForm
     * @return array ready for json conversion
     */
	protected function createFromRecords($arRecordSets, $strResultForm) {
		
		$arVarAssignments = $this->sg->arVarAssignments;
		$code = '';
		$arResultVars = $this ->getResultVars();
		$results = array();
		foreach ($arRecordSets[0] as $value) {
			$node = array();
			foreach ($arResultVars as $ResultVar) {
				$nodeType = $value[$arVarAssignments[$ResultVar][0].'.'.$arVarAssignments[$ResultVar]['sql_is']];
				if ($nodeType == 'r') {
					$node[substr($ResultVar,1)] = array('type'=> 'uri','value'=>$value[$arVarAssignments[$ResultVar][0].'.'.$arVarAssignments[$ResultVar]['sql_value']]);
					
				}
				
				if ($value[$arVarAssignments[$ResultVar][0].'.'.$arVarAssignments[$ResultVar]['sql_is']] == 'l') {
					$literalType = $value[$arVarAssignments[$ResultVar][0] . 
						'.' . $arVarAssignments[$ResultVar]['sql_type']];
					$literalLang = $value[$arVarAssignments[$ResultVar][0] . 
						'.' . $arVarAssignments[$ResultVar]['sql_lang']];
					$literalValue = $value[$arVarAssignments[$ResultVar][0] . 
						'.' . $arVarAssignments[$ResultVar]['sql_value']];
					$node[substr($ResultVar,1)] = $this->getLiteral($literalValue,$literalLang,$literalType);
				}
				
				if ($nodeType === 'b') {
					$literalValue = $value[$arVarAssignments[$ResultVar][0].'.'.$arVarAssignments[$ResultVar]['sql_value']];
					$node[substr($ResultVar,1)] = array ('type'=>'bnode' ,'value'=>$value[$arVarAssignments[$ResultVar][0].'.'.$arVarAssignments[$ResultVar]['sql_value']]);
				}
			}
			$results[]=$node;
			
			
		}
		return $results;

	}
	
	/**
	 * COnverting Literals to array ready for json
	 *
	 * @param unknown_type $value
	 * @param unknown_type $lang
	 * @param unknown_type $type
	 * @return unknown
	 */
	private function getLiteral($value, $lang , $type) {
		
		$ret = array();
		
		$type = 'literal';
		
		if ($value != 'literal') {
			$ret['value'] = $value;
		}
		
		if ($lang != '') {
			$ret['lang'] = $lang;
		}
		
		if ($type != '') {
			$ret['type'] = $type; 
		}
		
		return $ret;
	}
	
	/**
	 * Giving array of used Vars also resolves the all-quantifier *
	 *
	 * @return array of vars as strings
	 */
	protected function getResultVars() {
		$arResultVars = $this->query->getResultVars();
		if (in_array('*', $arResultVars)) {
			$arResultVars   = array_keys($this->sg->arVarAssignments);
		} else {
			$arResultVars = array();
			foreach ($this->query->getResultVars() as $Var) {
				$arResultVars[] = (string) $Var;
			}
		}
		
		return $arResultVars;
	}

}

?>