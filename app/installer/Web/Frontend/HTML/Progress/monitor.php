<?php
/**
 * The HTML_Progress_Monitor class allow an easy way to display progress
 * in a dialog. The user can cancel the task.
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
 * @subpackage Progress_Observer
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress
 */

require_once 'HTML/Progress.php';
require_once 'HTML/QuickForm.php';

/**
 * The HTML_Progress_Monitor class allow an easy way to display progress
 * in a dialog. The user can cancel the task.
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
 * @subpackage Progress_Observer
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 1.2.5
 * @link       http://pear.php.net/package/HTML_Progress
 */

class HTML_Progress_Monitor
{
    /**
     * Instance-specific unique identification number.
     *
     * @var        integer
     * @since      1.0
     * @access     private
     */
    var $_id;

    /**#@+
     * Attributes of monitor form.
     *
     * @var        string
     * @since      1.1
     * @access     public
     */
    var $windowname;
    var $buttonStart;
    var $buttonCancel;
    /**#@-*/

    /**
     * The progress object renders into this monitor.
     *
     * @var        object
     * @since      1.0
     * @access     private
     */
    var $_progress;

    /**
     * The quickform object that allows the presentation.
     *
     * @var        object
     * @since      1.0
     * @access     private
     */
    var $_form;


    /**
     * Constructor Summary
     *
     * o Creates a standard progress bar into a dialog box (QuickForm).
     *   Form name, buttons 'start', 'cancel' labels and style, and
     *   title of dialog box may also be changed.
     *   <code>
     *   $monitor = new HTML_Progress_Monitor();
     *   </code>
     *
     * o Creates a progress bar into a dialog box, with only a new
     *   form name.
     *   <code>
     *   $monitor = new HTML_Progress_Monitor($formName);
     *   </code>
     *
     * o Creates a progress bar into a dialog box, with a new form name,
     *   new buttons name and style, and also a different title box.
     *   <code>
     *   $monitor = new HTML_Progress_Monitor($formName, $attributes);
     *   </code>
     *
     * @param      string    $formName      (optional) Name of monitor dialog box (QuickForm)
     * @param      array     $attributes    (optional) List of renderer options
     * @param      array     $errorPrefs    (optional) Hash of params to configure error handler
     *
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function HTML_Progress_Monitor($formName = 'ProgressMonitor', $attributes = array(), $errorPrefs = array())
    {
        $bar = new HTML_Progress($errorPrefs);
        $this->_progress = $bar;

        if (!is_string($formName)) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$formName',
                      'was' => gettype($formName),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_array($attributes)) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$attributes',
                      'was' => gettype($attributes),
                      'expected' => 'array',
                      'paramnum' => 2));
        }

        $this->_id = md5(microtime());

        $this->_form = new HTML_QuickForm($formName);
        $this->_form->removeAttribute('name');        // XHTML compliance

        $this->windowname   = isset($attributes['title'])  ? $attributes['title']  : 'In progress ...';
        $this->buttonStart  = isset($attributes['start'])  ? $attributes['start']  : 'Start';
        $this->buttonCancel = isset($attributes['cancel']) ? $attributes['cancel'] : 'Cancel';
        $buttonAttr         = isset($attributes['button']) ? $attributes['button'] : '';

        $this->_form->addElement('header', 'windowname', $this->windowname);
        $this->_form->addElement('static', 'progressBar');
        $this->_form->addElement('static', 'progressStatus');

        $style = $this->isStarted() ? array('disabled'=>'true') : null;

        $buttons[] =& $this->_form->createElement('submit', 'start',  $this->buttonStart, $style);
        $buttons[] =& $this->_form->createElement('submit', 'cancel', $this->buttonCancel);

        $buttons[0]->updateAttributes($buttonAttr);
        $buttons[1]->updateAttributes($buttonAttr);

        $this->_form->addGroup($buttons, 'buttons', '', '&nbsp;', false);

        // default embedded progress element with look-and-feel
        $this->setProgressElement($bar);

        $str =& $this->_form->getElement('progressStatus');
        $str->setText('<div id="status" class="progressStatus">&nbsp;</div>');
    }

    /**
     * Listens all progress events from this monitor.
     *
     * @param      mixed     $event         A hash describing the progress event.
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        HTML_Progress::process()
     * @deprecated
     */
    function notify($event)
    {
    }

    /**
     * Sets a user-defined progress handler function.
     *
     * @param      mixed     $handler       Name of function or a class-method.
     *
     * @return     void
     * @since      1.1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_CALLBACK
     * @see        HTML_Progress::setProgressHandler()
     */
    function setProgressHandler($handler)
    {
        if (!is_callable($handler)) {
            return $this->raiseError(HTML_PROGRESS_ERROR_INVALID_CALLBACK, 'warning',
                array('var' => '$handler',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'element',
                      'paramnum' => 1));
        }
        $this->_progress->setProgressHandler($handler);
    }

    /**
     * Calls a user-defined progress handler function.
     *
     * @param      integer   $arg           Current value of the progress bar.
     *
     * @return     void
     * @since      1.1
     * @access     public
     * @deprecated
     */
    function callProgressHandler($arg)
    {
        $this->_progress->process();
    }

    /**
     * Returns TRUE if progress was started by user, FALSE otherwise.
     *
     * @return     bool
     * @since      1.1
     * @access     public
     */
    function isStarted()
    {
        $action = $this->_form->getSubmitValues();
        return isset($action['start']);
    }

    /**
     * Returns TRUE if progress was canceled by user, FALSE otherwise.
     *
     * @return     bool
     * @since      1.0
     * @access     public
     */
    function isCanceled()
    {
        $action = $this->_form->getSubmitValues();
        return isset($action['cancel']);
    }

    /**
     * Display Monitor and catch user action (cancel button).
     *
     * @return     void
     * @since      1.0
     * @access     public
     */
    function run()
    {
        if ($this->isStarted()) {
            $this->_progress->run();
        }
    }

    /**
     * Attach a progress bar to this monitor.
     *
     * @param      object    $bar           a html_progress instance
     *
     * @return     void
     * @since      1.1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getProgressElement()
     */
    function setProgressElement($bar)
    {
        if (!is_a($bar, 'HTML_Progress')) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$bar',
                      'was' => gettype($bar),
                      'expected' => 'HTML_Progress object',
                      'paramnum' => 1));
        }
        $this->_progress = $bar;

        $bar =& $this->_form->getElement('progressBar');
        $bar->setText( $this->_progress->toHtml() );
    }

    /**
     * Returns a reference to the progress bar object
     * used with the monitor.
     *
     * @return     object
     * @since      1.1
     * @access     public
     * @see        setProgressElement()
     */
    function &getProgressElement()
    {
        return $this->_progress;
    }

    /**
     * Returns progress styles (StyleSheet).
     *
     * @return     string
     * @since      1.0
     * @access     public
     */
    function getStyle()
    {
        return $this->_progress->getStyle();
    }

    /**
     * Returns progress javascript.
     *
     * @return     string
     * @since      1.0
     * @access     public
     */
    function getScript()
    {
        $js = "
function setStatus(pString)
{
    if (isDom) {
        prog = document.getElementById('status');
    } else if (isIE) {
        prog = document.all['status'];
    } else if (isNS4) {
        prog = document.layers['status'];
    }
    if (prog != null)  {
    prog.innerHTML = pString;
    }
}
";
        return $this->_progress->getScript() . $js;
    }

    /**
     * Returns Monitor forms as a Html string.
     *
     * @return     string
     * @since      1.0
     * @access     public
     */
    function toHtml()
    {
        return $this->_form->toHtml();
    }

    /**
     * Accepts a renderer
     *
     * @param      object    $renderer      An HTML_QuickForm_Renderer object
     *
     * @return     void
     * @since      1.1
     * @access     public
     */
    function accept(&$renderer)
    {
        if (!is_a($renderer, 'HTML_QuickForm_Renderer')) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$renderer',
                      'was' => gettype($renderer),
                      'expected' => 'HTML_QuickForm_Renderer object',
                      'paramnum' => 1));
        }
        $this->_form->accept($renderer);
    }

    /**
     * Display a caption on action in progress.
     *
     * The idea of a simple utility function for replacing variables
     * with values in an message template, come from sprintfErrorMessage
     * function of Error_Raise package by Greg Beaver.
     *
     * This simple str_replace-based function can be used to have an
     * order-independent sprintf, so messages can be passed in
     * with different grammar ordering, or other possibilities without
     * changing the source code.
     *
     * Variables should simply be surrounded by % as in %varname%
     *
     * @param      string    $caption       (optional) message template
     * @param      array     $args          (optional) associative array of
     *                                      template var -> message text
     * @since      1.1
     * @access     public
     */
    function setCaption($caption = '&nbsp;', $args = array() )
    {
        if (!is_string($caption)) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$caption',
                      'was' => gettype($caption),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_array($args)) {
            return $this->_progress->raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$args',
                      'was' => gettype($args),
                      'expected' => 'array',
                      'paramnum' => 2));
        }

        foreach($args as $name => $value) {
            $caption = str_replace("%$name%", $value, $caption);
        }
        if (function_exists('ob_get_clean')) {
            $status  = ob_get_clean();      // use for PHP 4.3+
        } else {
            $status  = ob_get_contents();   // use for PHP 4.2+
            ob_end_clean();
        }
        $status = '<script type="text/javascript">self.setStatus(\''.$caption.'\'); </script>';
        echo $status;
        ob_start();
    }
}
?>