<?php

/**
 * View condition
 * 
 * View Condition Class
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

$this->loadClass('classparser', 'decisiontable');

/**
 * View condition
 * 
 * View Condition Class
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
class viewCondition extends classParser
{
    /**
     * Object reference to the condition object.
     *
     * @access private
     * @var    object 
     */
    var $_objCondition = NULL;

    /**
     * Property used for storing the list of callback method available.
     *
     * @access private
     * @var    array  
     */
    var $_methods = array();

    /**
     * The object initialisation method.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_classPrefix = 'view';
    }

    /**
     * The object initialisation method.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function connect( $objCondition )
    {
        $this->_objCondition = $objCondition;
    }

    /**
     * The show the control for this object
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array  htmlelements
     */
    function elements()
    {
        $result = $this->callBack( $this->parser( $this->_objCondition->_params ) );
        return $result;
    }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array 
     */
    function setValue($value='TRUE')
    {
        $objRadio = $this->newObject('radio','htmlelements');
        $objRadio->name = 'value';
        $objRadio->addOption('setValue | TRUE', 'TRUE');
        $objRadio->addOption('setValue | FALSE', 'FALSE');
        $selected = 'setValue | '.$value;
        $objRadio->setSelected( $selected );

        $lblName = "Set Value";
        return array('lblName'=>$lblName,'element'=>$objRadio->show());
    }
} /* end of class viewCondition */
?>