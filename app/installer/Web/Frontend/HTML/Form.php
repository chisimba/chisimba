<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HTML form utility functions
 *
 * Release 1.3.0 introduces very important security fixes.
 * Please make sure you have upgraded.
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
 * @package    HTML_Form
 * @author     Stig Bakken <ssb@fast.no>
 * @author     Urs Gehrig <urs@circle.ch>
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License
 * @version    $Id$
 * @link       http://pear.php.net/package/HTML_Form
 */

if (!defined('HTML_FORM_TEXT_SIZE')) {
    /**
     * Default value for the $size parameter of most methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     */
    define('HTML_FORM_TEXT_SIZE', 20);
}

if (!defined('HTML_FORM_MAX_FILE_SIZE')) {
    /**
     * Default value for the $maxsize parameter of some methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     */
    define('HTML_FORM_MAX_FILE_SIZE', 1048576); // 1 MB
}

if (!defined('HTML_FORM_PASSWD_SIZE')) {
    /**
     * Default value for the $maxsize parameter of some methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     */
    define('HTML_FORM_PASSWD_SIZE', 8);
}

if (!defined('HTML_FORM_TEXTAREA_WT')) {
    /**
     * Default value for the $width parameter of some methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     */
    define('HTML_FORM_TEXTAREA_WT', 40);
}

if (!defined('HTML_FORM_TEXTAREA_HT')) {
    /**
     * Default value for the $height parameter of some methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     */
    define('HTML_FORM_TEXTAREA_HT', 5);
}

if (!defined('HTML_FORM_TH_ATTR')) {
    /**
     * Default value for the $thattr parameter of most methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     *
     * @since Constant available since Release 1.1.0
     */
    define('HTML_FORM_TH_ATTR', 'align="right" valign="top"');
}

if (!defined('HTML_FORM_TD_ATTR')) {
    /**
     * Default value for the $tdattr parameter of most methods.
     *
     * You can set this in your scripts before including Form.php
     * so you don't have to manually set the argument each time
     * you call a method.
     *
     * @since Constant available since Release 1.1.0
     */
    define('HTML_FORM_TD_ATTR', '');
}


/**
 * HTML form utility functions
 *
 * Release 1.3.0 introduces very important security fixes.
 * Please make sure you have upgraded.
 *
 * @category   HTML
 * @package    HTML_Form
 * @author     Stig Bakken <ssb@fast.no>
 * @author     Urs Gehrig <urs@circle.ch>
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Form
 */
class HTML_Form
{
    // {{{ properties

    /**
     * ACTION attribute of <form> tag
     * @var string
     */
    var $action;

    /**
     * METHOD attribute of <form> tag
     * @var string
     */
    var $method;

    /**
     * NAME attribute of <form> tag
     * @var string
     */
    var $name;

    /**
     * an array of entries for this form
     * @var array
     */
    var $fields;

    /**
     * DB_storage object, if tied to one
     */
    var $storageObject;

    /**
     * TARGET attribute of <form> tag
     * @var string
     */
    var $target;

    /**
     * ENCTYPE attribute of <form> tag
     * @var string
     */
    var $enctype;

    /**
     * additional attributes for <form> tag
     *
     * @var string
     * @since Property available since Release 1.1.0
     */
    var $attr;

    /**
     * an array indicating which parameter to an add*Row() method contains
     * the the field's $default value
     *
     * @var array
     * @access private
     * @since Property available since Release 1.3.0
     */
    var $_default_params = array(
        'blank'       => false,
        'checkbox'    => 3,
        'file'        => false,
        'hidden'      => false,
        'image'       => false,
        'password'    => 3,
        'passwordOne' => 3,
        'plaintext'   => false,
        'radio'       => 4,
        'reset'       => false,
        'select'      => 4,
        'submit'      => false,
        'text'        => 3,
        'textarea'    => 3,
    );

    /**
     * allow $_GET/$_POST data to show up in fields when a $default
     * hasn't been set?
     *
     * @var boolean
     * @access private
     * @see HTML_Form::setDefaultFromInput()
     * @since Property available since Release 1.3.0
     */
    var $_default_from_input = true;

    /**
     * escape the $_GET/$_POST data that shows up in fields when a $default
     * hasn't been set?
     *
     * @var boolean
     * @access private
     * @see HTML_Form::setEscapeDefaultFromInput()
     * @since Property available since Release 1.3.0
     */
    var $_escape_default_from_input = true;


    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * @param string $action  the string naming file or URI to which the form
     *                         should be submitted
     * @param string $method  a string indicating the submission method
     *                         ('get' or 'post')
     * @param string $name    a string used in the <form>'s 'name' attribute
     * @param string $target  a string used in the <form>'s 'target' attribute
     * @param string $enctype a string indicating the submission's encoding
     * @param string $attr    a string of additional attributes to be put
     *                         in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     */
    function HTML_Form($action, $method = 'get', $name = '', $target = '',
                       $enctype = '', $attr = '')
    {
        $this->action  = $action;
        $this->method  = $method;
        $this->name    = $name;
        $this->fields  = array();
        $this->target  = $target;
        $this->enctype = $enctype;
        $this->attr    = $attr;
    }

    /**
     * Enables/Disables $_GET/$_POST user input data showing up in fields
     * when a $default hasn't been set
     *
     * The default is TRUE.
     *
     * @param boolean $bool  TRUE to use $_GET/$_POST for the default,
     *                        FALSE to default to an empty string
     *
     * @return void
     *
     * @see HTML_Form::setEscapeDefaultFromInput()
     * @since Method available since Release 1.3.0
     */
    function setDefaultFromInput($bool) {
        $this->_default_from_input = $bool;
    }

    /**
     * Enables/Disables escaping of the $_GET/$_POST data that shows up in
     * fields when a $default hasn't been set
     *
     * The default is TRUE.
     *
     * Uses htmlspecialchars() for the escaping.
     *
     * @param boolean $bool  TRUE to escape, FALSE to disable escaping
     *
     * @return void
     *
     * @see HTML_Form::setDefaultFromInput()
     * @since Method available since Release 1.3.0
     */
    function setEscapeDefaultFromInput($bool) {
        $this->_escape_default_from_input = $bool;
    }

    // ===========  ADD  ===========

    // }}}
    // {{{ addText()

    /**
     * Adds a text input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayText(), HTML_Form::displayTextRow(),
     *      HTML_Form::returnText(), HTML_Form::returnTextRow()
     */
    function addText($name, $title, $default = null,
                     $size = HTML_FORM_TEXT_SIZE, $maxlength = 0,
                     $attr = '', $thattr = HTML_FORM_TH_ATTR,
                     $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('text', $name, $title, $default, $size,
                                $maxlength, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addPassword()

    /**
     * Adds a combined password input and password confirmation input
     * to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::addPasswordOne(), HTML_Form::display(),
     *      HTML_Form::displayPassword(), HTML_Form::displayPasswordRow(),
     *      HTML_Form::returnPassword(), HTML_Form::returnPasswordRow(),
     *      HTML_Form::displayPasswordOneRow(),
     *      HTML_Form::returnPasswordOneRow()
     */
    function addPassword($name, $title, $default = null,
                         $size = HTML_FORM_PASSWD_SIZE,
                         $maxlength = 0, $attr = '', $thattr = HTML_FORM_TH_ATTR,
                         $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('password', $name, $title, $default, $size,
                                $maxlength, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addPasswordOne()

    /**
     * Adds a password input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::addPassword(), HTML_Form::display(),
     *      HTML_Form::displayPassword(), HTML_Form::displayPasswordRow(),
     *      HTML_Form::returnPassword(), HTML_Form::returnPasswordRow(),
     *      HTML_Form::displayPasswordOneRow(),
     *      HTML_Form::returnPasswordOneRow()
     */
    function addPasswordOne($name, $title, $default = null,
                            $size = HTML_FORM_PASSWD_SIZE,
                            $maxlength = 0, $attr = '', $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('passwordOne', $name, $title, $default, $size,
                                $maxlength, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addCheckbox()

    /**
     * Adds a checkbox input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayCheckbox(), HTML_Form::displayCheckboxRow(),
     *      HTML_Form::returnCheckbox(), HTML_Form::returnCheckboxRow()
     */
    function addCheckbox($name, $title, $default = false, $attr = '',
                         $thattr = HTML_FORM_TH_ATTR,
                         $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('checkbox', $name, $title, $default, $attr,
                                $thattr, $tdattr);
    }

    // }}}
    // {{{ addTextarea()

    /**
     * Adds a textarea input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $width     an integer saying how many characters wide
     *                           the item should be
     * @param int    $height    an integer saying how many rows tall the
     *                           item should be
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayTextarea(), HTML_Form::displayTextareaRow(),
     *      HTML_Form::returnTextarea(), HTML_Form::returnTextareaRow()
     */
    function addTextarea($name, $title, $default = null,
                         $width = HTML_FORM_TEXTAREA_WT,
                         $height = HTML_FORM_TEXTAREA_HT, $maxlength = 0,
                         $attr = '', $thattr = HTML_FORM_TH_ATTR,
                         $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('textarea', $name, $title, $default, $width,
                                $height, $maxlength, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addSubmit()

    /**
     * Adds a submit button to the list of fields to be processed by display()
     *
     * @param string $name      a string used in the 'name' attribute
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displaySubmit(), HTML_Form::displaySubmitRow(),
     *      HTML_Form::returnSubmit(), HTML_Form::returnSubmitRow()
     */
    function addSubmit($name = 'submit', $title = 'Submit Changes',
                       $attr = '', $thattr = HTML_FORM_TH_ATTR,
                       $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('submit', $name, $title, $attr, $thattr,
                                $tdattr);
    }

    // }}}
    // {{{ addReset()

    /**
     * Adds a reset button to the list of fields to be processed by display()
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayReset(), HTML_Form::displayResetRow(),
     *      HTML_Form::returnReset(), HTML_Form::returnResetRow()
     */
    function addReset($title = 'Discard Changes', $attr = '',
                      $thattr = HTML_FORM_TH_ATTR,
                      $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('reset', $title, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addSelect()

    /**
     * Adds a select list to the list of fields to be processed by display()
     *
     * NOTE:  In order for defaults to be automatically selected in the
     * output, the PHP data types of the $default must match the data types
     * of the keys in the $entries array.
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param array  $entries   an array containing the <options> to be listed.
     *                           The array's keys become the option values and
     *                           the array's values become the visible text.
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer saying how many rows should be
     * @param string $blank     if this string is present, an <option> will be
     *                           added to the top of the list that will contain
     *                           the given text in the visible portion and an
     *                           empty string as the value
     * @param bool   $multiple  a bool saying if multiple choices are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displaySelect(), HTML_Form::displaySelectRow(),
     *      HTML_Form::returnSelect(), HTML_Form::returnSelectRow()
     */
    function addSelect($name, $title, $entries, $default = null, $size = 1,
                       $blank = '', $multiple = false, $attr = '',
                       $thattr = HTML_FORM_TH_ATTR,
                       $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('select', $name, $title, $entries, $default,
                                $size, $blank, $multiple, $attr, $thattr,
                                $tdattr);
    }

    // }}}
    // {{{ addRadio()

    /**
     * Adds a radio input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $value     the string used for the item's value
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayRadio(), HTML_Form::displayRadioRow(),
     *      HTML_Form::returnRadio(), HTML_Form::returnRadioRow()
     */
    function addRadio($name, $title, $value, $default = false, $attr = '',
                      $thattr = HTML_FORM_TH_ATTR,
                      $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('radio', $name, $title, $value, $default,
                                $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addImage()

    /**
     * Adds an image input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $src       the string denoting the path to the image.
     *                           Can be a relative path or full URI.
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayImage(), HTML_Form::displayImageRow(),
     *      HTML_Form::returnImage(), HTML_Form::returnImageRow()
     */
    function addImage($name, $title, $src, $attr = '',
                      $thattr = HTML_FORM_TH_ATTR,
                      $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('image', $name, $title, $src, $attr, $thattr,
                                $tdattr);
    }

    // }}}
    // {{{ addHidden()

    /**
     * Adds a hiden input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $value     the string used for the item's value
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayHidden(), HTML_Form::returnHidden()
     */
    function addHidden($name, $value, $attr = '')
    {
        $this->fields[] = array('hidden', $name, $value, $attr);
    }

    // }}}
    // {{{ addBlank()

    /**
     * Adds a blank row to the list of fields to be processed by display()
     *
     * @param int    $i         the number of rows to create.  Ignored if
     *                           $title is used.
     * @param string $title     a string to be used as the label for the row
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayBlank(), HTML_Form::displayBlankRow(),
     *      HTML_Form::returnBlank(), HTML_Form::returnBlankRow()
     */
    function addBlank($i, $title = '', $thattr = HTML_FORM_TH_ATTR,
                      $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('blank', $i, $title, $thattr, $tdattr);
    }

    // }}}
    // {{{ addFile

    /**
     * Adds a file upload input to the list of fields to be processed by display()
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayFile(), HTML_Form::displayFileRow(),
     *      HTML_Form::returnFile(), HTML_Form::returnFileRow(),
     *      HTML_Form::returnMultipleFiles()
     */
    function addFile($name, $title, $maxsize = HTML_FORM_MAX_FILE_SIZE,
                     $size = HTML_FORM_TEXT_SIZE, $accept = '', $attr = '',
                     $thattr = HTML_FORM_TH_ATTR,
                     $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->enctype = "multipart/form-data";
        $this->fields[] = array('file', $name, $title, $maxsize, $size,
                                $accept, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ addPlaintext()

    /**
     * Adds a row of text to the list of fields to be processed by display()
     *
     * @param string $title     the string used as the label
     * @param string $text      a string to be displayed
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::display(),
     *      HTML_Form::displayPlaintext(), HTML_Form::displayPlaintextRow(),
     *      HTML_Form::returnPlaintext(), HTML_Form::returnPlaintextRow()
     */
    function addPlaintext($title, $text = '&nbsp;',
                          $thattr = HTML_FORM_TH_ATTR,
                          $tdattr = HTML_FORM_TD_ATTR)
    {
        $this->fields[] = array('plaintext', $title, $text, $thattr, $tdattr);
    }


    // ===========  DISPLAY  ===========

    // }}}
    // {{{ start()

    /**
     * Prints the opening tags for the form and table
     *
     * NOTE: can NOT be called statically.
     *
     * @param bool $multipartformdata  a bool indicating if the form should
     *                                  be submitted in multipart format
     * @return void
     *
     * @access public
     * @see HTML_Form::display(), HTML_Form::end(), HTML_Form::returnStart()
     */
    function start($multipartformdata = false)
    {
        print $this->returnStart($multipartformdata);
    }

    // }}}
    // {{{ end()

    /**
     * Prints the ending tags for the table and form
     *
     * NOTE: can NOT be called statically.
     *
     * @return void
     *
     * @access public
     * @see HTML_Form::display(), HTML_Form::start(), HTML_Form::returnEnd()
     */
    function end()
    {
        print $this->returnEnd();
    }

    // }}}
    // {{{ displayText()

    /**
     * Prints a text input element
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayTextRow(), HTML_Form::addText(),
     *      HTML_Form::returnText(), HTML_Form::returnTextRow()
     */
    function displayText($name, $default = null, $size = HTML_FORM_TEXT_SIZE,
                         $maxlength = 0, $attr = '')
    {
        print HTML_Form::returnText($name, $default, $size, $maxlength, $attr);
    }

    // }}}
    // {{{ displayTextRow()

    /**
     * Prints a text input element inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayText(), HTML_Form::addText(),
     *      HTML_Form::returnText(), HTML_Form::returnTextRow()
     */
    function displayTextRow($name, $title, $default = null,
                            $size = HTML_FORM_TEXT_SIZE, $maxlength = 0,
                            $attr = '', $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnTextRow($name, $title, $default, $size,
                                       $maxlength, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ displayPassword()

    /**
     * Prints a password input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayPasswordRow(), HTML_Form::addPassword(),
     *      HTML_Form::returnPassword(), HTML_Form::returnPasswordRow()
     */
    function displayPassword($name, $default = null,
                             $size = HTML_FORM_PASSWD_SIZE,
                             $maxlength = 0, $attr = '')
    {
        print HTML_Form::returnPassword($name, $default, $size, $maxlength,
                                        $attr);
    }

    // }}}
    // {{{ displayPasswordRow()

    /**
     * Prints a combined password input and password
     * confirmation input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayPasswordOneRow(),
     *      HTML_Form::displayPassword(), HTML_Form::addPassword(),
     *      HTML_Form::returnPassword(), HTML_Form::returnPasswordRow()
     */
    function displayPasswordRow($name, $title, $default = null,
                                $size = HTML_FORM_PASSWD_SIZE,
                                $maxlength = 0, $attr = '',
                                $thattr = HTML_FORM_TH_ATTR,
                                $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnPasswordRow($name, $title, $default,
                                           $size, $maxlength, $attr, $thattr,
                                           $tdattr);
    }

    // }}}
    // {{{ displayPasswordOneRow()

    /**
     * Prints a password input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayPasswordRow(),
     *      HTML_Form::displayPassword(), HTML_Form::addPassword(),
     *      HTML_Form::returnPassword(), HTML_Form::returnPasswordRow(),
     *      HTML_Form::returnPasswordOneRow()
     */
    function displayPasswordOneRow($name, $title, $default = null,
                                   $size = HTML_FORM_PASSWD_SIZE,
                                   $maxlength = 0, $attr = '',
                                   $thattr = HTML_FORM_TH_ATTR,
                                   $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnPasswordOneRow($name, $title, $default,
                                              $size, $maxlength, $attr, $thattr,
                                              $tdattr);
    }

    // }}}
    // {{{ displayCheckbox()

    /**
     * Prints a checkbox input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayCheckboxRow(), HTML_Form::addCheckbox(),
     *      HTML_Form::returnCheckbox(), HTML_Form::returnCheckboxRow()
     */
    function displayCheckbox($name, $default = false, $attr = '')
    {
        print HTML_Form::returnCheckbox($name, $default, $attr);
    }

    // }}}
    // {{{ displayCheckboxRow()

    /**
     * Prints a checkbox input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayCheckboxRow(), HTML_Form::addCheckbox(),
     *      HTML_Form::returnCheckbox(), HTML_Form::returnCheckboxRow()
     */
    function displayCheckboxRow($name, $title, $default = false, $attr = '',
                                $thattr = HTML_FORM_TH_ATTR,
                                $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnCheckboxRow($name, $title, $default, $attr,
                                           $thattr, $tdattr);
    }

    // }}}
    // {{{ displayTextarea()

    /**
     * Prints a textarea input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $width     an integer saying how many characters wide
     *                           the item should be
     * @param int    $height    an integer saying how many rows tall the
     *                           item should be
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayTextareaRow(), HTML_Form::addTextarea(),
     *      HTML_Form::returnTextarea(), HTML_Form::returnTextareaRow()
     */
    function displayTextarea($name, $default = null, $width = 40,
                             $height = 5, $maxlength  = '', $attr = '')
    {
        print HTML_Form::returnTextarea($name, $default, $width, $height,
                                        $maxlength, $attr);
    }

    // }}}
    // {{{ displayTextareaRow()

    /**
     * Prints a textarea input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $width     an integer saying how many characters wide
     *                           the item should be
     * @param int    $height    an integer saying how many rows tall the
     *                           item should be
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayTextareaRow(), HTML_Form::addTextarea(),
     *      HTML_Form::returnTextarea(), HTML_Form::returnTextareaRow()
     */
    function displayTextareaRow($name, $title, $default = null, $width = 40,
                                $height = 5, $maxlength = 0, $attr = '',
                                $thattr = HTML_FORM_TH_ATTR,
                                $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnTextareaRow($name, $title, $default, $width,
                                           $height, $maxlength, $attr, $thattr,
                                           $tdattr);
    }

    // }}}
    // {{{ displaySubmit()

    /**
     * Prints a submit button
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $name      a string used in the 'name' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displaySubmit(), HTML_Form::addSubmit(),
     *      HTML_Form::returnSubmit(), HTML_Form::returnSubmitRow()
     */
    function displaySubmit($title = 'Submit Changes', $name = 'submit',
                           $attr = '')
    {
        print HTML_Form::returnSubmit($title, $name, $attr);
    }

    // }}}
    // {{{ displaySubmitRow()

    /**
     * Prints a submit button inside a table row
     *
     * @param string $name      a string used in the 'name' attribute
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displaySubmit(), HTML_Form::addSubmit(),
     *      HTML_Form::returnSubmit(), HTML_Form::returnSubmitRow()
     */
    function displaySubmitRow($name = 'submit', $title = 'Submit Changes',
                              $attr = '', $thattr = HTML_FORM_TH_ATTR,
                              $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnSubmitRow($name, $title, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ displayReset()

    /**
     * Prints a reset button
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayResetRow(), HTML_Form::addReset(),
     *      HTML_Form::returnReset(), HTML_Form::returnResetRow()
     */
    function displayReset($title = 'Clear contents', $attr = '')
    {
        print HTML_Form::returnReset($title, $attr);
    }

    // }}}
    // {{{ displayResetRow()

    /**
     * Prints a reset button inside a table row
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayReset(), HTML_Form::addReset(),
     *      HTML_Form::returnReset(), HTML_Form::returnResetRow()
     */
    function displayResetRow($title = 'Clear contents', $attr = '',
                             $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnResetRow($title, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ displaySelect()

    /**
     * Prints a select list
     *
     * NOTE:  In order for defaults to be automatically selected in the
     * output, the PHP data types of the $default must match the data types
     * of the keys in the $entries array.
     *
     * @param string $name      the string used in the 'name' attribute
     * @param array  $entries   an array containing the <options> to be listed.
     *                           The array's keys become the option values and
     *                           the array's values become the visible text.
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer saying how many rows should be
     * @param string $blank     if this string is present, an <option> will be
     *                           added to the top of the list that will contain
     *                           the given text in the visible portion and an
     *                           empty string as the value
     * @param bool   $multiple  a bool saying if multiple choices are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displaySelectRow(), HTML_Form::addSelect(),
     *      HTML_Form::returnSelect(), HTML_Form::returnSelectRow()
     */
    function displaySelect($name, $entries, $default = null, $size = 1,
                           $blank = '', $multiple = false, $attr = '')
    {
        print HTML_Form::returnSelect($name, $entries, $default, $size,
                                      $blank, $multiple, $attr);
    }

    // }}}
    // {{{ displaySelectRow()

    /**
     * Prints a select list inside a table row
     *
     * NOTE:  In order for defaults to be automatically selected in the
     * output, the PHP data types of the $default must match the data types
     * of the keys in the $entries array.
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param array  $entries   an array containing the <options> to be listed.
     *                           The array's keys become the option values and
     *                           the array's values become the visible text.
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer saying how many rows should be
     * @param string $blank     if this string is present, an <option> will be
     *                           added to the top of the list that will contain
     *                           the given text in the visible portion and an
     *                           empty string as the value
     * @param bool   $multiple  a bool saying if multiple choices are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displaySelect(), HTML_Form::addSelect(),
     *      HTML_Form::returnSelect(), HTML_Form::returnSelectRow()
     */
    function displaySelectRow($name, $title, $entries, $default = null,
                              $size = 1, $blank = '', $multiple = false,
                              $attr = '', $thattr = HTML_FORM_TH_ATTR,
                              $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnSelectRow($name, $title, $entries, $default,
                                         $size, $blank, $multiple, $attr,
                                         $thattr, $tdattr);
    }

    // }}}
    // {{{ displayImage()

    /**
     * Prints an image input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $src       the string denoting the path to the image.
     *                           Can be a relative path or full URI.
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayImageRow(), HTML_Form::addImage(),
     *      HTML_Form::returnImage(), HTML_Form::returnImageRow()
     * @since Method available since Release 1.1.0
     */
    function displayImage($name, $src, $attr = '')
    {
        print HTML_Form::returnImage($name, $src, $attr);
    }

    // }}}
    // {{{ displayImageRow()

    /**
     * Prints an image input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $src       the string denoting the path to the image.
     *                           Can be a relative path or full URI.
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayImage(), HTML_Form::addImage(),
     *      HTML_Form::returnImage(), HTML_Form::returnImageRow()
     * @since Method available since Release 1.1.0
     */
    function displayImageRow($name, $title, $src, $attr = '',
                             $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnImageRow($name, $title, $src, $attr, $thattr,
                                        $tdattr);
    }

    // }}}
    // {{{ displayHidden()

    /**
     * Prints a hiden input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $value     the string used for the item's value
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::returnHidden(), HTML_Form::addHidden()
     */
    function displayHidden($name, $value, $attr = '')
    {
        print HTML_Form::returnHidden($name, $value, $attr);
    }

    // }}}

    // assuming that $default is the 'checked' attribut of the radio tag

    // {{{ displayRadio()

    /**
     * Prints a radio input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $value     the string used for the item's value
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayRadioRow(), HTML_Form::addRadio(),
     *      HTML_Form::returnRadio(), HTML_Form::returnRadioRow()
     */
    function displayRadio($name, $value, $default = false, $attr = '')
    {
        print HTML_Form::returnRadio($name, $value, $default, $attr);
    }

    // }}}
    // {{{ displayRadioRow()

    /**
     * Prints a radio input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $value     the string used for the item's value
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayRadio(), HTML_Form::addRadio(),
     *      HTML_Form::returnRadio(), HTML_Form::returnRadioRow()
     */
    function displayRadioRow($name, $title, $value, $default = false,
                             $attr = '', $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnRadioRow($name, $title, $value, $default,
                                        $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ displayBlank()

    /**
     * Prints &nbsp;
     *
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayBlankRow(), HTML_Form::addBlank(),
     *      HTML_Form::returnBlank(), HTML_Form::returnBlankRow()
     */
    function displayBlank()
    {
        print HTML_Form::returnBlank();
    }

    // }}}
    // {{{ displayBlankRow()

    /**
     * Prints a blank row in the table
     *
     * @param int    $i         the number of rows to create.  Ignored if
     *                           $title is used.
     * @param string $title     a string to be used as the label for the row
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayBlank(), HTML_Form::addBlank(),
     *      HTML_Form::returnBlank(), HTML_Form::returnBlankRow()
     */
    function displayBlankRow($i, $title= '', $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnBlankRow($i, $title, $thattr, $tdattr);
    }

    // }}}
    // {{{ displayFile()

    /**
     * Prints a file upload input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayFileRow(), HTML_Form::addFile(),
     *      HTML_Form::returnFile(), HTML_Form::returnFileRow(),
     *      HTML_Form::returnMultipleFiles()
     */
    function displayFile($name, $maxsize = HTML_FORM_MAX_FILE_SIZE,
                         $size = HTML_FORM_TEXT_SIZE, $accept = '',
                         $attr = '')
    {
        print HTML_Form::returnFile($name, $maxsize, $size, $accept, $attr);
    }

    // }}}
    // {{{ displayFileRow()

    /**
     * Prints a file upload input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayFile(), HTML_Form::addFile(),
     *      HTML_Form::returnFile(), HTML_Form::returnFileRow(),
     *      HTML_Form::returnMultipleFiles()
     */
    function displayFileRow($name, $title, $maxsize = HTML_FORM_MAX_FILE_SIZE,
                            $size = HTML_FORM_TEXT_SIZE, $accept = '',
                            $attr = '', $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnFileRow($name, $title, $maxsize,
                                       $size, $accept, $attr, $thattr, $tdattr);
    }

    // }}}
    // {{{ displayPlaintext()

    /**
     * Prints the text provided
     *
     * @param string $text      a string to be displayed
     *
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayPlaintextRow(), HTML_Form::addPlaintext(),
     *      HTML_Form::returnPlaintext(), HTML_Form::returnPlaintextRow()
     */
    function displayPlaintext($text = '&nbsp;')
    {
        print $text;
    }

    // }}}
    // {{{ displayPlaintextRow()

    /**
     * Prints the text provided inside a table row
     *
     * @param string $title     the string used as the label
     * @param string $text      a string to be displayed
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @static
     * @see HTML_Form::displayPlaintext(), HTML_Form::addPlaintext(),
     *      HTML_Form::returnPlaintext(), HTML_Form::returnPlaintextRow()
     */
    function displayPlaintextRow($title, $text = '&nbsp;',
                                 $thattr = 'align="right valign="top""',
                                 $tdattr = HTML_FORM_TD_ATTR)
    {
        print HTML_Form::returnPlaintextRow($title, $text, $thattr, $tdattr);
    }


    // ===========  RETURN  ===========

    // }}}
    // {{{ returnText()

    /**
     * Produce a string containing a text input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayText(), HTML_Form::displayTextRow(),
     *      HTML_Form::returnTextRow(), HTML_Form::addText()
     */
    function returnText($name, $default = null, $size = HTML_FORM_TEXT_SIZE,
                        $maxlength = 0, $attr = '')
    {
        $str  = '<input type="text" name="' . $name . '" ';
        $str .= 'size="' . $size . '" value="' . $default . '" ';
        if ($maxlength) {
            $str .= 'maxlength="' . $maxlength. '" ';
        }
        return $str . $attr . "/>\n";
    }

    // }}}
    // {{{ returnTextRow()

    /**
     * Produce a string containing a text input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayText(), HTML_Form::displayTextRow(),
     *      HTML_Form::returnText(), HTML_Form::addText()
     */
    function returnTextRow($name, $title, $default = null,
                           $size = HTML_FORM_TEXT_SIZE, $maxlength = 0,
                           $attr = '', $thattr = HTML_FORM_TH_ATTR,
                           $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnText($name, $default, $size, $maxlength,
                                      $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnPassword()

    /**
     * Produce a string containing a password input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayPassword(), HTML_Form::displayPasswordRow(),
     *      HTML_Form::returnPasswordRow(), HTML_Form::addPassword()
     */
    function returnPassword($name, $default = null,
                            $size = HTML_FORM_PASSWD_SIZE,
                            $maxlength = 0, $attr = '')
    {
        $str  = '<input type="password" name="' . $name . '" ';
        $str .= 'size="' . $size . '" value="' . $default . '" ';
        if ($maxlength) {
            $str .= 'maxlength="' . $maxlength. '" ';
        }
        return $str . $attr . "/>\n";
    }

    // }}}
    // {{{ returnPasswordRow()

    /**
     * Produce a string containing a combined password input and password
     * confirmation input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayPassword(), HTML_Form::displayPasswordRow(),
     *      HTML_Form::returnPassword(), HTML_Form::addPassword()
     */
    function returnPasswordRow($name, $title, $default = null,
                               $size = HTML_FORM_PASSWD_SIZE,
                               $maxlength = 0, $attr = '',
                               $thattr = HTML_FORM_TH_ATTR,
                               $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnPassword($name, $default, $size,
                                          $maxlength, $attr);
        $str .= "   repeat: ";
        $str .= HTML_Form::returnPassword($name.'2', $default, $size,
                                          $maxlength, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnPasswordOneRow()

    /**
     * Produce a string containing a password input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer used in the 'size' attribute
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayPassword(), HTML_Form::displayPasswordRow(),
     *      HTML_Form::returnPassword(), HTML_Form::addPassword(),
     *      HTML_Form::displayPasswordOneRow()
     */
    function returnPasswordOneRow($name, $title, $default = null,
                                  $size = HTML_FORM_PASSWD_SIZE,
                                  $maxlength = 0, $attr = '',
                                  $thattr = HTML_FORM_TH_ATTR,
                                  $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnPassword($name, $default, $size,
                                          $maxlength, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnCheckbox()

    /**
     * Produce a string containing a checkbox input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayCheckbox(), HTML_Form::displayCheckboxRow(),
     *      HTML_Form::returnCheckboxRow(), HTML_Form::addCheckbox()
     */
    function returnCheckbox($name, $default = false, $attr = '')
    {
        $str = "<input type=\"checkbox\" name=\"$name\"";
        if ($default && $default !== 'off') {
            $str .= ' checked="checked"';
        }
        return $str . ' ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnCheckboxRow()

    /**
     * Produce a string containing a checkbox input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayCheckbox(), HTML_Form::displayCheckboxRow(),
     *      HTML_Form::returnCheckbox(), HTML_Form::addCheckbox()
     */
    function returnCheckboxRow($name, $title, $default = false, $attr = '',
                               $thattr = HTML_FORM_TH_ATTR,
                               $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnCheckbox($name, $default, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnTextarea()

    /**
     * Produce a string containing a textarea input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param mixed  $default   a default value for the element
     * @param int    $width     an integer saying how many characters wide
     *                           the item should be
     * @param int    $height    an integer saying how many rows tall the
     *                           item should be
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayTextarea(), HTML_Form::displayTextareaRow(),
     *      HTML_Form::returnTextareaRow(), HTML_Form::addTextarea()
     */
    function returnTextarea($name, $default = null, $width = 40, $height = 5,
                            $maxlength = 0, $attr = '')
    {
        $str  = '<textarea name="' . $name . '" cols="' . $width . '"';
        $str .= ' rows="' . $height . '" ';
        if ($maxlength) {
            $str .= 'maxlength="' . $maxlength. '" ';
        }
        $str .=  $attr . '>';
        $str .= $default;
        $str .= "</textarea>\n";

        return $str;
    }

    // }}}
    // {{{ returnTextareaRow()

    /**
     * Produce a string containing a textarea input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param mixed  $default   a default value for the element
     * @param int    $width     an integer saying how many characters wide
     *                           the item should be
     * @param int    $height    an integer saying how many rows tall the
     *                           item should be
     * @param int    $maxlength an integer used in the 'maxlength' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayTextarea(), HTML_Form::displayTextareaRow(),
     *      HTML_Form::returnTextareaRow(), HTML_Form::addTextarea()
     */
    function returnTextareaRow($name, $title, $default = null, $width = 40,
                               $height = 5, $maxlength = 0, $attr = '',
                               $thattr = HTML_FORM_TH_ATTR,
                               $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnTextarea($name, $default, $width, $height,
                                      $maxlength, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnSubmit()

    /**
     * Produce a string containing a submit button
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $name      a string used in the 'name' attribute
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displaySubmit(), HTML_Form::displaySubmitRow(),
     *      HTML_Form::returnSubmitRow(), HTML_Form::addSubmit()
     */
    function returnSubmit($title = 'Submit Changes', $name = 'submit',
                          $attr = '')
    {
        return '<input type="submit" name="' . $name . '"'
               . ' value="' . $title . '" ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnSubmitRow()

    /**
     * Produce a string containing a submit button inside a table row
     *
     * @param string $name      a string used in the 'name' attribute
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displaySubmit(), HTML_Form::displaySubmitRow(),
     *      HTML_Form::returnSubmit(), HTML_Form::addSubmit()
     */
    function returnSubmitRow($name = 'submit', $title = 'Submit Changes',
                             $attr = '', $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . ">&nbsp;</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnSubmit($title, $name, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnReset()

    /**
     * Produce a string containing a reset button
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayReset(), HTML_Form::displayResetRow(),
     *      HTML_Form::returnResetRow(), HTML_Form::addReset()
     */
    function returnReset($title = 'Clear contents', $attr = '')
    {
        return '<input type="reset"'
               . ' value="' . $title . '" ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnResetRow()

    /**
     * Produce a string containing a reset button inside a table row
     *
     * NOTE: Unusual parameter order.
     *
     * @param string $title     a string that appears on the button
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayReset(), HTML_Form::displayResetRow(),
     *      HTML_Form::returnReset(), HTML_Form::addReset()
     */
    function returnResetRow($title = 'Clear contents', $attr = '',
                            $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . ">&nbsp;</th>\n";
        $str .= '  <td ' . $tdattr . ">\n   ";
        $str .= HTML_Form::returnReset($title, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnSelect()

    /**
     * Produce a string containing a select list
     *
     * NOTE:  In order for defaults to be automatically selected in the
     * output, the PHP data types of the $default must match the data types
     * of the keys in the $entries array.
     *
     * @param string $name      the string used in the 'name' attribute
     * @param array  $entries   an array containing the <options> to be listed.
     *                           The array's keys become the option values and
     *                           the array's values become the visible text.
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer saying how many rows should be
     * @param string $blank     if this string is present, an <option> will be
     *                           added to the top of the list that will contain
     *                           the given text in the visible portion and an
     *                           empty string as the value
     * @param bool   $multiple  a bool saying if multiple choices are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displaySelect(), HTML_Form::displaySelectRow(),
     *      HTML_Form::returnSelectRow(), HTML_Form::addSelect()
     */
    function returnSelect($name, $entries, $default = null, $size = 1,
                          $blank = '', $multiple = false, $attr = '')
    {
        if ($multiple && substr($name, -2) != '[]') {
            $name .= '[]';
        }
        $str = '   <select name="' . $name . '"';
        if ($size) {
            $str .= ' size="' . $size . '"';
        }
        if ($multiple) {
            $str .= ' multiple="multiple"';
        }
        $str .= ' ' . $attr . ">\n";
        if ($blank) {
            $str .= '    <option value="">' . $blank . '</option>' . "\n";
        }

        foreach ($entries as $val => $text) {
            $str .= '    <option ';
                if (!is_null($default)) {
                    if ($multiple && is_array($default)) {
                        if ((is_string(key($default)) && $default[$val]) ||
                            (is_int(key($default)) && in_array($val, $default))) {
                            $str .= 'selected="selected" ';
                        }
                    } elseif ($default === $val) {
                        $str .= 'selected="selected" ';
                    }
                }
            $str .= 'value="' . $val . '">' . $text . "</option>\n";
        }
        $str .= "   </select>\n";

        return $str;
    }

    // }}}
    // {{{ returnSelectRow()

    /**
     * Produce a string containing a select list inside a table row
     *
     * NOTE:  In order for defaults to be automatically selected in the
     * output, the PHP data types of the $default must match the data types
     * of the keys in the $entries array.
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param array  $entries   an array containing the <options> to be listed.
     *                           The array's keys become the option values and
     *                           the array's values become the visible text.
     * @param mixed  $default   a default value for the element
     * @param int    $size      an integer saying how many rows should be
     * @param string $blank     if this string is present, an <option> will be
     *                           added to the top of the list that will contain
     *                           the given text in the visible portion and an
     *                           empty string as the value
     * @param bool   $multiple  a bool saying if multiple choices are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displaySelect(), HTML_Form::displaySelectRow(),
     *      HTML_Form::returnSelect(), HTML_Form::addSelect()
     */
    function returnSelectRow($name, $title, $entries, $default = null, $size = 1,
                             $blank = '', $multiple = false, $attr = '',
                             $thattr = HTML_FORM_TH_ATTR,
                             $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n";
        $str .= HTML_Form::returnSelect($name, $entries, $default, $size,
                                        $blank, $multiple, $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnRadio()

    /**
     * Produce a string containing a radio input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $value     the string used for the item's value
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayRadio(), HTML_Form::displayRadioRow(),
     *      HTML_Form::returnRadioRow(), HTML_Form::addRadio()
     * @since Method available since Release 1.1.0
     */
    function returnRadio($name, $value, $default = false, $attr = '')
    {
        return '<input type="radio" name="' . $name . '"' .
               ' value="' . $value . '"' .
               ($default ? ' checked="checked"' : '') .
               ' ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnRadioRow()

    /**
     * Produce a string containing a radio input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $value     the string used for the item's value
     * @param bool   $default   a bool indicating if item should be checked
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayRadio(), HTML_Form::displayRadioRow(),
     *      HTML_Form::returnRadio(), HTML_Form::addRadio()
     * @since Method available since Release 1.1.0
     */
    function returnRadioRow($name, $title, $value, $default = false,
                            $attr = '', $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        return " <tr>\n" .
               '  <th ' . $thattr . '>' . $title . "</th>\n" .
               '  <td ' . $tdattr . ">\n   " .
               HTML_Form::returnRadio($name, $value, $default, $attr) .
               "  </td>\n" .
               " </tr>\n";
    }

    // }}}
    // {{{ returnImage()

    /**
     * Produce a string containing an image input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $src       the string denoting the path to the image.
     *                           Can be a relative path or full URI.
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayImage(), HTML_Form::displayImageRow(),
     *      HTML_Form::returnImageRow(), HTML_Form::addImage()
     * @since Method available since Release 1.1.0
     */
    function returnImage($name, $src, $attr = '')
    {
        return '<input type="image" name="' . $name . '"' .
               ' src="' . $src . '" ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnImageRow()

    /**
     * Produce a string containing an image input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param string $src       the string denoting the path to the image.
     *                           Can be a relative path or full URI.
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayImage(), HTML_Form::displayImageRow(),
     *      HTML_Form::returnImage(), HTML_Form::addImage()
     * @since Method available since Release 1.1.0
     */
    function returnImageRow($name, $title, $src, $attr = '',
                            $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        return " <tr>\n" .
               '  <th ' . $thattr . '>' . $title . "</th>\n" .
               '  <td ' . $tdattr . ">\n   " .
               HTML_Form::returnImage($name, $src, $attr) .
               "  </td>\n" .
               " </tr>\n";
    }

    // }}}
    // {{{ returnHidden()

    /**
     * Produce a string containing a hiden input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $value     the string used for the item's value
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayHidden(), HTML_Form::addHidden()
     */
    function returnHidden($name, $value, $attr = '')
    {
        return '<input type="hidden" name="' . $name . '"'
               . ' value="' . $value . '" ' . $attr . "/>\n";
    }

    // }}}
    // {{{ returnBlank()

    /**
     * Produce a string containing &nbsp;
     *
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayBlank(), HTML_Form::displayBlankRow(),
     *      HTML_Form::returnBlankRow(), HTML_Form::addBlank()
     * @since Method available since Release 1.1.0
     */
    function returnBlank()
    {
        return '&nbsp;';
    }

    // }}}
    // {{{ returnBlankRow()

    /**
     * Produce a string containing a blank row in the table
     *
     * @param int    $i         the number of rows to create.  Ignored if
     *                           $title is used.
     * @param string $title     a string to be used as the label for the row
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayBlank(), HTML_Form::displayBlankRow(),
     *      HTML_Form::returnBlank(), HTML_Form::addBlank()
     * @since Method available since Release 1.1.0
     */
    function returnBlankRow($i, $title= '', $thattr = HTML_FORM_TH_ATTR,
                            $tdattr = HTML_FORM_TD_ATTR)
    {
        if (!$title) {
            $str = '';
            for ($j = 0; $j < $i; $j++) {
                $str .= " <tr>\n";
                $str .= '  <th ' . $thattr . ">&nbsp;</th>\n";
                $str .= '  <td ' . $tdattr . '>' . HTML_Form::returnBlank() . "</td>\n";
                $str .= " </tr>\n";
            }
            return $str;
        } else {
            return " <tr>\n" .
                   '  <th ' . $thattr . '>' . $title . "</th>\n" .
                   '  <td ' . $tdattr . '>' . HTML_Form::returnBlank() . "</td>\n" .
                   " </tr>\n";
        }
    }

    // }}}
    // {{{ returnFile()

    /**
     * Produce a string containing a file upload input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayFile(), HTML_Form::displayFileRow(),
     *      HTML_Form::returnFileRow(), HTML_Form::addFile(),
     *      HTML_Form::returnMultipleFiles()
     */
    function returnFile($name = 'userfile',
                        $maxsize = HTML_FORM_MAX_FILE_SIZE,
                        $size = HTML_FORM_TEXT_SIZE,
                        $accept = '', $attr = '')
    {
        $str  = '   <input type="hidden" name="MAX_FILE_SIZE" value="';
        $str .= $maxsize . "\" />\n";
        $str .= '   <input type="file" name="' . $name . '"';
        $str .= ' size="' . $size . '" ';
        if ($accept) {
            $str .= 'accept="' . $accept . '" ';
        }
        return $str . $attr . "/>\n";
    }

    // }}}
    // {{{ returnFileRow()

    /**
     * Produce a string containing a file upload input inside a table row
     *
     * @param string $name      the string used in the 'name' attribute
     * @param string $title     the string used as the label
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayFile(), HTML_Form::displayFileRow(),
     *      HTML_Form::returnFile(), HTML_Form::addFile(),
     *      HTML_Form::returnMultipleFiles()
     */
    function returnFileRow($name, $title, $maxsize = HTML_FORM_MAX_FILE_SIZE,
                           $size = HTML_FORM_TEXT_SIZE,
                           $accept = '', $attr = '',
                           $thattr = HTML_FORM_TH_ATTR,
                           $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n";
        $str .= HTML_Form::returnFile($name, $maxsize, $size, $accept,
                                      $attr);
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ returnMultipleFiles()

    /**
     * Produce a string containing a file upload input
     *
     * @param string $name      the string used in the 'name' attribute
     * @param int    $maxsize   an integer determining how large (in bytes) a
     *                           submitted file can be.
     * @param int    $files     an integer of how many file inputs to show
     * @param int    $size      an integer used in the 'size' attribute
     * @param string $accept    a string saying which MIME types are allowed
     * @param string $attr      a string of additional attributes to be put
     *                           in the element (example: 'id="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayFile(), HTML_Form::displayFileRow(),
     *      HTML_Form::returnFile(), HTML_Form::returnFileRow(),
     *      HTML_Form::addFile()
     */
    function returnMultipleFiles($name = 'userfile[]',
                                 $maxsize = HTML_FORM_MAX_FILE_SIZE,
                                 $files = 3,
                                 $size = HTML_FORM_TEXT_SIZE,
                                 $accept = '', $attr = '')
    {
        $str  = '<input type="hidden" name="MAX_FILE_SIZE" value="';
        $str .= $maxsize . "\" />\n";

        for($i=0; $i < $files; $i++) {
            $str .= '<input type="file" name="' . $name . '"';
            $str .= ' size="' . $size . '" ';
            if ($accept) {
                $str .= 'accept="' . $accept . '" ';
            }
            $str .= $attr . "/><br />\n";
        }
        return $str;
    }

    // }}}
    // {{{ returnStart()

    /**
     * Produces a string containing the opening tags for the form and table
     *
     * NOTE: can NOT be called statically.
     *
     * @param bool $multipartformdata  a bool indicating if the form should
     *                                  be submitted in multipart format
     * @return string
     *
     * @access public
     * @see HTML_Form::display(), HTML_Form::returnEnd(), HTML_Form::start()
     */
    function returnStart($multipartformdata = false)
    {
        $str = "<form action=\"" . $this->action . "\" method=\"$this->method\"";
        if ($this->name) {
            $str .= " name=\"$this->name\"";
        }
        if ($this->target) {
            $str .= " target=\"$this->target\"";
        }
        if ($this->enctype) {
            $str .= " enctype=\"$this->enctype\"";
        }
        if ($multipartformdata) {
            $str .= " enctype=\"multipart/form-data\"";
        }

        return $str . ' ' . $this->attr . ">\n";
    }

    // }}}
    // {{{ returnEnd()

    /**
     * Produces a string containing the opening tags for the form and table
     *
     * NOTE: can NOT be called statically.
     *
     * @return string
     *
     * @access public
     * @see HTML_Form::display(), HTML_Form::returnStart(), HTML_Form::start()
     */
    function returnEnd()
    {
        $fields = array();
        foreach ($this->fields as $data) {
            switch ($data[0]) {
                case 'reset':
                case 'blank':
                case 'plaintext':
                    continue 2;
            }
            $fields[$data[1]] = true;
        }
        $ret = HTML_Form::returnHidden('_fields',
                                       implode(':', array_keys($fields)));
        $ret .= "</form>\n\n";
        return $ret;
    }

    // }}}
    // {{{ returnPlaintext()

    /**
     * Produce a string containing the text provided
     *
     * @param string $text      a string to be displayed
     *
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayPlaintext(), HTML_Form::displayPlaintextRow(),
     *      HTML_Form::returnPlaintextRow(), HTML_Form::addPlaintext()
     */
    function returnPlaintext($text = '&nbsp;')
    {
        return $text;
    }

    // }}}
    // {{{ returnPlaintextRow()

    /**
     * Produce a string containing the text provided inside a table row
     *
     * @param string $title     the string used as the label
     * @param string $text      a string to be displayed
     * @param string $thattr    a string of additional attributes to be put
     *                           in the <th> element (example: 'class="foo"')
     * @param string $tdattr    a string of additional attributes to be put
     *                           in the <td> element (example: 'class="foo"')
     * @return string
     *
     * @access public
     * @static
     * @see HTML_Form::displayPlaintext(), HTML_Form::displayPlaintextRow(),
     *      HTML_Form::returnPlaintext(), HTML_Form::addPlaintext()
     */
    function returnPlaintextRow($title, $text = '&nbsp;',
                                $thattr = HTML_FORM_TH_ATTR,
                                $tdattr = HTML_FORM_TD_ATTR)
    {
        $str  = " <tr>\n";
        $str .= '  <th ' . $thattr . '>' . $title . "</th>\n";
        $str .= '  <td ' . $tdattr . ">\n  ";
        $str .= HTML_Form::returnPlaintext($text) . "\n";
        $str .= "  </td>\n";
        $str .= " </tr>\n";

        return $str;
    }

    // }}}
    // {{{ display()

    /**
     * Prints a complete form with all fields you specified via
     * the add*() methods
     *
     * If you did not specify a field's default value (via the $default
     * parameter to the add*() method in question), this method will
     * automatically insert the user input found in $_GET/$_POST.  This
     * behavior can be disabled via setDefaultFromInput(false).
     *
     * The $_GET/$_POST input is automatically escaped via htmlspecialchars().
     * This behavior can be disabled via setEscapeDefaultFromInput(false).
     *
     * If the $_GET/$_POST superglobal doesn't exist, then
     * $HTTP_GET_VARS/$HTTP_POST_VARS is used.
     *
     * NOTE: can NOT be called statically.
     *
     * @param string $attr     a string of additional attributes to be put
     *                          in the <table> tag (example: 'class="foo"')
     * @param string $caption  if present, a <caption> is added to the table
     * @param string $capattr  a string of additional attributes to be put
     *                          in the <caption> tag (example: 'class="foo"')
     * @return void
     *
     * @access public
     * @see HTML_Form::end(), HTML_Form::returnEnd(),
     *      HTML_Form::setDefaultFromInput(),
     *      HTML_Form::setEscapeDefaultFromInput()
     */
    function display($attr = '', $caption = '', $capattr = '')
    {
        // Determine where to get the user input from.

        if (strtoupper($this->method) == 'POST') {
            if (!empty($_POST)) {
                $input =& $_POST;
            } else {
                if (!empty($HTTP_POST_VARS)) {
                    $input =& $HTTP_POST_VARS;
                } else {
                    $input = array();
                }
            }
        } else {
            if (!empty($_GET)) {
                $input =& $_GET;
            } else {
                if (!empty($HTTP_GET_VARS)) {
                    $input =& $HTTP_GET_VARS;
                } else {
                    $input = array();
                }
            }
        }

        $this->start();
        print '<table ' .  $attr . ">\n";

        if ($caption) {
            print ' <caption ' . $capattr . ">\n  " . $caption;
            print "\n </caption>\n";
        }

        /*
         * Go through each field created through the add*() methods
         * and pass their arguments on to the display*Row() methods.
         */

        $hidden = array();
        foreach ($this->fields as $field_index => $field) {
            $type = $field[0];
            $name = $field[1];

            switch ($type) {
                case 'hidden':
                    // Deal with these later so they don't mess up layout.
                    $hidden[] = $field_index;
                    continue 2;
            }

            if ($this->_default_from_input
                && $this->_default_params[$type]
                && $field[$this->_default_params[$type]] === null
                && array_key_exists($name, $input))
            {
                // Grab the user input from $_GET/$_POST.
                if ($this->_escape_default_from_input) {
                    $field[$this->_default_params[$type]] =
                            htmlspecialchars($input[$name]);
                } else {
                    $field[$this->_default_params[$type]] = $input[$name];
                }
            }

            array_shift($field);
            call_user_func_array(
                array(&$this, 'display' . ucfirst($type) . 'Row'),
                $field
            );
        }

        print "</table>\n";

        for ($i = 0; $i < sizeof($hidden); $i++) {
            $this->displayHidden($this->fields[$hidden[$i]][1],
                                 $this->fields[$hidden[$i]][2],
                                 $this->fields[$hidden[$i]][3]);
        }

        $this->end();
    }

    // }}}
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */

?>
