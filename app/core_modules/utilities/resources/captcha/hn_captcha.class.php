<?php
/**
  * PHP-Class hn_captcha Version 1.3, released 11-Apr-2006
  * Author: Horst Nogajski, horst@nogajski.de
  *
  * License: GNU GPL (http://www.opensource.org/licenses/gpl-license.html)
  * Download: http://hn273.users.phpclasses.org/browse/package/1569.html
  *
  * If you find it useful, you might rate it on http://www.phpclasses.org/rate.html?package=1569
  * If you use this class in a productional environment, you may drop me a note, so I can add a link to the page.
  *
  **/

/**
  * changes in version 1.1:
  *  - added a new configuration-variable: maxrotation
  *  - added a new configuration-variable: secretstring
  *  - modified function get_try(): now ever returns a string of 16 chars
  *
  * changes in version 1.2:
  *  - added a new configuration-variable: secretposition
  *  - once more modified the function get_try(): generate a string of 32 chars length,
  *    where at secretposition is the number of current-try.
  *    Hopefully this is enough for hackprevention.
  *
  * changes in version 1.3:
  *  - fixed a security-hole, what was discovered by Daniel Jagszent. Many thank's for
  *    testing, fixing and sharing it, Daniel!
  *    He has tested the class in a modified way, like it is described here:
  *    http://www.puremango.co.uk/cm_breaking_captcha_115.php
  *    It was possible to manually do the captcha-test, notice the public and private keys.
  *    In automated way this keys could send as long as the image-file exists!
  *    (with different other datas and independent from the new captcha-string!)
  *
  **/

/**
  * License: GNU GPL (http://www.opensource.org/licenses/gpl-license.html)
  *
  * This program is free software;
  *
  * you can redistribute it and/or modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2 of the License,
  * or (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
  * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License along with this program;
  * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  *
  **/


/**
  * Tabsize: 4
  *
  **/

/**
  * This class generates a picture to use in forms that perform CAPTCHA test
  * (Completely Automated Public Turing to tell Computers from Humans Apart).
  * After the test form is submitted a key entered by the user in a text field
  * is compared by the class to determine whether it matches the text in the picture.
  *
  * The class is a fork of the original released at www.phpclasses.org
  * by Julien Pachet with the name ocr_captcha.
  *
  * The following enhancements were added:
  *
  * - Support to make it work with GD library before version 2
  * - Hacking prevention
  * - Optional use of Web safe colors
  * - Limit the number of users attempts
  * - Display an optional refresh link to generate a new picture with a different key
  *   without counting to the user attempts limit verification
  * - Support the use of multiple random TrueType fonts
  * - Control the output image by only three parameters: number of text characters
  *   and minimum and maximum size preserving the size proportion
  * - Preserve all request parameters passed to the page via the GET method,
  *   so the CAPTCHA test can be added to existing scripts with minimal changes
  * - Added a debug option for testing the current configuration
  *
  * All the configuration settings are passed to the class in an array when the object instance is initialized.
  *
  * The class only needs two function calls to be used: display_form() and validate_submit().
  *
  * The class comes with an examplefile.
  * If you don't have it: http://hn273.users.phpclasses.org/browse/package/1569.html
  *
  * @shortdesc Class that generate a captcha-image with text and a form to fill in this text
  * @public
  * @author Horst Nogajski, (mail: horst@nogajski.de)
  * @version 1.3
  * @date 2006-April-11
  *
  **/
class hn_captcha
{

	////////////////////////////////
	//
	//	PUBLIC PARAMS
	//

		/**
		  * @shortdesc Absolute path to a Tempfolder (with trailing slash!). This must be writeable for PHP and also accessible via HTTP, because the image will be stored there.
          * @type string
		  * @public
          *
          **/
		var $tempfolder;

		/**
          * @shortdesc Absolute path to folder with TrueTypeFonts (with trailing slash!). This must be readable by PHP.
		  * @type string
		  * @public
          *
          **/
		var $TTF_folder;

		/**
          * @shortdesc A List with available TrueTypeFonts for random char-creation.
		  * @type mixed[array|string]
		  * @public
          *
          **/
		var $TTF_RANGE  = array('COMIC.TTF','JACOBITE.TTF','LYDIAN.TTF','MREARL.TTF','RUBBERSTAMP.TTF','ZINJARON.TTF');

		/**
          * @shortdesc How many chars the generated text should have
		  * @type integer
		  * @public
          *
          **/
		var $chars		= 6;

		/**
          * @shortdesc The minimum size a Char should have
		  * @type integer
		  * @public
          *
          **/
		var $minsize	= 20;

		/**
          * @shortdesc The maximum size a Char can have
		  * @type integer
		  * @public
          *
          **/
		var $maxsize	= 40;

		/**
          * @shortdesc The maximum degrees a Char should be rotated. Set it to 30 means a random rotation between -30 and 30.
		  * @type integer
		  * @public
          *
          **/
		var $maxrotation = 30;

		/**
          * @shortdesc Background noise On/Off (if is Off, a grid will be created)
		  * @type boolean
		  * @public
          *
          **/
		var $noise		= TRUE;

		/**
          * @shortdesc This will only use the 216 websafe color pallette for the image.
		  * @type boolean
		  * @public
          *
          **/
		var $websafecolors = FALSE;

		/**
          * @shortdesc Switches language, available are 'en' and 'de'. You can easily add more. Look in CONSTRUCTOR.
		  * @type string
		  * @public
          *
          **/
		var $lang		= "de";

		/**
          * @shortdesc If a user has reached this number of try's without success, he will moved to the $badguys_url
		  * @type integer
		  * @public
          *
          **/
		var $maxtry		= 3;

		/**
          * @shortdesc Gives the user the possibility to generate a new captcha-image.
		  * @type boolean
		  * @public
          *
          **/
		var $refreshlink = TRUE;

		/**
          * @shortdesc If a user has reached his maximum try's, he will located to this url.
		  * @type boolean
		  * @public
          *
          **/
		var $badguys_url = "/";

		/**
		  * Number between 1 and 32
          *
          * @shortdesc Defines the position of 'current try number' in (32-char-length)-string generated by function get_try()
		  * @type integer
		  * @public
          *
          **/
		var $secretposition = 21;

		/**
          * @shortdesc The string is used to generate the md5-key.
		  * @type string
		  * @public
          *
          **/
		var $secretstring = "This is a very secret string. Nobody should know it, =:)";

		/**
          * @shortdesc Outputs configuration values for testing
		  * @type boolean
		  * @public
          *
          **/
		var $debug = FALSE;



	////////////////////////////////
	//
	//	PRIVATE PARAMS
	//

		/** @private **/
		var $lx;				// width of picture
		/** @private **/
		var $ly;				// height of picture
		/** @private **/
		var $jpegquality = 80;	// image quality
		/** @private **/
		var $noisefactor = 9;	// this will multiplyed with number of chars
		/** @private **/
		var $nb_noise;			// number of background-noise-characters
		/** @private **/
		var $TTF_file;			// holds the current selected TrueTypeFont
		/** @private **/
		var $msg1;
		/** @private **/
		var $msg2;
		/** @private **/
		var $buttontext;
		/** @private **/
		var $refreshbuttontext;
		/** @private **/
		var $public_K;
		/** @private **/
		var $private_K;
		/** @private **/
		var $key;				// md5-key
		/** @private **/
		var $public_key;    	// public key
		/** @private **/
		var $filename;			// filename of captcha picture
		/** @private **/
		var $gd_version;		// holds the Version Number of GD-Library
		/** @private **/
		var $QUERY_STRING;		// keeps the ($_GET) Querystring of the original Request
		/** @private **/
		var $current_try = 0;
		/** @private **/
		var $r;
		/** @private **/
		var $g;
		/** @private **/
		var $b;


	////////////////////////////////
	//
	//	CONSTRUCTOR
	//

		/**
		  * @shortdesc Extracts the config array and generate needed params.
		  * @private
		  * @type void
		  * @return nothing
		  *
		  **/
		function hn_captcha($config,$secure=TRUE)
		{

			// Test for GD-Library(-Version)
			$this->gd_version = $this->get_gd_version();
			if($this->gd_version == 0) die("There is no GD-Library-Support enabled. The Captcha-Class cannot be used!");
			if($this->debug) echo "\n<br>-Captcha-Debug: The available GD-Library has major version ".$this->gd_version;


			// Hackprevention
			if(
				(isset($_GET['maxtry']) || isset($_POST['maxtry']) || isset($_COOKIE['maxtry']))
				||
				(isset($_GET['debug']) || isset($_POST['debug']) || isset($_COOKIE['debug']))
				||
				(isset($_GET['captcharefresh']) || isset($_COOKIE['captcharefresh']))
				||
				(isset($_POST['captcharefresh']) && isset($_POST['private_key']))
				)
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Buuh. You are a bad guy!";
				if(isset($this->badguys_url) && !headers_sent()) header('location: '.$this->badguys_url);
				else die('Sorry.');
			}


			// extracts config array
			if(is_array($config))
			{
				if($secure && strcmp('4.2.0', phpversion()) < 0)
				{
					if($this->debug) echo "\n<br>-Captcha-Debug: Extracts Config-Array in secure-mode!";
					$valid = get_class_vars(get_class($this));
					foreach($config as $k=>$v)
					{
						if(array_key_exists($k,$valid)) $this->$k = $v;
					}
				}
				else
				{
					if($this->debug) echo "\n<br>-Captcha-Debug: Extracts Config-Array in unsecure-mode!";
					foreach($config as $k=>$v) $this->$k = $v;
				}
			}


			// check vars for maxtry, secretposition and min-max-size
			$this->maxtry = ($this->maxtry > 9 || $this->maxtry < 1) ? 3 : $this->maxtry;
			$this->secretposition = ($this->secretposition > 32 || $this->secretposition < 1) ? $this->maxtry : $this->secretposition;
			if($this->minsize > $this->maxsize)
			{
				$temp = $this->minsize;
				$this->minsize = $this->maxsize;
				$this->maxsize = $temp;
				if($this->debug) echo "<br>-Captcha-Debug: Arrghh! What do you think I mean with min and max? Switch minsize with maxsize.";
			}


			// check TrueTypeFonts
			if(is_array($this->TTF_RANGE))
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Check given TrueType-Array! (".count($this->TTF_RANGE).")";
				$temp = array();
				foreach($this->TTF_RANGE as $k=>$v)
				{
					if(is_readable($this->TTF_folder.$v)) $temp[] = $v;
				}
				$this->TTF_RANGE = $temp;
				if($this->debug) echo "\n<br>-Captcha-Debug: Valid TrueType-files: (".count($this->TTF_RANGE).")";
				if(count($this->TTF_RANGE) < 1) die('No Truetypefont available for the CaptchaClass.');
			}
			else
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Check given TrueType-File! (".$this->TTF_RANGE.")";
				if(!is_readable($this->TTF_folder.$this->TTF_RANGE)) die('No Truetypefont available for the CaptchaClass.');
			}

			// select first TrueTypeFont
			$this->change_TTF();
			if($this->debug) echo "\n<br>-Captcha-Debug: Set current TrueType-File: (".$this->TTF_file.")";


			// get number of noise-chars for background if is enabled
			$this->nb_noise = $this->noise ? ($this->chars * $this->noisefactor) : 0;
			if($this->debug) echo "\n<br>-Captcha-Debug: Set number of noise characters to: (".$this->nb_noise.")";


			// set dimension of image
			$this->lx = ($this->chars + 1) * (int)(($this->maxsize + $this->minsize) / 1.5);
			$this->ly = (int)(2.4 * $this->maxsize);
			if($this->debug) echo "\n<br>-Captcha-Debug: Set image dimension to: (".$this->lx." x ".$this->ly.")";


			// set all messages
			// (if you add a new language, you also want to add a line to the function "notvalid_msg()" at the end of the class!)
			$this->messages = array(
				'de'=>array(
							'msg1'=>'Du muﬂt die <b>'.$this->chars.' Zeichen</b> im Bild, (Zahlen&nbsp;von&nbsp;<b>0&nbsp;-&nbsp;9</b> und Buchstaben&nbsp;von&nbsp;<b>A&nbsp;-&nbsp;F</b>),<br>in das Feld eintragen und das Formular abschicken um den Download zu starten.',
							'msg2'=>'Ohje, das kann ich nicht lesen. Bitte, generiere mir eine ',
							'buttontext'=>'abschicken',
							'refreshbuttontext'=>'neue ID'
							),
				'en'=>array(
							'msg1'=>'You must read and type the <b>'.$this->chars.' chars</b> within <b>0..9</b> and <b>A..F</b>, and submit the form.',
							'msg2'=>'Oh no, I cannot read this. Please, generate a ',
							'buttontext'=>'submit',
							'refreshbuttontext'=>'new ID'
							)
			);
			$this->msg1 = $this->messages[$this->lang]['msg1'];
			$this->msg2 = $this->messages[$this->lang]['msg2'];
			$this->buttontext = $this->messages[$this->lang]['buttontext'];
			$this->refreshbuttontext = $this->messages[$this->lang]['refreshbuttontext'];
			if($this->debug) echo "\n<br>-Captcha-Debug: Set messages to language: (".$this->lang.")";


			// keep params from original GET-request
			// (if you use POST or COOKIES, you have to implement it yourself, sorry.)
			$this->QUERY_STRING = strlen(trim($_SERVER['QUERY_STRING'])) > 0 ? '?'.strip_tags($_SERVER['QUERY_STRING']) : '';
			$refresh = $_SERVER['PHP_SELF'].$this->QUERY_STRING;
			if($this->debug) echo "\n<br>-Captcha-Debug: Keep this params from original GET-request: (".$this->QUERY_STRING.")";


			// check Postvars
			if(isset($_POST['public_key']))  $this->public_K = substr(strip_tags($_POST['public_key']),0,$this->chars);
			if(isset($_POST['private_key'])) $this->private_K = substr(strip_tags($_POST['private_key']),0,$this->chars);
			$this->current_try = isset($_POST['hncaptcha']) ? $this->get_try() : 0;
			if(!isset($_POST['captcharefresh'])) $this->current_try++;
			if($this->debug) echo "\n<br>-Captcha-Debug: Check POST-vars, current try is: (".$this->current_try.")";


			// generate Keys
			$this->key = md5($this->secretstring);
			$this->public_key = substr(md5(uniqid(rand(),true)), 0, $this->chars);
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate Keys, public key is: (".$this->public_key.")";

		}



	////////////////////////////////
	//
	//	PUBLIC METHODS
	//

		/**
		  *
		  * @shortdesc displays a complete form with captcha-picture
		  * @public
		  * @type void
		  * @return HTML-Output
		  *
		  **/
		function display_form()
		{
			$try = $this->get_try(FALSE);
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate a string which contains current try: ($try)";
			$s  = '<div id="captcha">';
			$s .= '<form class="captcha" name="captcha1" action="'.$_SERVER['PHP_SELF'].$this->QUERY_STRING.'" method="POST">'."\n";
			$s .= '<input type="hidden" name="hncaptcha" value="'.$try.'">'."\n";
			$s .= '<p class="captcha_notvalid">'.$this->notvalid_msg().'</p>';
			$s .= '<p class="captcha_1">'.$this->display_captcha()."</p>\n";
			$s .= '<p class="captcha_1">'.$this->msg1.'</p>';
			$s .= '<p class="captcha_1"><input class="captcha" type="text" name="private_key" value="" maxlength="'.$this->chars.'" size="'.$this->chars.'">&nbsp;&nbsp;';
			$s .= '<input class="captcha" type="submit" value="'.$this->buttontext.'">'."</p>\n";
			$s .= '</form>'."\n";
			if($this->refreshlink)
			{
				$s .= '<form style="display:inline;" name="captcha2" action="'.$_SERVER['PHP_SELF'].$this->QUERY_STRING.'" method="POST">'."\n";
				$s .= '<input type="hidden" name="captcharefresh" value="1"><input type="hidden" name="hncaptcha" value="'.$try.'">'."\n";
				$s .= '<p class="captcha_2">'.$this->msg2;
				$s .= $this->public_key_input().'<input class="captcha" type="submit" value="'.$this->refreshbuttontext.'">'."</p>\n";
				$s .= '</form>'."\n";
			}
			$s .= '</div>';
			if($this->debug) echo "\n<br>-Captcha-Debug: Output Form with captcha-image.<br><br>";
			return $s;
		}


		/**
		  *
		  * @shortdesc validates POST-vars and return result
		  * @public
		  * @type integer
		  * @return 0 = first call | 1 = valid submit | 2 = not valid | 3 = not valid and has reached maximum try's
		  *
		  **/
		function validate_submit()
		{
			if($this->check_captcha($this->public_K,$this->private_K))
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (1)";
				return 1;
			}
			else
			{
				if($this->current_try > $this->maxtry)
				{
					if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (3)";
					return 3;
				}
				elseif($this->current_try > 0)
				{
					if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (2)";
					return 2;
				}
				else
				{
					if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (0)";
					return 0;
				}
			}
		}



	////////////////////////////////
	//
	//	PRIVATE METHODS
	//

		/** @private **/
		function display_captcha($onlyTheImage=FALSE)
		{
			$this->make_captcha();
			$is = getimagesize($this->get_filename());
			if($onlyTheImage) return "\n".'<img class="captchapict" src="'.$this->get_filename_url().'" '.$is[3].' alt="This is a captcha-picture. It is used to prevent mass-access by robots. (see: www.captcha.net)" title="">'."\n";
			else return $this->public_key_input()."\n".'<img class="captchapict" src="'.$this->get_filename_url().'" '.$is[3].' alt="This is a captcha-picture. It is used to prevent mass-access by robots. (see: www.captcha.net)" title="">'."\n";
		}

		/** @private **/
		function public_key_input()
		{
			return '<input type="hidden" name="public_key" value="'.$this->public_key.'">';
		}

		/** @private **/
		function make_captcha()
		{
			$private_key = $this->generate_private();
            $this->private_key = $private_key;
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate private key: ($private_key)";

			// create Image and set the apropriate function depending on GD-Version & websafecolor-value
			if($this->gd_version >= 2 && !$this->websafecolors)
			{
				$func1 = 'imagecreatetruecolor';
				$func2 = 'imagecolorallocate';
			}
			else
			{
				$func1 = 'imageCreate';
				$func2 = 'imagecolorclosest';
			}
			$image = $func1($this->lx,$this->ly);
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate ImageStream with: ($func1())";
			if($this->debug) echo "\n<br>-Captcha-Debug: For colordefinitions we use: ($func2())";


			// Set Backgroundcolor
			$this->random_color(224, 255);
			$back =  @imagecolorallocate($image, $this->r, $this->g, $this->b);
			@ImageFilledRectangle($image,0,0,$this->lx,$this->ly,$back);
			if($this->debug) echo "\n<br>-Captcha-Debug: We allocate one color for Background: (".$this->r."-".$this->g."-".$this->b.")";

			// allocates the 216 websafe color palette to the image
			if($this->gd_version < 2 || $this->websafecolors) $this->makeWebsafeColors($image);


			// fill with noise or grid
			if($this->nb_noise > 0)
			{
				// random characters in background with random position, angle, color
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with noise: (".$this->nb_noise.")";
				for($i=0; $i < $this->nb_noise; $i++)
				{
					srand((double)microtime()*1000000);
					$size	= intval(rand((int)($this->minsize / 2.3), (int)($this->maxsize / 1.7)));
					srand((double)microtime()*1000000);
					$angle	= intval(rand(0, 360));
					srand((double)microtime()*1000000);
					$x		= intval(rand(0, $this->lx));
					srand((double)microtime()*1000000);
					$y		= intval(rand(0, (int)($this->ly - ($size / 5))));
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					srand((double)microtime()*1000000);
					$text	= chr(intval(rand(45,250)));
					@ImageTTFText($image, $size, $angle, $x, $y, $color, $this->change_TTF(), $text);
				}
			}
			else
			{
				// generate grid
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with x-gridlines: (".(int)($this->lx / (int)($this->minsize / 1.5)).")";
				for($i=0; $i < $this->lx; $i += (int)($this->minsize / 1.5))
				{
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					@imageline($image, $i, 0, $i, $this->ly, $color);
				}
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with y-gridlines: (".(int)($this->ly / (int)(($this->minsize / 1.8))).")";
				for($i=0 ; $i < $this->ly; $i += (int)($this->minsize / 1.8))
				{
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					@imageline($image, 0, $i, $this->lx, $i, $color);
				}
			}

			// generate Text
			if($this->debug) echo "\n<br>-Captcha-Debug: Fill forground with chars and shadows: (".$this->chars.")";
			for($i=0, $x = intval(rand($this->minsize,$this->maxsize)); $i < $this->chars; $i++)
			{
				$text	= strtoupper(substr($private_key, $i, 1));
				srand((double)microtime()*1000000);
				$angle	= intval(rand(($this->maxrotation * -1), $this->maxrotation));
				srand((double)microtime()*1000000);
				$size	= intval(rand($this->minsize, $this->maxsize));
				srand((double)microtime()*1000000);
				$y		= intval(rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));
				$this->random_color(0, 127);
				$color	=  $func2($image, $this->r, $this->g, $this->b);
				$this->random_color(0, 127);
				$shadow = $func2($image, $this->r + 127, $this->g + 127, $this->b + 127);
				@ImageTTFText($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $this->change_TTF(), $text);
				@ImageTTFText($image, $size, $angle, $x, $y - (int)($size / 15), $color, $this->TTF_file, $text);
				$x += (int)($size + ($this->minsize / 5));
			}
			@ImageJPEG($image, $this->get_filename(), $this->jpegquality);
			$res = file_exists($this->get_filename());
			if($this->debug) echo "\n<br>-Captcha-Debug: Safe Image with quality [".$this->jpegquality."] as (".$this->get_filename().") returns: (".($res ? 'TRUE' : 'FALSE').")";
			@ImageDestroy($image);
			if($this->debug) echo "\n<br>-Captcha-Debug: Destroy Imagestream.";
			if(!$res) die('Unable to safe captcha-image.');
		}

		/** @private **/
		function makeWebsafeColors(&$image)
		{
			//$a = array();
			for($r = 0; $r <= 255; $r += 51)
			{
				for($g = 0; $g <= 255; $g += 51)
				{
					for($b = 0; $b <= 255; $b += 51)
					{
						$color = imagecolorallocate($image, $r, $g, $b);
						//$a[$color] = array('r'=>$r,'g'=>$g,'b'=>$b);
					}
				}
			}
			if($this->debug) echo "\n<br>-Captcha-Debug: Allocate 216 websafe colors to image: (".imagecolorstotal($image).")";
			//return $a;
		}

		/** @private **/
		function random_color($min,$max)
		{
			srand((double)microtime() * 1000000);
			$this->r = intval(rand($min,$max));
			srand((double)microtime() * 1000000);
			$this->g = intval(rand($min,$max));
			srand((double)microtime() * 1000000);
			$this->b = intval(rand($min,$max));
			//echo " (".$this->r."-".$this->g."-".$this->b.") ";
		}

		/** @private **/
		function change_TTF()
		{
			if(is_array($this->TTF_RANGE))
			{
				srand((float)microtime() * 10000000);
				$key = array_rand($this->TTF_RANGE);
				$this->TTF_file = $this->TTF_folder.$this->TTF_RANGE[$key];
			}
			else
			{
				$this->TTF_file = $this->TTF_folder.$this->TTF_RANGE;
			}
			return $this->TTF_file;
		}

		/** @private **/
		function check_captcha($public,$private)
		{
				$res = 'FALSE';
				// when check, destroy picture on disk
				if(file_exists($this->get_filename($public)))
				{
						$res = @unlink($this->get_filename($public)) ? 'TRUE' : 'FALSE';
						if($this->debug) echo "\n<br>-Captcha-Debug: Delete image (".$this->get_filename($public).") returns: ($res)";
						$res = (strtolower($private)==strtolower($this->generate_private($public))) ? 'TRUE' : 'FALSE';
						if($this->debug) echo "\n<br>-Captcha-Debug: Comparing public with private key returns: ($res)";
				}
				return $res == 'TRUE' ? TRUE : FALSE;
		}
			/* OLD FUNCTION, without HotFix from Daniel Jagszent :
				function check_captcha($public,$private)
				{
					// when check, destroy picture on disk
					if(file_exists($this->get_filename($public)))
					{
						$res = @unlink($this->get_filename($public)) ? 'TRUE' : 'FALSE';
						if($this->debug) echo "\n<br>-Captcha-Debug: Delete image (".$this->get_filename($public).") returns: ($res)";
					}
					$res = (strtolower($private)==strtolower($this->generate_private($public))) ? 'TRUE' : 'FALSE';
					if($this->debug) echo "\n<br>-Captcha-Debug: Comparing public with private key returns: ($res)";
					return $res == 'TRUE' ? TRUE : FALSE;
				}
			*/

		/** @private **/
		function get_filename($public="")
		{
			if($public=="") $public=$this->public_key;
			return $this->tempfolder.$public.".jpg";
		}

		/** @private **/
		function get_filename_url($public="")
		{
			if($public=="") $public = $this->public_key;
			return str_replace($_SERVER['DOCUMENT_ROOT'],'',$this->tempfolder).$public.".jpg";
		}

		/** @private **/
		function get_try($in=TRUE)
		{
			$s = array();
			for($i = 1; $i <= $this->maxtry; $i++) $s[$i] = $i;

			if($in)
			{
				return (int)substr(strip_tags($_POST['hncaptcha']),($this->secretposition -1),1);
			}
			else
			{
				$a = "";
				$b = "";
				for($i = 1; $i < $this->secretposition; $i++)
				{
					srand((double)microtime()*1000000);
					$a .= $s[intval(rand(1,$this->maxtry))];
				}
				for($i = 0; $i < (32 - $this->secretposition); $i++)
				{
					srand((double)microtime()*1000000);
					$b .= $s[intval(rand(1,$this->maxtry))];
				}
				return $a.$this->current_try.$b;
			}
		}

		/** @private **/
		function get_gd_version()
		{
			static $gd_version_number = null;
			if($gd_version_number === null)
			{
			   ob_start();
			   phpinfo(8);
			   $module_info = ob_get_contents();
			   ob_end_clean();
			   if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
			   {
				   $gd_version_number = $matches[1];
			   }
			   else
			   {
				   $gd_version_number = 0;
			   }
			}
			return $gd_version_number;
		}

		/** @private **/
		function generate_private($public="")
		{
			if($public=="") $public = $this->public_key;
			$key = substr(md5($this->key.$public), 16 - $this->chars / 2, $this->chars);
			return $key;
		}

		/**
		  *
		  * @shortdesc returns a message if the form validation has failed
		  * @private
		  * @type string
		  * @return string message or blankline as placeholder
		  *
		  **/
		function notvalid_msg()
		{
			// blank line for all languages
			if($this->current_try == 1) return '&nbsp;<br>&nbsp;';

			// invalid try's: en
			if($this->lang == "en" && $this->current_try > 2 && $this->refreshlink) return 'No valid entry. Please try again:<br>Tipp: If you cannot identify the chars, you can generate a new image!';
			if($this->lang == "en" && $this->current_try >= 2) return 'No valid entry. Please try again:<br>&nbsp;';

			// invalid try's: de
			if($this->lang == "de" && $this->current_try > 2 && $this->refreshlink) return 'Die Eingabe war nicht korrekt.<br>Tipp: Wenn Du die Zeichen nicht erkennen kannst, generiere neue mit dem Link unten!';
			if($this->lang == "de" && $this->current_try >= 2) return 'Die Eingabe war nicht korrekt. Bitte noch einmal versuchen:<br>&nbsp;';

		}


} // END CLASS hn_CAPTCHA

?>