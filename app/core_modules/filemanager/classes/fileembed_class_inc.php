<?
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
* @author Tohir Solomons
*/
class fileembed extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->loadClass('link', 'htmlelements');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        $this->objConfig =& $this->getObject('altconfig', 'config');
    }
    
    /**
    * Method to generate the embed code for a media object
    * @param string $file Full Path to the File
    * @param string $type Type of Embed
    * @param int $width Width of the Media File
    * @param int $height Height of the Media File
    * @return string The media file embed code
    */
    function embed($file, $type, $width=NULL, $height=NULL)
    {
        // For some reason, cleaning the URL messes things up. Turned off for so long
        // Please clean URLs yourself for the timebeing before calling this function
        //$this->objCleanUrl->cleanUpUrl($file);

        switch ($type)
        {
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
            case 'flv':
                return $this->showFLV($file, $width, $height);
            case 'ogg':
                return $this->showOggVideo($file, $width, $height);
            case 'svg':
                return $this->showSVG($file, $width, $height);
        }
    }
    
    /**
    * Method to provide a link to the file
    * @param string $file Path to the File
    */
    function linkToFile($file)
    {
        $link = new link ($file);
        $link->link = basename($file);
        
        return $link->show();
    }
    
    /**
    * Method to show an Image
    * @param string $file Path to the Image
    */
    function showImage($file)
    {
        return '<img src="'.$file.'" />';
    }
    
    /**
    * Method to show a 3d Object
    * This uses David Wafula's 3d Object Viewer
    * @param string $file Path to the Object
    */
    function showObj3d($file)
    {
        // This does not work :-(  $file = $this->objConfig->getsiteRoot().'/'.$file;
        // The Applet needs a relative path to the file, hence this approach
        $file = '../../../../'.$file;
        
        $this->objCleanUrl->cleanUpUrl($file);
        
        return '<applet CODE="ObjLoad.class"  CODEBASE="core_modules/files/resources/obj3d" width="100%" height="400">
   <param name="type" value="application/x-java-applet;version=1.4" />
   <param name="filename" value="'.$file.'" />
   </applet>';
    }
    
    /**
    * Method to embed a VRML file
    * @param string $file Path to the File
    */
    function showVRML($file)
    {
        $width = '100%';
        $height = '400';
        return '<embed src="'.$file.'" width="'.$width.'" height="'.$height.'" ></embed>';
    }
    
    /**
    * Method to embed a freemind map
    * Note. This uses the flash version rather than the java applet.
    * @param string $file Path to the File
    */
    function showFreemind($file)
    {
        $objFlashFreemind = $this->newObject('flashfreemind', 'freemind');
        $objFlashFreemind->setMindMap($file);
        return $objFlashFreemind->show();
    }
    
    /**
    * Method to show the Sound Player Applet
    * @param string $file Path to the File
    */
    function showSoundPlayer($file)
    {
        $objSoundPlayerBuilder = $this->newObject('buildplayer', 'soundplayer');
        $objSoundPlayerBuilder->setSoundFile($file);
        return $objSoundPlayerBuilder->show();
    }
    
    /**
    * Method to embed a flash object
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showFlash($file, $width='100%', $height='400')
    {
        return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="32" height="32">
  <param name="movie" value="'.$file.'" />
  <param name="quality" value="high" />
  <embed src="'.$file.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'"></embed>
</object>';
    }
    
    /**
    * Method to embed a Quicktime Object
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showQuicktime($file, $width='100%', $height='400')
    {
        $width = $width=='' ? '100%' : $width; 
        $height = $height=='' ? '400' : $height; 
        
        $replace = '<object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="'.$height.'" width="'.$width.'"';
        $replace .= '        id="quicktime" type="application/x-oleobject">';
        $replace .= "<param name=\"src\" value=\"{$file}\" />";
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<param name="loop" value="false" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= "\n<embed src=\"{$file}\" name=\"quicktime\" type=\"video/quicktime\" ";
        $replace .= ' height="'.$height.'" width="'.$width.'" scale="aspect" ';
        $replace .= ' autoplay="false" controller="true" loop="true" ';
        $replace .= ' pluginspage="http://quicktime.apple.com/">';
        $replace .= '</embed>';
        $replace .= '</object>';
        
        return $replace;
    }
    
    /**
    * Method to show a WMV video
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showWMV($file, $width='100%', $height='400')
    {
        $width = $width=='' ? '100%' : $width; 
        $height = $height=='' ? '400' : $height; 
        
        $replace = '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft® Windows® Media Player components..." ';
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
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showAVI($file, $width='100%', $height='400')
    {
        $width = $width=='' ? '100%' : $width; 
        $height = $height=='' ? '400' : $height; 
        
        $replace = '<object width="'.$width.'" height="'.$height.'">';
        $replace .= '<param name="src" value="'.$file.'" />';
        $replace .= '<param name="controller" value="1" />';
        $replace .= '<param name="autoplay" value="0" />';
        $replace .= '<embed src="'.$file.'" width="'.$width.'" height="'.$height.'" controller="1" autoplay="0" pluginspage ="http://www.microsoft.com/Windows/MediaPlayer/" type="video/x-ms-wvx" > </embed>';
        $replace .= '</object>';
        
        return $replace;
    }
    
    /**
    * Method to show a FLV video
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showFLV($file, $width='100%', $height='400')
    {
        $width = $width=='' ? '100%' : $width; 
        $height = $height=='' ? '400' : $height; 
        
        $objBuildPlayer = $this->getObject('buildflowplayer', 'flowplayer');
        $objBuildPlayer->setMovieFile($file);
        $objBuildPlayer->width = $width;
        $objBuildPlayer->height = $height;
        return $objBuildPlayer->show();
    }
    
    /**
    * Method to show a FLV video
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showOggVideo($file, $width='100%', $height='400')
    {
        return '
<applet code="com.fluendo.player.Cortado.class" 
           archive="cortado-ovt-0.1.2.jar" codebase="core_modules/files/resources/cortado_ogg_player/" 
	   width="'.$width.'" height="'.$height.'">
     <param name="url" value="'.$file.'"/>
     <param name="local" value="false"/>
     <param name="duration" value="232"/>
     <param name="keepAspect" value="true"/>
     <param name="video" value="true"/>
     <param name="audio" value="true"/>
     <param name="bufferSize" value="200"/>
     <param name="showStatus" value="show"/>
   </applet>';
    }
    
    /**
    * Method to show a Scalable Vector Graphics (SVG) Image
    * @param string $file Path to the File
    * @param string $width Width of Object
    * @param string $height Height of Object
    */
    function showSVG($file, $width='100%', $height='400')
    {
        return '<object data="'.$file.'" width="'.$width.'" height="'.$height.'" 
type="image/svg+xml"
codebase="http://www.adobe.com/svg/viewer/install/" />';
    }
    

}

?>