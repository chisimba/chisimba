<?

/**
* Class to manage import of users from other systems
* @author James Scoble
*/
class importuserdata extends object
{

    private $userfields;
    private $fieldcount;
    private $objUser;
    private $objUserAdmin;
    private $objPassword;
    
    public function init()
    {
        $this->userfields=array('userId','username','firstname','surname','title','sex','emailAddress');
        $this->fieldcount=count($this->userfields);

        $this->objUser=&$this->getObject('user','security');
        $this->objUserAdmin=&$this->getObject('sqlusers','security');
        $this->objPassword=&$this->getObject('passwords','useradmin');
    }

    /**
    * This is a method to read data from a comma-delimited file
    * and parse it into an array for adding new users
    * @param string $file the filename and path to load
    * returns array $info
    */
    public function readCSV($file)
    {
        $info=array();
        $fp=fopen($file,'r');
        while ($line = fgetcsv($fp, 1024, ","))
        {
            if (count($line)==$this->fieldcount){
                $newline=array();
                $num=0;
                foreach ($this->userfields as $key)
                {
                    $newline[$key]=$line[$num];
                    $num++;
                }
                $info[]=$newline;
            }
        }
        fclose($fp);
        return $info;
    }

    public function batchImport($file)
    {
        $info=$this->readCSV($file);
        $data=array();
        foreach($info as $line)
        {
            $rstring=$this->importUser($line);
            if ($rstring!='exists'){
                list($id,$userId,$username,$firstname,$surname)=explode('|',$rstring);
                $data[]=array('id'=>$id,'userId'=>$userId,'username'=>$username,'firstname'=>$firstname,'surname'=>$surname);
            }
        }
        return $data;
    }
    
    /**
    * Method to add a user to the database with info from the CSV
    * It calls the AddUser method in the sqlusers class
    * @param array $line
    * @returns string $id the new id field of the user
    */
    public function importUser($line)
    {
        $userId=$line['userId'];
        $username=$line['username'];
        $surname=$line['surname'];
        $firstname=$line['firstname'];
        $email=$line['emailAddress'];
        
        if ($username==''){
            $username=$firstname[0].$surname;
        }
        if ($userId==''){
            $userId=rand(1000,9999).date('Ymdhis');
        }
        
        if ($this->checkForUser($username,$firstname,$surname,$email)){
            return 'exists';
        }
        
        if ($this->objUserAdmin->valueExists('username',$username)){
            $username=$firstname[0].$firstname[1].$surname;
            if ($this->objUserAdmin->valueExists('username',$username)){
                $username=$firstname[0].$surname.rand(100,999);
            }
        }
        
        $line['userId']=$userId;
        $line['firstName']=$line['firstname'];
        $username=str_replace(' ','',strtolower($username));
        $line['username']=$username;
        
        if ((!isset($line['password']))||($line['password']=='')){
            $line['password']=$this->objPassword->createPassword();
        }
        if (!isset($line['country'])){
            $line['country']='';
        }
        $line['howCreated']='import';
        $id=$this->objUserAdmin->addUser($line);

        // Finally, send back the data about the new user
        return $id.'|'.$userId.'|'.$username.'|'.$firstname.'|'.$surname;
    }
    
    /**
    * This method checks if a specific user is already in the database
    * It returns TRUE only if all the fields match
    * @param string $userId
    * @param string $username
    * @param string $firstname
    * @param string $surname
    * @param string $email
    * @returns Boolena TRUE|FALSE
    */
    public function checkForUser($username,$firstname,$surname,$email)
    {
        $data=$this->objUser->getAll("where username='$username' and firstname='$firstname' and surname='$surname' and emailAddress='$email'");
        $count=count($data);
        if ($count==0){
            return FALSE;
        } else {
            return TRUE;
        }
    }

}// end of class importuserdata
?>
