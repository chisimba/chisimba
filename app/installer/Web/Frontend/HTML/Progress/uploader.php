<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Laurent Laville <pear@laurent-laville.org>                   |
// +----------------------------------------------------------------------+
//
// $Id$

require_once ('HTML/Progress/Error/Raise.php');
require_once ('HTML/Progress/FTP/upload.php');
require_once ('HTML/Progress.php');

require_once ('HTML/QuickForm.php');

/**
 *
 * The HTML_Progress_Uploader class provides a GUI interface
 * (with progress bar) to manage files to upload to a 
 * ftp server via your web browser.
 *
 * @version    1.1
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @access     public
 * @category   HTML
 * @package    HTML_Progress
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */

class HTML_Progress_Uploader extends FTP_Upload
{
    /**#@+
     * Attributes of upload form.
     *
     * @var        string
     * @since      1.1
     * @access     public
     */
    var $windowname;
    var $captionMask;
    var $buttonStart;
    var $buttonCancel;
    /**#@-*/
    
    /**
     * The progress object renders into this uploader.
     *
     * @var        object
     * @since      1.1
     * @access     private
     */
    var $_progress;

    /**
     * The quickform object that allows the presentation.
     *
     * @var        object
     * @since      1.1
     * @access     private
     */
    var $_form;

    
    /**
     * The progress uploader class constructor
     *
     * @param      string    $formName      (optional) Name of monitor dialog box (QuickForm)
     * @param      array     $attributes    (optional) List of renderer options
     *
     * @since      1.1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function HTML_Progress_Uploader($formName = 'ProgressUploader', $attributes = array())
    {
        $this->_package = 'HTML_Progress_Uploader';
        Error_Raise::initialize($this->_package, array('HTML_Progress', '_getErrorMessage'));

        if (!is_string($formName)) {
            return Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$formName',
                      'was' => gettype($formName),
                      'expected' => 'string',
                      'paramnum' => 1), PEAR_ERROR_TRIGGER);

        } elseif (!is_array($attributes)) {
            return Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$attributes',
                      'was' => gettype($attributes),
                      'expected' => 'array',
                      'paramnum' => 2), PEAR_ERROR_TRIGGER);
        }
        parent::FTP_Upload();          // checks all necessary dependencies
        
        $this->_form = new HTML_QuickForm($formName);

        $this->windowname   = isset($attributes['title'])  ? $attributes['title']  : 'Upload ...';
        $this->captionMask  = isset($attributes['mask'])   ? $attributes['mask']   : '%s';
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
        $this->_progress = new HTML_Progress();
        $this->setProgressElement($this->_progress);

        $str =& $this->_form->getElement('progressStatus');
        $str->setText('<div id="status" class="progressStatus">&nbsp;</div>');
    }

    /**
     * Attach a progress bar to this uploader.
     *
     * @param      object    $bar           a html_progress instance
     *
     * @return     void
     * @since      1.1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function setProgressElement(&$bar)
    {
        if (!is_a($bar, 'HTML_Progress')) {
            return Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$bar',
                      'was' => gettype($bar),
                      'expected' => 'HTML_Progress object',
                      'paramnum' => 1), PEAR_ERROR_TRIGGER);
        }
        $this->_progress =& $bar;

        $this->_progress->setStringPainted(true);     // get space for the string
        $this->_progress->setString("");              // but don't paint it
        $this->_progress->setIndeterminate(true);

        $bar =& $this->_form->getElement('progressBar');
        $bar->setText( $this->_progress->toHtml() );
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
     * @since      1.1
     * @access     public
     */
    function isCanceled()
    {
        $action = $this->_form->getSubmitValues();
        return isset($action['cancel']);
    }

    /**
     * Returns progress styles (StyleSheet).
     *
     * @return     string
     * @since      1.1
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
     * @since      1.1
     * @access     public
     */
    function getScript()
    {
        $js = "
function setStatus(pString)
{
        if (isDom)
            prog = document.getElementById('status');
        if (isIE)
            prog = document.all['status'];
        if (isNS4)
            prog = document.layers['status'];
	if (prog != null) 
	    prog.innerHTML = pString;
}";
        return $this->_progress->getScript() . $js;
    }

    /**
     * Returns Uploader forms as a Html string.
     *
     * @return     string
     * @since      1.1
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
            return Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$renderer',
                      'was' => gettype($renderer),
                      'expected' => 'HTML_QuickForm_Renderer object',
                      'paramnum' => 1), PEAR_ERROR_TRIGGER);
        }
        $this->_form->accept($renderer);
    }

    /**
     * Uploads the files asynchronously, so the class can perform other operations 
     * while files are being uploaded, such :
     * display a progress bar in indeterminate mode. 
     *
     * @param      string    $dest          Changes from current to the specified directory.
     * @param      boolean   $overwrite     (optional) overwrite existing files.
     *
     * @return     mixed                    a null array if all files transfered
     * @since      1.1
     * @access     public
     * @see        FTP_Upload::setFiles()
     */
    function moveTo($dest, $overwrite = false)
    {
        if (!is_string($dest)) {
            Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$dest',
                      'was' => gettype($dest),
                      'expected' => 'string',
                      'paramnum' => 1), PEAR_ERROR_TRIGGER);

        } elseif (!is_bool($overwrite)) {
            Error_Raise::raise($this->_package, HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$overwrite',
                      'was' => gettype($overwrite),
                      'expected' => 'boolean',
                      'paramnum' => 2), PEAR_ERROR_TRIGGER);
        }

        $dir = parent::_changeDir($dest);
        if (PEAR::isError($dir)) {
            return $dir;
        }
        $remoteFiles = ftp_nlist($this->_conn, '.');
        if ($remoteFiles === false) {
            return PEAR::raiseError('Couldn\'t read directory ' . $dest); 
        }
        
        $nomove = array();   // files not transfered on remote host
        
        foreach ($this->_files as $file) {
            if (!$overwrite && in_array(basename($file), $remoteFiles)) {
                // file already exists, skip to next one
                continue;
            }

            // writes file caption
            $status  = ob_get_clean();
            $status  = '<script type="text/javascript">self.setStatus(\'';
            $status .= sprintf($this->captionMask, basename($file));
            $status .= '\'); </script>';
            echo $status;
            ob_start();

            $ret = ftp_nb_put($this->_conn, basename($file), $file, FTP_BINARY);

            while ($ret == FTP_MOREDATA) {
  
                $this->_progress->display();
                // sleep a bit ...
                for ($i=0; $i<($this->_progress->_anim_speed*1000); $i++) { }
                 
                if ($this->_progress->getPercentComplete() == 1) {
                    $this->_progress->setValue(0);
                } else {
                    $this->_progress->incValue();
                }

                // upload Continue ...
                $ret = ftp_nb_continue($this->_conn);
            }
            if ($ret != FTP_FINISHED) {
                $nomove[] = $file;
            }
        }
        return $nomove;
    }
}
?>