<?php

/**
 * RPX Identifier dbtable derived class.
 * 
 * Class to interact with the database for the RPX module.
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
 * @package   rpx
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk.
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbrpxidentifiers extends dbTable 
{
    /**
     * The database list of the current user's RPX identifiers.
     *
     * @access protected
     * @var    array
     */
    protected $dbIdentifiers;

    /**
     * The working list of the current user's RPX identifiers.
     *
     * @access protected
     * @var    array
     */
    protected $identifiers;

    /**
     * Instance of the user class of the security module.
     *
     * @access protected
     * @var    object
     */
    protected $objUser;

    /**
     * The current user's unique identifier.
     *
     * @access protected
     * @var    string
     */
    protected $userId;

    /**
     * The standard class constructor.
     */
    public function init()
    {
        // Initialise the parent class with the table name.
        parent::init('tbl_rpx_identifiers');

        // Get the user object and user identifier.
        $this->objUser = $this->getObject('user', 'security');
        $this->userId  = $this->objUser->userId();

        // Populate the dbIdentifiers property from the database.
        $this->dbIdentifiers = array();
        $rows = $this->getAll('userid = ' . $this->userId);
        foreach ($rows as $row) {
            $this->dbIdentifiers[] = $row['identifier'];
        }

        // To start off with, use the database identifiers list as the working list.
        $this->identifiers = $this->dbIdentifiers;
    }

    /**
     * Returns the current working identifiers.
     *
     * @access public
     * @return array A single-dimensional array of strings.
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * Adds an identifier if it doesn't already exist.
     *
     * @access public
     * @param  string $identifier The new identifier.
     */
    public function addIdentifier($identifier)
    {
        if (!in_array($identifier, $this->identifiers)) {
            $this->identifiers[] = $identifier;
        }
    }

    /**
     * Removes an identifer if it exists.
     *
     * @access public
     * @param  string $identifier The identifier to remove.
     */
    public function removeIdentifier($identifier)
    {
        $key = array_search($identifier, $this->identifiers);
        if ($key !== FALSE) {
            unset($this->identifiers[$key]);
        }
    }

    /**
     * Update the identifiers in the database according to the working list.
     *
     * @access public
     */
    public function put()
    {
        // Insert all the new identifiers.
        foreach (array_diff($this->identifiers, $this->dbIdentifiers) as $identifier) {
            $this->insert(array('identifier'=>$identifier, 'userid'=>$this->userId));
        }

        // Delete all the removed identifiers.
        foreach (array_diff($this->dbIdentifiers, $this->identifiers) as $identifier) {
            $this->delete('identifier', $identifier);
        }

        // Update the dbIdentifiers property to reflect the current database state.
        $this->dbIdentifiers = $this->identifiers;
    }
}
