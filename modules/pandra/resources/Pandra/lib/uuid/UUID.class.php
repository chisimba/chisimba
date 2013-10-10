<?php
/**
 * UUID plugin registrar
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
class UUID {

    const UUID_BIN = 0;

    const UUID_STR = 1;

    static private $_pluginRef = NULL;

    static private $_pluginPfx = 'UUIDPlugin';

    /**
     * Automatically registers the first uuid plugin which meets capability
     */
    public static function auto() {
        $dir = PANDRA_INSTALL_DIR.'/uuid/';
        $files = scandir($dir);
        $ok = FALSE;
        foreach ($files as $fname) {
            if ($fname == '..' ||
                    $fname == '..' ||
                    !preg_match('/^'.(self::$_pluginPfx).'.*\.class.php/', $fname)) {
                continue;
            }

            $tokens = explode('.', $fname);
            $className = preg_replace('/^'.(self::$_pluginPfx).'/', '', $tokens[0]);
            if (self::register($className)) {
                PandraLog::info('Registered UUID class '.$className);
                $ok = TRUE;
                break;
            }
        }
        return $ok;
    }

    private static function bridge($function) {
        if (self::$_pluginRef !== NULL) {
            return call_user_func(array(self::$_pluginRef, $function));
        }
        throw new RuntimeException('No UUID Plugins initialised');
    }

    public static function generate() {
        return self::bridge('generate');
    }

    public static function v1() {
        return self::bridge('v1');
    }

    public static function v4() {
        return self::bridge('v4');
    }

    public static function v5() {
        return self::bridge('v5');
    }

    public static function register($pluginName) {
        $className = 'Pandra'.self::$_pluginPfx.$pluginName;

        if (class_exists($className) && call_user_func(array($className, 'isCapable'))) {
            self::$_pluginRef = $className;
            return TRUE;
        }
        return FALSE;
    }

    public static function toStr($uuid) {
        if (self::isBinary($uuid)) {
            $unpacked_string = unpack("H8a/H4b/H4c/H4d/H12e", $uuid);
            return(implode("-", $unpacked_string));
        }
        return $uuid;
    }

    public static function toBin($uuid) {
        if (!self::isBinary($uuid)) {
            $reduced_uuid = str_replace("-", "", $uuid);
            return (pack("H*", $reduced_uuid));
        }
        return $uuid;
    }

    public static function validUUID($uuidStr) {
        if (self::isBinary($uuidStr)) {
            return (strlen($uuidStr) == 16);
        } else {
            return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuidStr);
        }
    }

    public static function isBinary($uuid) {
        return preg_match('/((?![\x20-\x7E]).)/', $uuid);
    }
}
?>
