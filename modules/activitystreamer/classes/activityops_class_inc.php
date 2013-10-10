<?php
/**
 * Methods to track activities in the Chisimba framework
 * into the Chisimba framework
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
 * @package   activityops
 * @author    Wesley Nitsckie
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
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
 * Class to supply an easy API for use from this module or even other modules.
 * @author Wesley Nitsckie
 * @package turnitin
 */
class activityops extends object
{
    /**
     * The system configuration.
     *
     * @access protected
     * @var    object
     */
    protected $objAltConfig;

    /**
     * Property to hold pubsubhubbub object.
     * @access public
     */
    public $objPubSubHubbub;

    /**
     * Constructor
     *
     */
    
    public function init()
    {
        $this->objActDB = $this->getObject('activitydb');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objFeeds = $this->getObject('feeder', 'feed');
        $this->objPubSubHubbub = $this->getObject('pubsubhubbub', 'pubsubhubbub');
    }
    
    /**
     * This method will for the base for all 
     * posts to be listed and distributed
     *
     * @param object $notification
     */
    public function postmade($notification) 
    {
        //add to database
        $this->objActDB->insertPost($notification);

        // Notify the hub of the new entry.
        $id = $this->objAltConfig->getsiteRoot().'index.php?module=activitystreamer';
        $this->objPubSubHubbub->publish($id);
        
        //send to somewhere with XMPP or something
        
        //email it if you like

    }
    
    public function createFeeds($notification)
    {
        $this->objFeeds->setupFeed(false, 'Latest Activity',' some description', $this->uri(array()), 'http://localhost/somefeeds.rss');
        $content = $notification->getNotificationInfo();
        $this->objFeeds->addItem($notification->getNotificationName(), $content['link'], $content['description'], "wwww.somewhere.com", $content['author'] );
        error_log($this->objFeeds->output());
        
        //
    }
    
    
    
    
    
    

}
