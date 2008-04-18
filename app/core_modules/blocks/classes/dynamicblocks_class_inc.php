<?php

/**
 * Dynamic Blocks class
 * 
 * Class to handle dynamic block generation for Chisimba
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
 * @package   blocks
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!
    /**
     * Description for $GLOBALS
     * @global entry point $GLOBALS['kewl_entry_point_run']
     * @name   $kewl_entry_point_run
     */
    $GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
 * Dynamic Blocks class
 * 
 * Class to handle dynamic block generation in Chisimba
 * 
 * @category  Chisimba
 * @package   blocks
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dynamicblocks extends dbTable
{

    /**
    * @var object $objUser Property to hold the objUser object
    */
    public $objUser;

    /**
    * @var object $objModule Property to hold the Module object
    */
    public $objModule;

    /**
    * Constructor method
    */
    public function init()
    {
        //Create an instance of the modulesadmin class for checking
        // if a module is registered
        try {
            $this->objModule = $this->getObject('modules','modulecatalogue');
            $this->objUser = $this->getObject('user','security');
            parent::init('tbl_dynamicblocks');
        }
        catch (customException $e)
        {
            echo customException::cleanUp($e);
            die();
        }
    }
    
    /**
     * Method to show a Block
     * @param str $id Record Id of the Block
     * @return str Block
     */
    public function showBlock($id)
    {
        $block = $this->getRow('id', $id);
        
        if ($block == FALSE) {
            return FALSE;
        } else {
        
            return $this->showBlockFromArray($block);
        }
    }
    
    /**
     * Method to show a Block
     * @param array $block Record Details of Block
     * @return str Block
     */
    public function showBlockFromArray($block)
    {
        
        $dynamicBlock = $this->getObject($block['object'], $block['module']);
        
        $str = $dynamicBlock->{$block['function']}($block['parameter']);
        
        if ($str == '' || $str == FALSE) {
            return FALSE;
        } else {
            $objFeatureBox =  $this->newObject('featurebox', 'navigation');
            return $objFeatureBox->show($block['title'], $str);
        }
    }
    
    /**
     * Method to add a Dynamic Block
     * @param string $module Name of the Module block is for
     * @param string $object Object to call to render block
     * @param string $function Function to call to render block
     * @param string $parameterValue Parameter to pass to function
     * @param string $title Title of Block
     * @param string $typeofblock Type of Block: Either user, context, workgroup, admin, site
     * @param string $userOrContextOrWorkgroupCode Matching $typeofblock.
     *                  If $typeofblock is context, this will be contextcode
     *                  If $typeofblock is workgroup, this will be workgroup code
     *                  If $typeofblock is user, this will be user id
     *                  If $typeofblock is admin, this is not required
     * @param string $blocksize Size of the Block - either small or wide
     */
    public function addBlock($module, $object, $function, $parameterValue, $title, $typeofblock='context', $userOrContextOrWorkgroupCode, $blocksize='small', $creatorId = NULL)
    {
    	if($creatorId == NULL)
    	{
    		$creatorId = $this->objUser->userId();
    	}
        // First Check if block exists
        if ($this->blockExists($module, $object, $function, $parameterValue, $typeofblock)) {
            return TRUE;
        } else {
            return $this->insert(array(
                    'module' => $module,
                    'object' => $object,
                    'function' => $function,
                    'parameter' => $parameterValue,
                    'title' => $title,
                    'typeofblock' => $typeofblock,
                    'userorcontextorworkgroupcode' => $userOrContextOrWorkgroupCode,
                    'blocksize' => strtolower($blocksize),
                    'creatorid' => $creatorId,
                    'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
                    
                ));
        }
    }
    
    /**
     * Method to check whether a dynamic block exists or not
     *
     * Only these values are required to do the check
     * 
     * @param string $module Name of the Module block is for
     * @param string $object Object to call to render block
     * @param string $function Function to call to render block
     * @param string $parameterValue Parameter to pass to function
     * @param string $typeofblock Type of Block: Either user, context, workgroup, admin, site
     *
     * @return boolean
     */
    public function blockExists($module, $object, $function, $parameterValue, $typeofblock='context')
    {
        $where = ' WHERE module=\''.$module.'\' AND object=\''.$object.'\' AND function=\''.$function.'\' AND parameter=\''.$parameterValue.'\' AND typeofblock=\''.$typeofblock.'\'';
        
        $results = $this->getRecordCount($where);
        
        if ($results == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * Method to remove a Dynamic Block
     *
     * Only these values are required to do a delete
     * 
     * @param string $module Name of the Module block is for
     * @param string $object Object to call to render block
     * @param string $function Function to call to render block
     * @param string $parameterValue Parameter to pass to function
     * @param string $typeofblock Type of Block: Either user, context, workgroup, admin, site
     */
    public function removeBlock($module, $object, $function, $parameterValue, $typeofblock='context')
    {
        $where = ' WHERE module=\''.$module.'\' AND object=\''.$object.'\' AND function=\''.$function.'\' AND parameter=\''.$parameterValue.'\' AND typeofblock=\''.$typeofblock.'\'';
        
        $results = $this->getAll($where);
        
        if (count($results) > 0) {
            foreach ($results as $result)
            {
                $this->delete('id', $result['id']);
            }
        }
    }
    
    /**
     * Method to Update the title of a Dynamic Block
     *
     * Only these values are required to do a delete
     * 
     * @param string $module Name of the Module block is for
     * @param string $object Object to call to render block
     * @param string $function Function to call to render block
     * @param string $parameterValue Parameter to pass to function
     * @param string $typeofblock Type of Block: Either user, context, workgroup, admin, site
     * @param string $title Title of Block
     */
    public function updateTitle($module, $object, $function, $parameterValue, $typeofblock='context', $title)
    {
        $where = ' WHERE module=\''.$module.'\' AND object=\''.$object.'\' AND function=\''.$function.'\' AND parameter=\''.$parameterValue.'\' AND typeofblock=\''.$typeofblock.'\'';
        
        $results = $this->getAll($where);
        
        if (count($results) > 0) {
            foreach ($results as $result)
            {
                $this->update('id', $result['id'], array('title'=>$title));
            }
        }
    }
    
    /**
     * Method to get a list of blocks. This functions provides parameters
     * that can be used to filter the type of blocks requested
     * @return array List of Blocks
     */
    public function getBlocks($type='context', $codeId=NULL, $size=NULL, $module=NULL)
    {
        $where = ' WHERE typeofblock=\''.$type.'\'';
        
        if ($codeId != NULL) {
            $where .= ' AND userorcontextorworkgroupcode=\''.$codeId.'\'';
        }
        
        if ($size != NULL) {
            $where .= ' AND blocksize=\''.$size.'\'';
        }
        
        if ($module != NULL) {
            $where .= ' AND module=\''.$module.'\'';
        }
        
        return $this->getAll($where);
    }
    
    /**
     * Method to get a of all blocks related to a context
     * @param string $contextCode Context Code
     * @return array List of Blocks
     */
    public function getContextBlocks($contextCode)
    {
        return $this->getBlocks('context', $contextCode);
    }
    
    /**
     * Method to get a of all small blocks related to a context
     * @param string $contextCode Context Code
     * @return array List of Blocks
     */
    public function getSmallContextBlocks($contextCode)
    {
        return $this->getBlocks('context', $contextCode, 'small');
    }
    
    /**
     * Method to get a of all wide blocks related to a context
     * @param string $contextCode Context Code
     * @return array List of Blocks
     */
    public function getWideContextBlocks($contextCode)
    {
        return $this->getBlocks('context', $contextCode, 'wide');
    }
    
    /**
     * Method to get a of all blocks related to a user
     * @param string $userId User Id
     * @return array List of Blocks
     */
    public function getUserBlocks($userId)
    {
        return $this->getBlocks('user', $userId);
    }
    
    /**
     * Method to get a of all small blocks related to a user
     * @param string $userId User Id
     * @return array List of Blocks
     */
    public function getSmallUserBlocks($userId)
    {
        return $this->getBlocks('user', $userId, 'small');
    }
    
    /**
     * Method to get a of all wide blocks related to a user
     * @param string $userId User Id
     * @return array List of Blocks
     */
    public function getWideUserBlocks($userId)
    {
        return $this->getBlocks('user', $userId, 'wide');
    }
    
    /**
     * Method to get a of all site blocks
     * @return array List of Blocks
     */
    public function getSiteBlocks()
    {
        return $this->getBlocks('site');
    }
    
    /**
     * Method to get a of all small site blocks
     * @return array List of Blocks
     */
    public function getSmallSiteBlocks()
    {
        return $this->getBlocks('site', NULL, 'small');
    }
    
    /**
     * Method to get a of all wide site blocks
     * @return array List of Blocks
     */
    public function getWideSiteBlocks()
    {
        return $this->getBlocks('site', NULL, 'wide');
    }
    
    /**
     * Method to get a of all admin blocks
     * @return array List of Blocks
     */
    public function getAdminBlocks()
    {
        return $this->getBlocks('admin');
    }
    
    /**
     * Method to get a of all small admin blocks
     * @return array List of Blocks
     */
    public function getSmallAdminBlocks()
    {
        return $this->getBlocks('admin', NULL, 'small');
    }
    
    /**
     * Method to get a of all wide admin blocks
     * @return array List of Blocks
     */
    public function getWideAdminBlocks()
    {
        return $this->getBlocks('admin', NULL, 'wide');
    }
    
    /**
     * Method to get a of all blocks related to a workgroup
     * @param string $workgroupId Workgroup Id
     * @return array List of Blocks
     */
    public function getWorkgroupBlocks($workgroupId)
    {
        return $this->getBlocks('context', $workgroupId);
    }
    
    /**
     * Method to get a of all small blocks related to a workgroup
     * @param string $workgroupId Workgroup Id
     * @return array List of Blocks
     */
    public function getSmallWorkgroupBlocks($workgroupId)
    {
        return $this->getBlocks('context', $workgroupId, 'small');
    }
    
    /**
     * Method to get a of all wide blocks related to a workgroup
     * @param string $workgroupId Workgroup Id
     * @return array List of Blocks
     */
    public function getWideWorkgroupBlocks($workgroupId)
    {
        return $this->getBlocks('context', $workgroupId, 'wide');
    }


} //end of class
?>