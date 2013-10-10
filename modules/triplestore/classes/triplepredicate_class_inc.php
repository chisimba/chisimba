<?php

/**
 * Triplestore predicate data access class
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
 * @version   $Id: triplepredicate_class_inc.php 16724 2010-02-07 02:28:38Z charlvn $
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
 * Triplestore predicate data access class
 * 
 * Class to facilitate interaction with the framework triplestore.
 * 
 * @category  chisimba
 * @package   triplestore
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: triplepredicate_class_inc.php 16724 2010-02-07 02:28:38Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */

class triplepredicate extends object implements Iterator
{
    /**
     * The objects associated with this predicate.
     *
     * @access protected
     * @var    array
     */
    protected $objects;

    /**
     * Instance of the dbtriplestore class of the triplestore module.
     *
     * @access protected
     * @var    object
     */
    protected $objTriplestore;

    /**
     * The name of the predicate.
     *
     * @access protected
     * @var    string
     */
    protected $predicate;

    /**
     * The name of the subject.
     *
     * @access protected
     * @var    string
     */
    protected $subject;

    /**
     * Initialise the instance of the triplepredicate class.
     *
     * @access public
     */
    public function init()
    {
        $this->objects = array();
        $this->objTriplestore = $this->getObject('dbtriplestore', 'triplestore');
    }

    /**
     * Converts this instance to a string by impoding the object array.
     *
     * @access public
     * @return string The imploded object array.
     */
    public function __toString()
    {
        return implode(' ', $this->objects);
    }

    /**
     * Adds an object to this predicate.
     *
     * @access public
     * @param  string $object The new object to add.
     */
    public function add($object)
    {
        $id = $this->objTriplestore->insert($this->subject, $this->predicate, $object);
        $this->objects[$id] = $this->newObject('tripleobject', 'triplestore');
        $this->objects[$id]->populate($id, $object);
    }

    /**
     * Part of the Iterator interface.
     *
     * @access public
     */
    public function current()
    {
        return current($this->objects);
    }

    /**
     * Deletes all the objects in this predicate.
     *
     * @access public
     */
    public function delete()
    {
        // Delete the objects from the triplestore.
        foreach ($this->objects as $id => $object) {
            $this->objTriplestore->delete($id);
        }

        // Clear the object array.
        $this->objects = array();
    }

    /**
     * Gets an object at a particular index.
     *
     * @access public
     * @param  integer $index The index of the object to return.
     * @return object  An instance of the tripleobject class.
     */
    public function get($index)
    {
        return $this->objects[$index];
    }

    /**
     * Part of the Iterator interface.
     *
     * @access public
     */
    public function key()
    {
        return key($this->objects);
    }

    /**
     * Part of the Iterator interface.
     *
     * @access public
     */
    public function next()
    {
        return next($this->objects);
    }

    /**
     * Populates the properties of the object.
     *
     * @access public
     * @param  string $subject   The name of the subject.
     * @param  string $predicate The name of the predicate.
     * @param  array  $objects   The objects retrieved from the triplestore.
     */
    public function populate($subject, $predicate, $objects)
    {
        $this->subject   = $subject;
        $this->predicate = $predicate;
        $this->objects   = array();

        foreach ($objects as $id => $value) {
            $this->objects[$id] = $this->newObject('tripleobject', 'triplestore');
            $this->objects[$id]->populate($id, $value);
        }
    }

    /**
     * Part of the Iterator interface.
     *
     * @access public
     */
    public function rewind()
    {
        reset($this->objects);
    }

    /**
     * Sets the object or objects on this predicate.
     *
     * @access public
     * @param  mixed $objects A single object or an array of objects.
     */
    public function set($objects)
    {
        // Delete the old objects.
        $this->delete();

        // Ensure $objects is an array.
        if (!is_array($objects)) {
            $objects = array($objects);
        }

        // Insert the new objects.
        foreach ($objects as $object) {
            $this->add($object);
        }
    }

    /**
     * Returns an array of objects on this predicate.
     *
     * @access public
     * @return array An associative array of the string values of the objects.
     */
    public function toArray()
    {
        $objects = array();

        foreach ($this->objects as $id => $object) {
            $objects[$id] = (string) $object;
        }

        return $objects;
    }

    /**
     * Part of the Iterator interface.
     *
     * @access public
     */
    public function valid()
    {
        return $this->current() !== false;
    }
}
