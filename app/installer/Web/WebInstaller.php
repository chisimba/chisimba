<?php
/*
  +----------------------------------------------------------------------+
  | PHP Version 4                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2003 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available at through the world-wide-web at                           |
  | http://www.php.net/license/2_02.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Christian Dickmann <dickmann@php.net>                        |
  |         Pierre-Alain Joye <pajoye@php.net>                           |
  +----------------------------------------------------------------------+

  $Id$
*/
define('PEAR_Frontend_Web',1);
define('INSTALL_DIR','C:/xamp/xampp/htdocs/5ive/app/installer');
$useDHTML         = true;
@session_start();

if (!isset($_SESSION['_PEAR_Frontend_Web_js'])) {
    $_SESSION['_PEAR_Frontend_Web_js'] = false;
}
if (isset($_GET['enableJS']) && $_GET['enableJS'] == 1) {
    $_SESSION['_PEAR_Frontend_Web_js'] = true;
}
define('USE_DHTML_PROGRESS', (@$useDHTML && $_SESSION['_PEAR_Frontend_Web_js']));

// Include needed files
require_once 'PEAR/Frontend.php';
require_once 'PEAR/Registry.php';
require_once 'PEAR/Config.php';
require_once 'PEAR/Command.php';
require_once INSTALL_DIR.'/Web/Frontend/Web.php';

if (!isset($pear_user_config)) {
    if (OS_WINDOWS) {
        $pear_user_config = PEAR_CONFIG_SYSCONFDIR . '/pear.ini';
    } else {
        $pear_user_config = PEAR_CONFIG_SYSCONFDIR . '/pear.conf';
    }
}

// moving this here allows startup messages and errors to work properly
PEAR_Frontend::setFrontendClass('PEAR_Frontend_Web');
// Init PEAR Installer Code and WebFrontend
$GLOBALS['_PEAR_Frontend_Web_config'] = &PEAR_Config::singleton($pear_user_config, '');
$config  = &$GLOBALS['_PEAR_Frontend_Web_config'];

$ui = &PEAR_Command::getFrontendObject();

$ui->setConfig($config);

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ui, "displayFatalError"));

// Cient requests an Image/Stylesheet/Javascript
if (isset($_GET["css"])) {
    $ui->outputFrontendFile($_GET["css"], 'css');
}
if (isset($_GET["js"])) {
    $ui->outputFrontendFile($_GET["js"], 'js');
}
if (isset($_GET["img"])) {
    $ui->outputFrontendFile($_GET["img"], 'image');
}

$verbose = $config->get("verbose");
$cmdopts = array();
$opts    = array();
$params  = array();

$URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$dir = substr(dirname(__FILE__), 0, -strlen('PEAR/PEAR')); // strip PEAR/PEAR

$_ENV['TMPDIR'] = $_ENV['TEMP'] = $dir.'tmp';

if (!file_exists($pear_user_config)) {
    // I think PEAR_Frontend_Web is running for the first time!
    // Install it properly ...

    // First of all set some config-vars:
    $cmd = PEAR_Command::factory('config-set', $config);
    $ok = $cmd->run('config-set', array(), array('php_dir',  $dir.'PEAR'));
    $ok = $cmd->run('config-set', array(), array('doc_dir',  $dir.'docs'));
    $ok = $cmd->run('config-set', array(), array('ext_dir',  $dir.'ext'));
    $ok = $cmd->run('config-set', array(), array('bin_dir',  $dir.'bin'));
    $ok = $cmd->run('config-set', array(), array('data_dir', $dir.'data'));
    $ok = $cmd->run('config-set', array(), array('test_dir', $dir.'test'));
    $ok = $cmd->run('config-set', array(), array('cache_dir', $dir.'cache'));
    $ok = $cmd->run('config-set', array(), array('cache_ttl', 300));
    $ok = $cmd->run('config-set', array(), array('default_channel', 'pear.php.net'));
    $ok = $cmd->run('config-set', array(), array('preferred_mirror', 'pear.php.net'));

    // Register packages
    $packages = array(
                                'Archive_Tar',
                                'Console_Getopt',
                                'HTML_Template_IT',
                                'Net_UserAgent_Detect',
                                'PEAR',
                                'PEAR_Frontend_Web',
                                'XML_RPC'
                        );
    $reg = &$config->getRegistry();
    if (!file_exists($dir.'PEAR/.registry')) {
        PEAR::raiseError('Directory "'.$dir.'PEAR/.registry" does not exist. please check your installation');
    }

    foreach($packages as $pkg) {
        $info = $reg->packageInfo($pkg);
        foreach($info['filelist'] as $fileName => $fileInfo) {
            if($fileInfo['role'] == "php") {
                $info['filelist'][$fileName]['installed_as'] =
                    str_replace('{dir}',$dir, $fileInfo['installed_as']);
            }
        }
        $reg->updatePackage($pkg, $info, false);
    }
}

$cache_dir = $config->get('cache_dir');
if (!is_dir($cache_dir)) {
    include_once 'System.php';
    if (!System::mkDir('-p', $cache_dir)) {
        PEAR::raiseError('Directory "'.$cache_dir.'" does not exist and cannot be created. Please check your installation');
    }
}

// Handle some diffrent Commands
if (isset($_GET["command"])) {
    switch ($_GET["command"]) {
        case 'install':
        case 'uninstall':
        case 'upgrade':
            if (USE_DHTML_PROGRESS && isset($useDHTML)) {
                PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ui, "displayErrorImg"));
            }

            $command = $_GET["command"];
            if (strpos($_GET['pkg'], '\\\\') !== false) {
                $_GET['pkg'] = stripslashes($_GET['pkg']);
            }
            $params = array($_GET["pkg"]);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            // success
            if (USE_DHTML_PROGRESS && isset($useDHTML)) {
                echo '<script language="javascript">';
                if ($_GET["command"] == "uninstall") {
                    printf(' parent.deleteVersion(\'%s\'); ',  $_GET["pkg"]);
                    printf(' parent.displayInstall(\'%s\'); ', $_GET["pkg"]);
                    printf(' parent.hideDelete(\'%s\'); ',     $_GET["pkg"]);
                } else {
                    printf(' parent.newestVersion(\'%s\'); ',  $_GET["pkg"]);
                    printf(' parent.hideInstall(\'%s\'); ',    $_GET["pkg"]);
                    printf(' parent.displayDelete(\'%s\'); ',  $_GET["pkg"]);
                }
                echo '</script>';
                $html = sprintf('<img src="%s?img=install_ok" border="0">', $_SERVER['PHP_SELF']);
                echo $js.$html;
                exit;
            }

            if (isset($_GET['redirect']) && $_GET['redirect'] == 'info') {
                $URL .= '?command=remote-info&pkg='.$_GET["pkg"];
            } elseif (isset($_GET['redirect']) && $_GET['redirect'] == 'search') {
                $URL .= '?command=search&userDialogResult=get&0='.$_GET["0"].'&1='.$_GET["1"];
            } else {
                $URL .= '?command=list-all&pageID='.$_GET['pageID'].'#'.$_GET["pkg"];
            }
            Header("Location: ".$URL);
            exit;
        case 'run-scripts' :
            $command = $_GET["command"];
            $params = array($_GET["pkg"]);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
             $ui->outputData($ok); 
            exit;
        case 'remote-info':
            $command = $_GET["command"];
            $params = array($_GET["pkg"]);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
			 $ui->outputData($ok); 
            exit;
        case 'search':
            list($name, $description) = $ui->userDialog('search',
                array('Package Name', 'Package Info'), // Prompts
                array(), array(), // Types, Defaults
                'Package Search', 'pkgsearch' // Title, Icon
                );
		
            $command = $_GET["command"];
            $params = array($name, $description);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
			 $ui->outputData($ok); 
            exit;
        case 'config-show':
            $command = $_GET["command"];
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);
            foreach($GLOBALS['_PEAR_Frontend_Web_Config'] as $var => $value) {
                $command = 'config-set';
                $params = array($var, $value);
                $cmd = PEAR_Command::factory($command, $config);
                $res = $cmd->run($command, $opts, $params);
            }

            $URL .= '?command=config-show';
            header("Location: ".$URL);
            exit;
        case 'list-all':
            $command = $_GET["command"];
            $params = array();
            if (isset($_GET["mode"]))
                $opts['mode'] = $_GET["mode"];
                //$opts =array('force' => true);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
            $ui->outputData($ok); 
           
			
            exit;
        case 'channel-info':
            $command = $_GET["command"];
            $params = array();
            if (isset($_GET["chan"]))
                $params[] = $_GET["chan"];
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
			 $ui->outputData($ok); 
            exit;
        case 'channel-discover':
            $command = $_GET["command"];
            $params = array();
            if (isset($_GET["chan"]))
                $params[] = $_GET["chan"];
            $cmd = PEAR_Command::factory($command, $config);
            $ui->startSession();
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Channel Discovery', array('link' =>
                $_SERVER['PHP_SELF'] . '?command=channel-info&chan=' . urlencode($_GET['chan']),
                'text' => 'Click Here for ' . htmlspecialchars($_GET['chan']) . ' Information'));
            exit;
        case 'channel-delete':
            $command = $_GET["command"];
            $params = array();
            if (isset($_GET["chan"]))
                $params[] = $_GET["chan"];
            $cmd = PEAR_Command::factory($command, $config);
            $ui->startSession();
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Channel Deletion');
            exit;
        case 'list-channels':
            $command = $_GET["command"];
            $params = array();
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
			 $ui->outputData($ok); 
            exit;
        case 'update-channels':
            $command = $_GET["command"];
            $params = array();
            $cmd = PEAR_Command::factory($command, $config);
            $ui->startSession();
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Update Channel List', array('link' =>
                $_SERVER['PHP_SELF'] . '?command=list-channels',
                'text' => 'Click here to list all channels'));
            exit;
        case 'show-last-error':
            $GLOBALS['_PEAR_Frontend_Web_log'] = $_SESSION['_PEAR_Frontend_Web_LastError_log'];
            $ui->displayError($_SESSION['_PEAR_Frontend_Web_LastError'], 'Error', 'error', true);
            exit;
        default:
            $command = $_GET["command"];
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            $URL .= '?command='.$_GET["command"];
            header("Location: ".$URL);
            exit;
    }
}

$ui->displayStart();
?>
