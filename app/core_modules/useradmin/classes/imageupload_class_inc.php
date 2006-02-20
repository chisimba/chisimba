<?
/** 
* Class imageupload
* This class enables upload of images for useradmin
* @author James Scoble
* @version $Id$
* @copyright 2004
* @license GNU GPL
*/
class imageupload extends object
{
    var $imageFolder; // for the path on the filesystem of the file
    var $imageUrl;  // for the URL as seen from a webbrowse of the file
    var $objUser;
    var $objConfig;
   
    function init()
    {
        $this->objConfig=&$this->getObject('config','config');
        $this->imageFolder = $this->objConfig->siteRootPath().'/user_images/';
        $this->imageUrl = $this->objConfig->siteRoot().'user_images/';
        $this->objUser=&$this->getObject('user','security');
    }

    /**
    * method to upload the file, and resize it
    * This gets its info from the "superglobal" variable $_FILES
    * To resize the file it calls the resize class.
    * @param string $redim
    * @param string 4extra
    */
    function doUpload($redim=120,$extra='')
    {
        $userfile=$_FILES['userFile']['name'];
        $size=$_FILES['userFile']['size'];
        $type=$_FILES['userFile']['type'];
        $location=$_FILES['userFile']['tmp_name'];
        if (($type=='image/jpeg')||($type=='image/gif')||($type=='image/png')||($type=='image/bmp')){
            $dirObj=$this->getObject('dircreate','utilities');
            $dirObj->makeFolder('user_images');
            $newfile=$this->imageFolder.$this->objUser->userId().$extra.'.jpg';
                                                                                                                                             
            $icon=$this->getObject('resize');
            if ($icon->loadimage($location,$userfile)){
                $icon->size_auto($redim);
                $icon->setOutput('jpg');
                $icon->save($newfile);
            }
            //copy ($location,$newfile); // this is now done by the resize class
        }
    }

    /**
    * methods to supply url to user's picture
    * @param string $userId
    * @returns string url
    */
    function userpicture($userId)
    {
        if (file_exists($this->imageFolder.$userId.".jpg")){
            return($this->imageUrl.$userId.".jpg");
        } else {
            return ($this->imageUrl."default.jpg");
        }
    }
    
    function smallUserPicture($userId)
    {
        if (file_exists($this->imageFolder.$userId."_small.jpg")){
            return($this->imageUrl.$userId."_small.jpg");
        } else {
            return ($this->imageUrl."default_small.jpg");
        }
    }

    /**
    * method to reset user's picture
    * @param string $userId
    */
    function resetImage($userId)
    {
        if (file_exists($this->imageFolder.$userId.".jpg")){
            unlink($this->imageFolder.$userId.".jpg");
        }
    }
    
} // end of class
?>
