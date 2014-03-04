<?php
/**
 *
 * Streams class to build the Cortado applet
 *
 * Streams is a module to allow for the streaming of audio and video to a
 * web browser using the Cortado streaming applet for Ogg formats from
 * Flumotion at http://www.flumotion.net/cortado/
 *
 * Cortado currently include Java decoders for Ogg Theora, Ogg Vorbis,
 * Mulaw audio, MJPEG and Fluendo's own Smoke codec.
 *
 * You can get the applet source from
 *    http://www.flumotion.net/src/cortado/
 * under GPL.
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbox_class_inc.php 8227 2008-03-27 20:05:32Z dkeats $
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
 * Streams class to build the Cortado applet
*
* @author Derek Keats
* @package streams
*
*/
class streamapplet extends object
{

    /**
    *
    * Method to get the height from the URL and default to
    * 240 pixels
    *
    * @return string the height of the video player
    *
    */
    private function getHeight()
    {
        return $this->getParam("height", "240");
    }
    /**
    *
    * Method to get the width from the URL and default to
    * 320 pixels
    *
    * @return string the height of the video player
    *
    */
    private function getWidth()
    {
        return  $this->getParam("width", "320");
    }
    /**
    *
    * Method to get the buffer size from the URL
    * The size of the network buffer, in KB. A good value is max
    * Kbps of the stream * 33. Defaults to 400 here, 200 in the applet
    * if not specified.
    *
    * @return string the buffer size
    *
    */
    private function getBufferSize()
    {
        return  $this->getParam("buffersize", "400");
    }
    /**
    *
    * Return the URL to load, must be a fully qualified URL.
    *   IMPORTANT: if the applet is not signed, the hostname of the
    *   url *is required* to be the same as the hostname of the link
    *   to the page with the applet tag.  This is a Java security
    *   limitation.
    *
    * To sign the applet, make sure you have the JDK installed
    * and issue the following commands:
    *
    *    keytool -genkey -keyalg rsa -alias YOURMADEUPNAME
    *    jarsigner cortado-ovt.jar YOURMADEUPNAME
    *
    * This works on Linux, Google for it if you are on doze.
    *
    * @access Private
    * @return string The URL to load for streaming
    *
    */
    private function getUrl()
    {
        return  urldecode($this->getParam("url",
          "http://173.203.201.87:8000/theora.ogg"));
    }
    /**
    *
    * Method to get the frame rate from the URL and default to
    * 13
    *
    * @return string the buffer size
    *
    */
    private function getFramerate()
    {
        return  $this->getParam("framerate", "13");
    }
    /**
    *
    * Use video. When not using video, this property will not create
    * resources to play a video stream. true or false. Defaults to true
    *
    * @return string true|false as lower case strings
    * @access Private
    *
    */
    private function getVideo()
    {
        return  $this->getParam("video", "true");
    }
    /**
    *
    * Method to get whether we should keep the aspect ratio
    * for the parameter &lt;param name="keepaspect" value="true"/&gt;
    *
    *
    * @return string true|false as lower case strings
    * @access Private
    *
    */
    private function getKeepaspect()
    {
        return  $this->getParam("keepaspect", "true");
    }
    /**
    *
    * Method to get whether we should play audio from the stream
    * When not using audio, this property will not create resources
    * to play an audio stream.
    *
    * @return string true|false as lower case strings
    * @access Private
    *
    */
    private function getAudio()
    {
        return  $this->getParam("audio", "true");
    }
    /**
    *
    * Method to get the codebase for the applet, which is
    * in the resources directory of this module.
    *
    * @return string The url path to the applet excluding its name
    * @access Private
    *
    */
    private function getCodebase()
    {
         return $this->getResourceUri('', 'stream');
    }


    /**
    *
    * Method to get the embed applet code
    *
    * @access private
    * @return string The rendered embed code
    *
    */
    private function getEmbed()
    {
        return '<comment><embed type="application/x-java-applet" '
        . ' width="' . $this->getWidth() . '" '
        . 'height="'. $this->getHeight() . '" '
        . 'align="baseline" '
        . 'code="com.fluendo.player.Cortado.class" '
        . 'archive="cortado-ovt.jar?_=2" codebase="' . $this->getCodebase() . '" '
        . 'bufferSize="' . $this->getBufferSize()
        . '" url="' . $this->getUrl() . '" '
        . 'framerate="' . $this->getFramerate()
        . '" video="' . $this->getVideo()
        . '" keepaspect="' . $this->getKeepaspect()
        . '" audio="' . $this->getAudio() . '" local="false">'
        . '<noembed>You need Java to view this media file.</noembed>'
        . '</embed></comment>';

    }

    /**
    *
    * SHow method to render the applet and play the selected
    * stream of audio or video in ogg format.
    *
    * @return string The rendered applet code
    * @access Public
    *
    */
    public function show()
    {
        $ret = '<table cellpadding=10 cellspacing=10><tr><td>'
          . '<object id="cortado" classid="clsid:08B0E5C0-4FCB-11CF-AAA5-00401C608501" '
          . 'width="' . $this->getWidth() . '" height="'
          . $this->getHeight() . '" align="baseline" onerror="objectLoadError ();">'
          . '<param name="code" value="com.fluendo.player.Cortado.class"/>'
          . '<param name="codebase" value="' . $this->getCodebase() . '">'
          . '<param name="archive" value="cortado-ovt.jar?_=2"/>'
          . '<param name="bufferSize" value="' . $this->getBufferSize() . '"/>'
          . '<param name="url" value="' . $this->getUrl() . '"/>'
          . '<param name="framerate" value="' . $this->getFramerate() . '"/>'
          . '<param name="video" value="' . $this->getVideo() . '"/>'
          . '<param name="keepaspect" value="' . $this->getKeepaspect() . '"/>'
          . '<param name="audio" value="' . $this->getAudio() . '"/>'
          . '<param name="local" value="false"/>'
          . $this->getEmbed()
          . '</object></td>';
        $directLink = urldecode($this->getParam('directlink', NULL));
        if (!$directLink == NULL) {
            $directLink = '<a href="' . $directLink
            . '">Play in browser</a><br />Requires OGG compatible plugin.';
        }
        $ret .= '<td>You are watching this stream using Chisimba.<br /><br />'
          . $directLink . '</td></tr></table>';
        return $ret;
    }
}
