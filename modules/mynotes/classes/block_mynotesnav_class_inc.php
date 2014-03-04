<?php
/**
 *
 * A navigation and info block for My notes.
 * 
 * Take notes, organize them by tags, keep them private
 * or share them with your friends, all user, or the world.
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
 * @version    0.001
 * @package    mynotes
 * @author     Nguni Phakela nguni52@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * A navigation and info block for My notes.
 * 
 * Take notes, organize them by tags, keep them private
 * or share them with your friends, all user, or the world.
 *
 * @category  Chisimba
 * @author    Nguni Phakela nguni52@gmail.com 
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_mynotesnav extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    
    /*
     * The object used for module operations
     * 
     */
    public $objNoteOps;
    
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_mynotes_nav', 'mynotes', NULL, 'TEXT: mod_mynotes_nav, not found');
        
        // Load operations class for notes.
        $this->objNoteOps = $this->getObject('noteops', 'mynotes');
    }
    
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() {
        //return $this->objNoteOps->getTagCloud();
        return $this->buildNav();
    }
    
    /**
     * 
     * Build the content and links for the navigation block
     * 
     * @return string The navigation content.
     * @access private
     * 
     */
    private function buildNav()
    {
        // Build the notes home link.
        $url = $this->uri(array(), 'mynotes');
        $url = str_replace("&amp;", "&", $url);
        // Use the DOM to do the work.
        $doc = new DOMDocument('UTF-8');
        // Create the notes home link.
        $a = $doc->createElement('a');
        $a->setAttribute('href', $url);
        $lnkTxt = $this->objLanguage->languageText('mod_mynotes_allnotes', 'mynotes', 'My notes');
        $a->appendChild($doc->createTextNode($lnkTxt));
        // Create the surrounding DIV.
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'mynotes_nav');
        $div->appendChild($a);
        $doc->appendChild($div);
        // Create a div for the dynamic content added by Javascript.
        $dyndiv = $doc->createElement('div');
        $dyndiv->setAttribute('id', 'mynotes_dyn');
        $doc->appendChild($dyndiv);
        return $doc->saveHTML();
    }
}
?>