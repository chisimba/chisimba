<?php
/**
 * Monitoring of HTML loading bar into a dialog box.
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
 * @since      File available since Release 2.0.0RC1
 */

require_once 'HTML/Progress2.php';
require_once 'HTML/QuickForm.php';

/**
 * Monitoring of HTML loading bar into a dialog box.
 *
 * The HTML_Progress2_Monitor class allow an easy way to display progress bar
 * into a dialog box, and observe all changes easily. User-end can stop task
 * at anytime.
 *
 * Here is a basic example:
 * <code>
 * <?php
 * require_once 'HTML/Progress2/Monitor.php';
 *
 * $pm = new HTML_Progress2_Monitor();
 *
 * $pb =& $pm->getProgressElement();
 * $pb->setAnimSpeed(200);
 * $pb->setIncrement(10);
 * ?>
 * <html>
 * <head>
 * <?php
 * echo $pm->getStyle(false);
 * echo $pm->getScript(false);
 * ?>
 * </head>
 * <body>
 * <?php
 * $pm->display();
 * $pm->run();
 * ?>
 * </body>
 * </html>
 * </code>
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 2.0.0
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      Class available since Release 2.0.0RC1
 */

class HTML_Progress2_Monitor
{
    /**
     * Instance-specific unique identification number.
     *
     * @var        integer
     * @since      2.0.0
     * @access     private
     */
    var $_id;

    /**#@+
     * Attributes of monitor form.
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     */
    var $windowname;
    var $buttonStart;
    var $buttonCancel;
    var $autorun;
    /**#@-*/

    /**
     * Monitor status (text label of progress bar).
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     */
    var $caption = array();

    /**
     * The progress object renders into this monitor.
     *
     * @var        object
     * @since      2.0.0
     * @access     private
     */
    var $_progress;

    /**
     * The quickform object that allows the presentation.
     *
     * @var        object
     * @since      2.0.0
     * @access     private
     */
    var $_form;

    /**
     * Stores the event dispatcher which handles notifications
     *
     * @var        array
     * @since      2.0.0RC2
     * @access     protected
     */
    var $dispatcher;

    /**
     * Count the number of observer registered.
     * The Event_Dispatcher will be add on first observer registration, and
     * will be removed with the last observer.
     *
     * @var        integer
     * @since      2.0.0RC2
     * @access     private
     */
    var $_observerCount;


    /**
     * Constructor (ZE1)
     *
     * @since      2.0.0
     * @access     public
     */
    function HTML_Progress2_Monitor($formName = 'ProgressMonitor', $attributes = array(),
                                    $errorPrefs = array())
    {
        $this->__construct($formName, $attributes, $errorPrefs);
    }

    /**
     * Constructor (ZE2) Summary
     *
     * o Creates a standard progress bar into a dialog box (QuickForm).
     *   Form name, buttons 'start', 'cancel' labels and style, and
     *   title of dialog box may also be changed.
     *   <code>
     *   $monitor = new HTML_Progress2_Monitor();
     *   </code>
     *
     * o Creates a progress bar into a dialog box, with only a new
     *   form name.
     *   <code>
     *   $monitor = new HTML_Progress2_Monitor($formName);
     *   </code>
     *
     * o Creates a progress bar into a dialog box, with a new form name,
     *   new buttons name and style, and also a different title box.
     *   <code>
     *   $monitor = new HTML_Progress2_Monitor($formName, $attributes);
     *   </code>
     *
     * @param      string    $formName      (optional) Name of monitor dialog box (QuickForm)
     * @param      array     $attributes    (optional) List of renderer options
     * @param      array     $errorPrefs    (optional) Hash of params to configure error handler
     *
     * @since      2.0.0
     * @access     protected
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     */
    function __construct($formName = 'ProgressMonitor', $attributes = array(),
                         $errorPrefs = array())
    {
        $this->_progress = new HTML_Progress2($errorPrefs);

        if (!is_string($formName)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$formName',
                      'was' => gettype($formName),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_array($attributes)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$attributes',
                      'was' => gettype($attributes),
                      'expected' => 'array',
                      'paramnum' => 2));

        }

        $this->_id = md5(microtime());
        $this->_observerCount = 0;

        $this->_form = new HTML_QuickForm($formName);
        $this->_form->removeAttribute('name');        // XHTML compliance

        $captionAttr = array('id' => 'monitorStatus', 'valign' => 'bottom', 'left' => 0);

        $this->windowname   = isset($attributes['title'])  ? $attributes['title']  : 'In progress ...';
        $this->buttonStart  = isset($attributes['start'])  ? $attributes['start']  : 'Start';
        $this->buttonCancel = isset($attributes['cancel']) ? $attributes['cancel'] : 'Cancel';
        $buttonAttr         = isset($attributes['button']) ? $attributes['button'] : '';
        $this->autorun      = isset($attributes['autorun'])? $attributes['autorun']: false;
        $this->caption      = isset($attributes['caption'])? $attributes['caption']: $captionAttr;

        $this->_progress->addLabel(HTML_PROGRESS2_LABEL_TEXT, $this->caption['id']);
        $captionAttr = $this->caption;
        unset($captionAttr['id']);
        $this->_progress->setLabelAttributes($this->caption['id'], $captionAttr);
        $this->_progress->setProgressAttributes('top=0 left=0');

        $this->_form->addElement('header', 'windowname', $this->windowname);
        $this->_form->addElement('static', 'progressBar');

        if ($this->isStarted()) {
            $style = array('disabled'=>'true');
        } else {
            $style = null;
        }

        $buttons[] =& $this->_form->createElement('submit', 'start',  $this->buttonStart, $style);
        $buttons[] =& $this->_form->createElement('submit', 'cancel', $this->buttonCancel);

        $buttons[0]->updateAttributes($buttonAttr);
        $buttons[1]->updateAttributes($buttonAttr);

        $this->_form->addGroup($buttons, 'buttons', '', '&nbsp;', false);

        // default embedded progress element with look-and-feel
        $this->setProgressElement($this->_progress);
    }

    /**
     * Adds a new observer.
     *
     * Adds a new observer to the Event Dispatcher that will listen
     * for all messages emitted by this HTML_Progress2 instance.
     *
     * @param      mixed     $callback      PHP callback that will act as listener
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_CALLBACK
     * @see        removeListener()
     */
    function addListener($callback)
    {
        if (!is_callable($callback)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_CALLBACK, 'exception',
                array('var' => '$callback',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'callback',
                      'paramnum' => 1));
        }

        $this->dispatcher =& Event_Dispatcher::getInstance();
        $this->dispatcher->addObserver($callback);
        $this->_observerCount++;
    }

    /**
     * Removes a registered observer.
     *
     * This function removes a registered observer, and if there are no more
     * observer, remove the event dispatcher interface too.
     *
     * @param      mixed     $callback      PHP callback that act as listener
     *
     * @return     bool                     True if observer was removed, false otherwise
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_CALLBACK
     * @see        addListener()
     */
    function removeListener($callback)
    {
        if (!is_callable($callback)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_CALLBACK, 'exception',
                array('var' => '$callback',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'callback',
                      'paramnum' => 1));
        }

        $result = $this->dispatcher->removeObserver($callback);

        if ($result) {
            $this->_observerCount--;
            if ($this->_observerCount == 0) {
                unsset($this->dispatcher);
            }
        }
        return $result;
    }

    /**
     * Detect if progress monitor is started.
     *
     * This function returns TRUE if progress monitor was started by user,
     * FALSE otherwise.
     *
     * @return     bool
     * @since      2.0.0
     * @access     public
     */
    function isStarted()
    {
        $action = $this->_form->getSubmitValues();
        return (isset($action['start']) || $this->autorun);
    }

    /**
     * Detect if progress monitor is stopped.
     *
     * This function returns TRUE if progress monitor was canceled by user,
     * FALSE otherwise.
     *
     * @return     bool
     * @since      2.0.0
     * @access     public
     */
    function isCanceled()
    {
        $action = $this->_form->getSubmitValues();
        return (isset($action['cancel']));
    }

    /**
     * Runs the progress monitor.
     *
     * This function accept both modes: indeterminate and determinate,
     * and execute all actions defined in the user callback identified by
     * HTML_Progress2::setProgressHandler() method.
     *
     * All observers are also notified of main changes (start, stop meter).
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function run()
    {
        if ($this->isCanceled()) {
            $this->_postNotification('onCancel',
                array('handler' => __FUNCTION__, 'value' => $this->_progress->getValue()));
        }
        if ($this->isStarted()) {
            $this->_progress->_status = 'show';

            $this->_postNotification('onSubmit',
                array('handler' => __FUNCTION__, 'value' => $this->_progress->getValue()));
            $this->_progress->run();
            $this->_postNotification('onLoad',
                array('handler' => __FUNCTION__, 'value' => $this->_progress->getValue()));
        }
    }

    /**
     * Links a progress bar to this monitor.
     *
     * This function allow to define a complete progress bar from scratch.
     *
     * @param      object    $bar           a html_progress2 instance
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getProgressElement()
     */
    function setProgressElement(&$bar)
    {
        if (!is_a($bar, 'HTML_Progress2')) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$bar',
                      'was' => gettype($bar),
                      'expected' => 'HTML_Progress2 object',
                      'paramnum' => 1));
        }
        $this->_progress = $bar;

        $bar =& $this->_form->getElement('progressBar');
        $bar->setText( $this->_progress->toHtml() );
    }

    /**
     * Returns a reference to the progress bar.
     *
     * This function returns a reference to the progress meter
     * used with the monitor. Its allow to change easily part or all basic options.
     *
     * @return     object
     * @since      2.0.0
     * @access     public
     * @see        setProgressElement()
     */
    function &getProgressElement()
    {
        return $this->_progress;
    }

    /**
     * Returns the cascading style sheet (CSS).
     *
     * Get the CSS required to display the progress meter in a HTML document.
     *
     * @param      boolean   (optional) html output with script tags or just raw data
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     */
    function getStyle($raw = true)
    {
        if (!is_bool($raw)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$raw',
                      'was' => gettype($raw),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        return $this->_progress->getStyle($raw);
    }

    /**
     * Returns javascript progress meter handler.
     *
     * Get the javascript URL or inline code that will handle the progress meter
     * refresh.
     *
     * @param      boolean   (optional) html output with script tags or just raw data
     *
     * @return     string
     * @since      2.0.0
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @access     public
     */
    function getScript($raw = true)
    {
        if (!is_bool($raw)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$raw',
                      'was' => gettype($raw),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        return $this->_progress->getScript($raw);
    }

    /**
     * Returns the progress monitor structure as HTML.
     *
     * Get html code required to display the progress monitor in any html document.
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function toHtml()
    {
        $barAttr = $this->_progress->getProgressAttributes();
        $width = $barAttr['width'] + 70;

        $formTpl = "\n<form{attributes}>"
                 . "\n<div>"
                 . "\n{hidden}"
                 . "<table width=\"{$width}\" border=\"0\">"
                 . "\n{content}"
                 . "\n</table>"
                 . "\n</div>"
                 . "\n</form>";
        $renderer =& $this->_form->defaultRenderer();
        $renderer->setFormTemplate($formTpl);

        return $this->_form->toHtml();
    }

    /**
     * Renders the initial state of progress monitor.
     *
     * This function should be used only to display initial state of the
     * progress monitor with default QF renderer. If you use another QF renderer
     * (Smarty, ITDynamic, ...) read template engine renderer related documentation.
     *
     * @return     void
     * @since      2.0.0RC2
     * @access     public
     */
    function display()
    {
        echo $this->toHtml();
    }

    /**
     * Accepts a renderer.
     *
     * Accepts a QF renderer for design pattern.
     *
     * @param      object    $renderer      An HTML_QuickForm_Renderer object
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function accept(&$renderer)
    {
        if (!is_a($renderer, 'HTML_QuickForm_Renderer')) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
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
     * @since      2.0.0
     * @access     public
     */
    function setCaption($caption = '&nbsp;', $args = array())
    {
        if (!is_string($caption)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$caption',
                      'was' => gettype($caption),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_array($args)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$args',
                      'was' => gettype($args),
                      'expected' => 'array',
                      'paramnum' => 2));
        }

        foreach($args as $name => $value) {
            $caption = str_replace("%$name%", $value, $caption);
        }
        $this->_progress->setLabelAttributes($this->caption['id'], array('value' => $caption));
    }

    /**
     * Post a new notification to all observers registered.
     *
     * This notification occured only if a dispatcher exists. That means if
     * at least one observer was registered.
     *
     * @param      string    $event         Name of the notification handler
     * @param      array     $info          (optional) Additional information about the notification
     *
     * @return     void
     * @since      2.0.0RC2
     * @access     private
     */
    function _postNotification($event, $info = array())
    {
        if (isset($this->dispatcher)) {
            $info['sender'] = get_class($this);
            $info['time']   = microtime();
            $this->dispatcher->post($this, $event, $info);
        }
    }
}
?>