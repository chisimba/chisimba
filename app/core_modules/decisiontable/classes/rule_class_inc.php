<?php

/**
 * Decision Table Rule class
 * 
 * Decision Table Rule Class
 * 
 * PHP version 3
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
 * Decision Table Rule class
 * 
 * Decision Table Rule Class
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
class rule extends decisionTableAggregate
{

    // --- OPERATIONS ---

    /**
     * The object initialisation method.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_objParts = &$this->newObject( 'dbdecisiontablerule','decisiontable'  );
        $this->_objChild = &$this->newObject('dbrulecondition','decisiontable' );
        $this->_objActionRule = &$this->newObject('dbactionrule','decisiontable' );
        $this->_objCreated = &$this->newObject( 'condition','decisiontable'  );
        $this->_dbFK =  'conditionid';
        parent::init('tbl_decisiontable_rule' );
    }

    /**
     * Method to evaluate the rule and its conditions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return void  
     */
    function isValid()
    {
        // Rule is assumed TRUE.
        $result = TRUE;
        // Test all conditions.
        foreach( $this->_arrChildren as $condition ) {
            // Rule FAILS when one condition is FALSE.
            $result &= $condition->isValid();
        }
        // Return the result of the evaluation.
        return $result;
    }

    /**
     * Method to delete the object and all its children objects - modified to take into account no cascading deletes.
     * Explicitly calls deletes to the bridging tables reliant on the rule table
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
        
        $this->_objActionRule->delete($delObject->_id, 'ruleId');
        $this->_objChild->delete($delObject->_id, 'ruleId');
        $this->_objParts->delete('ruleId', $delObject->_id);

        return parent::delete( 'id', $delObject->_id );
    }
    
} /* end of class rule */
?>