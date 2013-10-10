<?php
/**
 *
 * Show the latest FAQ categories to be added
 *
 * Show the latest four FAQ categories to be added as a dynamic block.
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
 * @package    registerinterest
 * @author     Derek Keats derek@dkeats.com
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
 * Show the latest FAQ categories to be added
 *
 * Show the latest four FAQ categories to be added as a dynamic block.
 * 
 * @category  Chisimba
 * @author    Derek Keats derek@dkeats.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_latestcats extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     * 
     */
    public $title;
    /**
     * The user object
     *
     * @var    object
     * @access public
     * 
     */
    public $objUser;
    /**
     * The language object
     *
     * @var    object
     * @access public
     * 
     */    
    public $objLanguage;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->wrapStr = FALSE;
        $this->title = $this->objLanguage->languageText('mod_faq_latestcats', 
              'faq', "Latest FAQ categories");
    }
    /**
     * Show the latest categories block
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        $objContext = $this->getObject('dbcontext','context');
        $isInContext=$objContext->isInContext();
        if ($isInContext) {
            $contextId = $objContext->getContextCode();
        } else {
            $contextId = 'root';
        }
        $objDb = $this->getObject('dbfaqcategories', 'faq');
        $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        $cats = $objDb->getLatestContextCategories($contextId);
        $doc = new DOMDocument('UTF-8');
        $ret="";
        foreach ($cats as $cat) {
            $id = $cat['id'];
            $catName = $cat['categoryname'];
            $userId = $cat['userid'];
            $userName = $this->objUser->fullName($userId);
            $userName = " " . $this->objLanguage->languageText('mod_faq_addedby', 
              'faq', "Added by") . " " . $userName . " ";
            $updated = $cat['datelastupdated'];
            $fixedTime = strtotime($updated);
            $fixedTime = date('Y-m-d H:i:s', $fixedTime);
            $updated = $objHumanizeDate->getDifference($fixedTime);
            $ln = $this->uri(array(
              'action' => 'view',
               'category' => $id), 'faq');
            $ret .= $catName;
            $a = $doc->createElement('a');
            $a->setAttribute('href', $ln);
            $a->appendChild($doc->createTextNode($catName));
            $div = $doc->createElement('div');
            $div->setAttribute('class', 'faq_blocklink');
            $div->appendChild($a);
            $div->appendChild($doc->createTextNode($userName));
            $docFrag = $doc->createDocumentFragment();
            $docFrag->appendXML($updated);
            $div->appendChild($docFrag);
            $doc->appendChild($div);
        }
        return $doc->saveHTML();
    }
}
?>
