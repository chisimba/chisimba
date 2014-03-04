<?php

/**
*   Simplifies ("flattens") Query objects that have graph
*   patterns which are subpatterns of other patterns.
*
*   Example:
*      ?g ?h ?i .
*      {
*        {?person <some://typ/e> 'asd'}
*        UNION
*        {?person3 <some://typ/es2> 'three'}
*      }
*    is represented internally as three graph patterns, the latter
*    two referencing the first to be their pattern (they are subpatternOf).
*    Now this can be flattened to this which is the same:
*      {?g ?h ?i . ?person <some://typ/e> 'asd'}
*      UNION
*      {?g ?h ?i .?person3 <some://typ/es2> 'three'}
*
*   This class does this.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*/
class SparqlEngineDb_QuerySimplifier
{

    /**
    *   Simplify the query by flattening out subqueries.
    *   Modifies the passed query object directly.
    */
    public function simplify(Query $query)
    {
        $arPatterns = $query->getResultPart();
        self::dropEmpty($arPatterns);
        $arPlan     = $this->createPlan($arPatterns);
        if (count($arPlan) == 0) {
            $query->setResultPart($arPatterns);
            return 0;
        }

        $this->executePlan($arPatterns, $arPlan);
        $query->setResultPart($arPatterns);
    }//public function simplify(Query $query)



    /**
    *   Creates a plan what to do.
    *
    *   @return array Array of arrays. Key is the parent pattern id,
    *               value is an array of subpatterns that belong to
    *               that parental pattern.
    */
    protected function createPlan(&$arPatterns)
    {
        $arNumbers = $this->getNumbers($arPatterns);
        if (count($arNumbers) == 0) {
            return array();
        }

        $arPlan    = array();

        foreach ($arNumbers as $nId => $nPatternCount) {
            $nParent = $arPatterns[$nId]->getSubpatternOf();
            $arPlan[$nParent][$nId] = true;
        }

        return $arPlan;
    }//protected function createPlan(&$arPatterns)



    /**
    *   Executes the plan
    *
    *   @param array $arPatterns  Array of GraphPatterns
    *   @param array $arPlan      Plan array as returned by createPlan()
    */
    protected function executePlan(&$arPatterns, &$arPlan)
    {
        foreach ($arPlan as $nParent => $arChildren) {
            $base        = $arPatterns[$nParent];
            $grandParent = $base->getSubpatternOf();
            $nNextId     = $nParent;
            foreach ($arChildren as $nChild => $null) {
                $new = clone $base;
                $new->addTriplePatterns($arPatterns[$nChild]->getTriplePatterns());
                $new->addConstraints(   $arPatterns[$nChild]->getConstraints());
                $new->setId($nNextId);
                if ($nParent != $nNextId) {
                    $new->setUnion($nParent);
                }
                $arPatterns[$nNextId] = $new;

                if ($grandParent !== null) {
                    //dynamically adjust plan
                    $arPlan[$grandParent][$nNextId] = true;
                }

                $nNextId = $nChild;
            }
            //last one is not not needed anymore
            unset($arPatterns[$nNextId]);
        }
    }//protected function executePlan(&$arPatterns, &$arPlan)



    /**
    *   Returns an array of id-value pairs determining
    *   which pattern IDs (array id) are deepest nested
    *   (higher value).
    *   Array is sorted in reverse order, highest values
    *   first.
    *
    *   @param array $arPatterns    Array with GraphPatterns
    *   @return array Array with key-value pairs
    */
    protected function getNumbers(&$arPatterns)
    {
        $arNumbers = array();
        foreach ($arPatterns as $nId => &$pattern) {
            $nParent = $pattern->getSubpatternOf();
            if ($nParent !== null) {
                $arNumbers[$nId] = $arNumbers[$nParent] + 1;
            } else {
                $arNumbers[$nId] = 0;
            }
        }
        //remove the not so interesting ones
        foreach ($arNumbers as $nId => $nNumber) {
            if ($nNumber == 0) {
                unset($arNumbers[$nId]);
            }
        }

        arsort($arNumbers);

        return $arNumbers;
    }//protected function getNumbers(&$arPatterns)



    /**
    *   Removes all empty graph patterns from the array.
    *   Modifies it directly.
    */
    protected static function dropEmpty(&$arPatterns)
    {
        foreach ($arPatterns as $nId => &$pattern) {
            if ($pattern->isEmpty()) {
                unset($arPatterns[$nId]);
            }
        }

        foreach ($arPatterns as $nId => &$pattern) {
            $nParent = $pattern->getSubpatternOf();
            if (!isset($arPatterns[$nParent])) {
                $arPatterns[$nId]->setSubpatternOf(null);
            }
        }
        //FIXME: continued indexes?
    }//protected static function dropEmpty(&$arPatterns)

}//class SparqlEngineDb_QuerySimplifier

?>