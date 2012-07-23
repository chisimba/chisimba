<?php
/**
 *
 * Tabs class for jquery
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
 * Tabs class for jquery
 *
 * This class is a wrapper for the jquery ui dialog
 *
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class accordion extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId = "accordion";

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
     * Variable to hold the active tab
     * 
     * @access proteced
     * @var integer
     */
    protected $active = 0;

    /**
     * 
     * Variable to hold the animation option
     * 
     * @access proteced
     * @var string
     */
    protected $animated = 'slide';

    /**
     * 
     * Variable to hold the autoHeight option
     * 
     * @access proteced
     * @var boolean
     */
    protected $autoHeight = TRUE;

    /**
     * 
     * Variable to hold the collapsible option
     * 
     * @access proteced
     * @var boolean
     */
    protected $collapsible = TRUE;

    /**
     * 
     * Variable to hold the accordion event
     * 
     * @access proteced
     * @var boolean
     */
    protected $event = 'click';

    /**
     * 
     * Variable to hold the accordion header
     * 
     * @access proteced
     * @var string
     */
    protected $header = 'h3';

    /**
     * 
     * Variable to hold the accordion sections
     * 
     * @access proteced
     * @var array
     */
    protected $sections = array();

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
     * Method to set the tab element id.
     * 
     * @access public
     * @param string $cssId The id of the tab
     * @return VOID
     */
    public function setCssId($cssId)
    {
        if (isset($cssId) && is_string($cssId))
        {
            $this->cssId = $cssId;
        }
    }
    
    /**
     *
     * Method to set the tab disabled option.
     * 
     * @access public
     * @param boolean $disabled TRUE if the tabs are disabled | FALSE if not
     * @return VOID
     */
    public function setDisabled($disabled)
    {
        if (isset($disabled) && is_bool($disabled))
        {
            $this->disabled = $disabled;
        }
    }
    
    /**
     *
     * Method to set the active section.
     * 
     * @access public
     * @param integer $active The active section
     * @return VOID
     */
    public function setActive($active)
    {
        if (isset($active) && is_integer($active))
        {
            $this->active = $active;
        }
    }
    
    /**
     *
     * Method to set the section animation.
     * 
     * @access public
     * @param array $animated The animation for the section
     * @return VOID
     */
    public function setAnimated($animated)
    {
        if (isset($animated) && is_string($animated))
        {
            $this->animated = $animated;
        }
    }
    
    /**
     *
     * Method to set section height
     * 
     * @access public
     * @param boolean $autoHeight TRUE if the highest content is to be used for all section | FALSE if not
     * @return VOID 
     */
    public function setAutoHeight($autoHeight)
    {
        if (isset($autoHeight) && is_bool($autoHeight))
        {
            $this->autoHeight = $autoHeight;
        }
    }
    
    /**
     *
     * Method to set the tab collapisble option
     * 
     * @access public
     * @param boolean $collapsible TRUE if all sections can be collapsed | FALSE if not
     * @return VOID 
     */
    public function setCollapsible($collapsible)
    {
        if (isset($collapsible) && is_bool($collapsible))
        {
            $this->collapsible = $collapsible;
        }
    }
    
    /**
     *
     * Method to set the accordion event option
     * 
     * @access public
     * @param string $event The event to activate the section
     * @return VOID 
     */
    public function setEvent($event)
    {
        if (isset($event) && is_string($event))
        {
            $this->event = $event;
        }
    }
    
    /**
     *
     * Method to set the accordion header option
     * 
     * @access public
     * @param string $header The header for the section
     * @return VOID 
     */
    public function setHeader($header)
    {
        if (isset($header) && is_string($header))
        {
            $this->header = $header;
        }
    }
    
    /**
     *
     * Methiod to add a section to the accordion
     * 
     * @access public
     * @param array $section The section array
     * @return VOID 
     */
    public function addSection($section)
    {
        if (isset($section) && is_array($section))
        {
            $this->sections[] = $section;
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
        $script .= "jQuery('#$this->cssId').accordion({";
        $script .= $this->disabled ? "disabled: true" : "disabled: false";
        if (isset($this->collapsible))
        {
            $script .= $this->collapsible ? ",collapsible: true" : ",collapsible: false";
        }
        if (isset($this->animated))
        {
            $script .= ",animated: \"$this->animated\"";
        }
        if (isset($this->autoHeight))
        {
            $script .= $this->autoHeight? ",autoHeight: true" : ",autoHeight: false";
        }
        if (isset($this->event))
        {
            $script .= ",event: \"$this->event\"";
        }
        if (isset($this->active))
        {
            $script .= ",active: $this->active";
        }
        $script .= "});});</script>";  
        $this->script = $script;

        $this->appendArrayVar('headerParams', $script);

        $string = "<div id=\"$this->cssId\">";
        foreach ($this->sections as $section)
        {
            $string .= "<" . $this->header . "><a href=\"#\">" . $section['title'] . "</a></" . $this->header . ">";
            $string .= "<div>" . $section['content'] . "</div>";
        }
        $string .= "</div>";
        
        return $string;
    }
}
?>