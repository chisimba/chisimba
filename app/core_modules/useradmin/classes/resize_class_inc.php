<?php

/**
* Class to resize images
* Edited to fit the NextGen framework by James Scoble
*/

##############################################
# Shiege Iseng Resize Class
# 11 March 2003
# shiegege_at_yahoo.com
# View Demo :
#   http://kentung.f2o.org/scripts/thumbnail/sample.php
################
# Thanks to :
# Dian Suryandari <dianhau_at_yahoo.com>
/*############################################
Sample :
$thumb=new thumbnail("./shiegege.jpg");			// generate image_file, set filename to resize
$thumb->size_width(100);				// set width for thumbnail, or
$thumb->size_height(300);				// set height for thumbnail, or
$thumb->size_auto(200);					// set the biggest width or height for thumbnail
$thumb->jpeg_quality(75);				// [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
$thumb->show();						// show your thumbnail
$thumb->save("./huhu.jpg");				// save your thumbnail to file
----------------------------------------------
Note :
- GD must Enabled
- Autodetect file extension (.jpg/jpeg, .png, .gif, .wbmp)
  but some server can't generate .gif / .wbmp file types
- If your GD not support 'ImageCreateTrueColor' function,
  change one line from 'ImageCreateTrueColor' to 'ImageCreate'
  (the position in 'show' and 'save' function)
*/############################################


class resize extends object
{
    var $img;
    var $outputType = 'JPG';
    var $outputContentType = 'JPEG';
    var $outputFileName;

    /**
    * Method to initialise the object.
    * @author Jonathan Abrahams
    */
    function init()
    {
        if (!extension_loaded('gd')){
            $objLanguage=&$this->getObject('language','language');
            $msg=$objLanguage->languageText('mod_useradmin_nogd','Warning: KEWL.NextGen requires PHP to have the GD graphics module available in order to resize image files.');
            $this->objEngine->addMessage($msg);
        }
    }
    

    /**
    * Method to check for function validity
    * (to prevent a "crash" if the function isn't there)
    * NOTE: Since function can take a variable number of params
    * '_' is used instead of NULL, since NULL might sometimes actually
    * be a value.
    * @param string $fname the function
    * @param string $var the variables passed as params
    */
    function callFunction($fname,$var1='_',$var2='_',$var3='_',$var4='_',$var5='_')
    {
        if (function_exists($fname)){
            if ($var1=='_'){
                 return $fname();
            } else if ($var2=='_'){
                 return $fname($var1);
            } else if ($var3=='_'){
                 return $fname($var1,$var2);
            } else if ($var4=='_'){
                 return $fname($var1,$var2,$var3);
            }    
        } else {
            return FALSE;
        }
    }
    
        /**
        * method to load the image
        * @param string $imgfile
        * @param string $filename
        * @returbs Boolean TRUE|FALSE
        */
	function loadimage($imgfile,$filename)
	{
		//detect image format
		$this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$filename); 
		$this->img["format"]=strtoupper($this->img["format"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			$this->img["format"]="JPEG";
			$this->img["src"] = $this->callFunction('ImageCreateFromJPEG',$imgfile);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			$this->img["format"]="PNG";
			$this->img["src"] = $this->callFunction('ImageCreateFromPNG',$imgfile);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			$this->img["format"]="GIF";
			$this->img["src"] = $this->callFunction('ImageCreateFromGIF',$imgfile);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			$this->img["format"]="WBMP";
			$this->img["src"] = $this->callFunction('ImageCreateFromWBMP',$imgfile);
		} else {
			//DEFAULT
			return FALSE;
			exit();
		}
		@$this->img["lebar"] = $this->callFunction('imagesx',$this->img["src"]);
		@$this->img["tinggi"] = $this->callFunction('imagesy',$this->img["src"]);
		//default quality jpeg
		$this->img["quality"]=75;
                return TRUE;
	}

        /**
        * method to set the output type
        * @param string $outputType
        */
	function setOutput($outputType)
	{
		switch (strtoupper($outputType))
            {
                case 'JPG' : $this->outputType = 'JPG'; $this->outputContentType = 'JPEG'; break;
                
                case 'PNG' : $this->outputType = 'PNG'; $this->outputContentType = 'PNG'; break;
                
                case 'GIF' : $this->outputType = 'GIF'; $this->outputContentType = 'GIF'; break;
                
                case 'WBMP' : $this->outputType = 'WBMP'; $this->outputContentType = 'WBMP'; break;
                
                default :  break;
                
            }
	}
    
    /**
    * method to set the height
    * @param string $size
    */
    function size_height($size=100)
	{
		//height
    	$this->img["tinggi_thumb"]=$size;
    	@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	}

    /**
    * method to set the width
    * @param string $size
    */
    function size_width($size=100)
	{
		//width
		$this->img["lebar_thumb"]=$size;
    	@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	}

    /**
    * method to autosize
    * @param string $size
    */
    function size_auto($size=100)
	{
		//size
		if ($this->img["lebar"]>=$this->img["tinggi"]) {
    		$this->img["lebar_thumb"]=$size;
    		@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
		} else {
	    	$this->img["tinggi_thumb"]=$size;
    		@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
 		}
	}

    /**
    * method to set jpeg quality
    * @param string $quality
    */
	function jpeg_quality($quality=75)
	{
		//jpeg quality
		$this->img["quality"]=$quality;
	}

    /**
    * method to display
    */
	function show()
	{
		//show thumb
		@Header("Content-Type: image/".$this->outputContentType);

		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
    		@imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

		
        switch (strtoupper($this->outputType))
        {
            case 'JPG' : $this->callFunction('imageJPEG',$this->img["des"],"",$this->img["quality"]); break;
            
            case 'PNG' : $this->callFunction('imagePNG',$this->img["des"]); break;
            
            case 'GIF' : $this->callFunction('imageGIF',$this->img["des"]); break;
            
            case 'WBMP' : $this->callFunction('imageWBMP',$this->img["des"]); break;
            
            default : $this->callFunction('imageJPEG',$this->img["des"],"",$this->img["quality"]); break;
            
        }
	}

    /**
    * method to save to file
    * @param string $save
    */
	function save($save="")
	{
        //save thumb
		if (empty($save)){
        $save=strtolower("./thumb.".$this->outputType);
        }
            /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
            $this->img["des"] = ImageCreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);   
            @imagecopyresized($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
            switch ($this->outputType)
            {
                case 'JPG' : $this->callFunction('imageJPEG',$this->img["des"],"$save",$this->img["quality"]); break;
                
                case 'PNG' : $this->callFunction('imagePNG',$this->img["des"],"$save"); break;
                
                case 'GIF' : $this->callFunction('imageGIF',$this->img["des"],"$save"); break;
                
                case 'BMP' : $this->callFunction('imageWBMP',$this->img["des"],"$save"); break;
                
                default : $this->callFunction('imageJPEG',$this->img["des"],"$save",$this->img["quality"]); break;
                
            }
            
	}
}
?>
