<?php
/**
 * video_class_inc.php
 *
 * This is a class to create and manipulate the HTML5 Video tag for displaying video files in browser
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
 * @package   html5elements
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// Include the HTML base class
//require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
//require_once("ifhtml_class_inc.php");

/**
 * Video tag class.
 *
 *
 * @category  Chisimba
 * @package   html5elements
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 *
 * @example
 * $objVideo = $this->getObject('video', 'html5elements');
 * $objVideo->setVideo(198, 192, 'http://173.203.201.87:8000/theora.ogg', 'ogg', TRUE, FALSE, FALSE); // for streaming video from a URL
 * 
 * @example 
 * $objVideo = $this->getObject('video', 'html5elements');
 * $objVideo->setVideo(198, 192, 'theora.ogg', 'ogg', TRUE, TRUE, FALSE); // for a file
 */
class video extends object
{
    /**
     * @var integer $width integer object property for defining the height of the video
     *
     * @access public
     */
    public $width;
    
    /**
     * @var integer $height integer object property for defining the height of the video
     *
     * @access public
     */
    public $height;
    
    /**
     * @var Boolean $controls Turn on controls or not
     *
     * @access public
     */
    public $controls;
    
    /**
     * @var string $preload String preload the video on page load or not
     *
     * @access public
     */
    public $preload = "none";
    
    /**
     * @var string $autoplay Start playing the video immediately or not
     *
     * @access public
     */
    public $autoplay = "none";
    
    /**
     * @var string $src String the video source (file or URL)
     *
     * @access public
     */
    public $src;
    
    /**
     * @var string $videoId ID of the video object
     *
     * @access public
     */
    public $videoId = "movie";
    
    /**
     * @var string $oggtype Codec and type definition for OGG Theora
     *
     * @access private
     */
    private $oggType = 'video/ogg; codecs="theora, vorbis"';
    
    /**
     * @var string $mp4type Codec and type definition for MP4
     *
     * @access private
     */
    private $mp4Type = 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"';
    
    /**
     * @var string $h264Type Codec and type definition for h.264 video
     *
     * @access private
     */
    private $h264Type = 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"';
    
    /**
     * @var string $ieCSS String Property to hold IE CSS fixes via JS
     *
     * @access private
     */
    private $ieCss;
    
    /**
     * @var string $type Property to define final type of video object
     *
     * @access private
     */
    private $type;
    
    /**
     * Standard Constructor
     *
     * @access public
     * @return void
     * @params void
     */
    public function init() {
    
    }
    
    /**
     * setVideo method
     *
     * Method to all in one set up a video object to be played in the browser
     *
     * @access public
     * @return void
     * @params integer $height Height of the video
     * @params integer $width Width of the video
     * @params string $src Source URL or file path of video
     * @params string $format The format of the video (ogg, mp4 or h264)
     * @params boolean $controls whether to show video controls like play, pause etc
     * @params boolean $preload Start preloading video on page load or not
     * @params boolean $autoplay Start playing the video as soon as possible 
     */
    public function setVideo($height, $width, $src, $format = 'ogg', $controls = TRUE, $preload = TRUE, $autoplay = FALSE ) {
        $this->_addCSS();
        $this->_addFlowplayer();
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setSrc($src);
        $this->setType($format);
        $this->setControls($controls);
        $this->setPreload($preload);
        $this->setAutoplay($autoplay);
    }
    
    /**
     * Method to set the height of the video
     *
     * This method can be used to override the default, or to set the video height statically
     *
     * @params integer height Video height in px
     * @return void
     * @access public
     */
    public function setHeight($height) {
        $this->height = $height;
    }
    
    /**
     * Method to set the width of the video
     *
     * This method can be used to override the default, or to set the video width statically
     *
     * @params integer width Video width in px
     * @return void
     * @access public
     */
    public function setWidth($width) {
        $this->width = $width;
    }
    
    /**
     * Method to set the source of the video
     *
     * This method can be used to override the default, or to set the video source statically
     *
     * @params string src Video source (either URL or file path)
     * @return void
     * @access public
     */
    public function setSrc($src) {
        $this->src = $src;
    }
    
    /**
     * Method to set the controls of the video
     *
     * This method can be used to override the default, or to set the video controls statically
     *
     * @params string controls Video controls control. Show them or not
     * @return void
     * @access public
     */
    public function setControls($controls) {
        if($controls == TRUE) {
            $this->controls = "controls";
        }
    }
    
    /**
     * Method to set the id of the video
     *
     * This method can be used to override the default, or to set the video id statically
     *
     * @params string id Video id
     * @return void
     * @access public
     */
    public function setId($id) {
        $this->videoId = $id;
    }
    
    /**
     * Method to preload the video
     *
     * This method can be used to override the default, or to set the video preload status statically
     *
     * @params boolean preload Video preload true or false
     * @return void
     * @access public
     */
    public function setPreload($preload) {
        if($preload == TRUE) {
            $this->preload = "preload";
        }
        else {
            $this->preload = "none";
        }
    }
    
    /**
     * Method to autoplay the video
     *
     * This method can be used to override the default, or to set the video autoplay status statically
     *
     * @params boolean autoplay Video autoplay true or false
     * @return void
     * @access public
     */
    public function setAutoplay($autoplay) {
        if($autoplay == TRUE) {
            $this->autoplay = "autoplay";
        }
        else {
            $this->autoplay = "none";
        }
    }
    
    /**
     * Method to set the type the video
     *
     * This method can be used to override the default, or to set the video type status statically
     * The type is the format of the video and the class will set up a specific type with its associated codecs
     *
     * @params string $format the video format of either ogg, mp4 or h264
     * @return void
     * @access public
     */
    public function setType($format) {
        switch($format) {
            case 'ogg':
                $this->type = $this->oggType;
                break;
            case 'mp4':
                $this->type = $this->mp4Type;
                break;
            case 'h264':
                $this->type = $this->h264Type;
                break;
        }
    }
    
    /**
     * Show the video on screen
     *
     * Method to finally output to browser via the output buffer
     *
     * @params void
     * @return string $vid Video markup
     * @access public
     */
    public function show() {
        $vid = NULL;
        $vid .= '<video id="'.$this->videoId.'" width="'.$this->width.'" height="'.$this->height.'" '.$this->preload.' '.$this->controls.'>';
        $vid .= '<source src="'.$this->src.'" type=\''.$this->type.'\'>';
        $vid .= '</video>';
        
        return $vid;
    }
    
    /**
     * Method to add extra CSS and javascript to fix IE based browsers
     *
     * @params void
     * @return void
     * @access private
     */
    private function _addCSS() {
        $this->css = '<!--[if IE]>'.$this->getJavascriptfile('html5.js', 'html5elements').'<![endif]-->';
        $this->appendArrayVar('headerParams', $this->css);
    }
    
    /**
     * Method to add flowplayer as an alternative to browsers that do not support html5 video
     *
     * @params void
     * @return void
     * @access private
     */
    private function _addFlowplayer() {
        $this->getJavascriptfile('flowplayer-3.1.4.min.js', 'html5elements');
        $this->getJavascriptfile('html5-video.js', 'html5elements');
    }

}
