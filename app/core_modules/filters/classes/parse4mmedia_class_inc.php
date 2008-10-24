<?php

/**
 * Class for parsing anchored multimedia links and turning them
 * into embeded media links. This class is based on the similar filters
 * from Moodle. The original author was not identified in the Moodle
 * file.
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
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
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
 * Class for parsing anchored multimedia links and turning them
 * into embeded media links. This class is based on the similar filters
 * from Moodle. The original author was not identified in the Moodle
 * file.
 * 
 * @author    Derek Keats
 * @version   $Id$
 * @copyright 2003 GPL
 */
class parse4mmedia extends object {
    /**
     * Method to take a string and return it with anchor linkd to 
     * quicktime movies changed to embeded media
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseMov($str)
    {
        //$search = '/<a(.*?)href=\"([^<]+)\.mov\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.mov\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0*/$replace = '<p class="mediaplugin"><object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="300" width="400"';
        $replace .= '        id="quicktime" align="" type="application/x-oleobject">';
        $replace .= "<param name=\"src\" value=\"\\2.mov\" />";
        $replace .= '<param name="autoplay" value=false />';
        $replace .= '<param name="loop" value=true />';
        $replace .= '<param name="controller" value=true />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= "\n<embed src=\"\\2.mov\" name=\"quicktime\" type=\"video/quicktime\" ";
        $replace .= ' height="300" width="400" scale="aspect" ';
        $replace .= ' autoplay="false" controller="true" loop="true" ';
        $replace .= ' pluginspage="http://quicktime.apple.com/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to take a string and return it with anchor linkd to 
     * windows media movies changed to embeded media
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     *                
     */
    function parseWmv($str)
    {
        //$search = '/<a(.*?)href=\"([^<]+)\.wmv\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.wmv\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0*/$replace = '<p class="mediaplugin"><object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft Windows Media Player components..." ';
        $replace .= ' id="msplayer" align="" type="application/x-oleobject">';
        $replace .= "<param name=\"Filename\" value=\"\\2.wmv\">";
        $replace .= '<param name="ShowControls" value=true />';
        $replace .= '<param name="AutoRewind" value=true />';
        $replace .= '<param name="AutoStart" value=false />';
        $replace .= '<param name="Autosize" value=true />';
        $replace .= '<param name="EnableContextMenu" value=true />';
        $replace .= '<param name="TransparentAtStart" value=false />';
        $replace .= '<param name="AnimationAtStart" value=false />';
        $replace .= '<param name="ShowGotoBar" value=false />';
        $replace .= '<param name="EnableFullScreenControls" value=true />';
        $replace .= "\n<embed src=\"\\2.wmv\" name=\"msplayer\" type=\"video/x-ms\" ";
        $replace .= ' ShowControls="1" AutoRewind="1" AutoStart="0" Autosize="0" EnableContextMenu="1"';
        $replace .= ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
        $replace .= ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to take a string and return it with anchor linkd to 
     * flash movies changed to embeded media
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseWmm($str)
    {
        //$search = '/<a(.*?)href=\"([^<]+)\.swf\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.swf\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0*/$replace = '<p class="mediaplugin"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace .= ' width="400" height="300" id="mp3player" align="">';
        $replace .= " <param name=movie value=\"\\2.swf\">";
        $replace .= ' <param name=quality value=high>';
        $replace .= " <embed src=\"\\2.swf\" ";
        $replace .= "  quality=high width=\"400\" height=\"300\" name=\"flashfilter\" ";
        $replace .= ' type="application/x-shockwave-flash" ';
        $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to take a string and return it with anchor linkd to 
     * mpeg movies changed to embeded media
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseMpeg($str)
    {
        //$search = '/<a(.*?)href=\"([^<]+)\.(mpe?g)\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.(mpe?g)\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0*/$replace = '<p class="mediaplugin"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.\\3">';
        $replace .= '<param name="controller" value="true">';
        $replace .= '<param name="autoplay" value="false">';
        $replace .= '<embed src="\\2.\\3" width="240" height="180" controller="true" autoplay="false"> </embed>';
        $replace .= '</object></p>';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to take a string and return it with anchor linkd to 
     * avi movies changed to embeded media.
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseAvi($str)
    {
        //$search = '/<a(.*?)href=\"([^<]+)\.avi\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.avi\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0*/$replace = '<p class="mediaplugin"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.avi" />';
        $replace .= '<param name="controller" value="1" />';
        $replace .= '<param name="autoplay" value="0" />';
        $replace .= '<embed src="\\2.avi" width="240" height="180" controller="1" autoplay="0" pluginspage ="http://www.microsoft.com/Windows/MediaPlayer/" type="video/x-ms-wvx" > </embed>';
        $replace .= '</object></p>';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to take a string and return it with anchor linkd to 
     * mp3 files changed to embeded media. It uses a provided
     * flash MP3 player to play MP3 files
     * 
     * TEST: passed
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseMp3($str)
    { 
        // Get the configuration object for site root
        $objConfig = $this->getObject('altconfig', 'config');
        $player = $objConfig->getsiteRoot() . "core_modules/filters/resources/mp3player.swf?src=";
        
        //$search = '/<a(.*?)href=\"([^<]+)\.mp3\"([^>]*)>(.*?)<\/a>/isU';
        $search = '/\[EMBED\]\s*<a(.*?)href=\"([^<]+)\.mp3\"([^>]*)>(.*)<\/a>\s*\[\/EMBED\]/isU';
        
        /*\\0&nbsp;\n\n*/$replace = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"";
        
        $replace = ' <applet code="javazoom.jlGui.TinyPlayer" archive="tinyplayer.jar,jl020.jar"
width="59" height="32" name="playerid" codebase="core_modules/files/resources/tinyplayer/" align="middle">
<param name="skin" value="skins/Digitalized" />
<param name="bgcolor" value="638182" />
<param name="autoplay" value="no" />
<param name="audioURL" value="'."\\2.mp3".'" />
<param name="scriptable" value="true" />
</applet> ';

        return preg_replace($search, $replace, $str);
    } # end of function
    
    /**
     * Method to parse a string against all the available methods
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     */
    function parseAll($str)
    {
        $str = $this->parseAvi($str);
        $str = $this->parseMov($str);
        $str = $this->parseMp3($str);
        $str = $this->parseMpeg($str);
        $str = $this->parseWmm($str);

        return $this->parseWmv($str);
    } # end of function
    

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $str Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public 
     */
    public function parse($str)
    {
    	return $this->parseAll($str);
    }
} # end of class

?>