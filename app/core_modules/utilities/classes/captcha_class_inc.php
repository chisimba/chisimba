<?php
/* --------------------------- engine class ------------------------*/

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to generate CAPTCHA images
 *
 * This class is a wrapper to the one written by Horst Nogajski
 * http://hn273.users.phpclasses.org/browse/package/1569.html
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Tohir Solomons
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * Usage:
 *     $objCaptcha = $this->getObject('captcha', 'utilities');
 *     echo $objCaptcha->show();
 *
 * Instantiate this class, give it another [name] if required, and run the show() method.
 *
 * On your validation, check that [user input] = md5(strtoupper[name])
 *
 */

class captcha extends object
{

    /**
    * @var string Name of the Captcha Hidden Input;
    */
    public $name='captcha';
    
    /**
    * @var array $fonts List of Fonts Available
    * Set as private so that users can't use own fonts for timebeing
    */
	private $fonts = array('Base02.ttf');
    
    /**
    * @var int $numChars Number of Characters in captcha
    */
    public $numChars = 5;
    
    /**
    * @var int $minFontSize Minimum Size of Font
    */
    public $minFontSize = 20;
    
    /**
    * @var int $maxFontSize Maximum Size of Font
    */
    public $maxFontSize = 30;
    
    /**
    * @var int $maxRotation Maximum Rotation of Font
    */
    public $maxRotation = 15;
    
    /**
    * @var boolean $useNoise Use Noisy background or Grid
    */
    public $useNoise = TRUE; // If False, will show with grid in the background
    
    /**
    * @var boolean $useWebSafeColors Use Web Safe Colors or Not
    */
    public $useWebSafeColors = TRUE;
    
    /**
    * @var boolean $debug Enable debug mode
    */
    public $debug = FALSE;
    
    /**
    * @var string $tempFolder Directory to store Captcha Images in
    */
    private $tempFolder;
    
    /**
    * Standard init function
    *
    */
	function init()
    {
        require_once($this->getResourcePath('captcha/hn_captcha.class.php'));
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objMkdir = $this->getObject('mkdir', 'files');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        
        // Check whether directory exists
        $path = $this->objConfig->getcontentBasePath().'/captcha/';
        $this->objCleanUrl->cleanUpUrl($path);
        
        $this->objMkdir->mkdirs($path);
        
        $this->tempFolder = $this->objConfig->getcontentBasePath().'/captcha/';
        $this->objCleanUrl->cleanUpUrl($this->tempFolder);
        
        $this->localFolder = $this->objConfig->getcontentPath().'/captcha/';
        $this->objCleanUrl->cleanUpUrl($this->localFolder);
        
        $this->loadClass('hiddeninput', 'htmlelements');
	}
    
    /** 
    * This is a method to display the CAPTCHA images
    * 
    */
    function show()
    {
        
        
        // Creat an array
        $CAPTCHA_INIT = array(
            'tempfolder'     => $this->tempFolder,      // string: absolute path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
            'localfolder'     => $this->localFolder,      // string: relative path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
			'TTF_folder'     => $this->getResourcePath('captcha/'), // string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.
                                // mixed (array or string): basename(s) of TrueType-Fontfiles
			'TTF_RANGE'      => $this->fonts,
		//	'TTF_RANGE'      => 'COMIC.TTF',

            'chars'          => $this->numChars,      // integer: number of chars to use for ID
            'minsize'        => $this->minFontSize,   // integer: minimal size of chars
            'maxsize'        => $this->maxFontSize,   // integer: maximal size of chars
            'maxrotation'    => $this->maxRotation,   // integer: define the maximal angle for char-rotation, good results are between 0 and 30

            'noise'          => $this->useNoise,      // boolean: TRUE = noisy chars | FALSE = grid
            'websafecolors'  => $this->useWebSafeColors,   // boolean
            'lang'           => 'en',    // string:  ['en'|'de']
            'maxtry'         => 9,       // integer: [1-9]

            'badguys_url'    => '/',     // string: URL
            'secretstring'   => 'A very, very secret string which is used to generate a md5-key!',
            'secretposition' => 15,      // integer: [1-32]

            'debug'          => $this->debug
        );
        
        $captcha =& new hn_captcha($CAPTCHA_INIT);
        
        $image = $captcha->display_captcha(TRUE);
        $hiddenInput = new hiddeninput($this->name, md5(strtoupper($captcha->private_key)));
        $publicKey = new hiddeninput($this->name.'_publickey', $captcha->public_key);
        
        return $image.$hiddenInput->show().$publicKey->show();
    }


}
?>