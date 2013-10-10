<?php
/**
 * UUIDPlugin
 *
 * Plugin interface for UUID libraries
 *
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */
interface PandraUUIDPlugin {

    /**
     * Checks that local dependencies are met
     * @return bool UUID library dependencies met (module exists)
     */
    public static function isCapable();

    /**
     * Generates a string UUID of a default type (usually v1, time based)
     * @return string new timeuuid
     */
    public static function generate();

    /**
     * returns a type 1 (MAC address and time based) uuid
     * @return string
     */
    public static function v1();

    /**
     * returns a type 4 (random) uuid
     * @return string
     */
    public static function v4();

    /**
     * returns a type 5 (SHA-1 hash) uuid
     * @return string
     */
    public static function v5();
}
?>