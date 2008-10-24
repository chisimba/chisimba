<?php

/**
 * Class to Register File Usage as part of file tracking purposes
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to Register File Usage as part of file tracking purposes
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
class registerfileusage extends dbTable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_usage');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
    * Method to add the file usage information to the database
    *
    * This is a private function that is called by the other functions for registering file usage
    *
    * @access private
    * @param  string  $fileId    Record Id of File to be tracked
    * @param  string  $module    Module the file will be used in
    * @param  string  $table     Name of the Table holding the file
    * @param  string  $recordId  Record Id of Record Using the File
    * @param  string  $column    Column of Record Using the File
    * @param  string  $context   Context File will be used in
    * @param  string  $workgroup Workgroup the File will be used in
    * @param  boolean $fileLock  Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string  Color in Hexadecimal format
    */
    private function addItem ($fileId, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE, $userId = NULL)
    {
    	if($userId == NULL)
    	{
    		$userId = $this->objUser->userId();
    	}
        $fileLock = $fileLock ? 'Y' : 'N';
        
        $insertId = $this->insert(array(
                'fileid' => $fileId,
                'module' => $module,
                'tablename' => $table,
                'recordid' => $recordId,
                'columnname' => $column,
                'context' => $context,
                'workgroup' => $workgroup,
                'filelock' => $fileLock,
                'creatorid' => $userId,
                'datecreated' => strftime('%Y-%m-%d', mktime()),
                'timecreated' => strftime('%H:%M:%S', mktime())
        ));
        
        // Done to Avoid Null Values
        if ($context == '') {
            $this->update('id', $insertId, array('context'=>''));
        }
        
        if ($workgroup == '') {
            $this->update('id', $insertId, array('workgroup'=>''));
        }
        
        return $insertId;
    }
    
    /**
    * Method to Register Use of a File in a Column of a Table
    *
    * It first deregisters the usage, and then re-adds them to prevent duplication
    *
    * @param  string  $fileId    Record Id of File to be tracked
    * @param  string  $module    Module the file will be used in
    * @param  string  $table     Name of the Table holding the file
    * @param  string  $recordId  Record Id of Record Using the File
    * @param  string  $column    Column of Record Using the File
    * @param  string  $context   Context File will be used in
    * @param  string  $workgroup Workgroup the File will be used in
    * @param  boolean $fileLock  Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string  Color in Hexadecimal format
    */
    public function registerUse($fileId, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE, $userid = NULL)
    {
        $this->deregisterUse($module, $table, $recordId, $column);
        
        $this->addItem ($fileId, $module, $table, $recordId, $column, $context, $workgroup, $fileLock, $userid);
    }
    
    /**
    * Method to Parse through a text, find the files that are being used, and register them.
    * This function is better suited to checks of text that may or may not contain links to
    * files in the file manager.
    *
    * @param  string  $text      Record Id of File to be tracked
    * @param  string  $module    Module the file will be used in
    * @param  string  $table     Name of the Table holding the file
    * @param  string  $recordId  Record Id of Record Using the File
    * @param  string  $column    Column of Record Using the File
    * @param  string  $context   Context File will be used in
    * @param  string  $workgroup Workgroup the File will be used in
    * @param  boolean $fileLock  Flag whether to apply a lock on the file - prevents file from being deleted
    * @return string  Color in Hexadecimal format
    */
    public function parseFilesUse($text, $module, $table, $recordId, $column, $context='', $workgroup='', $fileLock=FALSE)
    {
        preg_match_all('/index\\.php\\?module=filemanager(?:&amp;|&)action=file(?:&amp;|&)id=(?P<fileid>[_|\\w]*)/', $text, $result, PREG_PATTERN_ORDER);
        
        $this->deregisterUse($module, $table, $recordId, $column);
        
        if (count($result['fileid']) > 0) {
            foreach ($result['fileid'] as $fileId)
            {
                $this->addItem ($fileId, $module, $table, $recordId, $column, $context, $workgroup, $fileLock);
            }
        }
        
        return;
    }
    
    /**
    * Method to deregister File Usage
    * @param string $module     Name of the Module using file
    * @param string $table      Name of the Table holding the file
    * @param string $recordId   Record Id of the Record in the table
    * @param string $columnName Name of the Column in which the file is stored
    */
    public function deregisterUse($module, $table, $recordId, $columnName)
    {
        $list = $this->getAll(' WHERE module=\''.$module.'\' AND tablename=\''.$table.'\' AND recordid=\''.$recordId.'\' AND columnname=\''.$columnName.'\'');
        foreach ($list as $item)
        {
            $this->delete('id', $item['id']);
        }
        
        return;
    }

}

?>