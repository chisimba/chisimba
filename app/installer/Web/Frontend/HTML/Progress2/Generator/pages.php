<?php
/**
 * The six classes below provides GUI for interactive tools: 
 * HTML Progress2 Generator.
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
 * @access     private
 */


/**
 *  Class for first Tab:
 *  Progress main properties
 *  @ignore
 */
class Property1 extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: main properties');

        $shape[] =& $this->createElement('radio', null, null, 'Horizontal', '1');
        $shape[] =& $this->createElement('radio', null, null, 'Vertical', '2');
        $this->addGroup($shape, 'shape', 'Shape:');

        $way[] =& $this->createElement('radio', null, null, 'Natural', 'natural');
        $way[] =& $this->createElement('radio', null, null, 'Reverse', 'reverse');
        $this->addGroup($way, 'way', 'Direction:');

        $autosize[] =& $this->createElement('radio', null, null, 'Yes', true);
        $autosize[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($autosize, 'autosize', 'Best size:');

        $progresssize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $progresssize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $progresssize['left']    =& $this->createElement('text', 'left', 'left', array('size' => 4));
        $progresssize['top']     =& $this->createElement('text', 'top', 'top', array('size' => 4));
        $progresssize['position']=& $this->createElement('text', 'position', 'position', array('disabled' => 'true'));
        $progresssize['bgcolor'] =& $this->createElement('text', 'bgcolor', 'bgcolor', array('size' => 7));
        $this->addGroup($progresssize, 'progresssize', 'Size, position and color:', ' ');

        $this->addElement('text', 'rAnimSpeed', array('Animation speed :', '(0-1000 ; 0:fast, 1000:slow)'));
        $this->addRule('rAnimSpeed', 'Should be between 0 and 1000', 'rangelength', array(0,1000), 'client');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('back','apply','process'));
    }
}

/**
 *  Class for second Tab:
 *  Cell properties
 *  @ignore
 */
class Property2 extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: cell properties');

        $this->addElement('text', 'cellid', 'Id mask:', array('size' => 32));
        $this->addElement('text', 'cellclass', 'CSS class:', array('size' => 32));

        $cellvalue['min'] =& $this->createElement('text', 'min', 'minimum', array('size' => 4));
        $cellvalue['max'] =& $this->createElement('text', 'max', 'maximum', array('size' => 4));
        $cellvalue['inc'] =& $this->createElement('text', 'inc', 'increment', array('size' => 4));
        $this->addGroup($cellvalue, 'cellvalue', 'Value:', ' ');

        $cellsize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $cellsize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $cellsize['spacing'] =& $this->createElement('text', 'spacing', 'spacing', array('size' => 2));
        $cellsize['count']   =& $this->createElement('text', 'count', 'count', array('size' => 2));
        $this->addGroup($cellsize, 'cellsize', 'Size:', ' ');

        $cellcolor['active']   =& $this->createElement('text', 'active', 'active', array('size' => 7));
        $cellcolor['inactive'] =& $this->createElement('text', 'inactive', 'inactive', array('size' => 7));
        $cellcolor['bgcolor']  =& $this->createElement('text', 'bgcolor', 'background', array('size' => 7));
        $this->addGroup($cellcolor, 'cellcolor', 'Color:', ' ');

        $cellfont['family'] =& $this->createElement('text', 'family', 'family', array('size' => 32));
        $cellfont['size']   =& $this->createElement('text', 'size', 'size', array('size' => 2));
        $cellfont['color']  =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($cellfont, 'cellfont', 'Font:', ' ');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('apply','process'));
    }
}

/**
 *  Class for third Tab:
 *  Progress border properties
 *  @ignore
 */
class Property3 extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: border properties');

        $borderpainted[] =& $this->createElement('radio', null, null, 'Yes', true);
        $borderpainted[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($borderpainted, 'borderpainted', 'Display the border:');

        $this->addElement('text', 'borderclass', 'CSS class:', array('size' => 32));

        $borderstyle['style'] =& $this->createElement('select', 'style', 'style', array('solid'=>'Solid', 'dashed'=>'Dashed', 'dotted'=>'Dotted', 'inset'=>'Inset', 'outset'=>'Outset'));
        $borderstyle['width'] =& $this->createElement('text', 'width', 'width', array('size' => 2));
        $borderstyle['color'] =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($borderstyle, 'borderstyle', null, ' ');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('apply','process'));
    }
}

/**
 *  Class for fourth Tab:
 *  Label properties
 *  @ignore
 */
class Property4 extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: string properties');

        $stringpainted[] =& $this->createElement('radio', null, null, 'Yes', true);
        $stringpainted[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($stringpainted, 'stringpainted', 'Render a custom string:');

        $this->addElement('text', 'stringid', 'Id:', array('size' => 32));
        $this->addElement('text', 'stringclass', 'CSS class:', array('size' => 32));
        $this->addElement('text', 'stringvalue', 'Content:', array('size' => 32));

        $stringsize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $stringsize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $stringsize['left']    =& $this->createElement('text', 'left', 'left', array('size' => 4));
        $stringsize['top']     =& $this->createElement('text', 'top', 'top', array('size' => 4));
        $stringsize['bgcolor'] =& $this->createElement('text', 'bgcolor', 'bgcolor', array('size' => 7));
        $this->addGroup($stringsize, 'stringsize', 'Size, position and color:', ' ');

        $stringvalign[] =& $this->createElement('radio', null, null, 'Left', 'left');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Right', 'right');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Top', 'top');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Bottom', 'bottom');
        $this->addGroup($stringvalign, 'stringvalign', 'Vertical alignment:');

        $stringalign[] =& $this->createElement('radio', null, null, 'Left', 'left');
        $stringalign[] =& $this->createElement('radio', null, null, 'Right', 'right');
        $stringalign[] =& $this->createElement('radio', null, null, 'Center', 'center');
        $this->addGroup($stringalign, 'stringalign', 'Horizontal alignment:');

        $stringfont['family'] =& $this->createElement('text', 'family', 'family', array('size' => 40));
        $stringfont['size']   =& $this->createElement('text', 'size', 'size', array('size' => 2));
        $stringfont['color']  =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($stringfont, 'stringfont', 'Font:', ' ');

        $stringweight[] =& $this->createElement('radio', null, null, 'normal', 'normal');
        $stringweight[] =& $this->createElement('radio', null, null, 'Bold', 'bold');
        $this->addGroup($stringweight, 'stringweight', 'Font weight:');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('apply','process'));
    }
}

/**
 *  Class for fifth Tab:
 *  Show a preview of your progress bar design.
 *  @ignore
 */
class Preview extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: run demo');

        $this->addElement('static', 'progressBar', 'Your progress meter looks like:');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('reset','process'));
    }
}

/**
 *  Class for sixth Tab:
 *  Save PHP and/or CSS code
 *  @ignore
 */
class Save extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->controller->createTabs($this, array('class' => 'flat'));

        $this->addElement('header', null, 'Progress2 Generator - Control Panel: save PHP/CSS code');

        $code[] =& $this->createElement('checkbox', 'P', null, 'PHP');
        $code[] =& $this->createElement('checkbox', 'C', null, 'CSS');
        $this->addGroup($code, 'phpcss', 'PHP and/or StyleSheet source code:');

        $buttons = array('back'   => $this->controller->_buttonBack,
                         'next'   => $this->controller->_buttonNext,
                         'cancel' => $this->controller->_buttonCancel,
                         'reset'  => $this->controller->_buttonReset,
                         'apply'  => $this->controller->_buttonApply,
                         'process'=> $this->controller->_buttonSave
                         );
        $this->controller->createButtons($this, $buttons, $this->controller->_buttonAttr);
        $this->controller->disableButton($this, array('next','apply'));
    }
}
?>