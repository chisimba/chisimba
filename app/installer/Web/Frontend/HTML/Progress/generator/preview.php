<?php
/**
 * The ActionPreview class provides a live demonstration
 * of the progress bar built by HTML_Progress_Generator.
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
 * @package    HTML_Progress
 * @subpackage Progress_UI
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress
 */

/**
 * The ActionPreview class provides a live demonstration
 * of the progress bar built by HTML_Progress_Generator.
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
 * @package    HTML_Progress
 * @subpackage Progress_UI
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 1.2.5
 * @link       http://pear.php.net/package/HTML_Progress
 */

class ActionPreview extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        // like in Action_Next
        $page->isFormBuilt() or $page->buildForm();
        $page->handle('display');

        $strings = $page->controller->exportValue('page4','strings');
        $bar = $page->controller->createProgressBar();

        do {
            $percent = $bar->getPercentComplete();
            if ($bar->isStringPainted()) {
                if (substr($strings, -1) == ";") {
                    $str = explode(";", $strings);
                } else {
                    $str = explode(";", $strings.";");
                }
                for ($i=0; $i<count($str)-1; $i++) {
                    list ($p, $s) = explode(",", $str[$i]);
                    if ($percent == floatval($p)/100) {
                        $bar->setString(trim($s));
                    }
                }
            }
            $bar->display();
            if ($percent == 1) {
                break;   // the progress bar has reached 100%
            }
            $bar->sleep();
            $bar->incValue();
        } while(1);
    }
}
?>