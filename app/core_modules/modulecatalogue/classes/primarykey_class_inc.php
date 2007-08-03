<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
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
/**
* class to make unique primary keys for NextGen initial setup
* and module registration
* @author James Scoble
*/
class primarykey extends object
{

    /**
     * Description for var
     * @var    array 
     * @access public
     */
    var $tables;
    
    /**
    * class constructor function
    */
    function primarykey()
    {
        $this->init();
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    function init()
    {
        $this->tables=array();
    }
    
    /**
    * returns a key
    * @param   string $table
    * @returns string $outstr
    */
    function newkey($table='blank')
    {
        if (isset($this->tables[$table]))
        {
            $this->tables[$table]++;
        } else {
            //$this->tables[$table]=rand(100,999);
            $this->tables[$table]=0;
        }
        //$outstr=date("Ymdhis").$this->tables[$table]."@init";
        $outstr=$this->tables[$table]."@init";
        return $outstr;
    }
}
?>