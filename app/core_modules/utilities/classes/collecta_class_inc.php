<?php
/**
 *
 * Collecta class
 *
 * Collecta returns realtime search results from multiple sources
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
 * @package   collecta
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
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
* Collecta API Class
*
* http://api.collecta.com/search
* The following parameters are supported:
*
* q - Required. The query string in the Collecta query language
* rpp - Optional. This controls the maximum number of results per page in the response. Default is 15.
* page - Optional. This controls results paging. When no page is specified, up to rpp of the most recent results are returned. A page value of 1 would return the next set results.
* since_id - Optional. When specified, this integer parameter only returns payloads that are more recent than the one with id element equal to this parameter's value.
* api_key - Required. ALL requests to the HTTP API require an API key, please request a key.
* format - Optional. Change format from atom to json. The default format is atom and when format=json is specified json is returned.
* callback - Optional. Used to support crossdomain jsonp callback. This is the name of the javascript function.
*
* @package utilities
* @author M. Dave Auayan
* @author Paul Scott <pscott@uwc.ac.za>
*/
 
class collecta extends object
{
    /**
     * Additional curl headers
     * 
     * @var $curl_headers
     */
    public $curl_headers;
    
    /**
     * Additional curl options
     * 
     * @var $curl_options
     */
    public $curl_options;
    
    /**
     * Standard construct based function
     * 
     */
    public function init() {
        $key = '066a0e2ff425a14416cdbe138a964349';
        $this->key = ($key) ? $key : null;
        $this->base_url = 'http://api.collecta.com/search';
 
        //custom headers and options that are passed to the curl method
        $this->curl_headers = array();
        $this->curl_options = array();
        
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
    }
  
    /**
     * Search function
     *
     * @param string $q query string
     * @param array $options additional options
     */ 
    public function search( $q, $options = array() ){
        if(isset($q)) {
          $options['q'] = $q;
          $results = $this->request($options);
        } 
        else {
          $results = false;
        }
        return $results;
    }
  
    /**
     * Request method
     * Fires off the request to the API
     *
     * @param array $params
     * @access private
     */
    private function request( $params = array() ) {
        $params['api_key'] = $this->key;
        $params['q'] = isset($params['q']) ? $params['q'] : null;
        $params['rpp'] = isset($params['rpp']) ? $params['rpp'] : null;
        $params['page'] = isset($params['page']) ? $params['page'] : null;
        $params['since_id'] = isset($params['since_id']) ? $params['since_id'] : null;
        $params['format'] = isset($params['format']) ? $params['format'] : null;
        $params['callback'] = isset($params['callback']) ? $params['callback'] : null;
        $url = $this->build_url($params);
        if($url) {
            $response = $this->curl($url, $this->curl_headers, $this->curl_options);
            switch($params['format']){
                case 'json':
                    $results = $response;
                    break;
                case 'atom':
                default:
                    $results = simplexml_load_string($response);
            }
        }  
        else {
            $results = false;
        }
        return $results;
    }
  
    /**
     * Build the URL
     *
     * @param array $param - array of parameters to pass
     * @access private
     */
    private function build_url( $params ) {
        if( isset($params['q']) && isset($params['api_key']) ) {
            //show specific categories
            if(isset($params['category'])) {
                if(is_array($params['category'])) {
                    foreach($params['category'] as $category)
                        $params['q'] .= ' category:'.$category.' OR';
                        $params['q'] = rtrim($params['q'], ' OR');
                } 
                else {
                    $params['q'] .= ' category:'.$params['category'];
                }
                unset($params['category']);
             }
             //exclude categories
             if(isset($params['exclude'])) {
                 if(is_array($params['exclude'])) {
                     foreach($params['exclude'] as $category)
                         $params['q'] .= ' -category:'.$category;
                 }   
                 else {
                     $params['q'] .= ' -category:'.$params['exclude'];
                 }
                 unset($params['exclude']);
             }
             $url = $this->base_url .'?'. http_build_query($params);
        }
        else {
            $url = false;
        }
        return $url;
    }
    
    /**
     * Curls the lot
     * 
     * @param string $url url
     * @access public
     */
    public function curl($url) {
        return $this->objCurl->exec($url);
    }
}
?>
