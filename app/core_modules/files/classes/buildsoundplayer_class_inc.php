<?php

/**
 * Sound player class definition for Chisimba
 * 
 * This file contains the class used to output sound media by chisimba's files module
 * 
 * PHP versions 4 and 5
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
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
/* ----------- wrapper for ogg vorbis / theora player applet ------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
 * Buildsoundplayer class
 * 
 * Class to play sound files in the user's browser with an applet.
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class buildsoundplayer extends object
{

    /**
    * 
    * @var string $codeBase The codebase for the Java applet
    *             
    */
    var $codeBase;

    /**
    * 
    * @var string $soundFile A fully qualified URL to the sound file
    *             
    */
    var $soundFile;
    
    /**
    * 
    * @var string object $objConfig A string to hold the config object
    *             
    */
    var $objConfig;
 

    /**
    * 
    * Constructor method to instantiate the database and 
    * user objects. 
    * 
    */
    function init() 
    {
        
        //Set up the path for the error file
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Load the sound file from the URL in the querystring 
        $this->loadSound();
        
    }
    
    /**
     * Method to render the audio file
     */
    function show()
    {
        return $this->showFlashVersion();
    }
    
    /**
     * Method to render the audio player in flash
     */
    function showFlashVersion()
    {
        $str = '<object type="application/x-shockwave-flash" data="[-FLASHFILE-]" id="audioplayer1" height="24" width="290">
<param name="movie" value="[-FLASHFILE-]" />
<param name="FlashVars" value="playerID=1&amp;soundFile=[-MP3FILE-]"/>
<param name="quality" value="high" />
<param name="menu" value="false" />

<param name="wmode" value="transparent" />
<embed src="[-FLASHFILE-]" width="290" height="24" flashvars="playerID=1&amp;soundFile=[-MP3FILE-]" quality="high" menu="false" wmode="transparent"></embed>
</object>';

        $str = str_replace('[-FLASHFILE-]', $this->getResourceUri('flashmp3/player.swf'), $str);
        $str = str_replace('[-MP3FILE-]', str_replace('&amp;', '&', $this->soundFile), $str);
        
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('flashmp3/audio-player.js'));
        
        return $str;
    }
    
    /**
    * 
    * Method to render the music player
    * @access Public
    * @return The    player applet code
    *                
    */
    function showJavaVersion()
    {
        if (!$this->soundFile=="") {
            return $this->__startApplet()
              . $this->__getParam("CODE")
              . $this->__getParam("CODEBASE")
              . $this->__getParam("ARCHIVE")
              . $this->__getParam("NAME")
              . $this->__getParam("TYPE")
              . $this->__getParam("SCRIPTABLE")
              . $this->__getParam("SKIN")
              . $this->__getParam("START")
              . $this->__getParam("SONG")
              . $this->__getParam("INIT")
              . $this->__getParam("LOCATION")
              . $this->__getParam("USERAGENT")
              . $this->__endApplet();
        } else {
            return "err";
        }

    }
    
    /**
    * 
    * Method to load the sound from the querystrng or 
    * a form submission.
    * 
    * @return True It always returns true
    *              
    */
    function loadSound()
    {
        $errFile = $this->getResourceUri('soundplayer/sounds/error.ogg');
        //$this->objConfig->getsiteRoot()."modules/soundplayer/resources/sounds/error.ogg";
        
        //Get the sound file from the query string, get error file if none
        $sndFile = $this->getParam('sndfile', $errFile);
        if ($this->__isValidFile($sndFile)) {
            $this->soundFile = $sndFile;
        } else {
            $this->soundFile = $errFile;
        }
        return TRUE;
    }
    
    /**
    * Method to Set the Sound File
    */
    function setSoundFile($file)
    {
        $errFile = $this->objConfig->getsiteRoot()."core_modules/files/resources/soundplayer/sounds/error.ogg";
        
        if ($this->__isValidFile($file)) {
            $this->soundFile = $file;
        }
        
        return TRUE;
    }
    
    
    /*-------------------- PRIVATE METHODS ----------------------------------*/
    
    /**
    * 
    * Method to return the APPLET tag with all its content
    * 
    * @return The     APPLET part of the tag
    * @access Private
    *                 
    */
    function __startApplet()
    {
        return "<applet code = \"javazoom.jlgui.player.amp.PlayerApplet\" 
    	  codebase = \"".$this->getResourceUri('soundplayer/lib/')."\" 
    	  archive = \"jlguiapplet2.3.2.jar,jlgui2.3.2-light.jar,
    	  tritonus_share.jar,basicplayer2.3.jar, mp3spi1.9.2.jar,
    	  jl1.0.jar, vorbisspi1.0.1.jar, jorbis-0.0.13.jar,
    	  jogg-0.0.7.jar, commons-logging-api.jar\" 
    	  width = \"485\" height = \"348\" name = \"player\">";
    }
    
    /**
    * 
    * Method to return the /APPLET closing tag
    * 
    * @return The     /APPLET part of the tag
    * @access Private
    *                 
    */
    function __endApplet()
    {
        return "</applet>";
    }
    
    /**
    * 
    * Method to set one of the APPLET parameters
    * 
    * @return The     <PARAM tag for the parameter
    * @access Private
    *                 
    */
    function __getParam($paramName)
    {
        switch ($paramName) {
            case NULL:
                return NULL;
                break;
            case "CODE":
                return "<param name = \"CODE\" "
                  . "value = \"javazoom.jlgui.player.amp.PlayerApplet\" />\n";
                break;
            case "CODEBASE":
                return "<param name = \"CODEBASE\" "
                  . "value = \"".$this->getResourceUri('soundplayer/lib/')."\" />\n";
                break;
            case "ARCHIVE":
                return "<param name = \"ARCHIVE\" "
                  . "value = \"jlguiapplet2.3.2.jar, "
	              . "jlgui2.3.2-light.jar, tritonus_share.jar, basicplayer2.3.jar, "
	              . "mp3spi1.9.2.jar, jl1.0.jar,vorbisspi1.0.1.jar, jorbis-0.0.13.jar, "
	              . "jogg-0.0.7.jar, commons-logging-api.jar\" />\n";
                break;
            case "NAME":
                return "<param name = \"NAME\" value = \"player\" />\n";
                break;
            case "TYPE":
                return "<param name=\"type\" "
                  . "value=\"application/x-java-applet;version=1.4\" />";
                break;
            case "SCRIPTABLE":
                return "<param name=\"scriptable\" value=\"true\" />";
                break;
            case "SKIN":
                $defaultSkin = $this->getResourceUri('soundplayer/lib/skins/blizzard2.wsz');
                $skin = $this->getParam('skin', $defaultSkin);
                return "<param name = \"skin\" value =\"" . $skin . "\" />\n";
                break;
            case "START":
                $start = $this->getParam('start', 'no');
                return "<param name = \"start\" value =\"" . $start . "\" />\n";
                break;
            case "SONG":
                return "<param name = \"song\" value =\"" . $this->soundFile . "\" />\n";
                break;
            case "INIT":
                $initFile = "core_modules/files/resources/soundplayer/jlgui.ini";
                return "<param name = \"init\" value =\"" . $initFile . "\" />\n";
                break;
            case "LOCATION":
                return "<param name = \"location\" value =\"url\" />\n";
                break;
            case "USERAGENT":
                return "<param name = \"useragent\" value =\"winampMPEG/2.7\" />\n";
                break;
            default:
                return NULL;
                break;
        }
    }
    
    
    /**
    * 
    * Method to validate the file
    * 
    * @param  string     $theFile The file to be evaluated
    * @return True|False depending on whether the file is valid or not
    * @access Private   
    *                    
    * @todo   -c Implement .make it actually work. Currently it just returns true.
    *         
    */
    function __isValidFile($theFile)
    {
        //Reverse any conversion of htmlentities
        $theFile = $this->__unhtmlentities($theFile);
        if ($this->__isUrl($theFile)) {
            return TRUE;
        } else {
            return TRUE;
        }
        
    }
    
    
    /**
    * 
    * Method to test if the file is a valid URL
    * 
    * @param  string     $theFile The file to be evaluated
    * @return True|False depending on whether the file is a valid Url or not
    * @access Private   
    *                    
    */
    function __isUrl($url) {
        if (!preg_match('#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to reverse htmlentities for validating URL
    * 
    * @param  string $str The string to reverse htmlentities for
    * @return string The reversed string
    *                
    */
    function __unhtmlentities($str)
    {
    	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
    	$trans_tbl = array_flip ($trans_tbl);
    	return strtr ($str, $trans_tbl);
    }
} #end of class

?>