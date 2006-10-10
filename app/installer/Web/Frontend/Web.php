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
  +----------------------------------------------------------------------+

  $Id$
*/

require_once "PEAR/Frontend.php";
require_once "PEAR/Remote.php";
require_once "HTML/Template/IT.php";
require_once "Net/UserAgent/Detect.php";

/**
* PEAR_Frontend_Web is a HTML based Webfrontend for the PEAR Installer
*
* The Webfrontend provides basic functionality of the Installer, such as
* a package list grouped by categories, a search mask, the possibility
* to install/upgrade/uninstall packages and some minor things.
* PEAR_Frontend_Web makes use of the PEAR::HTML_IT Template engine which
* provides the possibillity to skin the Installer.
*
* @author  Christian Dickmann <dickmann@php.net>
* @package PEAR_Frontend_Web
* @access  private
*/

class PEAR_Frontend_Web extends PEAR_Frontend
{
    // {{{ properties

    /**
     * What type of user interface this frontend is for.
     * @var string
     * @access public
     */
    var $type = 'Web';

    /**
     * Container, where values can be saved temporary
     * @var array
     * @access private
     */
    var $_data = array();
    var $_savedOutput = array();

    // }}}
    var $config;

    var $_no_delete_pkgs = array(
        'PEAR',
        'PEAR_Frontend_Web',
        'Archive_Tar',
        'Console_Getopt',
        'XML_RPC',
        'Net_UserAgent_Detect',
    );

    var $_no_delete_chans = array(
        'pear.php.net',
        '__uri',
        );

    /**
     * Flag to determine whether to treat all output as information from a post-install script
     * @var bool
     * @access private
     */
    var $_installScript = false;
    // {{{ constructor

    function PEAR_Frontend_Web()
    {
        parent::PEAR();
        $GLOBALS['_PEAR_Frontend_Web_log'] = '';
        $this->config = &$GLOBALS['_PEAR_Frontend_Web_config'];
    }

    // }}}

    function setConfig(&$config)
    {
        $this->config = &$config;
    }

    // {{{ displayLine()

    /* XXX some methods from CLI following. should be deleted in the near future */
    function displayLine($text)
    {
        trigger_error("Frontend::display deprecated", E_USER_ERROR);
    }

    // }}}
    // {{{ display()

    function display($text)
    {
        trigger_error("Frontend::display deprecated", E_USER_ERROR);
    }

    // }}}
    // {{{ userConfirm()

    function userConfirm($prompt, $default = 'yes')
    {
        trigger_error("Frontend::display deprecated", E_USER_ERROR);
        return false;
    }

    // }}}
    // {{{ displayStart(prompt, [default])

    function displayStart()
    {
        $tpl = $this->_initTemplate("start.tpl.html", 'PEAR Installer');
        $tpl->setVariable('Version', '0.5');
        $tpl->show();
        exit;
    }

    // }}}
    // {{{ _initTemplate()

    /**
     * Initialize a TemplateObject, add a title, and icon and add JS and CSS for DHTML
     *
     * @param string  $file     filename of the template file
     * @param string  $title    (optional) title of the page
     * @param string  $icon     (optional) iconhandle for this page
     * @param boolean $useDHTML (optional) add JS and CSS for DHTML-features
     *
     * @access private
     *
     * @return object Object of HTML/IT - Template - Class
     */

    function _initTemplate($file, $title = '', $icon = '', $useDHTML = true)
    {
        $tpl = new HTML_Template_IT(dirname(__FILE__)."/Web");
        $tpl->loadTemplateFile($file);
        $tpl->setVariable("InstallerURL", $_SERVER["PHP_SELF"]);
        if ($this->config->get('preferred_mirror') != $this->config->get('default_channel')) {
            $mirror = ' (mirror ' .$this->config->get('preferred_mirror') . ')';
        } else {
            $mirror = '';
        }
        $tpl->setVariable("_default_channel", $this->config->get('default_channel') . $mirror);
        $tpl->setVariable("ImgPEAR", $_SERVER["PHP_SELF"].'?img=pear');
        if ($title) {
            $tpl->setVariable("Title", $title);
        }
        if ($icon) {
            $tpl->setCurrentBlock("TitleBlock");
            $tpl->setVariable("_InstallerURL", $_SERVER["PHP_SELF"]);
            $tpl->setVariable("_Title", $title);
            $tpl->setVariable("_Icon", $icon);
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock();

        if ($useDHTML && Net_UserAgent_Detect::getBrowser('ie5up') == 'ie5up') {
            $dhtml = true;
        } else {
            $dhtml = false;
        }

        if ($dhtml) {
            $tpl->setVariable("JS", 'dhtml');
            $css = '<link rel="stylesheet" href="'.$_SERVER['PHP_SELF'].'?css=dhtml" />';
            $tpl->setVariable("DHTMLcss", $css);
        } else {
            $tpl->setVariable("JS", 'nodhtml');
        }

        if (!isset($_SESSION['_PEAR_Frontend_Web_js']) || $_SESSION['_PEAR_Frontend_Web_js'] == false) {
            $tpl->setCurrentBlock('JSEnable');
            $tpl->setVariable('RedirectURL', $_SERVER['REQUEST_URI']. (!empty($_GET) ? '&' : '?') .'enableJS=1');
            $tpl->parseCurrentBlock();
            $tpl->setCurrentBlock();
        }

        return $tpl;
    }

    // }}}
    // {{{ displayError()

    /**
     * Display an error page
     *
     * @param mixed   $eobj  PEAR_Error object or string containing the error message
     * @param string  $title (optional) title of the page
     * @param string  $img   (optional) iconhandle for this page
     * @param boolean $popup (optional) popuperror or normal?
     *
     * @access public
     *
     * @return null does not return anything, but exit the script
     */

    function displayError($eobj, $title = 'Error', $img = 'error', $popup = false)
    {
        $msg = '';
        if (isset($GLOBALS['_PEAR_Frontend_Web_log']) && trim($GLOBALS['_PEAR_Frontend_Web_log'])) {
            $msg = trim($GLOBALS['_PEAR_Frontend_Web_log'])."\n\n";
        }

        if (PEAR::isError($eobj)) {
            $msg .= trim($eobj->getMessage());
        } else {
            $msg .= trim($eobj);
        }

        $msg = nl2br($msg."\n");

        $tplfile = ($popup ? "error.popup.tpl.html" : "error.tpl.html");
        $tpl = $this->_initTemplate($tplfile, $title, $img);

        $tpl->setVariable("Error", $msg);
        $command_map = array(
            "install"   => "list-all",
            "uninstall" => "list-all",
            "upgrade"   => "list-all",
            );
        if (isset($_GET['command'])) {
            if (isset($command_map[$_GET['command']])) {
                $_GET['command'] = $command_map[$_GET['command']];
            }
            $tpl->setVariable("param", '?command='.$_GET['command']);
        }

        $tpl->show();
        exit;
    }

    // }}}
    // {{{ displayFatalError()

    /**
     * Alias for PEAR_Frontend_Web::displayError()
     *
     * @see PEAR_Frontend_Web::displayError()
     */

    function displayFatalError($eobj, $title = 'Error', $img = 'error')
    {
        $this->displayError($eobj, $title, $img);
    }

    function displayErrorImg($eobj)
    {
        $msg = '';
        if (isset($GLOBALS['_PEAR_Frontend_Web_log']) && trim($GLOBALS['_PEAR_Frontend_Web_log']))
            $msg = trim($GLOBALS['_PEAR_Frontend_Web_log'])."\n\n";

        $_SESSION['_PEAR_Frontend_Web_LastError']     = $eobj;
        $_SESSION['_PEAR_Frontend_Web_LastError_log'] = $msg;
        echo '<script language="javascript">';
        printf('window.open("%s?command=show-last-error", "PEAR", "width=600, height=400");',
            $_SERVER["PHP_SELF"]);
        echo ' </script>';
        printf('<img src="%s?img=install_fail" border="0">', $_SERVER['PHP_SELF']);
        exit;
    }

    // }}}
    // {{{ _outputListChannels()

    function _outputListChannels($data, $title = 'Manage Installer Channels',
                            $img = 'pkglist', $useDHTML = false, $paging = true)
    {
        $tpl = $this->_initTemplate("channel.list.tpl.html", $title, $img, $useDHTML);
        if (!isset($data['data'])) {
            $data['data'] = array();
        }
        $pageId = isset($_GET['from']) ? $_GET['from'] : 0;
        $paging_data = $this->__getData($pageId, 5, count($data['data']), false);

        $data['data'] = array_slice($data['data'], $pageId, 5);

        $links = array();
        $from = $paging_data['from'];
        $to = $paging_data['next'];

        // Generate Linkinformation to redirect to _this_ page after performing an action
        $link_str = '<a href="?command=%s&from=%s" class="paging_link">%s</a>';

        $command = isset($_GET['command']) ? $_GET['command'] : 'list-channels';

        if ($paging_data['from']>1) {
            $links['back'] = sprintf($link_str, $command, $paging_data['prev'], '&lt;&lt;');
        } else {
            $links['back'] = '';
        }

        if ( $paging_data['next']) {
            $links['next'] = sprintf($link_str, $command, $paging_data['next'], '&gt;&gt;');
        } else {
            $links['next'] = '';
        }

        $links['current'] = '&from=' . $paging_data['from'];

        $tpl->setVariable('Prev', $links['back']);
        $tpl->setVariable('Next', $links['next']);
        $tpl->setVariable('PagerFrom', $from);
        $tpl->setVariable('PagerTo', $to);
        $tpl->setVariable('PagerCount', $paging_data['numrows']);
        $reg = &$this->config->getRegistry();
        foreach($data['data'] as $row) {
            list($channel, $summary) = $row;
            $tpl->setCurrentBlock("Row");
            $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');
            $images = array(
                'delete' => '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="delete">',
                'info' => '<img src="'.$_SERVER["PHP_SELF"].'?img=info"  width="17" height="19" border="0" alt="info">',
                );
            $urls   = array(
                'delete' => sprintf('%s?command=channel-delete&chan=%s%s',
                    $_SERVER["PHP_SELF"], urlencode($channel), $links['current']),
                'info' => sprintf('%s?command=channel-info&chan=%s',
                    $_SERVER["PHP_SELF"], urlencode($channel)),
                );

            // detect whether any packages from this channel are installed
            $anyinstalled = $reg->listPackages($channel);
            $id = 'id="'.$channel.'_href"';
            if (is_array($anyinstalled) && count($anyinstalled)) {
                $del = '';
            } else {
                $del = sprintf('<a href="%s" onClick="return deleteChan(\'%s\');" %s >%s</a>',
                    $urls['delete'], $channel, $id, $images['delete']);
            }
            $info    = sprintf('<a href="%s">%s</a>', $urls['info'],    $images['info']);

            if (in_array($channel, $this->_no_delete_chans)) {
                $del = '';
            }

            $tpl->setVariable("NewChannelURL", $_SERVER['PHP_SELF']);
            $tpl->setVariable("Delete", $del);
            $tpl->setVariable("Info", $info);
            $tpl->setVariable("Channel", $channel);
            $tpl->setVariable("Summary", nl2br($summary));
            $tpl->parseCurrentBlock();
        }
        $tpl->show();
        return true;
    }
    // }}}
    // {{{ _outputListAll()

    /**
     * Output a list of packages, grouped by categories. Uses Paging
     *
     * @param array   $data     array containing all data to display the list
     * @param string  $title    (optional) title of the page
     * @param string  $img      (optional) iconhandle for this page
     * @param boolean $useDHTML (optional) add JS and CSS for DHTML-features
     * @param boolean $paging   (optional) use Paging or not
     *
     * @access private
     *
     * @return boolean true (yep. i am an optimist)
     */

    function _outputListAll($data, $title = 'Install / Upgrade / Remove PEAR Packages',
                            $img = 'pkglist', $useDHTML = false, $paging = true)
    {
        $tpl = $this->_initTemplate("package.list.tpl.html", $title, $img, $useDHTML);

        if (!isset($data['data'])) {
            $data['data'] = array();
        }

        $pageId = isset($_GET['from']) ? $_GET['from'] : 0;
        $paging_data = $this->__getData($pageId, 5, count($data['data']), false);

        $data['data'] = array_slice($data['data'], $pageId, 5);

        $links = array();
        $from = $paging_data['from'];
        $to = $paging_data['next'];

        // Generate Linkinformation to redirect to _this_ page after performing an action
        $link_str = '<a href="?command=%s&from=%s&mode=%s" class="paging_link">%s</a>';



        $command = isset($_GET['command']) ? $_GET['command']:'list-all';
        $mode = isset($_GET['mode'])?$_GET['mode']:'';



        if ($paging_data['from']>1) {
            $links['back'] = sprintf($link_str, $command, $paging_data['prev'], $mode, '&lt;&lt;');
        } else {
            $links['back'] = '';
        }

        if ( $paging_data['next']) {
            $links['next'] = sprintf($link_str, $command, $paging_data['next'], $mode, '&gt;&gt;');
        } else {
            $links['next'] = '';
        }

        $links['current'] = '&from=' . $paging_data['from']  . '&mode=' . $mode;

        if (isset($_GET['command']) && $_GET['command'] == 'search') {
            $links['current'] .= '&redirect=search&0='.$_REQUEST[0].'&1='.$_REQUEST[1];
        }

        $modes = array(
            'installed'    => 'list installed packages',
            ''             => 'list all packages',
            'notinstalled' => 'list non-installed packages',
            'upgrades'     => 'list avail. upgrades',
            );

        $i = 1;
        foreach($modes as $mode => $text) {
            $tpl->setVariable('mode' . $i , !empty($mode) ? '&mode='.$mode : '');
            $tpl->setVariable('mode' . $i.'text', $text);
            $i++;
        }

        $tpl->setVariable('Prev', $links['back']);
        $tpl->setVariable('Next', $links['next']);
        $tpl->setVariable('PagerFrom', $from);
        $tpl->setVariable('PagerTo', $to);
        $tpl->setVariable('PagerCount', $paging_data['numrows']);

        $reg = &$this->config->getRegistry();
        foreach($data['data'] as $category => $packages) {
            foreach($packages as $row) {
                list($pkgName, $pkgVersionLatest, $pkgVersionInstalled, $pkgSummary) = $row;
                $parsed = $reg->parsePackageName($pkgName, $this->config->get('default_channel'));
                $pkgChannel = $parsed['channel'];
                $pkgName = $parsed['package'];
                $tpl->setCurrentBlock("Row");
                $tpl->setVariable("ImgPackage", $_SERVER["PHP_SELF"].'?img=package');
                $images = array(
                    'install' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="install">',
                    'uninstall' => '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="uninstall">',
                    'upgrade' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="upgrade">',
                    'info' => '<img src="'.$_SERVER["PHP_SELF"].'?img=info"  width="17" height="19" border="0" alt="info">',
                    'infoExt' => '<img src="'.$_SERVER["PHP_SELF"].'?img=infoplus"  width="18" height="19" border="0" alt="extended info">',
                    );
                $urls   = array(
                    'install' => sprintf('%s?command=install&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgName, $links['current']),
                    'uninstall' => sprintf('%s?command=uninstall&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgName, $links['current']),
                    'upgrade' => sprintf('%s?command=upgrade&pkg=%s%s',
                        $_SERVER["PHP_SELF"], $pkgName, $links['current']),
                    'info' => sprintf('%s?command=remote-info&pkg=%s',
                        $_SERVER["PHP_SELF"], $pkgName),
                    'infoExt' => 'http://' . $this->config->get('preferred_mirror')
                         . '/package/' . $row[0],
                    );

                $compare = version_compare($pkgVersionLatest, $pkgVersionInstalled);
                $id = 'id="'.$pkgName.'_href"';
                if (!$pkgVersionInstalled || $pkgVersionInstalled == "- no -") {
                    $inst = sprintf('<a href="%s" onClick="return perform(\'%s\');" %s>%s</a>',
                        $urls['install'], $pkgName, $id, $images['install']);
                    $del = '';
                } else if ($compare == 1) {
                    $inst = sprintf('<a href="%s" onClick="return perform(\'%s\');" %s>%s</a>',
                        $urls['upgrade'], $pkgName, $id, $images['upgrade']);
                    $del = sprintf('<a href="%s" onClick="return deletePkg(\'%s\');" %s >%s</a>',
                        $urls['uninstall'], $pkgName, $id, $images['uninstall']);
                } else {
                    $del = sprintf('<a href="%s" onClick="return deletePkg(\'%s\');" %s >%s</a>',
                        $urls['uninstall'], $pkgName, $id, $images['uninstall']);
                    $inst = '';
                }
                $info    = sprintf('<a href="%s">%s</a>', $urls['info'],    $images['info']);
                $infoExt = sprintf('<a href="%s">%s</a>', $urls['infoExt'], $images['infoExt']);

                if ($reg->channelName($pkgChannel) == 'pear.php.net' &&
                      in_array($pkgName, $this->_no_delete_pkgs)) {
                    $del = '';
                }

                $tpl->setVariable("Latest", $pkgVersionLatest);
                $tpl->setVariable("Installed", $pkgVersionInstalled);
                $tpl->setVariable("Install", $inst);
                $tpl->setVariable("Delete", $del);
                $tpl->setVariable("Info", $info);
                $tpl->setVariable("InfoExt", $infoExt);
                $tpl->setVariable("Package", $pkgName);
                $tpl->setVariable("Channel", $pkgChannel);
                $tpl->setVariable("Summary", nl2br($pkgSummary));
                $tpl->parseCurrentBlock();
            }
            $tpl->setCurrentBlock("Category");
            $tpl->setVariable("categoryName", $category);
            $tpl->setVariable("ImgCategory", $_SERVER["PHP_SELF"].'?img=category');
            $tpl->parseCurrentBlock();
        }
        $tpl->show();

        return true;
    }

    function _getPackageDeps($deps)
    {
        if (count($deps) == 0) {
            return "<i>No dependencies registered.</i>\n";
        } else {
            $lastversion = '';
            $rel_trans = array(
                'lt' => 'older than %s',
                'le' => 'version %s or older',
                'eq' => 'version %s',
                'ne' => 'any version but %s',
                'gt' => 'newer than %s',
                'ge' => '%s or newer',
                );
            $dep_type_desc = array(
                'pkg'    => 'PEAR Package',
                'ext'    => 'PHP Extension',
                'php'    => 'PHP Version',
                'prog'   => 'Program',
                'ldlib'  => 'Development Library',
                'rtlib'  => 'Runtime Library',
                'os'     => 'Operating System',
                'websrv' => 'Web Server',
                'sapi'   => 'SAPI Backend',
                );
            $result = "      <dl>\n";
            foreach($deps as $row) {

                // Print link if it's a PEAR package
                if ($row['type'] == 'pkg') {
                    $row['name'] = sprintf('<a class="green" href="%s?command=remote-info&pkg=%s">%s</a>',
                        $_SERVER['PHP_SELF'], $row['name'], $row['name']);
                }

                if (isset($rel_trans[$row['relation']])) {
                    $rel = sprintf($rel_trans[$row['relation']], $row['version']);
                    $result .= sprintf("%s: %s %s",
                           $dep_type_desc[$row['type']], $row['name'], $rel);
                } else {
                    $result .= sprintf("%s: %s", $dep_type_desc[$row['type']], $row['name']);
                }
                $lastversion = $row['version'];
                $result .= '<br>';
            }
            if ($lastversion) {
            }
            $result .= "      </dl>\n";
        }
        return $result;
    }

    /**
     * Output details of one package
     *
     * @param array $data array containing all information about the package
     *
     * @access private
     *
     * @return boolean true (yep. i am an optimist)
     */

    function _outputPackageInfo($data)
    {
        include_once "PEAR/Downloader.php";
        $tpl = $this->_initTemplate("package.info.tpl.html", 'Package Management :: '.$data['name'], 'pkglist');

        $tpl->setVariable("PreferredMirror", $this->config->get('preferred_mirror'));
        $dl = &new PEAR_Downloader($this, array(), $this->config);
        $info = $dl->_getPackageDownloadUrl(array('package' => $data['name'],
            'channel' => $this->config->get('default_channel'), 'version' => $data['stable']));
        if (isset($info['url'])) {
            $tpl->setVariable("DownloadURL", $info['url']);
        } else {
            $tpl->setVariable("DownloadURL", $_SERVER['PHP_SELF']);
        }
        $tpl->setVariable("Latest", $data['stable']);
        $tpl->setVariable("Installed", $data['installed']);
        $tpl->setVariable("Package", $data['name']);
        $tpl->setVariable("License", $data['license']);
        $tpl->setVariable("Category", $data['category']);
        $tpl->setVariable("Summary", nl2br($data['summary']));
        $tpl->setVariable("Description", nl2br($data['description']));
        $deps = @$data['releases'][$data['stable']]['deps'];
        $tpl->setVariable("Dependencies", $this->_getPackageDeps($deps));

        $compare = version_compare($data['stable'], $data['installed']);

        $images = array(
            'install' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="install">',
            'uninstall' => '<img src="'.$_SERVER["PHP_SELF"].'?img=uninstall" width="18" height="17"  border="0" alt="uninstall">',
            'upgrade' => '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="upgrade">',
            );

        $opt_img = array();
        $opt_text = array();
        if (!$data['installed'] || $data['installed'] == "- no -") {
            $opt_img[] = sprintf(
                '<a href="%s?command=install&pkg=%s&redirect=info">%s</a>',
                $_SERVER["PHP_SELF"], $data['name'], $images['install']);
            $opt_text[] = sprintf(
                '<a href="%s?command=install&pkg=%s&redirect=info" class="green">Install package</a>',
                $_SERVER["PHP_SELF"], $data['name']);
        } else if ($compare == 1) {
            $opt_img[] = sprintf(
                '<a href="%s?command=upgrade&pkg=%s&redirect=info">%s</a><br>',
                $_SERVER["PHP_SELF"], $data['name'], $images['upgrade']);
            $opt_text[] = sprintf(
                '<a href="%s?command=upgrade&pkg=%s&redirect=info" class="green">Upgrade package</a>',
                $_SERVER["PHP_SELF"], $data['name']);
            if (!in_array($data['name'], $this->_no_delete_pkgs)) {
                $opt_img[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s&redirect=info" %s>%s</a>',
                    $_SERVER["PHP_SELF"], $data['name'],
                    'onClick="return confirm(\'Do you really want to uninstall \\\''.$data['name'].'\\\'?\')"',
                    $images['uninstall']);
                $opt_text[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s&redirect=info" class="green" %s>Uninstall package</a>',
                    $_SERVER["PHP_SELF"], $data['name'],
                    'onClick="return confirm(\'Do you really want to uninstall \\\''.$data['name'].'\\\'?\')"');
           }
        } else {
            if (!in_array($data['name'], $this->_no_delete_pkgs)) {
                $opt_img[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s&redirect=info" %s>%s</a>',
                    $_SERVER["PHP_SELF"], $data['name'],
                    'onClick="return confirm(\'Do you really want to uninstall \\\''.$data['name'].'\\\'?\')"',
                    $images['uninstall']);
                $opt_text[] = sprintf(
                    '<a href="%s?command=uninstall&pkg=%s&redirect=info" class="green" %s>Uninstall package</a>',
                    $_SERVER["PHP_SELF"], $data['name'],
                    'onClick="return confirm(\'Do you really want to uninstall \\\''.$data['name'].'\\\'?\')"');
           }
        }

        if (isset($opt_img[0]))
        {
            $tpl->setVariable("Opt_Img_1", $opt_img[0]);
            $tpl->setVariable("Opt_Text_1", $opt_text[0]);
        }
        if (isset($opt_img[1]))
        {
            $tpl->setVariable("Opt_Img_2", $opt_img[1]);
            $tpl->setVariable("Opt_Text_2", $opt_text[1]);
        }

        $tpl->show();
        return true;
    }

    /**
     * Output details of one channel
     *
     * @param array $data array containing all information about the channel
     *
     * @access private
     *
     * @return boolean true (yep. i am an optimist)
     */

    function _outputChannelInfo($data)
    {
        $tpl = $this->_initTemplate("channel.info.tpl.html",
            'Channel Management :: '.$data['main']['data']['server'][1], 'pkglist');

        $tpl->setVariable("Channel", $data['main']['data']['server'][1]);
        if (isset($data['main']['data']['alias'])) {
            $tpl->setVariable("Alias", $data['main']['data']['alias'][1]);
        } else {
            $tpl->setVariable("Alias", $data['main']['data']['server'][1]);
        }
        $tpl->setVariable("Summary", $data['main']['data']['summary'][1]);
        $tpl->setVariable("ValidationPackage", $data['main']['data']['vpackage'][1]);
        $tpl->setVariable("ChannelValidationPackageVersion",
            $data['main']['data']['vpackageversion'][1]);
        if (!in_array($data['main']['data']['server'][1], array('pear.php.net', '__uri'))) {
            // see if the validation package is installed.  If not, allow the user to install it
            $reg = &$this->config->getRegistry();
            do {
                if ($reg->packageExists($data['main']['data']['vpackage'][1],
                      $data['main']['data']['server'][1])) {
                    $installed = true;
                    if ($reg->packageInfo($data['main']['data']['vpackage'][1], 'version',
                          $data['main']['data']['server'][1]) ==
                          $data['main']['data']['vpackageversion'][1]) {
                        break; // poor man's throw
                    }
                } else {
                    $installed = false;
                }
                // finish this
                $pname = $reg->parsedPackageNameToString(array('channel' =>
                    $data['main']['data']['server'][1],
                    'package' => $data['main']['data']['vpackage'][1],
                    'version' => $data['main']['data']['vpackageversion'][1]));
                $opt_img[] = sprintf(
                    '<a href="%s?command=install&pkg=%s&redirect=info">%s</a>',
                    $_SERVER["PHP_SELF"], $pname
                    , '<img src="'.$_SERVER["PHP_SELF"].'?img=install" width="13" height="13" border="0" alt="install">');
                $opt_text[] = sprintf(
                    '<a href="%s?command=install&pkg=%s&redirect=info" class="green">Install package</a>',
                    $_SERVER["PHP_SELF"], $data['name']);
            } while (false);
        }
        $tpl->show();
        return true;
    }

    /**
     * Output all kinds of data depending on the command which called this method
     *
     * @param mixed  $data    datastructure containing the information to display
     * @param string $command (optional) command from which this method was called
     *
     * @access public
     *
     * @return mixed highly depends on the command
     */

    function outputData($data, $command = '_default')
    {
        switch ($command) {
            case 'config-show':
                $prompt  = array();
                $default = array();
                foreach($data['data'] as $group) {
                    foreach($group as $row) {
                        $prompt[$row[1]]  = $row[0];
                        $default[$row[1]] = $row[2];
                    }
                }
                $title = 'Configuration :: '.$GLOBALS['pear_user_config'];
                $GLOBALS['_PEAR_Frontend_Web_Config'] =
                    $this->userDialog($command, $prompt, array(), $default, $title, 'config');
                return true;
            case 'list-all':
                return $this->_outputListAll($data);
            case 'list-channels':
                return $this->_outputListChannels($data);
            case 'search':
                return $this->_outputListAll($data, 'Package Search :: Result', 'pkgsearch', false, false);
            case 'remote-info':
                return $this->_outputPackageInfo($data);
            case 'channel-info':
                return $this->_outputChannelInfo($data);
            case 'install':
            case 'upgrade':
            case 'uninstall':
                return true;
            case 'login':
                if ($_SERVER["REQUEST_METHOD"] != "POST")
                    $this->_data[$command] = $data;
                return true;
            case 'logout':
                $this->displayError($data, 'Logout', 'logout');
                break;
            case 'package':
            case 'channel-discover':
            case 'channel-delete':
            case 'update-channels':
                $this->_savedOutput[] = $data;
                break;
            default:
                if ($this->_installScript) {
                    $this->_savedOutput[] = $_SESSION['_PEAR_Frontend_Web_SavedOutput'][] = $data;
                    break;
                }
                /* TODO: figure out a sane way to manage the inconsisten new error msg */
                if (!is_array($data)) {
		            echo $data;
                }
        }

        return true;
    }

    function startSession()
    {
        if ($this->_installScript) {
            if (!isset($_SESSION['_PEAR_Frontend_Web_SavedOutput'])) {
                $_SESSION['_PEAR_Frontend_Web_SavedOutput'] = array();
            }
            $this->_savedOutput = $_SESSION['_PEAR_Frontend_Web_SavedOutput'];
        } else {
            $this->_savedOutput = array();
        }
    }

    function finishOutput($command, $redirectLink = false)
    {
        unset($_SESSION['_PEAR_Frontend_Web_SavedOutput']);
        $tpl = $this->_initTemplate('info.tpl.html', "$command output");
        foreach($this->_savedOutput as $row) {
            $tpl->setCurrentBlock('Infoloop');
            $tpl->setVariable("Info", $row);
            $tpl->parseCurrentBlock();
        }
        if ($redirectLink) {
            $tpl->setCurrentBlock('Infoloop');
            $tpl->setVariable("Info", '<a href="' . $redirectLink['link'] . '">' .
                $redirectLink['text'] . '</a>');
            $tpl->parseCurrentBlock();
        }
        $tpl->show();
    }

    /**
     * @param array An array of PEAR_Task_Postinstallscript objects (or related scripts)
     * @param PEAR_PackageFile_v2
     */
    function runPostinstallScripts(&$scripts, $pkg)
    {
        if (!isset($_SESSION['_PEAR_Frontend_Web_Scripts'])) {
            $saves = array();
            foreach ($scripts as $i => $task) {
                $saves[$i] = (array) $task->_obj;
            }
            $_SESSION['_PEAR_Frontend_Web_Scripts'] = $saves;
            $nonsession = true;
        } else {
            $nonsession = false;
        }
        foreach ($scripts as $i => $task) {
            if (!isset($_SESSION['_PEAR_Frontend_Web_ScriptIndex'])) {
                $_SESSION['_PEAR_Frontend_Web_ScriptIndex'] = $i;
            }
            if ($i != $_SESSION['_PEAR_Frontend_Web_ScriptIndex']) {
                continue;
            }
            if (!$nonsession) {
                // restore values from previous sessions to the install script
                foreach ($_SESSION['_PEAR_Frontend_Web_Scripts'][$i] as $name => $val) {
                    if ($name{0} == '_') {
                        // only public variables will be restored
                        continue;
                    }
                    $scripts[$i]->_obj->$name = $val;
                }
            }
            $this->_installScript = true;
            $this->startSession();
            $this->runInstallScript($scripts[$i]->_params, $scripts[$i]->_obj, $pkg);
            $saves = $scripts;
            foreach ($saves as $i => $task) {
                $saves[$i] = (array) $task->_obj;
            }
            $_SESSION['_PEAR_Frontend_Web_Scripts'] = $saves;
            unset($_SESSION['_PEAR_Frontend_Web_ScriptIndex']);
        }
        $this->_installScript = false;
        unset($_SESSION['_PEAR_Frontend_Web_Scripts']);
        $this->finishOutput($pkg->getPackage() . ' Install Script',
            array('link' => $GLOBALS['URL'] .
            '?command=remote-info&pkg='.$pkg->getPackage(),
                'text' => 'Click for ' .$pkg->getPackage() . ' Information'));
    }

    /**
     * Instruct the runInstallScript method to skip a paramgroup that matches the
     * id value passed in.
     *
     * This method is useful for dynamically configuring which sections of a post-install script
     * will be run based on the user's setup, which is very useful for making flexible
     * post-install scripts without losing the cross-Frontend ability to retrieve user input
     * @param string
     */
    function skipParamgroup($id)
    {
        $_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'][$sectionName] = true;
    }

    /**
     * @param array $xml contents of postinstallscript tag
     * @param object $script post-installation script
     * @param PEAR_PackageFile_v1|PEAR_PackageFile_v2 $pkg
     * @param string $contents contents of the install script
     */
    function runInstallScript($xml, &$script, &$pkg)
    {
        if (!isset($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'])) {
            $_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'] = array();
            $_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'] = array();
        }
        if (isset($_SESSION['_PEAR_Frontend_Web_ScriptObj'])) {
            foreach ($_SESSION['_PEAR_Frontend_Web_ScriptObj'] as $name => $val) {
                if ($name{0} == '_') {
                    // only public variables will be restored
                    continue;
                }
                $script->$name = $val;
            }
        } else {
            $_SESSION['_PEAR_Frontend_Web_ScriptObj'] = (array) $script;
        }
        if (!is_array($xml) || !isset($xml['paramgroup'])) {
            $script->run(array(), '_default');
        } else {
            if (!isset($xml['paramgroup'][0])) {
                $xml['paramgroup'][0] = array($xml['paramgroup']);
            }
            foreach ($xml['paramgroup'] as $i => $group) {
                if (isset($_SESSION['_PEAR_Frontend_Web_ScriptSkipSections'][$group['id']])) {
                    continue;
                }
                if (isset($_SESSION['_PEAR_Frontend_Web_ScriptSection'])) {
                    if ($i < $_SESSION['_PEAR_Frontend_Web_ScriptSection']) {
                        $lastgroup = $group;
                        continue;
                    }
                }
                if (isset($_SESSION['_PEAR_Frontend_Web_answers'])) {
                    $answers = $_SESSION['_PEAR_Frontend_Web_answers'];
                }
                if (isset($group['name'])) {
                    if (isset($answers)) {
                        if (isset($answers[$group['name']])) {
                            switch ($group['conditiontype']) {
                                case '=' :
                                    if ($answers[$group['name']] != $group['value']) {
                                        continue 2;
                                    }
                                break;
                                case '!=' :
                                    if ($answers[$group['name']] == $group['value']) {
                                        continue 2;
                                    }
                                break;
                                case 'preg_match' :
                                    if (!@preg_match('/' . $group['value'] . '/',
                                          $answers[$group['name']])) {
                                        continue 2;
                                    }
                                break;
                                default :
                                    $this->_clearScriptSession();
                                return;
                            }
                        }
                    } else {
                        $this->_clearScriptSession();
                        return;
                    }
                }
                if (!isset($group['param'][0])) {
                    $group['param'] = array($group['param']);
                }
                $_SESSION['_PEAR_Frontend_Web_ScriptSection'] = $i;
                if (!isset($answers)) {
                    $answers = array();
                }
                if (isset($group['param'])) {
                    if (method_exists($script, 'postProcessPrompts')) {
                        $prompts = $script->postProcessPrompts($group['param'], $group['name']);
                        if (!is_array($prompts) || count($prompts) != count($group['param'])) {
                            $this->outputData('postinstall', 'Error: post-install script did not ' .
                                'return proper post-processed prompts');
                            $prompts = $group['param'];
                        } else {
                            foreach ($prompts as $i => $var) {
                                if (!is_array($var) || !isset($var['prompt']) ||
                                      !isset($var['name']) ||
                                      ($var['name'] != $group['param'][$i]['name']) ||
                                      ($var['type'] != $group['param'][$i]['type'])) {
                                    $this->outputData('postinstall', 'Error: post-install script ' .
                                        'modified the variables or prompts, severe security risk. ' .
                                        'Will instead use the defaults from the package.xml');
                                    $prompts = $group['param'];
                                }
                            }
                        }
                        $answers = array_merge($answers,
                            $this->confirmDialog($prompts, $pkg->getPackage()));
                    } else {
                        $answers = array_merge($answers,
                            $this->confirmDialog($group['param'], $pkg->getPackage()));
                    }
                }
                if ($answers) {
                    array_unshift($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'],
                        $group['id']);
                    if (!$script->run($answers, $group['id'])) {
                        $script->run($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases'],
                            '_undoOnError');
                        $this->_clearScriptSession();
                        return;
                    }
                } else {
                    $script->run(array(), '_undoOnError');
                    $this->_clearScriptSession();
                    return;
                }
                $lastgroup = $group;
                foreach ($group['param'] as $param) {
                    // rename the current params to save for future tests
                    $answers[$group['id'] . '::' . $param['name']] = $answers[$param['name']];
                    unset($answers[$param['name']]);
                }
                // save the script's variables and user answers for the next round
                $_SESSION['_PEAR_Frontend_Web_ScriptObj'] = (array) $script;
                $_SESSION['_PEAR_Frontend_Web_answers'] = $answers;
                $_SERVER['REQUEST_METHOD'] = '';
            }
        }
        $this->_clearScriptSession();
    }

    function _clearScriptSession()
    {
        unset($_SESSION['_PEAR_Frontend_Web_ScriptObj']);
        unset($_SESSION['_PEAR_Frontend_Web_answers']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptSection']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptCompletedPhases']);
        unset($_SESSION['_PEAR_Frontend_Web_ScriptSkipSections']);
    }

    /**
     * Ask for user input, confirm the answers and continue until the user is satisfied
     * @param array an array of arrays, format array('name' => 'paramname', 'prompt' =>
     *              'text to display', 'type' => 'string'[, default => 'default value'])
     * @param string Package Name
     * @return array|false
     */
    function confirmDialog($params, $pkg)
    {
        $answers = array();
        $prompts = $types = array();
        foreach ($params as $param) {
            $prompts[$param['name']] = $param['prompt'];
            $types[$param['name']] = $param['type'];
            if (isset($param['default'])) {
                $answers[$param['name']] = $param['default'];
            } else {
                $answers[$param['name']] = '';
            }
        }
        $attempt = 0;
        do {
            if ($attempt) {
                $_SERVER['REQUEST_METHOD'] = '';
            }
            $title = !$attempt ? $pkg . ' Install Script Input' : 'Please fill in all values';
            $answers = $this->userDialog('run-scripts', $prompts, $types, $answers, $title, '',
                array('pkg' => $pkg));
            if ($answers === false) {
                return false;
            }
            $attempt++;
        } while (count(array_filter($answers)) != count($prompts));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        return $answers;
    }

    /**
     * Display a formular and return the given input (yes. needs to requests)
     *
     * @param string $command  command from which this method was called
     * @param array  $prompts  associative array. keys are the inputfieldnames
     *                         and values are the description
     * @param array  $types    (optional) array of inputfieldtypes (text, password,
     *                         etc.) keys have to be the same like in $prompts
     * @param array  $defaults (optional) array of defaultvalues. again keys have
     *                         to be the same like in $prompts
     * @param string $title    (optional) title of the page
     * @param string $icon     (optional) iconhandle for this page
     * @param array  $extra    (optional) extra parameters to put in the form action
     *
     * @access public
     *
     * @return array input sended by the user
     */

    function userDialog($command, $prompts, $types = array(), $defaults = array(), $title = '',
                        $icon = '', $extra = array())
    {
        // If this is an POST Request, we can return the userinput
        if (isset($_GET["command"]) && $_GET["command"]==$command
            && $_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['cancel'])) {
                return false;
            }
            $result = array();
            foreach($prompts as $key => $prompt) {
                $result[$key] = $_POST[$key];
            }
            return $result;
        }

        // If this is an Answer GET Request , we can return the userinput
        if (isset($_GET["command"]) && $_GET["command"]==$command
            && isset($_GET["userDialogResult"]) && $_GET["userDialogResult"]=='get') {
            $result = array();
            foreach($prompts as $key => $prompt) {
                $result[$key] = $_GET[$key];
            }
            return $result;
        }

        // Assign title and icon to some commands
        if ($command == 'login') {
            $title = 'Login';
            $icon = 'login';
        }

        $tpl = $this->_initTemplate("userDialog.tpl.html", $title, $icon);
        $tpl->setVariable("Command", $command);
        $extrap = '';
        if (count($extra)) {
            $extrap = '&';
            foreach ($extra as $name => $value) {
                $extrap .= urlencode($name) . '=' . urlencode($value);
            }
        }
        $tpl->setVariable("extra", $extrap);
        if (isset($this->_data[$command])) {
            $tpl->setVariable("Headline", nl2br($this->_data[$command]));
        }

        if (is_array($prompts)) {
            $maxlen = 0;
            foreach($prompts as $key => $prompt) {
                if (strlen($prompt) > $maxlen) {
                    $maxlen = strlen($prompt);
                }
            }

            foreach($prompts as $key => $prompt) {
                $tpl->setCurrentBlock("InputField");
                $type    = (isset($types[$key])    ? $types[$key]    : 'text');
                $default = (isset($defaults[$key]) ? $defaults[$key] : '');
                $tpl->setVariable("prompt", $prompt);
                $tpl->setVariable("name", $key);
                $tpl->setVariable("default", $default);
                $tpl->setVariable("type", $type);
                if ($maxlen > 25) {
                    $tpl->setVariable("width", 'width="275"');
                }
                $tpl->parseCurrentBlock();
            }
        }
        if ($command == 'run-scripts') {
            $tpl->setVariable("cancel", '<input type="submit" value="Cancel" name="cancel">');
        }
        $tpl->show();
        exit;
    }

    /**
     * Write message to log
     *
     * @param string $text message which has to written to log
     *
     * @access public
     *
     * @return boolean true
     */

    function log($text)
    {
        $GLOBALS['_PEAR_Frontend_Web_log'] .= $text."\n";
        $this->_savedOutput[] = $text;
        return true;
    }

    /**
     * Sends the required file along with Headers and exits the script
     *
     * @param string $handle handle of the requested file
     * @param string $group  group of the requested file
     *
     * @access public
     *
     * @return null nothing, because script exits
     */

    function outputFrontendFile($handle, $group)
    {
        $handles = array(
            "css" => array(
                "style" => "style.css",
                "dhtml" => "dhtml.css",
                ),
            "js" => array(
                "dhtml" => "dhtml.js",
                "nodhtml" => "nodhtml.js",
                ),
            "image" => array(
                "logout" => array(
                    "type" => "gif",
                    "file" => "logout.gif",
                    ),
                "login" => array(
                    "type" => "gif",
                    "file" => "login.gif",
                    ),
                "config" => array(
                    "type" => "gif",
                    "file" => "config.gif",
                    ),
                "pkglist" => array(
                    "type" => "png",
                    "file" => "pkglist.png",
                    ),
                "pkgsearch" => array(
                    "type" => "png",
                    "file" => "pkgsearch.png",
                    ),
                "package" => array(
                    "type" => "jpeg",
                    "file" => "package.jpg",
                    ),
                "category" => array(
                    "type" => "jpeg",
                    "file" => "category.jpg",
                    ),
                "install" => array(
                    "type" => "gif",
                    "file" => "install.gif",
                    ),
                "install_wait" => array(
                    "type" => "gif",
                    "file" => "install_wait.gif",
                    ),
                "install_ok" => array(
                    "type" => "gif",
                    "file" => "install_ok.gif",
                    ),
                "install_fail" => array(
                    "type" => "gif",
                    "file" => "install_fail.gif",
                    ),
                "uninstall" => array(
                    "type" => "gif",
                    "file" => "trash.gif",
                    ),
                "info" => array(
                    "type" => "gif",
                    "file" => "info.gif",
                    ),
                "infoplus" => array(
                    "type" => "gif",
                    "file" => "infoplus.gif",
                    ),
                "pear" => array(
                    "type" => "gif",
                    "file" => "pearsmall.gif",
                    ),
                "error" => array(
                    "type" => "gif",
                    "file" => "error.gif",
                    ),
                "manual" => array(
                    "type" => "gif",
                    "file" => "manual.gif",
                    ),
                "download" => array(
                    "type" => "gif",
                    "file" => "download.gif",
                    ),
                ),
            );

        $file = $handles[$group][$handle];
        switch ($group) {
            case 'css':
                header("Content-Type: text/css");
                readfile(dirname(__FILE__).'/Web/'.$file);
                exit;
            case 'image':
                $filename = dirname(__FILE__).'/Web/'.$file['file'];
                header("Content-Type: image/".$file['type']);
                header("Expires: ".gmdate("D, d M Y H:i:s \G\M\T", time() + 60*60*24*100));
                header("Last-Modified: ".gmdate("D, d M Y H:i:s \G\M\T", filemtime($filename)));
                header("Cache-Control: public");
                header("Pragma: ");
                readfile($filename);
                exit;
            case 'js':
                header("Content-Type: text/javascript");
                readfile(dirname(__FILE__).'/Web/'.$file);
                exit;
        }
    }

    /*
     * From DB::Pager. Removing Pager dependency.
     * @private
     */
    function __getData($from, $limit, $numrows, $maxpages = false)
    {
        if (empty($numrows) || ($numrows < 0)) {
            return null;
        }
        $from = (empty($from)) ? 0 : $from;

        if ($limit <= 0) {
            return false;
        }

        // Total number of pages
        $pages = ceil($numrows/$limit);
        $data['numpages'] = $pages;

        // first & last page
        $data['firstpage'] = 1;
        $data['lastpage']  = $pages;

        // Build pages array
        $data['pages'] = array();
        for ($i=1; $i <= $pages; $i++) {
            $offset = $limit * ($i-1);
            $data['pages'][$i] = $offset;
            // $from must point to one page
            if ($from == $offset) {
                // The current page we are
                $data['current'] = $i;
            }
        }
        if (!isset($data['current'])) {
            return PEAR::raiseError (null, 'wrong "from" param', null,
                                     null, null, 'DB_Error', true);
        }
              // Limit number of pages (Goole algorithm)
        if ($maxpages) {
            $radio = floor($maxpages/2);
            $minpage = $data['current'] - $radio;
            if ($minpage < 1) {
                $minpage = 1;
            }
            $maxpage = $data['current'] + $radio - 1;
            if ($maxpage > $data['numpages']) {
                $maxpage = $data['numpages'];
            }
            foreach (range($minpage, $maxpage) as $page) {
                $tmp[$page] = $data['pages'][$page];
            }
            $data['pages'] = $tmp;
            $data['maxpages'] = $maxpages;
        } else {
            $data['maxpages'] = null;
        }

        // Prev link
        $prev = $from - $limit;
        $data['prev'] = ($prev >= 0) ? $prev : null;

        // Next link
        $next = $from + $limit;
        $data['next'] = ($next < $numrows) ? $next : null;

        // Results remaining in next page & Last row to fetch
        if ($data['current'] == $pages) {
            $data['remain'] = 0;
            $data['to'] = $numrows;
        } else {
            if ($data['current'] == ($pages - 1)) {
                $data['remain'] = $numrows - ($limit*($pages-1));
            } else {
                $data['remain'] = $limit;
            }
            $data['to'] = $data['current'] * $limit;
        }
        $data['numrows'] = $numrows;
        $data['from']    = $from + 1;
        $data['limit']   = $limit;

        return $data;
    }

    // }}}
}

?>
