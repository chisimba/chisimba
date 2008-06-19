<?php
/**
* Class to parse a string (e.g. page content) that contains a presentation
* item from the a webpresent module, whether local, URL or remote API
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
* @package   filters
* @author    David Wafula
* @copyright 2008 David Wafula
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @link      http://avoir.uwc.ac.za
*/


    class parse4realtime extends object
    {
        /**
        *
        * String to hold an error message
        * @accesss private
        */
        private $errorMessage;
        public $objConfig;
        public $objLanguage;
        public $objExpar;
        public $id;
        public $url;

    /**
     *
     * Constructor for the wikipedia parser
     *
     * @return void
     * @access public
     *
     */
        function init()
        {
            // Get an instance of the language object
            $this->objLanguage = $this->getObject('language', 'language');
            // Get an instance of the params extractor
            $this->objExpar = $this->getObject("extractparams", "utilities");
            // Load the XML_RPC PEAR Class
            require_once($this->getPearResource('XML/RPC/Server.php'));
            // $this->objConfig = $this->getObject('altconfig', 'config');
        }
    /**
     * This function generates a random string. This is used as id for the java slides server as well as
     * the client (applet)
     * @param <type> $length
     * @return <type>
     */ 
        public function randomString($length)
        {
            // Generate random 32 charecter string
            $string = md5(time());

            // Position Limiting
            $highest_startpoint = 32-$length;

            // Take a random starting point in the randomly
            // Generated String, not going any higher then $highest_startpoint
            $randomString = substr($string,rand(0,$highest_startpoint),$length);

            return $randomString;

        }
        
        /**
         * This starts the slide server on the remote server. Needed for the applet
         * to run
         */
        public function startSlideServer($siteRoot,$slideServerId){
            // create a new cURL resource
            $ch = curl_init();

            $url= $siteRoot."/index.php?module=webpresent&action=runslideserver&slideServerId=".$slideServerId;
            
            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            // grab URL and pass it to the browser
            curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);
        }
      /**
        *
        * Method to parse the string
        * @param  string $str The string to parse
        * @return string The parsed string
        *
        */
        public function parse($txt)
        {
            // Match all [FILEPREVIEW /] tags
            preg_match_all('%\[REALTIME.*?/\]%six', $txt, $result, PREG_PATTERN_ORDER);
           
            $result = $result[0];
        
            // Combine duplicates
            $result = array_unique($result);
        
            // If there are any matches
            if (count($result) > 0) {
            
                // Load Preview Class
                $realtime = $this->getObject('realtimestarter', 'realtime');
                // Go through each result
                foreach ($result as $str)
                {
                    // Fix required - Replace &quot; with "
                    $strReplace = str_replace ('&quot;', '"', $str);
                
                    //($id,$agenda,$resourcesPath,$appletCodeBase,$slidesDir,$username,$fullnames,$userLevel,$runSlideServerCommand,$slideServerId)
                    
                    // Match ids
                    preg_match_all('/id\ *?=\ *?"(?P<id>.*?)"/six', $strReplace, $resultId, PREG_PATTERN_ORDER);
                    $sessionId = $resultId['id'];
                    
                    //the agenda:
                    preg_match_all('/agenda\ *?=\ *?"(?P<agenda>.*?)"/six', $strReplace, $agendaId, PREG_PATTERN_ORDER);
                    $agenda = $agendaId['agenda'];                    
                    
                    //resources path
                    preg_match_all('/resourcesPath\ *?=\ *?"(?P<resourcesPath>.*?)"/six', $strReplace, $resourcesId, PREG_PATTERN_ORDER);
                    $resourcesPath = $resourcesId['resourcesPath'];
                    
                    preg_match_all('/appletCodeBase\ *?=\ *?"(?P<appletCodeBase>.*?)"/six', $strReplace, $appletCodeId, PREG_PATTERN_ORDER);
                    $appletCodeBase = $appletCodeId['appletCodeBase'];
                    
                    preg_match_all('/slidesDir\ *?=\ *?"(?P<slidesDir>.*?)"/six', $strReplace, $slidesDirId, PREG_PATTERN_ORDER);
                    $slidesDir = $slidesDirId['slidesDir'];
                    
                    preg_match_all('/username\ *?=\ *?"(?P<username>.*?)"/six', $strReplace, $usernameId, PREG_PATTERN_ORDER);
                    $username = $usernameId['username'];
                    
                    preg_match_all('/fullnames\ *?=\ *?"(?P<fullnames>.*?)"/six', $strReplace, $fullnamesId, PREG_PATTERN_ORDER);
                    $fullnames = $fullnamesId['fullnames'];
                  
                    preg_match_all('/userlevel\ *?=\ *?"(?P<userlevel>.*?)"/six', $strReplace, $userlevelId, PREG_PATTERN_ORDER);
                    $userlevel = $userlevelId['userlevel'];
                  
                    preg_match_all('/slideServerId\ *?=\ *?"(?P<slideServerId>.*?)"/six', $strReplace, $xslideServerId, PREG_PATTERN_ORDER);
                    $slideServerId = $xslideServerId['slideServerId'];
                  
                    preg_match_all('/siteRoot\ *?=\ *?"(?P<siteRoot>.*?)"/six', $strReplace, $siteRootId, PREG_PATTERN_ORDER);
                    $siteRoot = $siteRootId['siteRoot'];
      
                    
                    //var_dump($siteRoot);
                    //exit();
                    
                    $this->startSlideServer($siteRoot[0],$slideServerId[0]);
                    
                    $preview = $realtime->generateURL($sessionId[0],$agenda[0],$resourcesPath[0],$appletCodeBase[0],$slidesDir[0],$username[0],$fullnames[0],$userLevel[0],$slideServerId[0]);
                    // Replace filter code with preview
                    $txt = str_replace($str, $preview, $txt);

                } // End foreach
            } // End if count
       
            // Return rendered text
            return $txt;
        }
    }
?>