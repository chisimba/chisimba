<?php
/**
 * Class to insert a pictureshow for a given location in the content. 
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
 * @package   filters
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:  $
 * @link      http://avoir.uwc.ac.za
 * @see
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to insert a picture show for a given location in the content. 
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 */

class parse4pictureshow extends object
{
    /**
     *
     * String to hold an error message
     * @accesss private
     */
    private $errorMessage;

    /**
     * Initialisation method
     *
     * Attaches data to the engine object for access
     *
     * @return void
     * @access public
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }
    
    /**
    * Method to parse the string
    * 
    * @param  string $str The string to parse
    * @return string The parsed string
    */
    public function parse($txt)
    {
        //Note the ? in the regex is important to enable the multiline
        //   feature, else it greedy
        preg_match_all('/(\\[PICTURESHOW:)(.*?)\\]/ism', $txt, $results);
        $counter = 0;
        //var_dump($results);
        foreach ($results[2] as $item) {
            //Parse for the parameters
            $str = trim($results[1][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            $ar = $this->objExpar->getArrayParams($item, ",");
            if (isset($this->objExpar->username)) {
                $username = $this->objExpar->username;
            } else {
                $username = 'admin';
            }
            if (isset($this->objExpar->folder)) {
                $folder = $this->objExpar->folder;
            } else {
                $folder = 'pictures';
            }
            
            $replacement = $this->buildshow($username, $folder); 
            $txt = str_replace($replaceable, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }
    
    /**
     * Method to create the javascript and append it to the headers
     * 
     * This creates and sets up the data and the javascript for use by the filter
     * 
     * @access private
     * @param $username string username that you want to use to create the show
     * @param $folder string foldername where pictures are stored
     */
    private function buildshow($username, $folder) {
        $data = $this->buildDataJs($username, $folder);
        $userid = $this->objUser->getUserId($username);
        if($data === FALSE) {
            return $this->objLanguage->languageText("mod_filters_foldernotfound", "filters");
        }
        $path = $this->objConfig->getcontentPath()."users/$userid/$folder/";
        $dpath = $path."data/data.js";
        file_put_contents($dpath, $data);
        $jsr = $this->getResourceUri('runway/api/runway-api.js', 'filters');
        // temp till we have a function to do it...
        $jsd = $this->getResourceUri('runway/'.$username.$folder.'data.js', 'filters');
        //var_dump($jsd);
        //var_dump($dpath); die();
        $css1 = $this->getResourceUri('styles/styles.css', 'filters');
        $css2 = $this->getResourceUri('styles/themes.css', 'filters');
        $js = 
        '<script type="text/javascript">
        var widget;
        function onLoad() {
            widget = Runway.createOrShowInstaller(
                document.getElementById("the-widget"),
                {
                    onReady: function() {
                        widget.setRecords(records);
                    },
                    onSelect: function(index, id) {
                        var record = records[index];
                        document.getElementById("selected-slide").innerHTML = record.image;
                        
                    },
                    onTitleClick: function(index, id) {
                        var record = records[index];
                        var image = record.image;
                        // alert(image);
                    },
                    onSubtitleClick: function(index, id) {
                    },
                    onZoom: function(index, id) {
                    },
                    onSideSlideMouseOver: function(index, id) {
                    },
                    onSideSlideMouseOut: function(index, id) {
                    }
                }
            );
        }
        </script>';
        $head = '<script type="text/javascript" src="'.$jsr.'"></script>';
        $head .= '<script type="text/javascript" src="'.$dpath.'"></script>';
        $head .= $js;
        $this->appendArrayVar('headerParams', $head);
        $body = ' onload="onLoad();"';
        $this->setVar('bodyParams', $body);
        
        return '<div id="the-widget" style="height: 400px;"></div><span id="selected-slide"></span>';
    }
    
    /**
     * Method to create the javascript and append data
     * 
     * This creates and sets up the data as javascript for use by the filter
     * 
     * @access private
     * @param $username string username that you want to use to create the show
     * @param $folder string foldername where pictures are stored
     */
    private function buildDataJs($username, $folder) {
        $userid = $this->objUser->getUserId($username);
        // go find the image path
        if(file_exists($this->objConfig->getcontentBasePath()."users/$userid/$folder/")) {
            $path = $this->objConfig->getcontentBasePath()."users/$userid/$folder/";
            if(!file_exists($path."data")) {
                mkdir($path."data", 0777);
            }
            $uri = $this->objConfig->getsiteRoot()."usrfiles/users/$userid/$folder/";
            $datafile = "var records = [";
            foreach(glob($path."{*.png,*.PNG,*.jpg,*.JPG,*.gif,*.GIF}", GLOB_BRACE) as $files) {
                $datafile .= '{ image: "'.$uri.basename($files).'", title: "'.basename($files).'", subtitle: "'.$username.'" },';
            }
            $datafile .= '];';
            return $datafile;
        }
        else {
            return FALSE;
        }
    }
}
?>
