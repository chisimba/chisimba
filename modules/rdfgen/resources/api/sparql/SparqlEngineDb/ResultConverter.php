<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/ResultRenderer.php';

/**
*   Converts a database result into a proper
*   rdf statement triple array
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngineDb_ResultConverter
{
    /**
    *   Determines the correct renderer and calls it.
    *
    *   The $resultform may be:
    *   - false: The default renderer is taken then
    *   - an object that implements SparqlEngineDb_ResultRenderer interface
    *   - a string like "HTML" or "XML". The appropriate renderer is used then.
    *   - a full class name, e.g. SparqlEngineDb_ResultRenderer_XML
    *
    *   @param array    $arRecordSets   Array of anything ADOConnection::Execute() can return
    *   @param SparqlEngineDb $engine   Sparql database engine.
    *   @param mixed    $resultform     Which format the results shall be in (false or "xml")
    *
    *   @return mixed   Most likely an array or a boolean value,
    *                   or anything else as determined by $resultform
    */
    public static function convertFromDbResults($arRecordSets, SparqlEngineDb $engine, $resultform = false)
    {
        if (is_object($resultform)) {
            if ($resultform instanceof SparqlEngineDb_ResultRenderer) {
                return $resultform->convertFromDbResults(
                    $arRecordSets,
                    $engine->getQuery(),
                    $engine
                );
            } else {
                throw new Exception(
                    'Result renderer object needs to implement'
                    . ' SparqlEngineDb_ResultRenderer interface'
                );
            }
        }

        if ($resultform === false) {
            $resultform = 'Default';
        } else if ($resultform == 'xml') {
            //kept for BC reasons
            $resultform = 'XML';
        }

        if ($strClass = self::loadClass($resultform)) {
            $rrObj = new $strClass();
            if ($rrObj instanceof SparqlEngineDb_ResultRenderer) {
                return $rrObj->convertFromDbResults(
                    $arRecordSets,
                    $engine->getQuery(),
                    $engine
                );
            } else {
                throw new Exception(
                    'Result renderer class "' . $strClass . '" needs to implement'
                    . ' SparqlEngineDb_ResultRenderer interface'
                );
            }
        } else {
            throw new Exception(
                'Result renderer class "' . $resultform . '" could not be loaded.'
            );
        }
    }//public static function convertFromDbResults($arRecordSets, SparqlEngineDb $engine, $resultform = false)



    /**
    *   Tries to load a given class if it doesn't exist,
    *   and returns true if the class can be used.
    *
    *   @param string $strClass Classname
    *   @return mixed Class name if the class is loaded and can be used, false if not.
    */
    protected static function loadClass($strClass)
    {
        if (class_exists($strClass, false)) {
            return $strClass;
        }

        //RAP style, shortcut notation
        $strFile = 'SparqlEngineDb/ResultRenderer/' . $strClass . '.php';
        @include_once RDFAPI_INCLUDE_DIR . 'sparql/' . $strFile;
        if (class_exists('SparqlEngineDb_ResultRenderer_' . $strClass, false)) {
            return 'SparqlEngineDb_ResultRenderer_' . $strClass;
        }

        //RAP style
        $strFile = str_replace('_', '/', $strClass) . '.php';
        @include_once RDFAPI_INCLUDE_DIR . 'sparql/' . $strFile;
        if (class_exists($strClass, false)) {
            return $strClass;
        }

        //PEAR style
        @include_once $strFile;
        if (class_exists($strClass, false)) {
            return $strClass;
        }

        return false;
    }//protected static function loadClass($strClass)

}//class SparqlEngineDb_ResultConverter
?>