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
class tabs extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId = "jq_tab";

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
     * Variable to whether or not the tabs are loaded via ajax
     * 
     * @access proteced
     * @var boolean
     */
    protected $isAjaxTabs = FALSE;

    /**
     * 
     * Variable to hold the ajaxOptions option
     * 
     * @access proteced
     * @var array
     */
    protected $ajaxOptions = array();

    /**
     * 
     * Variable to hold the cache option
     * 
     * @access proteced
     * @var boolean
     */
    protected $cache = FALSE;

    /**
     * 
     * Variable to hold the collapsible option
     * 
     * @access proteced
     * @var boolean
     */
    protected $collapsible;

    /**
     * 
     * Variable to hold the deselectable option (deprecated as from 1.7 use collapsible)
     * 
     * @access proteced
     * @var boolean
     */
    protected $deselectable = FALSE;

    /**
     * 
     * Variable to hold the disabledTabs option
     * 
     * @access proteced
     * @var array
     */
    protected $disabledTabs = array();

    /**
     * 
     * Variable to hold the event option
     * 
     * @access proteced
     * @var string
     */
    protected $event = 'click';

    /**
     * 
     * Variable to hold the fx option
     * eg. array(opacity => slow)
     * eg. array(hieght => normal)
     * eg. array(width => 500)
     * 
     * @access proteced
     * @var array
     */
    protected $fx = array();

    /**
     * 
     * Variable to hold the idPrefix option
     * 
     * @access proteced
     * @var string
     */
    protected $idPrefix = 'ui-tabs-';

    /**
     * 
     * Variable to hold the selected option
     * 
     * @access proteced
     * @var integer
     */
    protected $selected = 0;

    /**
     * 
     * Variable to hold the spinner option
     * 
     * @access proteced
     * @var string
     */
    protected $spinner = '<em>Loading&#8230;</em>';

    /**
     * 
     * Variable to hold the select method
     * 
     * @access proteced
     * @var string
     */
    protected $select = NULL;

    /**
     * 
     * Variable to hold the tabs
     * eg. array('title-1' => 'content-1',
     *         'title-2' => 'content-2') 
     * 
     * @access proteced
     * @var array
     */
    protected $tabs = array();

    /**
     * 
     * Variable to hold the ajax tabs
     * eg. array('link-1', 'link-2')
     * 
     * @access proteced
     * @var array
     */
    protected $ajaxTabs = array();

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
     * Method to set the whether the tabs are loaded via ajax.
     * 
     * @access public
     * @param boolean $ajaxTabs TRUE if the tabs are loaded via ajax | FALSE if not
     * @return VOID
     */
    public function setIsAjaxTabs($isAjaxTabs)
    {
        if (isset($isAjaxTabs) && is_bool($isAjaxTabs))
        {
            $this->isAjaxTabs = $isAjaxTabs;
        }
    }
    
    /**
     *
     * Method to set the tab ajaxOptions option.
     * 
     * @access public
     * @param array $ajaxOptions The array of options to be passed to an ajax function
     * @return VOID
     */
    public function setAjaxOptions($ajaxOptions)
    {
        if (isset($ajaxOptions) && is_array($ajaxOptions))
        {
            $this->ajaxOptions = $ajaxOptions;
        }
    }
    
    /**
     *
     * Method to set the tab cache option
     * 
     * @access public
     * @param boolean $cache TRUE if the remote tab is cached | FALSE if not
     * @return VOID 
     */
    public function setCache($cache)
    {
        if (isset($cache) && is_bool($cache))
        {
            $this->cache = $cache;
        }
    }
    
    /**
     *
     * Method to set the tab collapsible option
     * 
     * @access public
     * @param boolean $collapsible TRUE if the tab is collapsible | FALSE if not
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
     * Method to set the tab delselectable option
     * 
     * @access public
     * @param boolean $deselectable TRUE if the tab is delselectable | FALSE if not
     * @return VOID 
     */
    public function setDelselectable($deselectable)
    {
        if (isset($deselectable) && is_bool($deselectable))
        {
            $this->deselectable = $deselectable;
        }
    }
    
    /**
     *
     * Method to set the tab disabledTabs option
     * 
     * @access public
     * @param array $disabledTabs The array of disabled tabs
     * @return VOID 
     */
    public function setDisabledTabs($disabledTabs)
    {
        if (isset($disabledTabs) && is_array($disabledTabs))
        {
            $this->disabledTabs = $disabledTabs;
        }
    }
    
    /**
     *
     * Method to set the tab event option
     * 
     * @access public
     * @param string $event The event to activate the tab
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
     * Method to set the tab fx option
     * 
     * @access public
     * @param array $fx The effect on activating the tab
     * @return VOID 
     */
    public function setFx($fx)
    {
        if (isset($fx) && is_array($fx))
        {
            $this->fx = $fx;
        }
    }
    
    /**
     *
     * Method to set the tab idPrefix option
     * 
     * @access public
     * @param string $idPrefix The id prefix for remote tabs
     * @return VOID 
     */
    public function setIdPrefix($idPrefix)
    {
        if (isset($idPrefix) && is_string($idPrefix))
        {
            $this->idPrefix = $idPrefix;
        }
    }
    
    /**
     *
     * Method to set the tab select method
     * 
     * @access public
     * @param string $select The callback function to execute on selecting a tab
     * @return VOID 
     */
    public function setSelect($select)
    {
        if (isset($select) && is_string($select))
        {
            $this->select = $select;
        }
    }
    
    /**
     *
     * Method to set the tab selected option
     * 
     * @access public
     * @param integer $selected The selected tab
     * @return VOID 
     */
    public function setSelected($selected)
    {
        if (isset($selected) && is_integer($selected))
        {
            $this->selected = $selected;
        }
    }
    
    /**
     *
     * Method to set the tab spinner option
     * 
     * @access public
     * @param string $spinner The spinner to show when loading a tab
     * @return VOID 
     */
    public function setSpinner($spinner)
    {
        if (isset($spinner) && is_string($spinner))
        {
            $this->spinner = $spinner;
        }
    }
    
    /**
     *
     * Method to add a tab 
     * 
     * @access public 
     * @param array $tab The array of tab data
     * @return VOID
     */
    public function addTab($tab)
    {
        if (isset($tab) && is_array($tab))
        {
            $this->tabs[$tab['title']] = $tab['content'];
        }
    }
    
    /**
     *
     * Method to prepend a tab 
     * 
     * @access public 
     * @param array $tab The array of tab data
     * @return VOID
     */
    public function prependTab($tab)
    {
        if (isset($tab) && is_array($tab))
        {
            array_unshift($this->tabs, $tab);
        }
    }
    
    /**
     *
     * Method to add an ajax tab 
     * 
     * @access public 
     * @param string $tab The tab link
     * @return VOID
     */
    public function addAjaxTab($tab)
    {
        if (isset($tab) && is_string($tab))
        {
            $this->ajaxTabs[] = $tab;
        }
    }
    
    /**
     *
     * Method to prepend an ajax tab 
     * 
     * @access public 
     * @param string $tab The tab link
     * @return VOID
     */
    public function prependAjaxTab($tab)
    {
        if (isset($tab) && is_string($tab))
        {
            array_unshift($this->tabs, $tab);
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
        $script .= "jQuery('#$this->cssId').tabs({";
        $script .= $this->disabled ? "disabled: true" : "disabled: false";
        if (isset($this->cache))
        {
            $script .= $this->cache ? ",cache: true" : ",cache: false";
        }
        if (isset($this->deselectable))
        {
            $script .= $this->deselectable ? ",deselectable: true" : ",deselectable: false";
        }
        if (isset($this->collapsible))
        {
            $script .= $this->collapsible ? ",collapsible: true" : ",collapsible: false";
        }
        if (isset($this->ajaxOptions))
        {
            $script .= ",ajaxOptions: {";
            $i = 0;
            foreach ($this->ajaxOptions as $name => $option)
            {
                $i++;
                $script .= $name . ": '" . $option . '"';
                if ($i != count($this->ajaxOptions))
                {
                    $script .= ",";
                }
            }
            $script .= "}";
        }
        if (isset($this->disabledTabs))
        {
            $script .= ",disabled: [";
            $i = 0;
            foreach ($this->disabledTabs as $tab)
            {
                $i++;
                $script .= $tab;
                if ($i != count($this->disabledTabs))
                {
                    $script .= ",";
                }
            }
            $script .= "]";
        }
        if (isset($this->fx))
        {
            $script .= ",fx: {";
            $i = 0;
            foreach ($this->fx as $effect => $speed)
            {
                $i++;
                $script .= $effect . ': "toggle", duration: ' . is_numeric($speed) ? $speed : '"' . $speed . '"';
                if ($i != count($this->fx))
                {
                    $script .= ",";
                }
            }
            $script .= "}";
        }
        if (isset($this->event))
        {
            $script .= ",event: \"$this->event\"";
        }
        if (isset($this->idPrefix))
        {
            $script .= ",idPrefix: \"$this->idPrefix\"";
        }
        if (isset($this->select))
        {
            $script .= ",select: \"$this->select\"";
        }
        if (isset($this->selected))
        {
            $script .= ",selected: \"$this->selected\"";
        }
        if (isset($this->spinner))
        {
            $script .= ",spinner: \"$this->spinner\"";
        }
        $script .= "});});</script>";  
        $this->script = $script;

        $this->appendArrayVar('headerParams', $script);
        
        if ($this->isAjaxTabs)
        {
            $string = "<div id=\"$this->cssId\"><ul>";
            foreach ($this->ajaxTabs as $link)
            {
                $string .= "<li><a href=\"$link\"><span>Content</span></a></li>";
            }
            $string .= "</ul></div>";
        }
        else
        {
            $string = "<div id=\"$this->cssId\"><ul>";
            $i = 0;
            foreach ($this->tabs as $title => $content)
            {
                $string .= "<li><a href=\"#tabs-" . $i++ . "\">$title</a></li>";
            }
            $string .= "</ul>";
            $i = 0;
            foreach ($this->tabs as $title => $content)
            {
                $string .= "<div id=\"tabs-" . $i++ . "\"><p>$content</p></div>";
            }
            $string .= "</div>";
        }
        return $string;
    }
}
?>