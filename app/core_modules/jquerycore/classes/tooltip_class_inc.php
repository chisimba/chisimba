<?php
/**
 *
 * Tooltip class for jquery
 *
 * This class is a wrapper for the jquery tooltip plugin
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
class tooltip extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId;

    /**
     * 
     * Variable to hold the delay option
     * 
     * @access proteced
     * @var inetger
     */
    protected $delay = 0;

    /**
     * 
     * Variable to hold the track option
     * 
     * @access proteced
     * @var boolean
     */
    protected $track = TRUE;

    /**
     * 
     * Variable to hold the show url option
     * 
     * @access protected
     * @var boolean
     */
    protected $showUrl = TRUE;

    /**
     * 
     * Variable to hold a content jquery function
     * 
     * @access proteced
     * @var string
     */
    protected $content;

    /**
     * 
     * Variable to hold the showBody
     * 
     * @access proteced
     * @var string
     */
    protected $showBody = ' - ';

    /**
     * 
     * Variable to hold the pngFix
     * 
     * @access proteced
     * @var boolean
     */
    protected $pngFix = TRUE;

    /**
     * 
     * Variable to hold the opacity
     * 
     * @access proteced
     * @var numeric
     */
    protected $opacity;

    /**
     * 
     * Variable to hold the top position
     * 
     * @access proteced
     * @var numeric
     */
    protected $top;

    /**
     * 
     * Variable to hold the left position
     * 
     * @access proteced
     * @var numeric
     */
    protected $left;

    /**
     * 
     * Variable to hold the extra css class
     * 
     * @access proteced
     * @var string
     */
    protected $extraClass;

    /**
     *
     * Intialiser for the tooltip class
     * 
     * @access public
     * @return VOID
     */
    public function init()
    {
    }
    
    /**
     *
     * Method to set the tooltip element id.
     * 
     * @access public
     * @param string $cssId The id of the element to have a tooltip
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
     * Method to set the tooltip display delay.
     * 
     * @access public
     * @param inetger $delay The delay in showing the tooltip 
     * @return VOID
     */
    public function setDelay($delay)
    {
        if (!empty($delay) && is_integer($delay))
        {
            $this->delay = $delay;
        }
    }
    
    /**
     *
     * Method to set the tooltip tracking.
     * 
     * @access public
     * @param boolean $track TRUE to track | FALSE if not
     * @return VOID
     */
    public function setTrack($track)
    {
        if (!empty($track) && is_bool($track))
        {
            $this->track = $track;
        }
    }
    
    /**
     *
     * Method to set the tooltip show url.
     * 
     * @access public
     * @param boolean $showUrl TRUE to show the href/src | FALSE if not
     * @return VOID
     */
    public function setShowUrl($showUrl)
    {
        if (!empty($showUrl) && is_bool($showUrl))
        {
            $this->showUrl = $showUrl;
        }
    }

    /**
     *
     * Method to set the tooltip content if not element title.
     * 
     * @access public
     * @param string $contentString The tooltip content string
     * @return VOID
     */
    public function setContentString($contentString)
    {
        if (!empty($contentString) && is_string($contentString))
        {
            $this->contentString = $contentString;
        }
    }
    
    /**
     *
     * Method to set the tooltip content to a jquery function.
     * 
     * @access public
     * @param string $contentFunction The tooltip content
     * @return VOID
     */
    public function setContentFunction($contentFunction)
    {
        if (!empty($contentFunction) && is_string($contentFunction))
        {
            $this->contentFunction = $contentFunction;
        }
    }
    
    /**
     *
     * Method to set the tooltip title string break.
     * 
     * @access public
     * @param string $showBody The tooltip title / content break
     * @return VOID
     */
    public function setShowBody($showBody)
    {
        if (!empty($showBody) && is_string($showBody))
        {
            $this->showBody = $showBody;
        }
    }
    
    /**
     *
     * Method to set the tooltip pngFix.
     * 
     * @access public
     * @param boolean $pngFix TRUE if the pngFix must be applied | FALSE if not
     * @return VOID
     */
    public function setPngFix($pngFix)
    {
        if (!empty($pngFix) && is_bool($pngFix))
        {
            $this->pngFix = $pngFix;
        }
    }
    
    /**
     *
     * Method to set the tooltip image opacity.
     * 
     * @access public
     * @param float $opacity The opacity of the image
     * @return VOID
     */
    public function setOpacity($opacity)
    {
        if (!empty($opacity) && is_numeric($opacity))
        {
            $this->opacity = $opacity;
        }
    }
    
    /**
     *
     * Method to set the tooltip top position.
     * 
     * @access public
     * @param float $top The top position
     * @return VOID
     */
    public function setTop($top)
    {
        if (!empty($top) && is_numeric($top))
        {
            $this->top = $top;
        }
    }
    
    /**
     *
     * Method to set the tooltip left position.
     * 
     * @access public
     * @param float $left The left position
     * @return VOID
     */
    public function setLeft($left)
    {
        if (!empty($left) && is_numeric($left))
        {
            $this->left = $left;
        }
    }
    
    /**
     *
     * Method to set the tooltip extra css.
     * 
     * @access public
     * @param string $extraClass The extra css class
     * @return VOID
     */
    public function setExtraClass($extraClass)
    {
        if (!empty($extraClass) && is_string($extraClass))
        {
            $this->extraClass = $extraClass;
        }
    }
    
    /**
     *
     * Method to generate the tooltip javascript and add it to the page
     * 
     * @access public
     * @return VOID 
     */
    public function show()
    {
        $script = "<script type=\"text/javascript\">";
        $script .= "jQuery(function() {";
        $script .= "jQuery('#$this->cssId').tooltip({";
        $script .= "delay: $this->delay";        
        $script .= $this->track ? ",track: true" : ",track: false";
        $script .= $this->showUrl ? ",showUrl: true" : ",showUrl: false";
        $script .= ",showBody: \"$this->showBody\"";        
        $script .= $this->pngFix ? ",pngFix: true" : ",pngFix: false";
        if (isset($this->extraClass))
        {
            $script .= ",extraClass: \"$this->extraClass\"";
        }
        if (isset($this->top))
        {
            $script .= ",top: $this->top";
        }
        if (isset($this->left))
        {
            $script .= ",left: $this->left";
        }
        if (isset($this->opacity))
        {
            $script .= ",opacity: $this->opacity";
        }
        if (isset($this->content))
        {
            $content = addslashes($ths->content);
            $script .= ",bodyHandler: function() { return $content;}";
        }
        $script .= "});});</script>";
        
        $this->appendArrayVar('headerParams', $script);
    }
}
?>