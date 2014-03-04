<?php
require_once RDFAPI_INCLUDE_DIR . 'util/Object.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/FilterFunctions.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngine/ResultConverter.php';

// ----------------------------------------------------------------------------------
// Class: SparqlEngine
// ----------------------------------------------------------------------------------

/**
* This engine executes SPARQL queries against an RDF Datatset.
*
* @version  $Id$
* @author   Tobias Gauß <tobias.gauss@web.de>
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
* @package sparql
*/

Class SparqlEngine extends Object{


    /**
    *   The query object.
    *   @var Query
    */
    protected $query;

    /**
    *   The RDF Dataset.
    *   @var Dataset
    */
    protected $dataset;



    /**
    * Use SparqlEngine::factory() instead of this
    * constructor.
    */
    protected function __construct()
    {
        //protected to prevent direct instantiation
    }


    /**
    * Creates a new instance of the SparqlEngine, depending on the
    * given model. For example, if you pass a DbModel, you will
    * get a SparqlEngine specialized on databases.
    *
    * @param Model $model   RDF model that uses the engine
    */
    public function factory($model = null)
    {
        if ($model !== null && $model instanceof DbModel) {
            require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb.php';
            return new SparqlEngineDb($model);
        } else {
            return new SparqlEngine();
        }
    }



    /**
    * The query engine's main method.
    *
    * @param  Dataset       $dataset    the RDF Dataset
    * @param  mixed         $query      the parsed SPARQL query
    * @param  String        $resultform the result form. If set to 'xml' the result will be
    *                                   SPARQL Query Results XML Format as described in http://www.w3.org/TR/rdf-sparql-XMLres/ .
    * @return Array/String  Type of the result depends on $resultform.
    */
    public function queryModel($dataset, Query $query, $resultform = false)
    {
        $this->query   = $query;
        $this->dataset = $dataset;

        if($this->query->isEmpty){
            $vartable[0]['patternResult'] = null;
            return SparqlEngine_ResultConverter::convertFromResult(
                $vartable,
                $this,
                $resultform
            );
        }

        $graphlist = $this->preselectGraphs();
        /// match graph patterns against the RDF Dataset
        $patternlist = $this->matchPatterns($graphlist);
        // filter results- apply inner filters
        $patternlist = $this->filterPatterns($patternlist,false);
        // join pattern results
        $vartable = $this->joinResults($patternlist);
        // filter results- apply outer filters
        $vartable = $this->filterPatterns($vartable,true);

        if ($vartable[0]['patternResult'] != null) {
            // sort vars (ORDER BY, LIMIT, OFFSET)
            $vartable = $this->sortVars($vartable[0]['patternResult']);
            $qrf      = $this->query->getResultForm();
            if ($qrf == 'select' || $qrf == 'select distinct') {
                $vars     = $this->query->getResultVars();
                $vartable = $this->selectVars($vartable, $vars);
                if ($qrf == 'select distinct') {
                    $vartable = $this->distinct($vartable);
                }
            }
        } else {
            $vartable = null;
        }

        return SparqlEngine_ResultConverter::convertFromResult(
            $vartable,
            $this,
            $resultform
        );
    }//public function queryModel($dataset, Query $query, $resultform = false)



    /**
    * Matches all graph Patterns against the dataset and generates an array which
    * contains the result sets for every given GraphPattern.
    *
    * @param  Array      $graphlist   the graphlist which contains the names of the named
    *                    graphs which has to be queried.
    * @return Array
    */
    protected function matchPatterns($graphlist){
        $patternlist = array();
        // get the result part from the query
        $resultPart = $this->query->getResultPart();
        // for each GrapPattern in the result part
        if($resultPart)
        foreach($resultPart as $graphPattern){
            $this->matchPattern($patternlist, $graphlist, $graphPattern);
        }
        return $patternlist;
    }



    /**
    * Finds tuples that match one graph pattern.
    *
    * @param  Array        $patternlist list that contains the graphPatterns
    * @param  array        $graphlist   the graphlist
    * @param  GraphPattern $graphPattern the pattern which has to be matched
    * @return void
    */
    protected function matchPattern(&$patternlist, $graphlist, &$graphPattern) {
        // generate an empty result set
        $finalRes = null;
        // if the GraphPattern has triple patterns
        if (count($graphPattern->getTriplePatterns()) > 0) {
            // check if the pattern has a GRAPH clause and if this Iri is in $graphlist
            $newGraphList = $this->_checkGraphs($graphPattern,$graphlist);
            if($newGraphList){
                $qt = $graphPattern->getTriplePatterns();
                $resultSet = $this->findTuplesMatchingOnePattern($qt[0], $newGraphList);
                for ($i=1; $i<count($qt); $i++) {
                    $rs = $this->findTuplesMatchingOnePattern($qt[$i], $newGraphList);
                    $resultSet = $this->joinTuples($resultSet, $rs);
                    if(!$resultSet)
                    break;
                }
                if($finalRes != null){
                    $finalRes = $this->joinTuples($finalRes,$resultSet);
                }else{
                    $finalRes = $resultSet;
                }
            }
        }
        // dependencies between pattern results
        $patternlist[$graphPattern->getId()]['hasOptional']     = 0;
        $patternlist[$graphPattern->getId()]['hasUnion']        = 0;
        $patternlist[$graphPattern->getId()]['patternResult']   = $finalRes;

        $op = $graphPattern->getOptional();
        $un = $graphPattern->getUnion();

        $patternlist[$graphPattern->getId()]['optionalTo']      = $op;
        if(is_int($op))
        $patternlist[$op]['hasOptional']++;

        $patternlist[$graphPattern->getId()]['unionWith']       = $un;
        if(is_int($un))
        $patternlist[$un]['hasUnion']++;

        $constraint = $graphPattern->getConstraints();
        if(count($constraint) > 0){
            foreach($constraint as $constr){
                if($constr->isOuterFilter()){
                    $patternlist[$graphPattern->getId()]['outerFilter'][]          = $constr;
                    $patternlist[$graphPattern->getId()]['innerFilter'][]          = null;
                }else{
                    $patternlist[$graphPattern->getId()]['innerFilter'][]          = $constr;
                    $patternlist[$graphPattern->getId()]['outerFilter'][]          = null;
                }
            }
        }else{
            $patternlist[$graphPattern->getId()]['innerFilter']          = null;
            $patternlist[$graphPattern->getId()]['outerFilter']          = null;
        }
    }


    /**
    * Finds Tuples matching one TriplePattern.
    *
    * @param  TriplePattern $pattern
    * @param  Array         $graphlist
    * @return Array
    */
    protected function findTuplesMatchingOnePattern($pattern, $graphlist){
        $var = null;
        $sub  = $pattern->getSubject();
        $pred = $pattern->getPredicate();
        $obj  = $pattern->getObject();

        if(is_string($sub)||$sub instanceof BlankNode){
            if(is_string($sub))
            $var['sub'] = $sub;
            $sub = null;
        }
        if(is_string($pred)||$pred instanceof BlankNode ){
            if(is_string($pred))
            $var['pred'] = $pred;
            $pred = null;
        }
        if(is_string($obj)||$obj instanceof BlankNode){
            if(is_string($obj))
            $var['obj'] = $obj;
            $obj = null;
        }
        $intBindings = $this->_buildIntBindings($var);
        $k = 0;

        $key = 0;
        // search in named graphs
        if($graphlist['var'][0] != null||$graphlist['list'][0] != null){
            foreach($graphlist['list'] as $key => $graphnode){

                // query the dataset
                $it = $this->dataset->findInNamedGraphs($graphnode,$sub,$pred,$obj,false);
                if($it->valid()){
                    // add statements to the result list
                    while($it->valid()){
                        if($graphnode == null){
                            $element = $it->current()->getStatement();
                            $grname  = $it->current()->getGraphname();
                        }else{
                            if($it->current() instanceof Quad)
                                $element = $it->current()->getStatement();
                            else
                                $element = $it->current();

                            $grname  = $graphnode;
                        }
                        if($this->checkIntBindings($element,$intBindings)){
                            $resmodel['trip'][$k]  = $element;
                            $resmodel['graph'][$k] = $grname;
                        //    $resmodel['graphvar'][$k] = $graphlist['var'][$key];
                            $resmodel['graphvar'][$k] = $graphlist['var'][0];
                            $k++;

                        }
                        $it->next();
                    }
                }

            }
        }
        // search in the default graph
        if($graphlist['list'][0] == null && $graphlist['var'][0] == null){


            $gr = $this->dataset->getDefaultGraph();

            $res = $gr->find($sub,$pred,$obj);

            foreach($res->triples as $innerkey => $element){
                if($this->checkIntBindings($element,$intBindings)){
                        $resmodel['trip'][$k]  = $element;
                        $resmodel['graph'][$k] = null;
                        $resmodel['graphvar'][$k] = $graphlist['var'][$key];
                        $k++;
                    }
            }
        }
        if($k == 0)
        return false;
        return $this->_buildResultSet($pattern,$resmodel);
    }

    /**
    * Checks it there are internal bindings between variables.
    *
    * @param  Triple  $trip
    * @param  Array   $intBindings
    * @return boolean
    */
    protected function checkIntBindings($trip, $intBindings){
        switch($intBindings){
            case -1:
            return true;
            break;
            case 0:
            if($trip->subj != $trip->pred)
            return false;
            break;
            case 1:
            if(is_a($trip->obj,'Literal'))
            return false;
            if($trip->subj != $trip->obj)
            return false;
            break;
            case 2:
            if(is_a($trip->obj,'Literal'))
            return false;
            if($trip->pred != $trip->obj)
            return false;
            break;
            case 3:
            if(is_a($trip->obj,'Literal'))
            return false;
            if($trip->pred != $trip->obj || $trip->pred != $trip->subj )
            return false;
            break;
        }
        return true;
    }


    /**
    * Perform an SQL-like inner join on two resultSets.
    *
    * @param   Array   &$finalRes
    * @param   Array   &$res
    * @return  Array
    */
    protected function joinTuples(&$finalRes, &$res) {

        if (!$finalRes || !$res)
        return array();

        // find joint variables and new variables to be added to $finalRes
        $jointVars = array();
        $newVars = array();
        $k = key($res);

        foreach ($res[$k] as $varname => $node) {
            if (array_key_exists($varname, $finalRes[0]))
            $jointVars[] = $varname;
            else
            $newVars[] = $varname;
        }

        // eliminate rows of $finalRes in which the values of $jointVars do not have
        // a corresponding row in $res.
        foreach ($finalRes as $n => $fRes) {
            foreach ($res as $i => $r) {
                $ok = TRUE;
                foreach ($jointVars as $j_varname)
                if ($r[$j_varname] != $fRes[$j_varname]) {
                    $ok = FALSE;
                    break;
                }
                if ($ok)
                break;
            }
            if (!$ok)
            unset($finalRes[$n]);
        }

        // join $res and $finalRes
        $joinedRes = array();
        foreach ($res as $r) {
            foreach ($finalRes as $n => $fRes) {
                $ok = TRUE;
                foreach ($jointVars as $j_varname)
                if ($r[$j_varname] != $fRes[$j_varname]) {
                    $ok = FALSE;
                    break;
                }
                if ($ok) {
                    $joinedRow = $finalRes[$n];
                    foreach($newVars as $n_varname)
                    $joinedRow[$n_varname] = $r[$n_varname];
                    $joinedRes[] = $joinedRow;
                }
            }
        }
        return $joinedRes;
    }


    /**
    * Joins OPTIONAL pattern results.
    *
    * @param   Array   &$finalRes
    * @param   Array   &$res
    * @return  Array    the joined Array
    */
    protected function joinOptionalTuples(&$finalRes, &$res) {

        if(!$finalRes && !$res)
        return array();

        if(!$finalRes)
        return $res;

        if(!$res)
        return $finalRes;

        // find joint variables and new variables to be added to $finalRes
        $jointVars = array();
        $newVars = array();
        $result = array();

        $k = key($res);

        foreach ($res[$k] as $varname => $node) {
            if (array_key_exists($varname, $finalRes[0])){
                $jointVars[] = $varname;
            }else{
                $newVars[] = $varname;
            }
        }
        $joined = array();
        $joinc = 0;
        foreach($finalRes as $i =>$fRes){
            foreach($res as $n =>$r){
                $join = false;
                foreach($jointVars as $j_varname){
                    if($r[$j_varname]==$fRes[$j_varname]){
                        $join = true;
                        //break;
                    }else{
                        $join = false;
                    }
                }
                if($join){
                    $result[$joinc] = $fRes;
                    foreach($newVars as $n_varname)
                    $result[$joinc][$n_varname] = $r[$n_varname];
                    $joined[]=$n;
                    $joinc++;
                }

            }
        }

        $count = count($result);
        foreach($res as $k =>$val){
            if(!in_array($k,$joined)){
                $result[$count] = $finalRes[0];
                foreach($result[$count] as $varname => $varVal){
                    $result[$count][$varname]='';
                }

                foreach($val as $varname2 => $varVal2){
                    $result[$count][$varname2]=$varVal2;
                }
                $count++;
            }
        }
        return $result;
    }



    /**
    * Looks in from and from named part of the query and
    * adds the graphs to the graphlist.
    *
    * @return Array
    */
    protected function preselectGraphs(){
        $fromNamed = $this->query->getFromNamedPart();
        if($fromNamed == null)
        $fromNamed[] = null;
        return $fromNamed;
    }


    /**
    * Evaluates the GRPAH clause if there is one. Checks if
    * the GRAPH clause contains an IRI, variable or nothing.
    * Returns an array which contains the graphs that has to be matched.
    *
    * @param  GraphPattern $pattern
    * @param  Array        $graphlist
    * @return Array
    */
    protected function _checkGraphs(&$pattern,$graphlist){

        $gr = $pattern->getGraphname();
        if($gr instanceof Resource ){
            if($graphlist[0]==null || in_array($gr,$graphlist)){
                $newGraphList['list'][] = $gr;
                $newGraphList['var'][]  = null;
            }else{
                return false;
            }
        }elseif (is_string($gr)){
            $newGraphList['list'] = $graphlist;
            $newGraphList['var'][]  = $gr;
        }else{
            $newGraphList['list'] = $graphlist;
            $newGraphList['var'][]  = null;
        }
        return $newGraphList;
    }

    /**
    * Marks triples with internal bindings.
    * int bindings -1 :none 0:sub=pred 1:sub=obj 2:pred=obj 3:sub=pred=obj.
    *
    * @param  Array $var
    * @return Array
    */
    protected function _buildIntBindings($var){
        $intBindings = -1;
        if(!$var)
        return $intBindings;

        if(isset($var['sub'])){
            if(isset($var['pred']))
            if($var['sub'] == $var['pred'])
            $intBindings = 0;
            if(isset($var['obj']))
            if($var['sub'] == $var['obj']){
                if( $intBindings == 0){
                    $intBindings = 3;
                }else{
                    $intBindings = 1;
                }
            }
        }
        if(isset($var['pred'])){
            if(isset($var['obj']))
            if($var['pred']==$var['obj']&&$intBindings!=3)
            $intBindings = 2;
        }
        return $intBindings;
    }

    /**
    * Builds the resultset.
    *
    * @param  GraphPattern $pattern
    * @param  Array        $resmodel
    * @return Array
    */
    protected function _buildResultSet($pattern,$resmodel){
        // determine variables and their corresponding values
        $result = null;
        if(is_string($pattern->getSubject())){
            $n = 0;
            foreach($resmodel['trip'] as $key => $triple){
                if(isset($resmodel['graphvar'][$key]))
                $result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
                $result[$n++][$pattern->getSubject()] = $triple->subj;
            }
        }
        if(is_string($pattern->getPredicate())){
            $n = 0;
            foreach($resmodel['trip'] as $key => $triple){
                if(isset($resmodel['graphvar'][$key]))
                $result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
                $result[$n++][$pattern->getPredicate()] = $triple->pred;
            }
        }
        if(is_string($pattern->getObject())){
            $n = 0;
            foreach($resmodel['trip'] as $key => $triple){
                if(isset($resmodel['graphvar'][$key]))
                $result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
                $result[$n++][$pattern->getObject()] = $triple->obj;
            }
        }
        return $result;
    }

    /**
    * Selects the result variables and builds a result table.
    *
    * @param  Array  $table the result table
    * @param  Array  $vars the result variables
    * @return Array
    */
    protected function selectVars($table,$vars){
        if($vars[0]=='*')
        $vars = $this->query->getAllVars();
        $resTable = array();
        $hits = 0;
        foreach($table as $val){
            foreach($vars as $var){
                if(isset($val[(string)$var])){
                    $resTable[$hits][(string)$var]=$val[(string)$var];
                }else{
                    $resTable[$hits][(string)$var]="";
                }
            }
            $hits++;
        }
        return $resTable;
    }

    /**
    * Joins the results of the different Graphpatterns.
    *
    * @param  Array $patternlist
    * @return Array
    */
    protected function joinResults($patternlist){
        $joined[0]['patternResult'] = null;
        $joined[0]['outerFilter'] = null;

        while(count($patternlist)>0){
            foreach($patternlist as $key => $pattern){
                if($pattern['hasOptional'] == 0 && $pattern['hasUnion'] == 0){
                    if(is_int($pattern['optionalTo'])){
                        $patternlist[$pattern['optionalTo']]['hasOptional']--;
                        $patternlist[$pattern['optionalTo']]['patternResult'] = $this->joinOptionalTuples($pattern['patternResult'],$patternlist[$pattern['optionalTo']]['patternResult']);
                        unset($patternlist[$key]);
                        break;
                    }
                    else if(is_int($pattern['unionWith'])){
                        $patternlist[$pattern['unionWith']]['hasUnion']--;
                        foreach($pattern['patternResult'] as $value)
                        array_push($patternlist[$pattern['unionWith']]['patternResult'],$value);
                        unset($patternlist[$key]);
                        break;
                    }else{
                        if($joined[0]['patternResult'] == null){
                            $joined[0]['patternResult'] = $pattern['patternResult'];
                            if($joined[0]['outerFilter'] == null )
                            $joined[0]['outerFilter']  = $pattern['outerFilter'];
                            unset($patternlist[$key]);
                            break;
                        }
                    //    if($pattern['patternResult'] !=null ){
                            $joined[0]['patternResult'] = $this->joinTuples($joined[0]['patternResult'],$pattern['patternResult']);
                            $joined[0]['outerFilter']   = $pattern['outerFilter'];
                            unset($patternlist[$key]);
                            break;
                    //    }
                    }
                }
            }
        }
        return $joined;
    }

    /**
    * Filters the pattern results.
    *
    * @param  Array   $patternlist list containing the results of the GraphPatterns
    * @param  boolean $outer TRUE if its an outer filter FALSE if not
    * @return Array   the filtered patternlist
    */
    protected function filterPatterns($patternlist,$outer){
        if($outer)
        $filter = 'outerFilter';
        else
        $filter = 'innerFilter';
        foreach($patternlist as $patkey => $pattern){
            // get constraints
            $constraint = $pattern[$filter];

            if(count($constraint)>0){
                foreach($constraint as $constr){
                    if($constr != null){
                        // extract Vars and function calls
                        $evalString = $constr->getExpression();
                        preg_match_all("/\?.[^\s\)\,]*/",$evalString,$vars);
                        preg_match_all("/bound\((.[^\)]*)\)/i",$evalString,$boundcalls);
                        preg_match_all("/isuri\((.[^\)]*)\)/i",$evalString,$isUricalls);
                        preg_match_all("/isblank\((.[^\)]*)\)/i",$evalString,$isBlankcalls);
                        preg_match_all("/isLiteral\((.[^\)]*)\)/i",$evalString,$isLiteralcalls);
                        preg_match_all("/lang\((.[^\)]*)\)/i",$evalString,$langcalls);
                        preg_match_all("/datatype\((.[^\)]*)\)/i",$evalString,$datatypecalls);
                        preg_match_all("/str\((.[^\)]*)\)/i",$evalString,$stringcalls);

                        // is Bound
                        if(count($boundcalls[1])>0)
                        $function['bound'] = $boundcalls[1];
                        else
                        $function['bound'] = false;

                        // is URI
                        if(count($isUricalls[1])>0)
                        $function['isUri'] = $isUricalls[1];
                        else
                        $function['isUri'] = false;

                        // is Blank
                        if(count($isBlankcalls[1])>0)
                        $function['isBlank'] = $isBlankcalls[1];
                        else
                        $function['isBlank'] = false;

                        // is Literal
                        if(count($isLiteralcalls[1])>0)
                        $function['isLiteral'] = $isLiteralcalls[1];
                        else
                        $function['isLiteral'] = false;

                        // lang
                        if(count($langcalls[1])>0)
                        $function['lang'] = $langcalls[1];
                        else
                        $function['lang'] = false;

                        // datatype
                        if(count($datatypecalls[1])>0)
                        $function['datatype'] = $datatypecalls[1];
                        else
                        $function['datatype'] = false;

                        // string
                        if(count($stringcalls[1])>0)
                        $function['string'] = $stringcalls[1];
                        else
                        $function['string'] = false;


                        foreach($pattern['patternResult'] as $key => $res){
                            $result = false;
                            $evalString = $this->fillConstraintString($vars,$res,$constr,$function);
                            $evalString = '$result =('.$evalString.');';
                            // evaluate Constraint
                            @eval($evalString);

                            if(!$result)
                            unset($patternlist[$patkey]['patternResult'][$key]);

                        }
                    }
                }
            }
        }
        return $patternlist;
    }

    /**
    * Builds an evaluation string to determine wether the result passes
    * the filter or not. This string is evaluatet by the php buildin eval() function
    *
    * @param  Array      $vars a list which contains the used variables
    * @param  Array      $res  the result part which have to be evaluated
    * @param  Constraint $constraint the Constrain object
    * @param  Array      $function an Array which contains the used functions
    * @return String
    */

    protected function fillConstraintString($vars,$res,$constraint,$function){

        $boundExpr = false;
        $evalString = $constraint->getExpression();

        // extract Literals
        $pattern1 = "/\".[^\"]*\"[^\^\@]/";
        $pattern2 = "/\'.[^\']*\'[^\^\@]/";
        preg_match_all($pattern1,$evalString,$hits1);
        preg_match_all($pattern2,$evalString,$hits2);

        foreach($hits1[0] as $k => $val){
            $evalString = preg_replace('/\".[^\"]*\"[^\^]/','_REPLACED1_'.$k++,$evalString,1);
        }
        foreach($hits2[0] as $k => $val){
            $evalString = preg_replace('/\".[^\"]*\"[^\^]/','_REPLACED2_'.$k++,$evalString,1);
        }

        // replace namespaces
        $prefs = $this->query->getPrefixes();
        foreach($prefs as $key => $val){
            if($key == '')
            $key = ' ';
            $evalString = preg_replace("/^(".$key."\:)(.[^\s]*)|([\s\(]?[^\^])(".$key."\:)(.[^\s\)]*)([\s\)]?)/","$3'<".$val."$2$5>'$6",$evalString);

            $evalString = preg_replace("/(\^)(".$key."\:)(.[^\s]*)/","$1<".$val."$3>",$evalString);
        }

        $xsd = "http\:\/\/www.w3.org\/2001\/XMLSchema\#";

        // evaluate bound calls
        if($function['bound']){
            $boundExpr = true;
            foreach($function['bound'] as $var){
                if(isset($res[$var]) && $res[$var]!=="")
                $replacement = 'true';
                else
                $replacement = 'false';
                $evalString = preg_replace("/bound\(\\".$var."\)/i",$replacement,$evalString);
            }

        }
        // evaluate isBlank calls
        if($function['isBlank']){
            foreach($function['isBlank'] as $var){
                if(isset($res[$var]) && $res[$var]!=="" && $res[$var] instanceof BlankNode )
                $replacement = 'true';
                else
                $replacement = 'false';
                $evalString = preg_replace("/isBlank\(\\".$var."\)/i",$replacement,$evalString);
            }

        }
        // evaluate isLiteral calls
        if($function['isLiteral']){
            foreach($function['isLiteral'] as $var){
                if(isset($res[$var]) && $res[$var]!=="" && $res[$var] instanceof Literal  )
                $replacement = 'true';
                else
                $replacement = 'false';
                $evalString = preg_replace("/isLiteral\(\\".$var."\)/i",$replacement,$evalString);
            }

        }
        // evaluate isUri calls
        if($function['isUri']){
            foreach($function['isUri'] as $var){
                if(isset($res[$var]) && $res[$var]!=="" && $res[$var] instanceof Resource && $res[$var]->getUri() && !$res[$var] instanceof BlankNode )
                $replacement = 'true';
                else
                $replacement = 'false';
                $evalString = preg_replace("/isUri\(\\".$var."\)/i",$replacement,$evalString);
            }
        }
        // evaluate lang calls
        if($function['lang']){
            foreach($function['lang'] as $var){
                if(isset($res[$var]) && $res[$var]!=="" && $res[$var] instanceof Literal && $res[$var]->getLanguage() )
                $replacement = '"'.$res[$var]->getLanguage().'"';
                else
                $replacement = 'null';
                $evalString = preg_replace("/lang\(\\".$var."\)/i",$replacement,$evalString);
            }
        }
        // evaluate datatype calls
        if($function['datatype']){
            foreach($function['datatype'] as $var){
                if(isset($res[$var]) && $res[$var]!=="" && $res[$var] instanceof Literal && $res[$var]->getDatatype() )
                $replacement = '\'<'.$res[$var]->getDatatype().'>\'';
                else
                $replacement = 'false';
                $evalString = preg_replace("/datatype\(\\".$var."\)/i",$replacement,$evalString);
            }
        }
        // evaluate string calls
        if($function['string']){
            foreach($function['string'] as $var){
                if($var{0}=='?' || $var{0}=='$'){
                    if(isset($res[$var]) && $res[$var]!==""){
                        $replacement = "'str_".$res[$var]->getLabel()."'";
                        if($res[$var] instanceof BlankNode)
                        $replacement = "''";
                    }else{
                        $replacement = 'false';
                    }
                    $evalString = preg_replace("/str\(\\".$var."\)/i",$replacement,$evalString);
                }else{
                    if($var{0}=='<'){
                        $evalString = preg_replace("/str\(\s*\<(.[^\>]*)\>\s*\)/i","'str_$1'",$evalString);
                    }
                    if($var{0}=='"'){
                        $evalString = preg_replace("/str\(\s*\"(.[^\>]*)\"\@[a-z]*\s*\)/i","'str_$1'",$evalString);
                    }
                }

            }
        }
        // evaluate VARS
        foreach($vars[0] as $var){
            if(isset($res[$var])&&$res[$var]!== ""){
                //$replacement = "'".$res[$var]->getLabel()."'";
                $replacement = '" "';
                if($res[$var] instanceof Literal){
                    if($res[$var]->getDatatype()!= null){
                        if($res[$var]->getDatatype() == XML_SCHEMA.'boolean')
                        $replacement = $res[$var]->getLabel();
                        if($res[$var]->getDatatype() == XML_SCHEMA.'double')
                        $replacement = $res[$var]->getLabel();
                        if($res[$var]->getDatatype() == XML_SCHEMA.'integer')
                        $replacement = $res[$var]->getLabel();
                        if($res[$var]->getDatatype() == XML_SCHEMA.'dateTime')
                        $replacement = strtotime($res[$var]->getLabel());
                    }else{
                        if($res[$var]->getLabel()=="")
                        $replacement = 'false';
                        else
                        $replacement = "'str_".$res[$var]->getLabel()."'";
                    }
                }else{
                    if($res[$var] instanceof Resource){
                        $replacement = "'<".$res[$var]->getLabel().">'";
                    }
                }
                $evalString = preg_replace("/\\".$var."/",$replacement,$evalString);
            }

            // problem with PHP: false < 13 is true
            if(isset($res[$var])){
                if($res[$var] === ""){
                    if($boundExpr)
                    $evalString = preg_replace("/\\".$var."/","false",$evalString);
                    else
                    $evalString = 'false';
                }
            }else{
                $evalString = preg_replace("/\\".$var."/","false",$evalString);
            }

        }

        // replace '=' with '=='
        $evalString = preg_replace("/(.[^\=])(\=)(.[^\=])/","$1==$3",$evalString);


        // rewrite Literals
        foreach($hits1[0] as $k => $val){
            $pattern = '/_REPLACED1_'.$k.'/';
            $evalString = preg_replace($pattern,$hits1[0][$k],$evalString,1);
        }

        foreach($hits2[0] as $k => $val){
            $pattern = '/_REPLACED2_'.$k.'/';
            $evalString = preg_replace($pattern,$hits2[0][$k],$evalString,1);
        }

        // replace xsd:boolean expressions
        $pattern = $pattern = '/\"\s?true\s?\"\^\^\<'.$xsd.'boolean\>|\'\s?true\s?\'\^\^xsd:boolean/';
        $evalString = preg_replace($pattern,"true",$evalString);

        $pattern = $pattern = '/\"\s?false\s?\"\^\^\<'.$xsd.'boolean\>|\'\s?false\s?\'\^\^xsd:boolean/';
        $evalString = preg_replace($pattern,"false",$evalString);

        // replace xsd:date expressions
        $pattern = "/\"(.[^\"]*)\"\^\^".$xsd."dateTime/";
        preg_match_all($pattern,$evalString,$hits);

        foreach($hits[1] as $dummy)
        $evalString = preg_replace("/\".[^\"]*\"\^\^".$xsd."dateTime/",strtotime($dummy),$evalString,1);


        $evalString = preg_replace("/(\'\<".$xsd."dateTime\()(.[^\)]*\))\>\'/","dateTime($2",$evalString);

        $evalString = preg_replace("/(\'\<".$xsd."integer\()(.[^\)]*\))\>\'/","integer($2",$evalString);

        // tag plain literals
        $evalString = preg_replace("/\"(.[^\"]*)\"([^\^])|\"(.[^\"]*)\"$/","'str_$1$3'$2",$evalString);

        return $evalString;
    }

    /**
    * Sorts the results.
    *
    * @param  Array  $vartable List containing the unsorted result vars
    * @return Array  List containing the sorted result vars
    */
    protected function sortVars($vartable)
    {
        $newTable = array();
        $mod = $this->query->getSolutionModifier();
        // if no ORDER BY solution modifier return vartable
        if($mod['order by']!= null){
            $order = $mod['order by'];
            $map = $this->buildVarmap($order,$vartable);
            foreach($map as $val){
                $newTable[] = $vartable[$val];
            }
        }else{
            $newTable = $vartable;
        }

        if($mod['offset'] != null){
            $newTable = array_slice ($newTable, $mod['offset']);
        }
        if($mod['limit'] != null){
            $newTable = array_slice($newTable,0,$mod['limit']);
        }

        return $newTable;
    }

    /**
    * Sorts the result table.
    *
    * @param  String $order (ASC/DESC)
    * @param  Array  $vartable the vartable
    * @return Array  A map that contains the new order of the result vars
    */
    protected function buildVarmap($order, $vartable)
    {
        $n= 0;
        $result = array();
        $num_var = array();
        foreach($order as $variable)
        $num_var[$variable['val']] = 0;

        foreach($vartable as $k => $x){
            foreach($order as $value){
                // if the value is a typed Literal try to determine if it
                // a numeric datatype
                if($x[$value['val']] instanceof Literal){
                    $dtype = $x[$value['val']]->getDatatype();
                    if($dtype){
                        switch($dtype){
                            case XML_SCHEMA."integer":
                            $num_var[$value['val']]++;
                            break;
                            case XML_SCHEMA."double":
                            $num_var[$value['val']]++;
                            break;

                        }
                    }
                }
                if($x[$value['val']]){
                    if($x[$value['val']]instanceof Literal){
                        $pref = "2";
                    }
                    if($x[$value['val']]instanceof Resource){
                        $pref = "1";
                    }
                    if($x[$value['val']]instanceof BlankNode){
                        $pref = "0";
                    }
                    $result[$value['val']][$n] = $pref.$x[$value['val']]->getLabel();
                }else{
                    $result[$value['val']][$n] = "";
                }
            }
            $result['oldKey'][$n] = $k;
            $n++;
        }
        $sortString = "";
        foreach($order as $value){
            if($num_var[$value['val']] == $n)
            $sort = SORT_NUMERIC;
            else
            $sort = SORT_STRING;

            if($value['type'] == 'asc')
            $type = SORT_ASC;
            else
            $type = SORT_DESC;

            $sortString = $sortString.'$result["'.$value['val'].'"],'.$type.','.$sort.',';
        }
        $sortString = "array_multisort(".$sortString.'$result["oldKey"]);';

        @eval($sortString);
        return $result['oldKey'];
    }



    /**
    * Eliminates duplicate results.
    *
    * @param  Array  $vartable a table that contains the result vars and their bindings
    * @return Array the result table without duplicate results
    */
    protected function distinct($vartable)
    {
        $index = array();
        foreach($vartable as $key => $value){
            $key_index="";
            foreach($value as $k => $v)
            if($v instanceof Object)
                $key_index = $key_index.$k.$v->toString();
            if(isset($index[$key_index]))
            unset($vartable[$key]);
            else
            $index[$key_index]= 1;
        }
        return $vartable;
    }


    /**
    * Prints a query result as HTML table.
    * You can change the colors in the configuration file.
    *
    * @param array $queryResult [][?VARNAME] = object Node
    * @return void
    */
    public function writeQueryResultAsHtmlTable($queryResult) {
        // Import Package Utility
        include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

        if ( $queryResult[0] == null) {
            echo 'no match<br>';
            return;
        }
        if ( $queryResult == 'false') {
            echo 'boolean: false<br>';
            return;
        }
        if ( $queryResult == 'true') {
            echo 'boolean: true<br>';
            return;
        }


        echo '<table border="1" cellpadding="3" cellspacing="0"><tr><td><b>No.</b></td>';
        foreach ($queryResult[0] as $varName => $value)
        echo "<td align='center'><b>$varName</b></td>";
        echo '</tr>';

        foreach ($queryResult as $n => $var) {


            echo '<tr><td width="20" align="right">' .($n + 1) .'.</td>';
            foreach ($var as $varName => $value) {
                if($value !=''){
                    echo INDENTATION . INDENTATION . '<td bgcolor="';
                    echo RDFUtil::chooseColor($value);
                    echo '">';
                    echo '<p>';

                    $lang  = NULL;
                    $dtype = NULL;
                    if (is_a($value, 'Literal')) {
                        if ($value->getLanguage() != NULL)
                        $lang = ' <b>(xml:lang="' . $value->getLanguage() . '") </b> ';
                        if ($value->getDatatype() != NULL)
                        $dtype = ' <b>(rdf:datatype="' . $value->getDatatype() . '") </b> ';
                    }
                    echo  RDFUtil::getNodeTypeName($value) .$value->getLabel() . $lang . $dtype .'</p>';
                }else{
                    echo "<td bgcolor='white'>unbound";
                }
            }
            echo '</tr>';
        }
        echo '</table>';
    }



    /*
    *   Dumb getters
    */



    public function getQuery()
    {
        return $this->query;
    }//public function getQuery()



    public function getDataset()
    {
        return $this->dataset;
    }//public function getDataset()

} // end: Class SparqlEngine

?>