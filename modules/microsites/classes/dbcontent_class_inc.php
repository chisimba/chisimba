<?php
/**
 *
 * The database access class for the tbl_microsites_content table
 * 
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
 * @package   microsites
 * @author    Wesley Nitsckie
 * @copyright 2007 AVOIR
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
*
* An blog snippet provider for oembed
*
* An blog snippet provider for oembed. oEmbed is an open format designed to allow
* embedding content from a website into another page. This content is of the
* types photo, video, link or rich. An oEmbed exchange occurs between a
* consumer and a provider. A consumer wishes to show an embedded representation
* of a third-party resource on their own website, such as a photo or an
* embedded video. A provider implements the oEmbed API to allow consumers to
* fetch that representation. This is a provider for blog posts that are
* created using the blog module.
*
* @author Derek Keats
* @package oembed
*
*/

class dbcontent extends dbTable{
    /**
    *
    * Constructor for the imageprovider class
    *
    * @access public
    * @return VOID
    *
    */
    public function init(){
        parent::init('tbl_microsites_content');
    }
    
    /**
    * Method for adding a new page
    * @param array $params   
    * @return pageid
    */
    public function add($params){
        $fields = array();
        $fields['site_id'] = $params['site_id'];
        $fields['content_title'] = $params['content_title'];
        $fields['content'] = $params['content'];
        $fields['hits'] = 0;

        return $this->insert($fields);        
    
    }
    
    /**
    * Method for updating the page content
    * @param string $pageId 
    * @param string $content
    * @return timestamp
    */
    public function savePage($pageId, $content){
        return $this->update('id', $pageId, array(
                            'content' => $content,
                            'modified' =>  date('Y-m-d H:i:s')
                            ));
    }
    
    /**
    * Method for getting the page content
    * @param string $pageId     
    * @return array
    */
    public function getPage($pageId){
        return $this->getRow('id', $pageId);
    }
    
    
    /**
    * Method for removing the page content
    * @param string $pageId     
    * @return boolean
    */
    public function delete($id){
    
    }
    
    /**
    * Method for getting all the pages for a site
    * @param string $siteId   
    * @return array
    */
    public function getSiteContent($siteId){      
        return $this->getAll("WHERE site_id='$siteId'");
    }
	
}
