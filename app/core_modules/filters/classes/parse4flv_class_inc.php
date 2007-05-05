<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a flv (Flash video file) and render the video in the page, YouTube! style
*
* @author Paul Scott
* @package filters
* @access public
* @copyright AVOIR GNU/GPL
*
*/

class parse4flv extends object
{
    
    function init()
    {
    	$this->objConfig = $this->getObject('altconfig', 'config');
        
    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($str)
    {
        preg_match_all('/\[FLV\](.*)\[\/FLV\]/U', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $videoId = $item;
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($results[0][$counter], $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    /**
     * 
     * Method to build the flv video object code
     * @param string $videoId The id of the flv video
     * @return String The object code
     * @access private
     * 
     */
    private function getVideoObject($videoId)
    {
    	$player = $this->objConfig->getsiteRoot().$this->getResourceUri('flvplay.swf');
    	$skin = $this->objConfig->getsiteRoot().$this->getResourceUri('flvskin.swf');
    	$sskin = str_replace(".swf",'',$skin);
        return '<object width="320" height="240" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
        		codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"> 
        		<param name="salign" value="lt"> 
        		<param name="quality" value="high">   
        		<param name="scale" value="noscale"> 
        		<param name="wmode" value="transparent"> 
        		<param name="movie" value="'.$player.'"> 
        		<param name="FlashVars" value="&amp;streamName='.$videoId.'&amp;skinName='.$sskin.'&amp;autoPlay=false&amp;autoRewind=true">  
        		<embed width="320" height="240" flashvars="&amp;streamName='.$videoId.'&amp;autoPlay=false&amp;autoRewind=true&amp;skinName='.$sskin.'" 
        		quality="high" scale="noscale" salign="LT" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" 
        		src="'.$player.'" wmode="transparent"> </embed>
        		</object>';
    }
    
}
?>