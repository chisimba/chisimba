<?php
/**
 * PandraValidator
 *
 * Validates an input against an array of defined data types, populating an error
 * message and logging to debug loggers
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class PandraValidator {

    // primitives for which there is self::check() logic
    static public $primitive = array(
            'notempty',
            'isempty',          // honeypot
            'int',
            'float',
            'numeric',
            'string',
            'bool',             // PHP bool types or strings 'true', 'false', 't', 'f', '1', '0', 'y', 'n'
            'maxlength',	// =[length]
            'minlength',	// =[length]
            'enum',		// =[comma,delimitered,enumerates]
            'email',
            'url',
            'uuid'
    );

    /**
     * Complex types are aggregates of the predefined primitive type definitions. Similarly,
     * the type definitions can also be aggregated to build even more complex types (try not to get crazy with the stack yo).
     * In cases where there appears to be collision between types (aggregate types with different maxlength options for example)
     * the final type will be viewed as authoritive.
     */
    static public $complex = array(
            'stringregular' => array('string', 'notempty'),
            'string20' => array('stringregular', 'maxlength=20'),
    );

    /**
     * Type definition is defined
     * @param string $typeDef check type is available
     * @return bool type exists
     */
    static public function exists($typeDef) {
        if (stripos($typeDef, '=') != 0) {
            list($type, $args) = explode('=', $typeDef);
        } else {
            $type = $typeDef;
        }

        return (in_array($type, self::$primitive) || array_key_exists($type, self::$complex));
    }

    /**
     * given a typedef array, detects complex types and expands to primitives
     * @param array &$typeDefs validating type definitions
     */
    static private function typeExpander(&$typeDefs) {

        $isComplex = FALSE;

        foreach ($typeDefs as $idx => $typeDef) {

            // check if type is complex
            if (array_key_exists($typeDef, self::$complex)) {

                // drop this complex type from our typeDefs, ready to expand
                unset($typeDefs[$idx]);

                // merge against complex type def
                $typeDefs = array_merge($typeDefs, self::$complex[$typeDef]);

                // if it looks like this type has expanded to another complex type, then flag for recursion
                foreach ($typeDefs as $xType) {
                    if (array_key_exists($xType, self::$complex)) {
                        $isComplex = TRUE;
                    }
                }
            }
        }

        // recurse, expand out new complex type
        if ($isComplex) self::typeExpander($typeDefs);
    }


    /**
     * Validates a field
     * @param string $errorMsg custom error message for field validation error
     * @return bool field validated correctly
     */
    static public function check($value, $label, $typeDefs, &$errors) {
        if (empty($typeDefs)) return TRUE;

        if (!is_array($typeDefs)) $typeDefs = array($typeDefs);

        // normalise to real type defs if complex types found
        self::typeExpander($typeDefs);

        $error = FALSE;
        $errorMsg = array();

        foreach ($typeDefs as $type) {

            if (preg_match('/=/', $type)) {
                list($type, $args) = explode("=", $type);
            }

            if (!in_array($type, self::$primitive)) {
                throw new RuntimeException("undefined type definition ($type)");
            }

            // check for basic validator types
            switch ($type) {
                case 'notempty' :
                    $error = empty($value);
                    if ($error) $errorMsg[] = "Field cannot be empty";
                    break;

                case 'isempty' :
                // NULL is never allowed, just empty strings
                    $error = ($value != '');
                    if ($error) $errorMsg[] = "Field must be empty";
                    break;

                case 'email' :
                    $error = !filter_var($value, FILTER_VALIDATE_EMAIL);
                    if ($error) $errorMsg[] = "Invalid email address";
                    break;

                case 'url' :
                    $error = !filter_var($value, FILTER_VALIDATE_URL);
                    if ($error) $errorMsg[] = "Invalid URL";
                    break;

                case 'float' :
                    $error = !is_float($value);
                    if ($error) $errorMsg[] = "Field error, expected ".$type;
                    break;
                case 'int' :
                case 'numeric' :
                    $error = !is_numeric($value);
                    if ($error) $errorMsg[] = "Field error, expected ".$type;
                    break;

                case 'string' :
                    $error = !is_string($value);
                    if ($error) $errorMsg[] = "Field error, expected ".$type;
                    break;

                case 'bool' :
                    $val = strtolower($value);
                    $boolVals = array('true', 'false', 't', 'f', '1', '0', 'y', 'n');
                    $error = !is_bool($value) && !(in_array($val, $boolVals));
                    if ($error) $errorMsg[] = "Field error, expected ".$type;
                    break;

                case 'maxlength' :
                    if (empty($args)) throw new RuntimeException("type $type requires argument");
                    $error = (strlen($value) > $args);
                    if ($error) $errorMsg[] = "Maximum length $args exceeded";
                    break;

                case 'minlength' :
                    if (empty($args)) throw new RuntimeException("type $type requires argument");
                    $error = (strlen($value) < $args);
                    if ($error) $errorMsg[] = "Minimum length $args unmet";
                    break;

                case 'enum' :
                    if (empty($args)) throw new RuntimeException("type $type requires argument");
                    $enums = explode(",", $args);
                    $error = (!in_array($value, $enums));
                    if ($error) $errorMsg[] = "Invalid Argument";
                    break;

                case 'uuid' :
                    $error = (!UUID::validUUID($value));
                    if ($error) $errorMsg[] = "Invalid UUID (UUID String expected)";
                    break;

                default :
                    throw new RuntimeException("Unhandled type definition ($type)");
                    break;
            }
        }

        if (!empty($errorMsg)) {
            $errors[] = array($label => $errorMsg);
            PandraLog::debug(array($label => $errorMsg));
        }
 
        return empty($errorMsg);
    }
}
?>