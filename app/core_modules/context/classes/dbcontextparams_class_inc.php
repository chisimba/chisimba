<?php

/**
 * Context params
 * 
 * Class to manipulate context parameters in Chisimba
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
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check


/**
 * Context params
 * 
 * Class to manipulate context parameters in Chisimba
 * 
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbcontextparams extends dbTable{
     /**
    *Initialize by send the table name to be accessed 
    */
     function init(){
        parent::init('tbl_contextparams');
        
        $this->_objDBContext =  $this->newObject('dbcontext', 'context');
    }
    
   
    /**
     * Method to add a parameter
     * @param  string $param      
     * @param  string $value      
     * @param  string $contextCode
     * @access public
     * @return bool  
     */
    public function setParam($contextCode, $param, $value = null)
    {
    	try{
	    	$fields = array(
	    				'param' => $param,
	    				'value' => $value,
	    				'contextcode' => $contextCode);
	    	
	    	if($this->getParamValue($contextCode, $param))
	    	{
	    		//edit the param
	    		$sql = "UPDATE tbl_contextparams SET value = '".$value."' WHERE contextcode = '".$contextCode."' AND param = '".$param."'";
	    		return $this->getArray($sql);
	    	} else {
	    		return $this->insert($fields);	
	    	}
	    	
    	
    	 }                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    				
    }
    
    /**
     * Method to get a param
     * @param  string $contextCode
     * @param  string $param      
     * @return string
     */
    public function getParamValue($contextCode, $param)
    {
    	
    	$line = $this->getAll("WHERE contextcode = '".$contextCode."'  AND param = '".$param."'");
    	if(count($line) > 0)
    	{
    		return $line[0]['value'];
    	} else {
    		return FALSE;
    	}  	
    	
    }
    
 }

?>