<?php
/**
 * Get the Compatibility info from PHP CLI
 *
 * @version    $Id$
 * @author     Davey Shafik <davey@php.net>
 * @package    PHP_CompatInfo
 * @access     public
 */

require_once 'PHP/CompatInfo/Cli.php';

$cli = new PHP_CompatInfo_Cli();
$cli->run();
?>