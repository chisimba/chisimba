<?php

/**
 * Triplestore object data access class
 * 
 * Class to facilitate interaction with the framework triplestore.
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
 * @category  chisimba
 * @package   triplestore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tripleobject_class_inc.php 16716 2010-02-07 01:15:13Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Triplestore object data access class
 * 
 * Class to facilitate interaction with the framework triplestore.
 * 
 * @category  chisimba
 * @package   triplestore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tripleobject_class_inc.php 16716 2010-02-07 01:15:13Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */

class tripleobject extends object
{
    /**
     * The triple id of this object.
     *
     * @access protected
     * @var    string
     */
    protected $id;

    /**
     * Instance of the dbtriplestore class of the triplestore module.
     *
     * @access protected
     * @var    object
     */
    protected $objTriplestore;

    /**
     * The value of this object.
     *
     * @access protected
     * @var    string
     */
    protected $value;

    /**
     * Initialise the instance of the triplepredicate class.
     *
     * @access public
     */
    public function init()
    {
        $this->objTriplestore = $this->getObject('dbtriplestore', 'triplestore');
    }

    /**
     * Converts this object to a string.
     *
     * @access public
     * @return string The value of this object.
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Deletes the triple containing this object.
     *
     * @access public
     */
    public function delete()
    {
        $this->objTriplestore->delete($this->id);
    }

    /**
     * Changes the value of this object.
     *
     * @access public
     * @param  string $value The new value.
     */
    public function edit($value)
    {
        $this->objTriplestore->update($this->id, FALSE, FALSE, $value);
        $this->value = $value;
    }

    /**
     * Populates the properties of the object.
     *
     * @access public
     * @param  string $id    The triple id of the object.
     * @param  string $value The value of the object.
     */
    public function populate($id, $value)
    {
        $this->id    = $id;
        $this->value = $value;
    }
}
