<?php
/**
 * The ActionDisplay class provides a HTML_Page form rendering.
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

require_once 'HTML/Page.php';

/**
 * The ActionDisplay class provides a HTML_Page form rendering.
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

class ActionDisplay extends HTML_QuickForm_Action_Display
{
    function _renderForm(&$page)
    {
        $pageName = $page->getAttribute('name');
        $tabPreview = array_slice ($page->controller->_tabs, -2, 1);
        $tab = '  ';

        $p = new HTML_Page(array(
                 'lineend'  => OS_WINDOWS ? 'win' : 'unix',
                 'tab'      => $tab,
                 'doctype'  => "XHTML 1.0 Strict",
                 'language' => 'en',
                 'cache'    => 'false'
             ));
        $p->disableXmlProlog();
        $p->setTitle("PEAR::HTML_Progress - Generator");
        $p->setMetaData("author", "Laurent Laville");

        $css = '
body {
  background-color: #7B7B88;
  font-family: Verdana, Arial, helvetica;
  font-size: 10pt;
}

h1 {
  color: #FFC;
  text-align: center;
}

.maintable {
  width: 100%;
  border-width: 0;
  border-style: thin dashed;
  border-color: #D0D0D0;
  background-color: #EEE;
  cellspacing: 2;
  cellspadding: 3;
}

th {
  text-align: center;
  color: #FFC;
  background-color: #AAA;
  white-space: nowrap;
}

input {
  font-family: Verdana, Arial, helvetica;
}

input.flat {
  border-style: solid;
  border-width: 2px 2px 0px 2px;
  border-color: #996;
}
';

        // on preview tab, add progress bar javascript and stylesheet
        if ($pageName == $tabPreview[0][0]) {
            $bar = $page->controller->createProgressBar();
            $ui =& $bar->getUI();
            $ui->setTab($tab);

            $p->addStyleDeclaration( $css . $bar->getStyle() );
            $p->addScriptDeclaration( $bar->getScript() );

            $barElement =& $page->getElement('progressBar');
            $barElement->setText( $bar->toHtml() );
        } else {
            $p->addStyleDeclaration($css);
        }

        $renderer =& $page->defaultRenderer();

        $renderer->setFormTemplate('<table class="maintable"><form{attributes}>{content}</form></table>');
        $renderer->setHeaderTemplate('<tr><th colspan="2">{header}</th></tr>');
        $renderer->setGroupTemplate('<table><tr>{content}</tr></table>', 'name');
        $renderer->setGroupElementTemplate('<td>{element}<br /><span style="font-size:10px;"><span class="label">{label}</span></span></td>', 'name');

        $page->accept($renderer);

        $p->addBodyContent( $renderer->toHtml() );
        $p->display();
    }
}
?>