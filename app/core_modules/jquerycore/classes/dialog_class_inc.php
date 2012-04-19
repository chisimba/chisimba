<?php
/**
 *
 * Dialog class for jquery
 *
 * This class is a wrapper for the jquery ui dialog
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   jquerycore
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Main class for the jquerycore module
*
* This module loads the jquery and also performs checks on versions and duplications
*
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dialog extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId = "jq_dialog";

    /**
     * 
     * Variable to hold the disabled option
     * 
     * @access proteced
     * @var boolean
     */
    protected $disabled = FALSE;

    /**
     * 
     * Variable to hold the autoOpen option
     * 
     * @access proteced
     * @var boolean
     */
    protected $autoOpen = FALSE;

    /**
     * 
     * Variable to hold the buttons option
     * 
     * @access protected
     * @var array
     */
    protected $buttons = array(
        'Ok' => "jQuery(this).dialog(\"close\");"
    );

    /**
     * 
     * Variable to hold the closeOnEscape option
     * 
     * @access protected
     * @var boolean
     */
    protected $closeOnEscape = FALSE;

    /**
     * 
     * Variable to hold the closeText option
     * 
     * @access protected
     * @var string
     */
    protected $closeText;

    /**
     * 
     * Variable to hold the dialogClass option
     * 
     * @access protected
     * @var string
     */
    protected $dialogClass;

    /**
     * 
     * Variable to hold the draggable option
     * 
     * @access protected
     * @var boolean
     */
    protected $draggable = TRUE;

    /**
     * 
     * Variable to hold the height option
     * 
     * @access protected
     * @var mixed
     */
    protected $height = 'auto';

    /**
     * 
     * Variable to hold the hide option
     * 
     * @access protected
     * @var string
     */
    protected $hide;

    /**
     * 
     * Variable to hold the max height option
     * 
     * @access protected
     * @var integer
     */
    protected $maxHeight;

    /**
     * 
     * Variable to hold the max width option
     * 
     * @access protected
     * @var integer
     */
    protected $maxWidth;

    /**
     * 
     * Variable to hold the min height option
     * 
     * @access protected
     * @var integer
     */
    protected $minHeight;

    /**
     * 
     * Variable to hold the min width option
     * 
     * @access protected
     * @var integer
     */
    protected $minWidth;

    /**
     * 
     * Variable to hold the modal option
     * 
     * @access protected
     * @var boolean
     */
    protected $modal = TRUE;

    /**
     * 
     * Variable to hold the position option
     * 
     * @access protected
     * @var string
     */
    protected $position;

    /**
     * 
     * Variable to hold the resizable option
     * 
     * @access protected
     * @var boolean
     */
    protected $resizable = TRUE;

    /**
     * 
     * Variable to hold the show option
     * 
     * @access protected
     * @var string
     */
    protected $show;

    /**
     * 
     * Variable to hold the stack option
     * 
     * @access protected
     * @var boolean
     */
    protected $stack;

    /**
     * 
     * Variable to hold the title option
     * 
     * @access protected
     * @var string
     */
    protected $title;

    /**
     * 
     * Variable to hold the width option
     * 
     * @access protected
     * @var mixed
     */
    protected $width = 'auto';

    /**
     * 
     * Variable to hold the zindex option
     * 
     * @access protected
     * @var integer
     */
    protected $zindex;

    /**
     * 
     * Variable to hold the dialog content
     * 
     * @access protected
     * @var string
     */
    protected $content;

    /**
     * 
     * Variable to hold the open event
     * 
     * @access protected
     * @var string
     */
    protected $open;

    /**
     * 
     * Variable to hold the close event
     * 
     * @access protected
     * @var string
     */
    protected $close;

    /**
     * 
     * Variable to hold the beforeClose event
     * 
     * @access protected
     * @var string
     */
    protected $beforeClose;

    /**
     * 
     * Variable to hold the script string
     * 
     * @access public
     * @var string
     */
    public $script;

    /**
     *
     * Intialiser for the dialog class.
     * 
     * @access public
     * @return VOID
     */
    public function init()
    {
    }
    
    /**
     *
     * Method to set the dialog element id.
     * 
     * @access public
     * @param string $cssId The id of the element to have a link to a dialog
     * @return VOID
     */
    public function setCssId($cssId)
    {
        if (!empty($cssId) && is_string($cssId))
        {
            $this->cssId = $cssId;
        }
    }
    
    /**
     *
     * Method to set the dialog disabled option.
     * 
     * @access public
     * @param boolean $disabled TRUE if the dialog is disabled | FALSE if not
     * @return VOID
     */
    public function setDisabled($disabled)
    {
        if (!empty($disabled) && is_bool($disabled))
        {
            $this->disabled = $disabled;
        }
    }
    
    /**
     *
     * Method to set the dialog auto open option.
     * 
     * @access public
     * @param boolean $autoOpen TRUE if the dialog is opened by default | FALSE if not
     * @return VOID
     */
    public function setAutoOpen($autoOpen)
    {
        if (!empty($autoOpen) && is_bool($autoOpen))
        {
            $this->autoOpen = $autoOpen;
        }
    }
    
    /**
     *
     * Method to set the dialog buttons option.
     * 
     * @access public
     * @param array $buttons An array of buttons options
     * @return VOID
     */
    public function setButtons(array $buttons)
    {
        if (!empty($buttons) && is_array($buttons))
        {
            $this->buttons = $buttons;
        }
    }
    
    /**
     *
     * Method to unset the dialog buttons option.
     * 
     * @access public
     * @param array $buttons An array of buttons options
     * @return VOID
     */
    public function unsetButtons()
    {
        $this->buttons = array();
    }
    
    /**
     *
     * Method to set the dialog close on escape option.
     * 
     * @access public
     * @param boolean $closeOnEscape TRUE if the dialog is can be closed with the esc key| FALSE if not
     * @return VOID
     */
    public function setCloseOnEscape($closeOnEscape)
    {
        if (!empty($closeOnEscape) && is_bool($closeOnEscape))
        {
            $this->closeOnEscape = $closeOnEscape;
        }
    }
    
    /**
     *
     * Method to set the dialog close button text option.
     * 
     * @access public
     * @param string $closeText The text to display on the close button
     * @return VOID
     */
    public function setCloseText($closeText)
    {
        if (!empty($closeText) && is_string($closeText));
        {
            $this->closeText = $closeText;
        }
    }
    
    /**
     *
     * Method to set the dialog class option.
     * 
     * @access public
     * @param string $dialogClass The dialog class for addional theming
     * @return VOID
     */
    public function setDialogClass($dialogClass)
    {
        if (!empty($dialogClass) && is_string($dialogClass));
        {
            $this->dialogClass = $dialogClass;
        }
    }
    
    /**
     *
     * Method to set the dialog draggable option.
     * 
     * @access public
     * @param string $draggable TRUE if the dialog is draggable | FALSE if not
     * @return VOID
     */
    public function setDraggable($draggable)
    {
        if (!empty($draggable) && is_bool($draggable));
        {
            $this->draggable = $draggable;
        }
    }
    
    /**
     *
     * Method to set the dialog hieght option.
     * 
     * @access public
     * @param mixed $hieght The hieght of the dialog - default is "auto"
     * @return VOID
     */
    public function setHeight($height)
    {
        if (!empty($height) && (is_numeric($height) || $height == 'auto'));
        {
            $this->height = $height;
        }
    }
    
    /**
     *
     * Method to set the dialog hide option.
     * 
     * @access public
     * @param string $hide The effect to use on close
     * @return VOID
     */
    public function setHide($hide)
    {
        if (!empty($hide) && is_string($hide));
        {
            $this->hide = $hide;
        }
    }
    
    /**
     *
     * Method to set the dialog max height option.
     * 
     * @access public
     * @param string $maxHeight The max hieght of the dialog
     * @return VOID
     */
    public function setMaxHeight($maxHeight)
    {
        if (!empty($maxHeight) && is_integer($maxHeight));
        {
            $this->maxHeight = $maxHeight;
        }
    }
    
    /**
     *
     * Method to set the dialog max width option.
     * 
     * @access public
     * @param string $maxWidth The max width of the dialog
     * @return VOID
     */
    public function setMaxWidth($maxWidth)
    {
        if (!empty($maxWidth) && is_integer($maxWidth));
        {
            $this->maxWidth = $maxWidth;
        }
    }
    
    /**
     *
     * Method to set the dialog min height option.
     * 
     * @access public
     * @param string $minHeight The min hieght of the dialog
     * @return VOID
     */
    public function setMinHeight($minHeight)
    {
        if (!empty($minHeight) && is_integer($minHeight));
        {
            $this->minHeight = $minHeight;
        }
    }
    
    /**
     *
     * Method to set the dialog min width option.
     * 
     * @access public
     * @param string $minWidth The min width of the dialog
     * @return VOID
     */
    public function setMinWidth($minWidth)
    {
        if (!empty($minWidth) && is_integer($minWidth));
        {
            $this->minWidth = $minWidth;
        }
    }
    
    /**
     *
     * Method to set the dialog modal option.
     * 
     * @access public
     * @param boolean $modal TRUE if the dialog is modal | FALSE if not
     * @return VOID
     */
    public function setModal($modal)
    {
        if (!empty($modal) && is_bool($modal));
        {
            $this->modal = $modal;
        }
    }
    
    /**
     *
     * Method to set the dialog position option.
     * 
     * @access public
     * @param string $position The position of the dialog
     * @return VOID
     */
    public function setPosition($position)
    {
        if (!empty($position) && is_string($position));
        {
            $this->position = $position;
        }
    }
    
    /**
     *
     * Method to set the dialog resizable option.
     * 
     * @access public
     * @param boolean $resizable TRUE if the dialog is resizable | FALSE if not
     * @return VOID
     */
    public function setResizable($resizable)
    {
        if (!empty($resizable) && is_string($resizable));
        {
            $this->resizable = $resizable;
        }
    }
    
    /**
     *
     * Method to set the dialog show option.
     * 
     * @access public
     * @param string $show The effect to use on open
     * @return VOID
     */
    public function setShow($show)
    {
        if (!empty($show) && is_string($show));
        {
            $this->show = $show;
        }
    }
    
    /**
     *
     * Method to set the dialog stack option.
     * 
     * @access public
     * @param boolean $stack TRUE if the dialog must be on to | FALSE if not
     * @return VOID
     */
    public function setStack($stack)
    {
        if (!empty($stack) && is_bool($stack));
        {
            $this->stack = $stack;
        }
    }
    
    /**
     *
     * Method to set the dialog title option.
     * 
     * @access public
     * @param boolean $title The title of the dialog
     * @return VOID
     */
    public function setTitle($title)
    {
        if (!empty($title) && is_string($title));
        {
            $this->title = $title;
        }
    }
    
    /**
     *
     * Method to set the dialog width option.
     * 
     * @access public
     * @param mixed $width The width of the dialog
     * @return VOID
     */
    public function setWidth($width)
    {
        if (!empty($width) && (is_numeric($width) || $width == 'auto'));
        {
            $this->width = $width;
        }
    }
    
    /**
     *
     * Method to set the dialog z-index option.
     * 
     * @access public
     * @param integer $zindex The z-index of the dialog
     * @return VOID
     */
    public function setZindex($zindex)
    {
        if (!empty($zindex) && is_numeric($zindex));
        {
            $this->zindex = $zindex;
        }
    }
    
    /**
     *
     * Method to set the dialog open event.
     * 
     * @access public
     * @param integer $open The callback function for the open event
     * @return VOID
     */
    public function setOpen($open)
    {
        if (!empty($open) && is_string($open));
        {
            $this->open = $open;
        }
    }
    
    /**
     *
     * Method to set the dialog close event.
     * 
     * @access public
     * @param integer $close The callback function for the close event
     * @return VOID
     */
    public function setClose($close)
    {
        if (!empty($close) && is_string($close));
        {
            $this->close = $close;
        }
    }
    
    /**
     *
     * Method to set the dialog before close event.
     * 
     * @access public
     * @param integer $beforeClose The callback function for the before close event
     * @return VOID
     */
    public function setBeforeClose($beforeClose)
    {
        if (!empty($beforeClose ) && is_string($beforeClose ));
        {
            $this->beforeClose  = $beforeClose ;
        }
    }
    
    /**
     *
     * Method to set the dialog content.
     * 
     * @access public
     * @param string $content The content of the dialog
     * @return VOID
     */
    public function setContent($content)
    {
        if (!empty($content) && is_string($content));
        {
            $this->content = $content;
        }
    }
    
    /**
     *
     * Method to generate the dialog javascript and add it to the page
     * 
     * @access public
     * @return VOID 
     */
    public function show()
    {
        $script = "<script type=\"text/javascript\">";
        $script .= "jQuery(function() {";
        $script .= "jQuery('#$this->cssId').dialog({";
        $script .= $this->autoOpen ? "autoOpen: true" : "autoOpen: false";
        $script .= $this->disabled ? ",disabled: true" : ",disabled: false";
        if (isset($this->buttons))
        {
            $script .= ",buttons: {";
            $i = 0;
            foreach ($this->buttons as $name => $function)
            {
                $i++;
                $script .= "\"$name\": function() {" . $function . "}";
                if ($i != count($this->buttons))
                {
                    $script .= ',';
                }
            }
            $script .= "}";
        }
        if (isset($this->closeOnEscape))
        {
            $script .= $this->closeOnEscape ? ",closeOnEscape: true" : ",closeOnEscape: false";
        }
        if (isset($this->closeText))
        {
            $script .= ",closeText: \"$this->closeText\"";
        }
        if (isset($this->dialogClass))
        {
            $script .= ",dialogClass: \"$this->dialogClass\"";
        }
        if (isset($this->draggable))
        {
            $script .= $this->draggable ? ",draggable: true" : ",draggable: false";
        }
        if (isset($this->height))
        {
            $height = is_numeric($this->height) ? $this->height : '"' . $this->height .'"';
            $script .= ",height: $height";
        }
        if (isset($this->hide))
        {
            $script .= ",hide: \"$this->hide\"";
        }
        if (isset($this->maxHeight))
        {
            $script .= ",maxHeight: $this->maxHeight";
        }
        if (isset($this->maxWidth))
        {
            $script .= ",maxWidth: $this->maxWidth";
        }
        if (isset($this->minHeight))
        {
            $script .= ",minHeight: $this->minHeight";
        }
        if (isset($this->minWidth))
        {
            $script .= ",minWidth: $this->minWidth";
        }
        if (isset($this->modal))
        {
            $script .= $this->modal ? ",modal: true" : ",modal: false";
        }
        if (isset($this->position))
        {
            $script .= ",position: \"$this->poistion\"";
        }
        if (isset($this->resizable))
        {
            $script .= $this->resizable ? ",resizable: true" : ",resizable: false";
        }
        if (isset($this->show))
        {
            $script .= ",show: \"$this->show\"";
        }
        if (isset($this->stack))
        {
            $script .= $this->stack ? ",stack: true" : ",stack: false";
        }
        if (isset($this->title))
        {
            $script .= ",title: \"$this->title\"";
        }
        if (isset($this->width))
        {
            $width = is_numeric($this->width) ? $this->width : '"' . $this->width .'"';
            $script .= ",width: $width";
        }
        if (isset($this->zindex))
        {
            $script .= ",zindex: \"$this->zindex\"";
        }
        if (isset($this->open))
        {
            $script .= ",open: function(event, ui){" . $this->open . "}";
        }
        if (isset($this->beforeClose))
        {
            $script .= ",beforeClose: function(event, ui){" . $this->beforeClose . "}";
        }
        if (isset($this->close))
        {
            $script .= ",close: function(event, ui){" . $this->close . "}";
        }
        $script .= "});});</script>";  
        $this->script = $script;
        
        $this->appendArrayVar('headerParams', $script);
        
        $string = "<div id=\"$this->cssId\" title=\"$this->title\" style=\"display: none\">" . $this->content . "</div>";
        return $string;
    }
}
?>