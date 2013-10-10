<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngine/ResultRenderer.php';

/**
*   Converts a memory result into a proper
*   rdf statement triple array
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*   @package sparql
*/
class SparqlEngine_ResultConverter
{
    /**
    *   Determines the correct renderer and calls it.
    *
    *   The $resultform may be:
    *   - false: The default renderer is taken then
    *   - an object that implements SparqlEngine_ResultRenderer interface
    *   - a string like "HTML" or "XML". The appropriate renderer is used then.
    *   - a full class name, e.g. SparqlEngine_ResultRenderer_XML
    *
    *   @param array $arVartable        Variable table
    *   @param SparqlEngine $engine   Sparql database engine.
    *   @param mixed    $resultform     Which format the results shall be in (false or "xml")
    *
    *   @return mixed   Most likely an array or a boolean value,
    *                   or anything else as determined by $resultform
    */
    public static function convertFromResult($arVartable, SparqlEngine $engine, $resultform = false)
    {
        if (is_object($resultform)) {
            if ($resultform instanceof SparqlEngine_ResultRenderer) {
                return $resultform->convertFromResult(
                    $arVartable,
                    $engine->getQuery(),
                    $engine
                );
            } else {
                throw new Exception(
                    'Result renderer object needs to implement'
                    . ' SparqlEngine_ResultRenderer interface'
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
            if ($rrObj instanceof SparqlEngine_ResultRenderer) {
                return $rrObj->convertFromResult(
                    $arVartable,
                    $engine->getQuery(),
                    $engine
                );
            } else {
                throw new Exception(
                    'Result renderer class "' . $strClass . '" needs to implement'
                    . ' SparqlEngine_ResultRenderer interface'
                );
            }
        } else {
            throw new Exception(
                'Result renderer class "' . $resultform . '" could not be loaded.'
            );
        }
    }//public static function convertFromResult($arVartable, SparqlEngine $engine, $resultform = false)



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
        $strFile = 'SparqlEngine/ResultRenderer/' . $strClass . '.php';
        @include_once RDFAPI_INCLUDE_DIR . 'sparql/' . $strFile;
        if (class_exists('SparqlEngine_ResultRenderer_' . $strClass, false)) {
            return 'SparqlEngine_ResultRenderer_' . $strClass;
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

}//class SparqlEngine_ResultConverter
?>