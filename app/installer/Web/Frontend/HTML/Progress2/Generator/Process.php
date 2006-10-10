<?php
/**
 * The ActionProcess class provides final step of ProgressBar creation.
 * Manage php/css source-code save and cancel action.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress2
 */


/**
 * The ActionProcess class provides final step of ProgressBar creation.
 * Manage php/css source-code save and cancel action.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 2.0.0
 * @link       http://pear.php.net/package/HTML_Progress2
 */

class ActionProcess extends HTML_QuickForm_Action
{
    /**
     * Performs an action on a page of the controller (wizard)
     *
     * @param      string    $page          current page displayed by the controller
     * @param      string    $actionName    page action asked
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function perform(&$page, $actionName)
    {
        if ($actionName == 'cancel') {
            echo '<h1>Progress2 Generator Task was canceled</h1>';
            echo '<p>None (PHP/CSS) source codes are available.</p>';
        } else {
            // Checks whether the pages of the controller are valid
            $page->isFormBuilt() or $page->buildForm();
            $page->controller->isValid();

            // what kind of source code is requested
            $code = $page->exportValue('phpcss');
            $pb = $page->controller->createProgressBar();

            $phpCode = (isset($code['P']) === true);
            $cssCode = (isset($code['C']) === true);

            if ($cssCode) {
                $strCSS = $this->sprintCSS($pb);
                $this->exportOutput($strCSS);
            }
            if ($phpCode) {
                $strPHP = $this->sprintPHP($pb, $cssCode);
                $this->exportOutput($strPHP);
            }

            // reset session data
            $page->controller->container(true);
        }
    }

    /**
     * Returns a formatted string of the progress meter stylesheet
     *
     * @param      object    $pBar          progress meter object reference
     * @param      boolean   $raw           (optional) decides whether to put html tags or not
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function sprintCSS(&$pBar, $raw = false)
    {
        return $pBar->getStyle($raw);
    }

    /**
     * Returns a formatted string of the progress meter php code
     *
     * @param      object    $pBar          progress meter object reference
     * @param      boolean   $cssCode       returns css source code
     * @param      boolean   $raw           (optional) decides whether to put php tags or not
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function sprintPHP(&$pBar, $cssCode, $raw = false)
    {
        $structure = $pBar->toArray();

        if ($raw) {
            $strPHP = PHP_EOL;
        } else {
            $strPHP = '<?php' . PHP_EOL;
        }
        $strPHP .= 'require_once \'HTML/Progress2.php\';' . PHP_EOL . PHP_EOL;
        $strPHP .= '$pb = new HTML_Progress2();' . PHP_EOL;
        $strPHP .= '$pb->setIdent(\'PB1\');' . PHP_EOL;

        if ($pBar->isIndeterminate()) {
            $strPHP .= '$pb->setIndeterminate(true);' . PHP_EOL;
        }
        if ($pBar->isBorderPainted()) {
            $strPHP .= '$pb->setBorderPainted(true);' . PHP_EOL;
        }
        if ($structure['animspeed'] > 0) {
            $strPHP .= '$pb->setAnimSpeed(' . $structure['animspeed'] . ');' . PHP_EOL;
        }
        if ($structure['minimum'] != 0) {
            $strPHP .= '$pb->setMinimum(' . $structure['minimum'] . ');' . PHP_EOL;
        }
        if ($structure['maximum'] != 100) {
            $strPHP .= '$pb->setMaximum(' . $structure['maximum'] . ');' . PHP_EOL;
        }
        if ($structure['increment'] != 1) {
            $strPHP .= '$pb->setIncrement(' . $structure['increment'] . ');' . PHP_EOL;
        }
        if ($structure['orientation'] == '2') {
            $strPHP .= '$pb->setOrientation(HTML_PROGRESS2_BAR_VERTICAL);' . PHP_EOL;
        }
        if ($structure['fillway'] != 'natural') {
            $strPHP .= '$pb->setFillWay(\'' . $structure['fillway'] . '\');' . PHP_EOL;
        }

        /* Page 1: Progress attributes **************************************************/
        $strPHP .= $this->_attributesArray('$pb->setProgressAttributes(', $structure['progress']);
        $strPHP .= PHP_EOL;

        /* Page 2: Cell attributes ******************************************************/
        $strPHP .= '$pb->setCellCount(' . $structure['cellcount'] . ');' . PHP_EOL;
        $strPHP .= $this->_attributesArray('$pb->setCellAttributes(', $structure['cell']);
        $strPHP .= PHP_EOL;

        /* Page 3: Border attributes ****************************************************/
        $strPHP .= $this->_attributesArray('$pb->setBorderAttributes(', $structure['border']);
        $strPHP .= PHP_EOL;

        /* Page 4: Label attributes *****************************************************/
        foreach ($structure['label'] as $name => $data) {
            if ($data['type'] == HTML_PROGRESS2_LABEL_TEXT) {
                $strPHP .= '$pb->addLabel(HTML_PROGRESS2_LABEL_TEXT, \''. $name .'\');';
                $strPHP .= PHP_EOL;
            }
            unset($data['type']);
            $strPHP .= $this->_attributesArray('$pb->setLabelAttributes(\''.$name.'\', ', $data);
            $strPHP .= PHP_EOL;
        }

        $strPHP .= PHP_EOL;
        $strPHP .= '// code below is only for run demo; its not nececessary to create progress bar';
        $strPHP .= PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'<head>\' . PHP_EOL;' . PHP_EOL;
            $strPHP .= 'echo $pb->getStyle(false) . PHP_EOL;' . PHP_EOL;
        }
        $strPHP .= 'echo $pb->getScript(false) . PHP_EOL;' . PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'</head>\' . PHP_EOL;' . PHP_EOL;
            $strPHP .= 'echo \'<body>\' . PHP_EOL;' . PHP_EOL;
        }
        $strPHP .= '$pb->display();' . PHP_EOL;
        $strPHP .= '$pb->run();' . PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'</body>\' . PHP_EOL;' . PHP_EOL;
        }
        if (!$raw) {
            $strPHP .= '?>';
        }
        return $strPHP;
    }

    /**
     * Prints a string to standard output, with http headers if necessary
     *
     * @param      string    $str           string to print
     * @param      string    $mime          (optional) mime description
     * @param      boolean   $raw           (optional) charset to use
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function exportOutput($str, $mime = 'text/plain', $charset = 'iso-8859-1')
    {
        if (!headers_sent()) {
            header("Expires: Tue, 1 Jan 1980 12:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
            header("Content-Type: $mime; charset=$charset");
        }
        print $str;
    }

    /**
     * Complete a php function arguments line with appropriate attributes
     *
     * @param      string    $str           php function to complete
     * @param      array     $attributes    function arguments list of values
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _attributesArray($str, $attributes)
    {
        $strPHP = $str . 'array(';
        foreach ($attributes as $attr => $val) {
            if (is_integer($val)) {
                $strPHP .= "'$attr'=>$val, ";
            } elseif (is_bool($val)) {
                $strPHP .= "'$attr'=>" . ($val ? 'true' : 'false') . ', ';
            } else {
                $strPHP .= "'$attr'=>'$val', ";
            }
        }
        $strPHP = ereg_replace(', $', '', $strPHP);
        $strPHP .= '));';
        return $strPHP;
    }
}
?>