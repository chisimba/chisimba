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
 * @package   activitystreamer
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
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
 * Methods to track activities in the Chisimba framework
 * into the Chisimba framework
 * 
 * @author Paul Scott <pscott@uwc.ac.za>
 */
class activitystreamsencoder extends object
{
    /**
     * Property to hold database object
     * @access public
     */
    public $objActDB;

    /**
     * Property to hold pubsubhubbub object.
     * @access public
     */
    public $objPubSubHubbub;
    
    /**
     * Property to hold id object
     * @access private
     */
    private $id = "";
	
	/**
     * Property to hold title object
     * @access private
     */	
    private $title = "";
    
    /**
     * Property to hold link object
     * @access private
     */
    private $link = "";
    
    /**
     * Property to hold description
     * @access public
     */
    public $description;

    /**
     * Property to hold the URL of the PubSubHubbub hub.
     * @access private
     */
    private $hub;
    
    /**
     * Property to hold entries
     * @access private
     */
    private $entries = array();
    
    /**
     * Constructor
     *
     */
    public function init()
    {
        $this->objActDB = $this->getObject('activitydb');
        $this->objPubSubHubbub = $this->getObject('pubsubhubbub', 'pubsubhubbub');
        $this->hub = $this->objPubSubHubbub->getHub();
    }
    
    /**
     * Method to set the stream ID
     *
     * @access public
     * @param string id $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * Method to set the stream title
     *
     * @access public
     * @param string title $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * Method to set the stream description
     *
     * @access public
     * @param string description $desc
     */
    public function setDescription($desc) {
        $this->description = $desc;
    }
    
    /**
     * Method to add an entry to the stream
     *
     * @access public
     * @param object entry $entry
     */
    function addEntry(activitystreamsentry $entry) {
			$this->entries[] = $entry;
    }
	
	/**
     * Method to set the stream to a string
     *
     * @access public
     * @param void
     */	
    function __toString() {
        // Display header
        $string = '';
        $updated_time = date('c',time());
        $string .=  <<<END
<?xml version="1.0" encoding="UTF-8"?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:thr="http://purl.org/syndication/thread/1.0"
  xmlns:activity="http://activitystrea.ms/spec/1.0/"
  xml:lang="en"
   >
   
	<title type="text">{$this->title}</title>
	<updated>{$updated_time}</updated>
	
	<link rel="alternate" type="text/html" href="{$this->link}" />
	<id>{$this->id}</id>
	<link rel="self" type="application/atom+xml" href="{$this->id}" />
	<link rel="hub" href="{$this->hub}" />
   
END;
        if (sizeof($this->entries))
            foreach($this->entries as $entry)
            if ($entry instanceof activitystreamsentry) $string .= (string) $entry;
					
            $string .=  <<<END
</feed>
END;
         return $string;
    }
}
?>
