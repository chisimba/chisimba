<?php

/**
 * Class the controls the list of pages available.
 *
 * It doesn't contain the content of pages, just the index to track which pages
 * are translations of each other.
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
 * @package   learningcontent
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2006-2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: db_learningcontent_titles_class_inc.php 15438 2009-11-06 16:10:24Z davidwaf $
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
 * Class the controls the list of pages available.
 *
 * It doesn't contain the content of pages, just the index to track which pages
 * are translations of each other.
 *
 * @category  Chisimba
 * @package   learningcontent
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2006-2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

class db_learningcontent_titles extends dbtable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_learningcontent_titles');
        $this->objContentPages =& $this->getObject('db_learningcontent_pages');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objSysConfig =& $this->getObject('dbsysconfig', 'sysconfig');
    }
    
    /**
     * Method to add a title.
     *
     * @access public
     * @param string $titleId Record controlling translation group index
     * @param string $menutitle Menu title of the page
     * @param string $content Content of the Page
     * @param string $language Language of the Page
     * @param string $headerScript Any Script to go in the header
     * @return string The title id.
     */
    public function addTitle($titleId='', $menutitle, $content, $picture=Null, $formula=Null, $language, $headerScript=null,$scorm='N')
    {
        if ($titleId == '') {
            $titleId = $this->autoCreateTitle();
        } else {
            $this->createTitle($titleId);
        }
        $language=$this->objSysConfig->getValue('LANGUAGE', 'learningcontent');
        $pageId = $this->objContentPages->addPage($titleId, $menutitle, $content, $picture, $formula, $language, $headerScript,$scorm);
        
        return $titleId;
    }

    /**
     * Checks if translation group id exists.
     *
     * @access public
     * @param string $id The title id.
     * @return boolean
     */
    public function idExists($id)
    {
        return $this->valueExists('id', $id);
    }

    /**
     * Method to manually create a translation group index.
     *
     * @access private
     * @param string $id The title id.
     * @return string The title id.
     */
    private function createTitle($id)
    {
        $row = array();
        $row['id'] = $id;
        $row['creatorid'] = $this->objUser->userId();
        $row['datecreated'] = strftime('%Y-%m-%d %H:%M:%S', mktime());

        return $this->insert($row);
    }
    
    /**
     * Method to auto create a translation group index.
     *
     * @access private
     * @return The title id.
     */
    private function autoCreateTitle()
    {
        $row = array();
        $row['creatorid'] = $this->objUser->userId();
        $row['datecreated'] = strftime('%Y-%m-%d %H:%M:%S', mktime());

        return $this->insert($row);
    }
    
    /**
     * Method to delete a title. It also deletes all translations of the page.
     *
     * @access public
     * @param string $id The title id.
     */
    public function deleteTitle($id)
    {
        $this->delete('id', $id);
        $this->objContentPages->delete('titleid', $id);

        $objContextOrder = $this->getObject('db_learningcontent_order');
        $contexts = $objContextOrder->getContextWithPages($id);
        
        if (is_array($contexts) && count($contexts) > 0) {
            foreach ($contexts as $context) {
                $objContextOrder->deletePage($context['id']);
            }
        }
    }

}


?>
