<?php
/**
 * Geo Flickr block block
 *
 * Class to show flickr images (CC licensed) from a particular geo location
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
 * @version    $Id $
 * @package    flickrshow
 * @subpackage blocks
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        www.flickr.com
 * @see        http://developer.yahoo.net/blog/archives/2010/04/building_flickr_urls_from_yql_flickrphotossearch_results.html
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
 * A block to return flickr images from a location
 *
 * @category  Chisimba
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @version   0.1
 * @copyright 2006-2007 AVOIR
 *
 */
class block_flickrbylocation extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    
    /**
     * last ten posts box
     *
     * @var    object
     * @access public
     */
    public $display;
    
    /**
     * Description for public
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUser = $this->getObject('user', 'security');
        $this->title = $this->objLanguage->languageText("mod_flickrshow_block_flickrbylocation", "flickrshow");
        $this->expose = TRUE;
    }
    
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return $this->getLastData();
    }

    public function getLastData()
    {
        $location = $this->sysConfig->getValue('block_location', 'flickrshow');
        $yql_url = 'http://query.yahooapis.com/v1/public/yql?';
        $query = 'SELECT * FROM flickr.photos.search WHERE has_geo="true" AND text="'.$location.'"';
        $query_url = $yql_url . 'q=' . urlencode($query) . '&format=xml';

        $photos = simplexml_load_file($query_url);
        $result = $this->build_photos($photos->results->photo);
        
        return $result;         
    }
    
    public function build_photos($photos) {
        $html = NULL; //'<ul>';
        if (count($photos) > 0){
            foreach ($photos as $photo){
                $html .= '<a href="http://www.flickr.com/photos/'.
                         $photo['owner'].'/'.$photo['id'].
                         '"><img src="http://farm'.$photo['farm'].
                         '.static.flickr.com/'.$photo['server'].
                         '/'.$photo['id'].'_'.$photo['secret'].
                         '.jpg" width="150" height="150" alt="'.$photo['title'].
                         '" /></a><br />';
            }
        } else {
            $html .= 'No Photos Found';
        }
    
        return $html;
    }
}
?>
