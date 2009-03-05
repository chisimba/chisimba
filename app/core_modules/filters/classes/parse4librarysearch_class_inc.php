<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a dynamic form. i.e. the forms created in the Form Builder module (forms)
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
 * @package   filters
 * @author    Charl Mert <cmert@uwc.ac.za>
 * @copyright 2007 Paul Scott & Derek Keats 
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: parse4librarysearch_class_inc.php 11052 2008-10-25 16:04:14Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
     // security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a flv (Flash video file) and render the video in the page, YouTube! style
 *
 * @author    Paul Scott
 * @package   filters
 * @access    public
 * @copyright AVOIR GNU/GPL
 *
 */

class parse4librarysearch extends object
{
    
    /**
     * init
     * 
     * Standard Chisimba init function
     * 
     * @return void  
     * @access public
     */
    function init()
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');

        $this->loadClass('layer', 'htmlelements');

        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");

		$this->objLanguage =$this->newObject('language', 'language');
		$this->loadClass('textinput', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textarea','htmlelements');
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('layer', 'htmlelements');
        
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($txt)
    {
        preg_match_all('/\[LIBRARYSEARCH\](.*)\[\/LIBRARYSEARCH\]/U', $txt, $results, PREG_PATTERN_ORDER);
        preg_match_all('/\\[LIBRARYSEARCH:(.*?)\\]/', $txt, $results2, PREG_PATTERN_ORDER);
        
		$counter = 0;
        foreach ($results[1] as $item)
        {

			$objForm = new form('library_search_form', $this->uri(array('action' => 'search'), 'librarysearch'));
			$searchInput = new textinput ('search_key');

			$selectCluster = new dropdown('subject_cluster');
	
			$selectCluster->addOption('cat_database','Database');
			$selectCluster->addOption('cat_books','Books');
			$selectCluster->addOption('cat_websites','Websites');
			$selectCluster->selected = 'cat_websites';
	
			// Submit Button
	        $button = new button('submit_search', $this->objLanguage->languageText('word_search'));
	        $button->setToSubmit();
	
			$objForm->addToForm($searchInput->show() . ' ');
			$objForm->addToForm($selectCluster->show() . ' ');
			$objForm->addToForm($button->show());
	
			$replacement = $objForm->show();
			$txt = str_replace($results[0][$counter], $replacement, $txt);
			$counter++;
		}
        return $txt;
    }
    
    /**
     * 
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     * 
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->url)) {
            $this->id = $this->objExpar->url;
        } else {
            $this->id=NULL;
        }
        
        if (isset($this->objExpar->width)) {
            $this->id = $this->objExpar->width;
        } else {
            $this->id=NULL;
        }
        
        
    }
    
}
?>
