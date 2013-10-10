<?php
/**
 * This file houses the flickrshow controller class.
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
 * @package   flickrshow
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 15856 2009-12-09 08:52:55Z paulscott $
 * @link      http://avoir.uwc.ac.za
 */

class flickrshow extends controller {
    
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objConfig = $this->getObject('altconfig', 'config');
        
    }
    
    public function requiresLogin() {
        return FALSE;
    }
    
    public function dispatch($action) {
        if ($action == 'getphotoset') {
            
            $apiKey = $this->sysConfig->getValue('api_key', 'flickrshow');
            $id = $this->getParam('id');
            $photoset = $this->getParam('photoset');
            $size = $this->getParam('size');
            $proxy = $this->objConfig->getProxy();
            if ($proxy) {
                $aContext = array(
                    'http' => array(
                        'proxy' => str_replace('http', 'tcp', $proxy), // This needs to be the server and the port of the NTLM Authentication Proxy Server.
                        'request_fulluri' => true,
                    ),
                );
                $cxContext = stream_context_create($aContext);
            } else {
                $cxContext = null;
            }
            // Now all file stream functions can use this context.
            $url = "http://api.flickr.com/services/rest/?method=flickr.photosets.getphotos&photoset_id=$photoset&api_key=$apiKey&format=json&extras=url_o";
            $response = file_get_contents($url, false, $cxContext);
            $setObject = json_decode(substr($response, 14, strlen($response)-15));
            $owner = $setObject->photoset->owner;
            foreach ($setObject->photoset->photo as $photo) {
                $src = ($size == '_o')? $photo->url_o :
                    "http://farm$photo->farm.static.flickr.com/$photo->server/{$photo->id}_{$photo->secret}$size.jpg";
                $url = "http://flickr.com/photos/$owner/$photo->id";
                $array_response['images'][] = array('id'=>$photo->id, 'src'=>$src, 'url'=>$url, 'title'=>$photo->title);
            }

            echo "flickrshowCache[$id] = ".str_replace("\\", "", json_encode($array_response)).";";
            
        } else {
            return 'no_access_tpl.php';
        }
    }
}