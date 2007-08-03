<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/*
 * Class for manipulating tables for moduleadmin
 * @author Paul Scott
 * @version $Id$
 * @copyright 2004
 * @license GNU GPL
 */

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class tableinfo extends dbtable
{
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        parent::init('tbl_users');
    }
    
   /**
    * This is a method to return a list of the tables in the database.
    * 
    * @access  public
    * @param   void  
    * @returns array $list
    */
    public function tablelist()
    {
    	return $this->listDbTables();
    }
    
    /**
    * This is a method to check if a given table's name is in an array - by default the array used is class variable $tables
    * 
    * @access  public
    * @param   string $name
    * @param   array  $list (optional)
    * @returns TRUE or FALSE
    */
    public function checktable($name,$list=NULL)
    {
        if (is_null($list)){
            $list = $this->tablelist();
        }
        return in_array($name, $list);
    }
}
?>