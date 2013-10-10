<?php
/**
 *
 * Database access for statusbar
 *
 * Database access for statusbar. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @package   statusbar
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Database access for statusbar
*
* Database access for statusbar. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   statusbar
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbstatusbar_bridging extends dbtable
{
    /**
    * 
    * @var string $table  String object property for holding the table object
    * @access public;
    */
    public $table;    

    /**
     * 
     * Variable to hold the userId
     * 
     * @access public
     * @var string
     */
    public $userId;

    /**
    *
    * Intialiser for the statusbar database connector
    * @access public
    * @return VOID
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        //Set the parent table to our demo table
        parent::init('tbl_statusbar_bridging');
        $this->table = 'tbl_statusbar_bridging';
    }
    
    /**
     *
     * Method to get context calendar alerts
     * 
     * @access public
     * @param string $contextCode The context code
     * @param string $alert The alert date
     * @return $array The array of context calendar alerts
     */
    public function getContextCalendarAlerts($alert, $contextCode)
    {        
        $events = $this->getCalendarEvents($alert, $contextCode);
        $entries = $this->getCalendarBridgingEntries();
        
        $this->createCalendarEntries($events, $entries);
        
        $alerts = $this->getCalendarAlerts($alert, $contextCode);

        return $alerts;
        
    }
    
    /**
     *
     * Method to get bridging entries
     * 
     * @access public
     * @return array $entries The bridging table entries
     */
    public function getCalendarBridgingEntries()
    {
        $sql = "SELECT * FROM tbl_statusbar_bridging";
        $sql .= " WHERE `calendar_id` IS NOT NULL";
        $sql .= " AND `user_id` = '$this->userId'";
        
        $entries = $this->getArray($sql);
        
        return $entries;
    }
    
    /**
     *
     * Method to get calendar events
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @param string $contextCode The context code
     * @return array $events The context calendar events
     */
    public function getCalendarEvents($alert, $contextCode)
    {
        $date = date('Y-m-d', $alert);
        
        $sql = "SELECT * FROM tbl_calendar";
        $sql .= " WHERE `context` = '$contextCode'";
        $sql .= " AND `eventdate` <= '$date'";

        $events = $this->getArray($sql);

        return $events;
    }
    
    /**
     *
     * Method to create bridging entries
     * 
     * @access public
     * @param array $events The calendar events array
     * @param array $entries The briding entries 
     * @return VOID
     */
    public function createCalendarEntries($events, $entries)
    {
        if (!empty($events))
        {
            if (empty($entries))
            {
                foreach ($events as $event)
                {
                    $data['calendar_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
            else
            {
                foreach ($events as $key => $event)
                {
                    foreach ($entries as $entry)
                    {
                        if ($event['id'] == $entry['calendar_id'])
                        {
                            unset($events[$key]);
                        }
                    }
                }

                foreach ($events as $event)
                {
                    $data['calendar_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
        }
    }
    
    /**
     *
     * Method to get context alerts
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @param string $contextCode The context code
     * @return array $alerts The context calendar alerts
     */
    public function getCalendarAlerts($alert, $contextCode)
    {
        $date = date('Y-m-d', $alert);
        
        $sql = "SELECT *, c.id AS c_id, b.id AS b_id, c.alert_state AS c_alert_state, b.alert_state AS b_alert_state FROM tbl_calendar AS c";
        $sql .= " LEFT JOIN tbl_statusbar_bridging AS b";
        $sql .= " ON c.id = b.calendar_id";
        $sql .= " WHERE c.context = '$contextCode'";
        $sql .= " AND c.eventdate <= '$date'";
        $sql .= " AND b.user_id = '$this->userId'";
        $sql .= " AND b.alert_state = '0'";
        $sql .= " ORDER BY c.eventdate ASC, c.timefrom ASC";
        
        $alerts = $this->getArray($sql);
        
        return $alerts;
    }
    
    /**
     *
     * Method to update the bridging alert
     * 
     * @access public
     * @param string $id The id of the alert to update
     * @return VOID 
     */
    public function updateAlert($id)
    {
        return $this->update('id', $id, array('alert_state' => 1));
    }

    /**
     *
     * Method to get context content alerts
     * 
     * @access public
     * @param string $contextCode The context code
     * @param string $alert The alert date
     * @return $array The array of context calendar alerts
     */
    public function getContextContentAlerts($alert, $contextCode)
    {        
        $events = $this->getContentEvents($alert, $contextCode);
        $entries = $this->getContentBridgingEntries();

        $this->createContentEntries($events, $entries);
        
        $alerts = $this->getContentAlerts($alert, $contextCode);

        return $alerts;
        
    }
    
    /**
     *
     * Method to get bridging entries
     * 
     * @access public
     * @return array $entries The bridging table entries
     */
    public function getContentBridgingEntries()
    {
        $sql = "SELECT * FROM tbl_statusbar_bridging";
        $sql .= " WHERE `activity_id` IS NOT NULL";
        $sql .= " AND `user_id` = '$this->userId'";
        
        $entries = $this->getArray($sql);
        
        return $entries;
    }
    
    /**
     *
     * Method to get content events
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @param string $contextCode The context code
     * @return array $events The context content events
     */
    public function getContentEvents($alert, $contextCode)
    {
        $date = date('Y-m-d H:i:s', $alert);
        
        $sql = "SELECT * FROM tbl_activity";
        $sql .= " WHERE `contextcode` = '$contextCode'";
        $sql .= " AND `module` = 'contextcontent'";
        $sql .= " AND `createdon` <= '$date'";

        $events = $this->getArray($sql);

        return $events;
    }
    
    /**
     *
     * Method to create bridging entries
     * 
     * @access public
     * @param array $events The calendar events array
     * @param array $entries The briding entries 
     * @return VOID
     */
    public function createContentEntries($events, $entries)
    {
        if (!empty($events))
        {
            if (empty($entries))
            {
                foreach ($events as $event)
                {
                    $data['activity_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
            else
            {
                foreach ($events as $key => $event)
                {
                    foreach ($entries as $entry)
                    {
                        if ($event['id'] == $entry['activity_id'])
                        {
                            unset($events[$key]);
                        }
                    }
                }

                foreach ($events as $event)
                {
                    $data['activity_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
        }
    }
    
    /**
     *
     * Method to get context alerts
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @param string $contextCode The context code
     * @return array $alerts The context calendar alerts
     */
    public function getContentAlerts($alert, $contextCode)
    {
        $date = date('Y-m-d H:i:s', $alert);
        
        $sql = "SELECT *, a.id AS a_id, b.id AS b_id, b.alert_state AS b_alert_state FROM tbl_activity AS a";
        $sql .= " LEFT JOIN tbl_statusbar_bridging AS b";
        $sql .= " ON a.id = b.activity_id";
        $sql .= " WHERE a.contextcode = '$contextCode'";
        $sql .= " AND a.createdon <= '$date'";
        $sql .= " AND b.user_id = '$this->userId'";
        $sql .= " AND b.alert_state = '0'";
        $sql .= " ORDER BY a.createdon ASC";
        
        $alerts = $this->getArray($sql);
        
        return $alerts;
    }

    /**
     *
     * Method to get photo gallery alerts
     * 
     * @access public
     * @param string $alert The alert date
     * @return $array The array of context calendar alerts
     */
    public function getPhotoGalleryAlerts($alert)
    {        
        $events = $this->getGalleryEvents($alert);
        $entries = $this->getGalleryBridgingEntries();

        $this->createGalleryEntries($events, $entries);
        
        $alerts = $this->getGalleryAlerts($alert);

        return $alerts;
        
    }
    
    /**
     *
     * Method to get bridging entries
     * 
     * @access public
     * @return array $entries The bridging table entries
     */
    public function getGalleryBridgingEntries()
    {
        $sql = "SELECT * FROM tbl_statusbar_bridging";
        $sql .= " WHERE `photo_id` IS NOT NULL";
        $sql .= " AND `user_id` = '$this->userId'";
        
        $entries = $this->getArray($sql);
        
        return $entries;
    }
    
    /**
     *
     * Method to get gallery events
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @return array $events The context content events
     */
    public function getGalleryEvents($alert)
    {
        $date = date('Y-m-d H:i:s', $alert);
        
        $sql = "SELECT *, i.id AS i_id, a.id AS a_id, b.id AS b_id FROM tbl_photogallery_images AS i";
        $sql .= " LEFT JOIN tbl_photogallery_albums AS a ON a.id = i.album_id";
        $sql .= " LEFT JOIN tbl_buddies AS b ON a.user_id = b.buddyid";
        $sql .= " WHERE a.is_shared = '1'";
        $sql .= " AND b.userid = '$this->userId'";
        $sql .= " AND i.date_created <= '$date'";

        $events = $this->getArray($sql);

        return $events;
    }
    
    /**
     *
     * Method to create bridging entries
     * 
     * @access public
     * @param array $events The calendar events array
     * @param array $entries The briding entries 
     * @return VOID
     */
    public function createGalleryEntries($events, $entries)
    {
        if (!empty($events))
        {
            if (empty($entries))
            {
                foreach ($events as $event)
                {
                    $data['photo_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
            else
            {
                foreach ($events as $key => $event)
                {
                    foreach ($entries as $entry)
                    {
                        if ($event['id'] == $entry['photo_id'])
                        {
                            unset($events[$key]);
                        }
                    }
                }

                foreach ($events as $event)
                {
                    $data['photo_id'] = $event['id'];
                    $data['user_id'] = $this->userId;
                    $this->insert($data);
                }
            }
        }
    }
    
    /**
     *
     * Method to get context alerts
     * 
     * @access public
     * @param timestamp $alert The alert timestamp
     * @param string $contextCode The context code
     * @return array $alerts The context calendar alerts
     */
    public function getGaleryAlerts($alert)
    {
        $date = date('Y-m-d H:i:s', $alert);
        
        $sql = "SELECT *, a.id AS a_id, b.id AS b_id, b.alert_state AS b_alert_state FROM tbl_activity AS a";
        $sql .= " LEFT JOIN tbl_statusbar_bridging AS b";
        $sql .= " ON a.id = b.activity_id";
        $sql .= " WHERE a.contextcode = '$contextCode'";
        $sql .= " AND a.createdon <= '$date'";
        $sql .= " AND b.user_id = '$this->userId'";
        $sql .= " AND b.alert_state = '0'";
        $sql .= " ORDER BY a.createdon ASC";
        
        $alerts = $this->getArray($sql);
        
        return $alerts;
    }
}
?>