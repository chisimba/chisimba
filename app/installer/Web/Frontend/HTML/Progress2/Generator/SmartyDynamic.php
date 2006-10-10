<?php
/**
 * The ActionDisplay class provides a dynamic form rendering
 * with Smarty template engine.
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

require_once 'HTML/QuickForm/Renderer/Array.php';
// fix this if your Smarty is somewhere else
require_once 'Smarty.class.php';

/**
 * The ActionDisplay class provides a dynamic form rendering
 * with Smarty template engine.
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

class ActionDisplay extends HTML_QuickForm_Action_Display
{
    function _renderForm(&$page) 
    {
        $pageName = $page->getAttribute('name');
        $tabPreview = array_slice ($page->controller->_tabs, -2, 1);

        // setup a template object
        $tpl =& new Smarty();
        $tpl->template_dir = './templates';
        $tpl->compile_dir  = './templates_c';

        // on preview tab, add progress bar javascript and stylesheet
        if ($pageName == $tabPreview[0][0]) {
            $pb = $page->controller->createProgressBar();

            $tpl->assign(array(
                'qf_style'  => $pb->getStyle(),
                'qf_script' => $pb->getScript()
                )
            );

            $pbElement =& $page->getElement('progressBar');
            $pbElement->setText($pb->toHtml() . '<br /><br />');
        }

        $renderer =& new HTML_QuickForm_Renderer_Array(true);

        $page->accept($renderer);
        $tpl->assign('form', $renderer->toArray());

        // capture the array stucture 
        // (only for showing in sample template)
        ob_start();
        print_r($renderer->toArray());
        $tpl->assign('dynamic_array', ob_get_contents());
        ob_end_clean();

        $tpl->display('smarty-dynamic.tpl');
    }
}
?>