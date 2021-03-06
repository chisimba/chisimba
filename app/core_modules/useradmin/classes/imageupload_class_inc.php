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
    private $grav_enabled = TRUE;

    public function init()
    {
        $this->objConfig=$this->getObject('altconfig','config');
        $this->objUser=$this->getObject('user','security');
        $this->imagePath = $this->objConfig->getsiteRootPath().'/user_images/';
        $this->imageUrl = $this->objConfig->getsiteRoot().'user_images/';
        $this->imageUri = 'user_images/';
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->grav_enabled = $this->objSysConfig->getValue('enable_gravitar', 'useradmin');
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
                $extension = substr($name, -3);
                $ext = ($extension == 'gif' || $extension == 'png') ? 'png' : 'jpg';
                $objResize->store($this->imagePath.$userId.$extra.$ext);
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
        $path = $_SERVER ['PHP_SELF'];
        $path = str_replace("/index.php", "", $path);
        
        if (file_exists($this->imageUri.$userId.".png"))
        {
            return($this->imageUri.$userId.".png");
        }
        elseif (file_exists($this->imageUri.$userId.".jpg"))
        {
            return($this->imageUri.$userId.".jpg");
        } elseif($this->grav_enabled == 'TRUE') {
            //Include gravatar option if nothing has been uploaded
            $grav_email = md5(strtolower($this->objUser->email()));
            $grav_default = urlencode($this->objConfig->getsiteRoot().$this->imageUri."default.jpg");
            $grav_size = 130;
            $grav_url = "http://www.gravatar.com/avatar/".$grav_email."?default=".$grav_default."&size=".$grav_size;
            return $grav_url;
        }
        else {
            return $this->imageUri."default.jpg";
        }
    }

    /**
    * Return url to user's small picture.
    * @param string $userId
    * @returns string The url
    */
    public function smallUserPicture($userId)
    {
        if (file_exists($this->imagePath.$userId."_small.png"))
        {
            return($this->imageUri.$userId."_small.png");
        }
        elseif (file_exists($this->imagePath.$userId."_small.jpg"))
        {
            return($this->imageUri.$userId."_small.jpg");
        }
        else
        {
            return ($this->imageUri."default_small.jpg");
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
        if (file_exists($this->imagePath.$userId.".png")){
            @unlink($this->imagePath.$userId.".png");
        }
    }

}
?>
