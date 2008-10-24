<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a flv (Flash video file) and render the video in the page, YouTube! style
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
 * @copyright 2007 Paul Scott & Derek Keats 
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
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a flv (Flash video file) and render the video in the page, YouTube! style
 *
 * @author    Paul Scott
 * @package   filters
 * @access    public
 * @copyright AVOIR GNU/GPL
 *
 */

class parse4flv extends object
{
    
    /**
     * init
     * 
     * Standard Chisimba init function
     * 
     * @return void  
     * @access public
     */
    function init()
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($txt)
    {
        preg_match_all('/\[FLV\](.*)\[\/FLV\]/U', $txt, $results, PREG_PATTERN_ORDER);
        preg_match_all('/\\[FLV:(.*?)\\]/', $txt, $results2, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $videoId = $item;
            $replacement = $this->getVideoObject($videoId);
            $txt = str_replace($results[0][$counter], $replacement, $txt);
            $counter++;
        }
        
        //Get all the ones [FLV: xx=yy] tags (added by Derek 2007 09 23)
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $this->item=$item;
            $str = $results2[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->width)) {
                $width = $this->objExpar->width;
            } else {
                $width=320;
            }
            if (isset($this->objExpar->height)) {
                $height = $this->objExpar->height;
            } else {
                $height=240;
            }
            if (isset($this->objExpar->url)) {
                $url = $this->objExpar->url;
            }
            $replacement = $this->getVideoObject($url, $width, $height);
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }
        
        return $txt;
    }
    
    /**
     * 
     * Method to build the flv video object code
     * @param  string  $videoId The id of the flv video
     * @return String  The object code
     * @access private
     *                 
     */
    private function getVideoObject($videoId, $width=320, $height=240)
    {
    	$player = $this->objConfig->getsiteRoot().$this->getResourceUri('flvplay.swf');
    	$skin = $this->objConfig->getsiteRoot().$this->getResourceUri('flvskin.swf');
    	$sskin = str_replace(".swf",'',$skin);
        return '<object width="' . $width . '" height="' . $height . '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        		codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"> 
        		<param name="salign" value="lt"> 
        		<param name="quality" value="high">   
        		<param name="scale" value="noscale"> 
        		<param name="wmode" value="transparent"> 
        		<param name="movie" value="'.$player.'"> 
        		<param name="FlashVars" value="&amp;streamName='.$videoId.'&amp;skinName='.$sskin.'&amp;autoPlay=false&amp;autoRewind=true">  
        		<embed width="' . $width . '" height="' . $height . '" flashvars="&amp;streamName='.$videoId.'&amp;autoPlay=false&amp;autoRewind=true&amp;skinName='.$sskin.'" 
        		quality="high" scale="noscale" salign="LT" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" 
        		src="'.$player.'" wmode="transparent"> </embed>
        		</object>';
    }
    
    /**
     * 
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     * 
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->url)) {
            $this->id = $this->objExpar->url;
        } else {
            $this->id=NULL;
        }
        
        if (isset($this->objExpar->width)) {
            $this->id = $this->objExpar->width;
        } else {
            $this->id=NULL;
        }
        
        
    }
    
}
?>