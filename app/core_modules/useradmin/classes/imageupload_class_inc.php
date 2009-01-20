<?php
/**
* Handles upload of images.
* @author James Scoble
* @author Jeremy O'Connor
* @copyright 2004, 2006 AVOIR
* @license GNU GPL
*/
class imageupload extends object
{
    public $objConfig;
    private $objUser;
    /**
    * @var string The path of the file.
    */
    private $imagePath;
    /**
    * @var string The URL of the file.
    */
    private $imageUrl;

    public function init()
    {
        $this->objConfig=$this->getObject('altconfig','config');
        $this->objUser=$this->getObject('user','security');
        $this->imagePath = $this->objConfig->getsiteRootPath().'/user_images/';
        $this->imageUrl = $this->objConfig->getsiteRoot().'user_images/';
    }

    /**
    * Upload the file and resize it.
    * @param string $redim
    * @param string 4extra
    */
    public function doUpload($userId, $redim=120, $extra='')
    {
        $name=$_FILES['userFile']['name'];
        $type=$_FILES['userFile']['type'];
        $size=$_FILES['userFile']['size'];
        $tmp_name=$_FILES['userFile']['tmp_name'];

        if (
            ($type=='image/jpeg')
            ||($type=='image/gif')
            ||($type=='image/png')
            ||($type=='image/bmp')
        ){
            $dirObj=$this->getObject('dircreate','utilities');
            $dirObj->makeFolder('user_images');
            $objResize=$this->getObject('imageresize', 'files');
            $objResize->setImg($tmp_name);
            
            if ($objResize->canCreateFromSouce) {
                $objResize->resize($redim, $redim);
                $objResize->store($this->imagePath.$userId.$extra.'.jpg');
            }
        }
    }

    /**
    * Return url to user's picture.
    * @param string $userId
    * @returns string The url
    */
    public function userpicture($userId)
    {
        if (file_exists($this->imagePath.$userId.".jpg")){
            return($this->imageUrl.$userId.".jpg");
        } else {
        //Include gravatar option if nothing has been uploaded
        		$grav_email = md5($this->objUser->email());
        		$grav_default = $this->imageUrl."default.jpg";
        		$grav_rating = 'G';
        		$grav_size = 130;
        		$grav_border = "000000";        		
        		$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".$grav_email."&default=".$grav_default."&size=".$grav_size."&border=".$grav_border."&rating=".$grav_rating;
        		return $grav_url;
            
        }
    }

    /**
    * Return url to user's small picture.
    * @param string $userId
    * @returns string The url
    */
    public function smallUserPicture($userId)
    {
        if (file_exists($this->imagePath.$userId."_small.jpg")){
            return($this->imageUrl.$userId."_small.jpg");
        } else {
            return ($this->imageUrl."default_small.jpg");
        }
    }

    /**
    * Reset user's picture.
    * @param string $userId
    */
    public function resetImage($userId)
    {
        if (file_exists($this->imagePath.$userId.".jpg")){
            @unlink($this->imagePath.$userId.".jpg");
        }
    }

}
?>
