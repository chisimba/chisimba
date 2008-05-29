<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

// end security check

/**
 * Toy class is used to create ascii art from images found on websites.
 * There is probably not many use cases for this, so do what you will with it
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @author based on code by Jonathan Ford
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       Any relevant section
 */

class asciiart extends object
{
	/**
	 * Headers to send out to browser etc
	 *
	 * @var string
	 */
	public $headers;

	/**
	 * The image that we are converting
	 *
	 * @var string
	 */
	public $image;

	/**
	 * URL to an image for url_fopen to use
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Default size
	 *
	 * @var integer
	 */
	public $size = 2;

	/**
     * Default image quality
     *
     * @var integer
     */
	public $quality = 2;

	/**
     * Default colour - yes or no? (true/false)
     *
     * @var boolean
     */
	public $color = false;

	/**
     * Characters used to create the image(s) in black and white
     * These are in order from lightest to darkest
     *
     * @var array
     */
	public $chars = array('@', '#', '+', '\'', ';', ':', ',', '.', '`', ' ');

	/**
     * Block character for colour pictures
     *
     * @var string
     */
	public $color_char = '#';

	/**
     * Constant for the max filesize of images that can be used with this class
     * Make sure you keep this below the memory_limit specified in php.ini
     *
     */
	const max_filesize = 6000000;

	public function init()
	{

	}

	/**
	 * public method to get the url image and start the whole deal 
	 *
	 * @param string $url
	 * @return void
	 * @access public
	 */
	public function getUrl($url)
	{
		$this->url = $url;
		// We only want to get the headers, so do a HEAD request instead of GET (default)
		$opts = array(
			'http' => array(
			'method' => 'HEAD'
			)
		);

		$context = stream_context_get_default($opts);
		$this->headers = get_headers($this->url, 1);
		if(strstr($this->headers[0], '200') !== false)
		{
			if($this->headers['Content-Length'] < self::max_filesize) // Check that the file isn't too big
			{
				if($this->is_image($this->headers['Content-Type']) !== false) // Makes sure that a content type was specified
				{
					// Pretty self-explanatory - figure out which sort of image we're going to be processing and let GD know
					switch($this->headers['Content-Type'])
					{
						case image_type_to_mime_type(IMAGETYPE_GIF):
							$this->image = imagecreatefromgif($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_JPEG):
							$this->image = imagecreatefromjpeg($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_PNG):
							$this->image = imagecreatefrompng($this->url);
							break;
							/*case image_type_to_mime_type(IMAGETYPE_BMP):
							$this->image = $this->imagecreatefrombmp($this->url);
							break;*/
						case image_type_to_mime_type(IMAGETYPE_WBMP):
							$this->image = imagecreatefromwbmp($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_XBM):
							$this->image = imagecreatefromxbm($this->url);
							break;
						default:
							throw new customException($this->objLanguage->languageText('mod_utilties_unknown_format', 'utilities')); //die('Something\'s gone horribly wrong...'); // If this happens scream very loudly and bang your head into a wall
							break;
					}
				}
				else
				{
					throw new customException($this->objLanguage->languageText('mod_utilties_unknown_format_cannotbedetermined', 'utilities'));
				}
			}
			else
			{
				throw new customException($this->objLanguage->languageText('mod_utilties_filetoobig', 'utilities') . round(self::max_filesize / 1024) . 'KB.');
			}
		}
		else
		{
			throw new customException($this->objLanguage->languageText('mod_utilties_urlnoaccess', 'utilities') . $this->url);
		}
	}

	/**
	 * Figure out if the file is in a (GD) supported file format or not
	 * 
	 * @access public
	 * @param string
	 * @return boolean
	 */
	public function is_image($content_type)
	{
		switch($content_type)
		{
			case image_type_to_mime_type(IMAGETYPE_GIF):
			case image_type_to_mime_type(IMAGETYPE_JPEG):
			case image_type_to_mime_type(IMAGETYPE_PNG):
				//case image_type_to_mime_type(IMAGETYPE_BMP): BMP doesn't work (yet?) :-(
			case image_type_to_mime_type(IMAGETYPE_WBMP):
			case image_type_to_mime_type(IMAGETYPE_XBM):
				return true;
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * Draw the ASCII art. Color support is implemented really badly
	 *
	 * @param string $img
	 * @return string
	 */
	public function draw($img = '')
	{
		if(empty($img) === true) 
		{
			$img = $this->image; // Make sure there's *something* in the image
		}

		$width = imagesx($img); // Work out the width
		$height = imagesy($img); // Work out the height

		// If we're working in colour start our <span>s
		if($this->color === true)
		{
			$pixel_color = imagecolorat($img, 1, 1);
			$rgb = imagecolorsforindex($img, $pixel_color);
			$output = '<span style="color: ' . $this->rgbtohex($rgb['red'], $rgb['green'], $rgb['blue']) . ';">';
		}
		else
		{
			$output = '';
		}

		// Start looping through pixels working out how bright/colorful they are. I suppose this probably should be stuck into an array before we sort out the output
		for($y = 0; $y < $height; $y = $y + $this->quality)
		{
			for($x = 0; $x < $width; $x = $x + $this->quality)
			{
				$pixel_color = imagecolorat($img, $x, $y); // Get pixel color at x,y
				$rgb = imagecolorsforindex($img, $pixel_color); // Make the color into an array we can use

			/**
	 * The image that we are converting
	 *
	 * @var string
	 */	// Do some more color processing stuff
				if($this->color === true)
				{
					// Work out if the last pixel is the same as this one
					if($x > $this->quality && $y > $this->quality && $pixel_color == imagecolorat($img, $x - $this->quality, $y))
					{
						$char = $this->color_char;
					}
					// Or if it's not...
					else
			/**
	 * The image that we are converting
	 *
	 * @var string
	 */		{
						$char = '</span><span style="color: ' . $this->rgbtohex($rgb['red'], $rgb['green'], $rgb['blue']) . ';">#';
					}
				}
			/**
	 * The image that we are converting
	 *
	 * @var string
	 */	// Work out the "brighness" by adding up the RGB values and doing some division
				else
				{
					$brightness = $rgb['red'] + $rgb['green'] + $rgb['blue'];
					$brightness = round($brightness / (765 / (count($this->chars) - 1)));
					$char = $this->chars[$brightness];
				}
				$output .= $char;
			}
			$output .= "\n"; // Newline, might need adjusting on Windows systems (though *seems* to work OK, at least in a browser)
		}

		// Close our colorfulness
		if($this->color === true)
		{
			$output .= '</span>';
		}

		return $output;
	}

	/**
	 * Converts RGB (red, green, blue) values to their hex equivalent (for HTML)
	 *
	 * @param string $red
	 * @param string $green
	 * @param string $blue
	 * @return string
	 */
	public function rgbtohex($red, $green, $blue)
	{
		$hex = '#';
		$hex .= str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
		$hex .= str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
		$hex .= str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);
		return($hex);
	}

	// Lets clean up after ourselves
	public function __destruct()
	{
		imagedestroy($this->image); // Remove the image from the memory (important with some configurations)
	}

}
?>