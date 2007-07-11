<?php
/**
* Class to Display a Freemind File as a Flash Object
* 
* Using this flash object, the user only needs to have flash installed,
* no need for Java. This flash object also has the following built in features:
*
* - Search
* - Snapshot
* - Max width for nodes
* - Zoom capability
*
* @author Tohir Solomons
*/
class flashfreemind extends object 
{
    /**
    * @var string $mindMap Path and Name of the Mind Map file
    */
    private $mindMap = 'modules/freemind/resources/freeMindFlashBrowser.mm'; //example
    
    /**
    * @var string $mindMapId JavaScript Id of the Mindmap
    */
    public $mindMapId = 'mindmap'; // Name of the Flash Object
    
    /**
    * @var string $width Width of the Flash Object
    */
    public $width = '100%';
    
    /**
    * @var string $height Height of the Flash Object
    */
    public $height = '500px';
    
    /**
    * @var string $openUrl Target Window for opening links
    */
    public $openUrl = '_self';
    
    /**
    * @var int $startCollapsedToLevel Level from which nodes should be collapsed. -1 for all nodes
    */
    public $startCollapsedToLevel = 10;
    
    /**
    * @var string $mainNodeShape Shape of Main Node, either elipse or rectangle
    */
    public $mainNodeShape = 'elipse';
    
    /**
    * @var int $defaultWordWrap Max width of text node, 600 is the plugin's default
    */
    public $defaultWordWrap = 600;
    
    /**
    * @var int $ShotsWidth Width of Snapshot, 200 is the plugin's default
    */
    public $ShotsWidth = '200';
    
    
    /**
    * Constructor
    */
    public function init()
    {
    
    }
    
    /**
    * Method to set the path to the Mindmap file
    * @param string $url Url to the Mindmap File
    */
    public function setMindMap($url)
    {
        $url = str_replace('&amp;', '&', $url);
        $this->mindMap = urlencode($url);
    }
    
    /**
    * Method to return the javascript for the freemind flash object
    * Only necessary for javascript versions
    */
    public function getMindmapScript()
    {
        return $this->getJavascriptFile('flashfreemind/flashobject.js', 'files');
    }
    
    /**
    * Method to Display the Mindmap
    */
    public function show()
    {
        return $this->showHTML();
    }
    
    /**
    * Method to show the Flash Freemind Object JavaScript Version
    */
    private function showJS()
    {
        $this->appendArrayVar('headerParams', $this->getMindmapScript());
        
        return '<div id="flashcontent_'.$this->mindMapId.'" style="z-index:0; width:'.$this->width.'; height:'.$this->height.'">
		 Flash plugin or Javascript are turned off.
		 Activate both  and reload to view the mindmap
	</div>
    <script type="text/javascript">
    		// <![CDATA[
    		var fo = new FlashObject("'.$this->getResourceUri('visorFreemind.swf','freemind').'", "'.$this->mindMapId.'", "'.$this->width.'", "'.$this->height.'", 6, "#ffffff");
    		fo.addParam("quality", "high");
    		fo.addParam("bgcolor", "#ffffff");
    		fo.addParam("wmode", "transparent");
    		fo.addVariable("openUrl", "'.$this->openUrl.'");
    		fo.addVariable("initLoadFile", "'.$this->mindMap.'");
    		fo.addVariable("startCollapsedToLevel","'.$this->startCollapsedToLevel.'");
            fo.addVariable("mainNodeShape","'.$this->mainNodeShape.'");
            fo.addVariable("defaultWordWrap","'.$this->defaultWordWrap.'");
            fo.addVariable("ShotsWidth","'.$this->ShotsWidth.'");
    		fo.write("flashcontent_'.$this->mindMapId.'");
    		// ]]>
    	</script>';
    }
    
    /**
    * Method to show the Flash Freemind Object - HTML Version
    */
    private function showHTML()
    {
        return '
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$this->width.'" height="600">
  <param name="movie" value="'.$this->getResourceUri('flashfreemind/visorFreemind.swf','freemind').'" />
  <param name="quality" value="high" />
  <param name="wmode" value="transparent" />
  <param name="flashVars" value="openUrl='.$this->openUrl.'&amp;mainNodeShape='.$this->mainNodeShape.'&amp;startCollapsedToLevel='.$this->startCollapsedToLevel.'&amp;initLoadFile='.$this->mindMap.'&amp;defaultWordWrap='.$this->defaultWordWrap.'&amp;ShotsWidth='.$this->ShotsWidth.'" />
  <embed src="'.$this->getResourceUri('flashfreemind/visorFreemind.swf','files').'" flashVars="openUrl='.$this->openUrl.'&amp;mainNodeShape='.$this->mainNodeShape.'&amp;startCollapsedToLevel='.$this->startCollapsedToLevel.'&amp;initLoadFile='.$this->mindMap.'&amp;defaultWordWrap='.$this->defaultWordWrap.'&amp;ShotsWidth='.$this->ShotsWidth.'" width="'.$this->width.'" height="'.$this->height.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>
</object>';
    }
 
} 
?>