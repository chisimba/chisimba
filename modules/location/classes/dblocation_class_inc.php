<?php

/**
 * Location IM dbtable derived class
 * 
 * Class to interact with the database for the location module
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
 * @package   location
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dblocation_class_inc.php 11975 2008-12-29 22:10:39Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dblocation extends dbTable 
{
    protected $objUser;
    protected $userId;

    private $id;
    private $longitude;
    private $latitude;
    private $name;
    private $fireEagleToken;
    private $fireEagleSecret;
    private $twitter;

    /**
     * Constructor
     *
     */
    public function init()
    {
        parent::init('tbl_location');

        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();

        if ($this->userId) {
            $row = $this->getRow('userid', $this->userId);
            $this->populate($row);
        }
    }

    private function populate($row) {
        if ($row && is_array($row) && count($row)) {
            $this->id = $row['id'];
            $this->longitude = $row['longitude'];
            $this->latitude = $row['latitude'];
            $this->name = $row['name'];
            $this->fireEagleToken = $row['fireeagle_token'];
            $this->fireEagleSecret = $row['fireeagle_secret'];
            $this->twitter = (bool) $row['twitter'];
        }
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getFireEagleToken()
    {
        return $this->fireEagleToken;
    }

    public function setFireEagleToken($fireEagleToken)
    {
        $this->fireEagleToken = $fireEagleToken;
    }

    public function getFireEagleSecret()
    {
        return $this->fireEagleSecret;
    }

    public function setFireEagleSecret($fireEagleSecret)
    {
        $this->fireEagleSecret = $fireEagleSecret;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    public function loadByFireEagleToken($token)
    {
        $row = $this->getRow('fireeagle_token', $token);
        $this->populate($row);
    }

    public function put()
    {
        $row = array();
        $row['userid'] = $this->userId;
        $row['longitude'] = $this->longitude;
        $row['latitude'] = $this->latitude;
        $row['name'] = $this->name;
        $row['fireeagle_token'] = $this->fireEagleToken;
        $row['fireeagle_secret'] = $this->fireEagleSecret;
        $row['twitter'] = (int) $this->twitter;
        if ($this->id) {
            $this->update('id', $this->id, $row);
        } else {
            $this->id = $this->insert($row);
        }
    }
}

?>
