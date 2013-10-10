<?php
/**
 *
 * User blocks functionality for oeruserdata module
 *
 * User blocks functionality for oeruserdata module provides 
 * blocks that give access to various user related functionality.
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
 * @package   oer
 * @author    Derek Keats derek@dkeats.com
 * @author    David Wafula
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
 * User blocks functionality for oeruserdata module
 *
 * User blocks functionality for oeruserdata module provides 
 * blocks that give access to various user related functionality.
*
* @package   oer
* @author    Derek Keats derek@dkeats.com
*
*/
class userblocks extends object
{

    public $objLanguage;
    public $objUser;

    /**
    *
    * Intialiser for insitution editor UI builder class. It instantiates
    * language object and loads the required classes.
    * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->setupLanguageItems();
        $this->loadJS();
    }
/**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['confirm_delete_user'] = "mod_oeruserdata_confirm_delete_user";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oeruserdata');
    }
    /**
     * JS an CSS for product rating and product download
     */
    function loadJS() {        
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('userlist.js'));
    }
    /**
     *
     * Render the input form for the user data.
     *
     * @return string The rendered form
     * @access public
     * 
     */
    public function showEditMyDetails()
    {
        $this->loadClass('link', 'htmlelements');
        if ($this->objUser->isLoggedIn()) {
            // Put a link for them to edit their own data.
            $id = $this->objUser->PKId();
            $uri = $this->uri(array(
              'action' => 'edituser', 'id' => $id, 'mode' => 'edit'), 
              'oeruserdata');
            $link = new link($uri);
            $link->link = $this->objLanguage->languageText(
              'mod_oeruserdata_edityou', 'oeruserdata');
            $ret = $link->show();
            
            // Put a register link for admins
            $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $groupId = $objGroups->getId("Usermanagers");
            $objGroupOps = $this->getObject("groupops", "groupadmin");
            $userId = $this->objUser->userId();
            if ($this->objUser->isAdmin() || 
              $objGroupOps->isGroupMember($groupId, $userId )) {
                  $linkText = $this->objLanguage->languageText(
                    'mod_oeruserdata_adminreg', 'oeruserdata');
                    $ret .= "<br /><br />" 
                    . $this->putLink($linkText, 'adduser');
            }
        } else {
            // Put a registration link
            $linkText = $this->objLanguage->languageText(
              'mod_oeruserdata_selfreg', 'oeruserdata');
            $ret = $this->putLink($linkText, 'selfregister');
            
        }
        return $ret;
    }
    
    /**
     *
     * Add a link to register or add user
     * 
     * @param string $linkText The text to display
     * @param string $linkType The type of link (selfregister, or adduser)
     * @return string The rendered link 
     * @access private
     * 
     */
    private function putLink($linkText, $linkType)
    {
        // Put a registration link
        $uri = $this->uri(array(
          'action' => $linkType), 
          'oeruserdata');
        $link = new link($uri);
       
        $link->link = $linkText;
        return $link->show();
    }
    
    /**
     *
     * Show a paginated list of users
     * 
     * @param boolean $firstRender Should be true to set up the page, false thereafter for Ajax
     * @return string A rendered list with edit/delete links
     * @access public
     * 
     */
    public function showUserList($firstRender=TRUE)
    {
        $pageSize = 10;
        // Some security: only admin and the appropriate group should do this
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("Usermanagers");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        if ($this->objUser->isAdmin() || 
          $objGroupOps->isGroupMember($groupId, $userId )) {
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('htmltable','htmlelements');
           
            // Set up the page navigation.
            $page = $this->getParam('page', 1);
           
            $objDb = $this->getObject('dboerusermain', 'oeruserdata');
            $count = $objDb->getUserCount();
            $pages = ceil($count/$pageSize);
            // Set up the sql elements.
            $start = (($page) * $pageSize);
            if($start < 0){
                $start=0;
            }
            $objDb = $this->getObject('dboeruserdata', 'oeruserdata');
            $rs = $objDb->getForListing($start, $pageSize);
           
            // Edit icon
            $edIcon = $this->newObject('geticon', 'htmlelements');
            $edIcon->setIcon('edit');
            $editIcon = $edIcon->show();
            unset($edIcon);
            // Delete icon.
            $delIcon = $this->newObject('geticon', 'htmlelements');
            $delIcon->setIcon('delete');
            $deleteIcon = $delIcon->show();
            unset($delIcon);
            
            // Display the records.
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cssId = 'users';
            $table->startHeaderRow();
            $table->addHeaderCell('Title');
            $table->addHeaderCell('First name');
            $table->addHeaderCell('Surname');
            $table->addHeaderCell('Username');
            $table->addHeaderCell('&nbsp;');
            $table->endHeaderRow();
            if (!empty($rs)) {
                foreach($rs as $record) {
                    $edUrl = $this->uri(array(
                      'action' => 'edituser', 
                      'id' => $record['id'], 
                      'mode' => 'edit'), 'oeruserdata');
                    $link = new link($edUrl);
                    $link->link = $editIcon;
                    /*$delUrl = $this->uri(array(
                      'action' => 'delete',
                      'id' => $record['id']), 'oeruserdata');                    
                    $delLink = new link($delUrl);*/
                    $delLink = new link("#delete_user");
                    $delLink->cssId = $record['id'];
                    $delLink->cssClass = "confirm_del_user_link";
                    //
                    $delLink->extra = 'name="modal" onclick="showDelConfirm(\''.$record['id'].'\');" alt="' . $this->objLanguage->languageText('word_delete', 'system') . '"';
                    $delLink->link = $deleteIcon;
                    $table->startRow(NULL, "ROW_" . $record['id']);
                    $table->addCell($record['title']);
                    $table->addCell($record['firstname']);
                    $table->addCell($record['surname']);
                    $table->addCell($record['username']);
                    $table->addCell('<div id="manageuser">'.$link->show() ." ". $delLink->show() . '</div> ');
                    $table->endRow();
                }
            }
            $ret = $table->show();
                $h = $this->objLanguage->languageText(
                      'mod_oeruserdata_ulst', 'oeruserdata');
                if ($firstRender == TRUE) {
                    $ret = "<h1>$h</h1><br />"
                      . "<div id='userlisting'>$ret</div><br/>";
                } 
            
          } else {
            $ret = $this->objLanguage->languageText(
              'mod_oeruserdata_nvrts', 'oeruserdata');
          }
          return $ret;
    }
    
    /**
     * 
     * Paginated recordset using the pagination class in navigation
     *
     * @param type $pageSize
     * @return type 
     * 
     */
    public function showUserListPaginated($pageSize=10)
    {
        
        // Some security: only admin and the appropriate group should do this
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("Usermanagers");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        if ($this->objUser->isAdmin() || 
          $objGroupOps->isGroupMember($groupId, $userId )) {
            $objPagination = $this->newObject ( 'pagination', 'navigation' );
            $objPagination->module = 'oeruserdata';
            $objPagination->action = 'userlistajax';
            $objPagination->id = 'oeruserlist_div';
            $objDb = $this->getObject('dboerusermain', 'oeruserdata');
            $objPagination->currentPage = 0;
            $count = $objDb->getUserCount();
            $pages = ceil($count/$pageSize);
            $objPagination->numPageLinks = $pages;
            return $objPagination->show();
          } else {
              return FALSE;
          }
    }
}
?>