<?php

/**
 * Class to Generate the Code to <embed> a Media Object in a Webpage
 *
 * At present, it caters for various types of media such as:
 * - audio
 * - video
 * - flash
 * - images
 * - 3d obj
 * - vrml
 *
 * PHP version 3
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */

/**
 * Class to Generate the Code to <embed> a Media Object in a Webpage
 *
 * At present, it caters for various types of media such as:
 * - audio
 * - video
 * - flash
 * - images
 * - 3d obj
 * - vrml
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see
 */
class fileembed extends object {
    /**
    *
    * @var object $objLanguage The language object
    *
    */
    private $objLanguage;

    /**
     * Constructor
     */
    function init() {
        $this->loadClass('link', 'htmlelements');
        $this->objCleanUrl = $this->getObject('cleanurl');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Method to generate the embed code for a media object
     * @param  string $file   Full Path to the File
     * @param  string $type   Type of Embed
     * @param  int    $width  Width of the Media File
     * @param  int    $height Height of the Media File
     * @return string The media file embed code
     */
    function embed($file, $type, $width="100%", $height="100%") {
        // For some reason, cleaning the URL messes things up. Turned off for so long
        // Please clean URLs yourself for the timebeing before calling this function
        //$this->objCleanUrl->cleanUpUrl($file);

        switch ($type) {
            case 'link':
            default:
                return $this->linkToFile($file);
            case 'image':
                return $this->showImage($file, $width, $height);
            case 'obj3d':
                return $this->showObj3d($file, $width, $height);
            case 'vrml':
                return $this->showVRML($file, $width, $height);
            case 'freemind':
                return $this->showFreemind($file, $width, $height);
            case 'audio':
                return $this->showSoundPlayer($file);
            case 'flash':
                return $this->showFlash($file, $width, $height);
            case 'quicktime':
                return $this->showQuicktime($file, $width, $height);
            case 'wmv':
                return $this->showWMV($file, $width, $height);
            case 'avi':
                return $this->showAVI($file, $width, $height);
            case 'mpg':
            case 'mpeg':
                return $this->showMPG($file, $width, $height);
            case 'mp4':
                return $this->showMP4($file, $width, $height);
            case 'flv':
                return $this->showFLV($file, $width, $height);
            case 'ogg':
            case 'ogv':
                return $this->showOggVideo($file, $width, $height);
            case 'svg':
                return $this->showSVG($file, $width, $height);
            case 'webm':
                return $this->showWebm($file, $width, $height);
            case 'pdf':
                return $this->showPDF($file, $width, $height);
        }
    }

    /**
     *
     * Embed a WEBM video using HTML5 code
     *
     * @param string $file The file URL to embed
     * @param integer $width The width of the video
     * @param integer $height The height of the video
     * @return string The rendered video HTML5 tags
     * @access public
     */
    public function showWebm($file, $width, $height)
    {
        $ret = "\n\n<video width=\"$width\" height=\"$height\" controls=\"controls\">\n";
        $ret .= "<source src=\"$file\"  type=\"video/webm\"></source>\n";
        $ret .= $this->objLanguage->languageText("mod_files_err_nohtmk5", 'files',
           "Your browser does not support the HTMK5 VIDEO tag.");
        $ret .= "</video>";
        return $ret;
    }

    /**
     * Method to provide a link to the file
     * @param string $file Path to the File
     */
    function linkToFile($file) {
        $link = new link($file);
        $link->link = basename($file);
        return $link->show();
    }

    /**
     * Method to show an Image
     * @param string $file Path to the Image
     */
    function showImage($file, $width=null, $height=null) {
        if ($width != null && $height != null) {
            return '<img src="' . $file . '" width="' . $width . '" height="' . $height . '" />';
        } else {
            return '<img src="' . $file . '"/>';
        }
    }

    /**
     * Method to show a 3d Object
     * This uses David Wafula's 3d Object Viewer
     * @param string $file Path to the Object
     */
    function showObj3d($file) {
        // This does not work :-(  $file = $this->objConfig->getsiteRoot().'/'.$file;
        // The Applet needs a relative path to the file, hence this approach
        $file = '../../../../' . $file;

        $file = $this->objCleanUrl->cleanUpUrl($file);

        return '<applet CODE="ObjLoad.class"  CODEBASE="core_modules/files/resources/obj3d" width="100%" height="400">
   <param name="type" value="application/x-java-applet;version=1.4" />
   <param name="filename" value="' . $file . '" />
   </applet>';
    }

    /**
     * Method to embed a VRML file
     * @param string $file Path to the File
     */
    function showVRML($file) {
        $width = '100%';
        $height = '400';
        return '<embed src="' . $file . '" width="' . $width . '" height="' . $height . '" ></embed>';
    }

    /**
     * Method to embed a freemind map
     * Note. This uses the flash version rather than the java applet.
     * @param string $file Path to the File
     */
    function showFreemind($file) {
        $objFlashFreemind = $this->newObject('flashfreemind', 'files');
        $objFlashFreemind->setMindMap($file);
        return $objFlashFreemind->show();
    }

    /**
     * Method to show the Sound Player Applet
     * @param string $file Path to the File
     */
    function showSoundPlayer($file) {
        $objSoundPlayerBuilder = $this->newObject('buildsoundplayer', 'files');
        $objSoundPlayerBuilder->setSoundFile($file);
        return $objSoundPlayerBuilder->show();
    }

    /**
     * Method to embed a flash object
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    function showFlash($file, $width='100%', $height='400') {
        return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="' . $width . '" height="' . $height . '">
  <param name="movie" value="' . $file . '" />
  <param name="quality" value="high" />
  <embed src="' . $file . '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '"></embed>
</object>';
    }

    /**
     * Method to embed a Quicktime Object
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    function showQuicktime($file, $width='100%', $height='400') {
        $width = $width == '' ? '100%' : $width;
        $height = $height == '' ? '400' : $height;

        $replace = '<object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="' . $height . '" width="' . $width . '"';
        $replace .= '        id="quicktime" type="application/x-oleobject">';
        $replace .= "<param name=\"src\" value=\"{$file}\" />";
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<param name="loop" value="false" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= "\n<embed src=\"{$file}\" name=\"quicktime\" type=\"video/quicktime\" ";
        $replace .= ' height="' . $height . '" width="' . $width . '" scale="aspect" ';
        $replace .= ' autoplay="false" controller="true" loop="true" ';
        $replace .= ' pluginspage="http://quicktime.apple.com/">';
        $replace .= '</embed>';
        $replace .= '</object>';

        return $replace;
    }

    /**
     * Method to show a WMV video
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    function showWMV($file, $width='100%', $height='400') {
        $width = $width == '' ? '100%' : $width;
        $height = $height == '' ? '400' : $height;

        $replace = '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft Windows Media Player components..." ';
        $replace .= ' id="msplayer" align="" type="application/x-oleobject">';
        $replace .= "<param name=\"Filename\" value=\"{$file}\" />";
        $replace .= '<param name="ShowControls" value="true" />';
        $replace .= '<param name="AutoRewind" value="true" />';
        $replace .= '<param name="AutoStart" value="false" />';
        $replace .= '<param name="Autosize" value="true" />';
        $replace .= '<param name="EnableContextMenu" value="true" />';
        $replace .= '<param name="TransparentAtStart" value="false" />';
        $replace .= '<param name="AnimationAtStart" value="false" />';
        $replace .= '<param name="ShowGotoBar" value="false" />';
        $replace .= '<param name="EnableFullScreenControls" value="true" />';
        $replace .= "\n<embed src=\"{$file}\" name=\"msplayer\" type=\"video/x-ms\" ";
        $replace .= ' ShowControls="1" AutoRewind="1" AutoStart="0" Autosize="0" EnableContextMenu="1"';
        $replace .= ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
        $replace .= ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/" >';
        $replace .= '</embed>';
        $replace .= '</object>';

        return $replace;
    }

    /**
     * Method to show a AVI video
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    function showAVI($file, $width='100%', $height='400') {
        $width = $width == '' ? '100%' : $width;
        $height = $height == '' ? '400' : $height;

        $replace = '<object width="' . $width . '" height="' . $height . '">';
        $replace .= '<param name="src" value="' . $file . '" />';
        $replace .= '<param name="controller" value="1" />';
        $replace .= '<param name="autoplay" value="0" />';
        $replace .= '<embed src="' . $file . '" width="' . $width . '" height="' . $height . '" controller="1" autoplay="0" pluginspage ="http://www.microsoft.com/Windows/MediaPlayer/" type="video/x-ms-wvx" > </embed>';
        $replace .= '</object>';

        return $replace;
    }

    /**
     * Method to show a FLV video
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    public function old______showFLV($file, $width='100%', $height='400') {
        $width = $width == '' ? '100%' : $width;
        $height = $height == '' ? '400' : $height;

        $objBuildPlayer = $this->getObject('buildflowplayer', 'files');
        $objBuildPlayer->setMovieFile($file);
        $objBuildPlayer->width = $width;
        $objBuildPlayer->height = $height;
        return $objBuildPlayer->show();
    }

    /**
     * Show a OGG video
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     * @access public
     * @return string The rendered video
     */
    public function showOggVideo($file, $width='100%', $height='400') {
        //die($width . " / " . $height);
        $css = '<link href="' . $this->getResourceUri('video-js/video-js.css',
          'files') . '" rel="stylesheet">';
        $js = $this->getJavaScriptFile('video-js/video.min.js',
          'files');
        $load = "\n\n$css\n$js\n\n";
        $this->appendArrayVar('headerParams', $load);
        $videoId = md5($file);
        return '<video id="' . $videoId . '" class="video-js vjs-default-skin" controls
  preload="auto" width="' . $width . '" height="' . $height . '" poster="' . $file . '.png"
  data-setup="{}">
  <source src="' . $file . '" type="application/ogg">
</video>';
    }

    /**
     * Method to show a Scalable Vector Graphics (SVG) Image
     * NOTE: Width and height are kept for legacy reasons,  but
     * they are not used as they do not work as expected. Rather
     * render the SVG in a fluid div.
     *
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     * @access public
     * @return string The rendered SVG
     *
     */
    function showSVG($file, $width=NULL, $height=NULL) {
        return '<object data="' . $file
          . '" type="image/svg+xml"></object>';
    }

    /**
     * Method to show a MPEG Moview
     * @param string $file   Path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     */
    function showMPG($file, $width='100%', $height='400') {

        return '<object ID="MediaPlayer" width="' . $width . '" height="' . $height . '"
CLASSID="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95"
STANDBY="Loading Windows Media Player components..."
TYPE="application/x-oleobject">
<param name="FileName" value="' . $file . '" />
<embed TYPE="application/x-mplayer2" src="' . $file . '" name="MediaPlayer"
width="' . $width . '" height="' . $height . '"></embed></object>';
    }


    /**
     *
     * Show a FLV flash video. Note that width and height are here for
     * legacy reasons, they are not needed since we have implemented this
     * as a fluid div, and the video will resize to available space or
     * can be styled using the CSS Fluid video settings. See common2.css
     * in the core _common2 skin.
     *
     * @param string $file   URL path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     * @access public
     * @return string The rendered video
     *
     */
    public function showFLV($file, $width=FALSE, $height=FALSE) {
        $objFlow = $this->getObject('flowplayer', 'files');
        return $objFlow->show($file);
    }

    /**
     *
     * Show an MP4 or Flash video with the standard settings using
     * FlowPlayer.
     *
     * @param string $file The full URL for the file to play
     * @return string The rendered player with the video
     * @access public
     *
     */
    public function showWithFlowPlayer($file)
    {
        $objFlow = $this->getObject('flowplayer', 'files');
        return $objFlow->show($file);
    }

    /**
     *
     * Show a MP4 Movie. Note that width and height are here for
     * legacy reasons, they are not needed since we have implemented this
     * as a fluid div, and the video will resize to available space or
     * can be styled using the CSS Fluid video settings. See common2.css
     * in the core _common2 skin.
     *
     * @param string $file   URL path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     * @access public
     * @return string The rendered video
     *
     */
    public function showMP4($file, $width='100%', $height='auto')
    {
        // Use flowplayer to play it.
        return $this-> showWithFlowPlayer($file);
    }

    /**
     *
     * Embed a PDF file
     *
     * @param string $file   URL path to the File
     * @param string $width  Width of Object
     * @param string $height Height of Object
     * @access public
     * @return string The rendered PDF
     *
     */
    public function showPDF($file, $width="100%", $height="500")
    {
        return "<div class='pdf_wrapper'>"
          . "<embed src='$file' width='$width' height='$height'>"
          . "</embed></div>";
    }
}
?>