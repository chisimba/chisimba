<?php

/**
 * Action Class
 * 
 * Decision Table Action Class
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
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
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

$this->loadClass( 'decisiontableaggregate', 'decisiontable' );

/**
 * Action Class
 * 
 * Decision Table Action Class
 * 
 * @category  Chisimba
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class action extends decisionTableAggregate
{
    /**
     * The object initialization method.
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function init()
    {
        $this->_objParts= $this->newObject( 'dbdecisiontableaction','decisiontable'  );
        $this->_objChild = $this->newObject('dbactionrule','decisiontable');
        $this->_objCreated = $this->newObject( 'rule','decisiontable' );
        $this->_dbFK =  'ruleid';
        parent::init('tbl_decisiontable_action' );
    }

    /**
     * Method to evaluate the action and its rules.
     *
     * @access public    
     * @param  void      
     * @return true|false
     */
    public function isValid()
    {
        // Action is assumed FALSE.
        $result = FALSE;
        // Test all rules.
        foreach( $this->_arrChildren as $rule ) {

            // Action is ACCEPTED when any rule is TRUE.
            $result |= $rule->isValid();
        }
        // Return the result of the evaluation.

        return $result;
    }

     /**
     * Method to delete the object and all its children objects - modified to take into account no cascading deletes.
     * Explicitly calls deletes to the bridging tables reliant on the action table
     *
     * @access public    
     * @author Serge Meunier
     * @param  string     Delete object by name( optional )
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $name = NULL )
    {
        // Delete by name
        $delObject = $name ? $this->create( $name ) : $this;

        $this->_objChild->delete($delObject->_id, 'id');
        $this->_objParts->delete('id', $delObject->_id);

        return parent::delete( 'id', $delObject->_id );
    }
}
?>
