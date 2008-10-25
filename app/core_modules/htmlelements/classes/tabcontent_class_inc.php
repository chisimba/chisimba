<?php

/**
 * Tabcontent class
 * 
 * Class to generate a multi tab panel
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
 * @package   htmlelements
 * @author Kevin Cyster <kcyster@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * Class to generate a multi tab panel
 * @author Kevin Cyster
 *         
 *         This class wraps the tabcontent script available from Dynamic Drive
 *         http://www.dynamicdrive.com/dynamicindex17/tabcontent.htm
 *         
 *         This class is similar to the tabpane and multitabbedbox classes,
 *         though in the following respects:
 *         
 *         a) It is unobtrusive, will work when JavaScript is disabled, so allow for better accessibility
 *         b) It is easier to style using CSS
 *         
 */
class tabcontent extends object
{
    
    /**
     * Unique Name for the Tab Content Object
     *
     * @var string
     */
    public $name;
    
    /**
     * Width of the Tab content Object
     * 450px is the default width as per the CSS Stylesheet
     *
     * @var string
     */
    public $width='450px';
    
    /**
     * Height of the Tab content Object
     * If no height is specified, it will take the height per tab. Content below will shift
     * up/down depending on the difference in the heights of tabs
     *
     * @var string
     */
    public $height = '';
    
    /**
     * Private Variable to hold list of tabs submitted
     *
     * @var array
     */
    private $tabs=array();
    
    /**
     * Private Variable to indicate whether user has set at least one tab as a default
     * Where the user adds more then one default tab, the first will always be shown
     *
     * @var boolean
     */
    private $hasDefaultSelected = FALSE;
    
    /**
     * Constructor
     *
     */
    public function init()
    {
        // Generate a Unique Name for the tab content object
        $this->name = 'tabcontent'.rand().'s';
    }
    
    /**
     * Method to add a tab panel
     *
     * @param string  $label           Label of the Tab
     * @param string  $content         Content of the Tab
     * @param string  $link            Link. The tab will be a link to a URL instead of having content
     * @param boolean $defaultSelected Flag whether this should be the default tab to be shown
     */
    public function addTab($label, $content, $link='', $defaultSelected=FALSE, $height='')
    {
        // Create an array with details
        $tab = array('label'=>$label, 'selected'=>$defaultSelected);
        
        // If link is provided, tab should not have content, as well as vice-versa
        if ($link=='') {
            $tab['content'] = $content;
            $tab['link'] = '';
        } else {
            $tab['content'] = '';
            $tab['link'] = $link;
        }
        
        // Add Tab to List of Tabs
        $this->tabs[] = $tab;
        
        // Update Flag if Default Selected is Set
        if ($defaultSelected) {
            $this->hasDefaultSelected = TRUE;
        }
    }
    
    /**
     * Method to Display the TabContent
     *
     * @return string
     */
    public function show()
    {
        // Return Nothing if No tabs are added
        if (count($this->tabs) == 0) {
            return '';
        } else {
            // Commence Rendering Labels
            $tabnavigation = '<ul id="'.$this->name.'" class="shadetabs">';
            
            if ($this->height == '') {
                $height = '';
            } else {
                $height = 'style="height: '.$this->height.'; overflow-y: auto;"';
            }
            
            // Commence Rendering Tab Content
            $tabcontent = '<div id="tabcontentstyle'.$this->name.'" class="tabcontentstyle" '.$height.'>';
            
            // Counter to keep tabs unique
            $counter = 1;
            
            // Loop through tabs
            foreach ($this->tabs as $tab)
            {
                // If tab is a link, create link
                if ($tab['link'] != '') {
                    $tabnavigation .= '<li><a href="'.$tab['link'].'">'.$tab['label'].'</a></li>';
                } else {
                    // Check whether tab is default selected
                    if (!$this->hasDefaultSelected) { // First Tab is default if none is provided
                        $selected = ($counter == 1) ? 'class="selected"' : '';
                    } else {
                        $selected = ($tab['selected']) ? 'class="selected"' : '';
                    }
                    
                    $selectedDiv = ($selected == '') ? '' : ' tabcontentselected';
                    
                    // Add Tab Label
                    $tabnavigation .= '<li '.$selected.'><a href="javascript:;" rel="'.$this->name.'_tcontent'.$counter.'">'.$tab['label'].'</a></li>';
                    
                    // Add Tab Content
                    $tabcontent .= '<div id="'.$this->name.'_tcontent'.$counter.'" class="tabcontent'.$selectedDiv.'">'.$tab['content'].'</div>';
                    
                    // Increase Counter for next tab
                    $counter++;
                }
            }
            
            // Complete Rendering
            $tabnavigation .= '</ul>';
            $tabcontent .= '</div>';
            
            // Add JavaScript to Header
            $this->appendArrayVar('headerParams', '<script type="text/javascript" src="core_modules/htmlelements/resources/tabcontent/tabcontent.js">

/***********************************************
* Tab Content script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

</script>');
             
            // Add Width Customization to Header
            if ($this->width != '') {
                $this->appendArrayVar('headerParams', '
<style type="text/css">
    div#tabcontentstyle'.$this->name.' {
        width: '.$this->width.';
    }
</style>');
            } // END Width
            
            // Add Tab Initialisation
            $this->appendArrayVar('bodyOnLoad', 'initializetabcontent("'.$this->name.'");');
            
            // Return Tab
            return $tabnavigation.$tabcontent;
        }
    }
}

?>
