<?php
/**
*
* Class for returning an avatar from gravatar.com
* A gravatar Globally Recognized AVATAR, which is a dynamic image 
* resource that is requested from the gravatar server. The request 
* URL always begins with 
*     http://www.gravatar.com/avatar.php?
* followed by a mandatory parameter named "gravatar_id". It's value 
* is the hexadecimal MD5 hash of the requested user's email address 
* with all whitespace trimmed. The value is case insensitive.
* 
* Thus a call to the gravatar of a user looks something like
*      http://www.gravatar.com/avatar.php?gravatar_id=279aa12c3326f87c460aa4f31d18a065
*
* Since some GRAVATARS may contain nudity and other potentially restricted content,
* an optional "rating" parameter may follow with a value of [ G | PG | R | X ] that 
* determines the highest rating (inclusive) that will be returned. This takes the form
* &rating=R
* 
* An optional "size" parameter may follow that specifies the desired width and 
* height of the gravatar. Valid values are from 1 to 80 inclusive. Any size 
* other than 80 will cause the original gravatar image to be downsampled using 
* bicubic resampling before output.
* 
* &size=40 
* 
* An optional "default" parameter may follow that specifies the full
* , URL encoded URL, protocol included, of a GIF, JPEG, or PNG image 
* that should be returned if either the requested email address has 
* no associated gravatar, or that gravatar has a rating higher than is 
* allowed by the "rating" parameter.
* 
* &default=http%3A%2F%2Fwww.example.com%2Fsomeimage.jpg
* 
* @usage 
*   
*
* @package gravatar
* @author Derek Keats
*
*/
class getgravatar extends object {
    
    /**
    * 
    * @var $objLanguage String object property for holding the 
    * language object
    * @access private
    * 
    */
    public $objLanguage;
    
    /**
    * 
    * The base URL for the gravatar site including the 
    * querystring delimiter ?
    * @access private
    * 
    */
    private $gravatarBase;
    
    public $rating=NULL;
    public $defaultavatar=NULL;
    public $avatarSize=NULL;
    public $gravatarLink;
    public $gravatarId;
	public $objConfig;

    /**
    *
    * Constructor method to instantiate the language 
    * object and setup the base URL for gravatar, as well
    * as check settings and set parameters accordingly
    *
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        //Set the URL to the gravatar site + the querystring delimiter
        $this->gravatarBase = "http://www.gravatar.com/avatar.php?";
        //Create the configuration object
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        //Get the ratings allowed from the config
        $this->rating = $this->getRating();
        //Get the default avatar from config
        $this->defaultavatar = $this->getDefaultavatar();
        //Get the avatar size from config
        $this->avatarSize = $this->getavatarSize();
    }
    
    /**
    * 
    * Get the rating from the configuration.
    * @return string The rating of NULL if the ratind is NONE
    *  
    */
    public function getRating()
    {
        $rating = $this->objConfig->getValue('mod_gravatar_rating', 'gravatar');
        if ($rating=="NONE") {
            return NULL;
        } else {
        	$validRatings = array("G", "PG", "R", "X");
        	if (in_array($rating, $validRatings)) {
        	    return $rating;
        	} else {
        		//Default to G if the value stored is invalid
        	    return "G";
        	}
        }
    }
    
    /**
    * 
    * Get the rating from the configuration.
    * @return string The rating of NULL if the ratind is NONE
    *  
    */
    public function getDefaultavatar()
    {
        $avatar = $this->objConfig->getValue('mod_gravatar_default', 'gravatar');
        if ($avatar=="DEFAULT" || $avatar=="NONE") {
            return NULL;
        } else {
       	    return $avatar;
        }
    }
    
    /**
    * 
    * Get the rating from the configuration.
    * @return string The rating of NULL if the ratind is NONE
    *  
    */
    public function getAvatarSize()
    {
        $size = $this->objConfig->getValue('mod_gravatar_size', 'gravatar');
        if ($size=="DEFAULT" || $avatar=="NONE") {
            return NULL;
        } else {
       	    return $size;
        }
    }
    
    /**
     * 
     * Show method to build the menu return it for rendering
     * @access public
     * @return string The avatar formatted as an image
     * 
     */
    public function show($email)
    {
 		$this->gravatarId = md5($email);
 		$ret = $this->gravatarBase ."gravatar_id="
 		  . $this->gravatarId . "{EXTRA}";
 		//Determine if extra parameters should be passed
 		$extra="";
 		if ($this->rating !== NULL) {
 		    $extra .= "&rating=" . $this->rating;
 		}
 		if ($this->defaultavatar !== NULL) {
 		    $extra .= "&default=" . $this->defaultavatar;
 		}
 		if ($this->avatarSize !== NULL) {
 		    $extra .= "&size=" . $this->avatarSize;
 		}
 		$ret = str_replace("{EXTRA}", $extra, $ret);
 		$this->gravatarLink = str_replace("&", "&amp;", $ret);
 		$ret = "<img src=\"" . $this->gravatarLink . "\" alt=\"\" />";
        return $ret;
    }
  
}  #end of class
?>