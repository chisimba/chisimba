<?
/**
* User Admin Functionality Class
*
* This class is a revised version of useradmin_model also found in this module.
* DO NOT use this class. It will replace useradmin_model and be removed afterwards
*
* Still work in progress
* 
* @author Tohir Solomons
*/
class useradmin_model2 extends dbtable
{

    public $objConfig;
    private $objUser;
    private $objLanguage;

    public function init()
    {
	    parent::init('tbl_users');
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->objUser=&$this->getObject('user','security');
        $this->objLanguage=&$this->getObject('language','language');
    }


    public function getUserDetails($id)
    {
        return $this->getRow('id', $id);
    }
    
    public function removeUserImage($id)
    {
        $image = $this->objConfig->getsiteRootPath().'/user_images/'.$id.'.jpg';
        
        if (file_exists($image)) {
            unlink($image);
        }
        
        $image = $this->objConfig->getsiteRootPath().'/user_images/'.$id.'_small.jpg';
        
        if (file_exists($image)) {
            unlink($image);
        }
        
        return;
    }
    
    function updateUserDetails($id, $firstname, $surname, $title, $email, $sex, $country, $password='')
    {
        $userArray = array(
                'firstname' => $firstname,
                'surname' => $surname,
                'title' => $title,
                'emailaddress' => $email,
                'sex' => $sex,
                'country' => $country
            );
        
        if ($password != '') {
            $userArray['pass'] = sha1($password);
        }
        
        return $this->update('id', $id, $userArray);
    }
    
    function getUsers($letter, $field, $orderby='', $inactive=TRUE)
    {
        $whereArray = array();
        if ($letter != 'listall') {
            $whereArray[] = $field.' LIKE \''.$letter.'%\'';
        }
        
        if (!$inactive) {
            $whereArray[] = ' isactive=\'1\'';
        }
        
        if (count($whereArray) == 0) {
            $where = '';
        } else {
            $and = '';
            $where = ' WHERE ';
            
            foreach ($whereArray as $clause)
            {
                $where .= $and.' ('.$clause.')';
                $and = ' AND ';
            }
        }
        
        if ($orderby != '') {
            $orderby = ' ORDER BY '.$orderby;
        }
        
        //echo $where.$orderby;
        return $this->getAll($where.$orderby);
    }
} // end of class sqlUsers

?>