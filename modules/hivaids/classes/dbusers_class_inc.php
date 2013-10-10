<?php
/**
* dbusers class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbusers class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbusers extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_hivaids_users');
        $this->table = 'tbl_hivaids_users';
        $this->tblUser = 'tbl_users';
        $this->tblLogin = 'tbl_userloginhistory';
        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to add a new user
    *
    * @access public
    * @param string $id The row id of the user
    * @return void
    */
    public function addUser($userId = NULL, $id = NULL)
    {
        $fields = array();
        $fields['staff_student'] = $this->getParam('staff_student');
        $fields['course'] = $this->getParam('course');
        $fields['study_year'] = $this->getParam('yearstudy');
        $fields['language'] = $this->getParam('language');
        $fields['updated'] = $this->now();
        
        if(!empty($id)){
            $fields['modifierid'] = $this->userId;
            $this->update('id', $id, $fields);
        }else{
            $fields['user_id'] = $userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
    }
    
    /**
    * Method to get the list of registered users
    *
    * @access public
    * @return int the number of staff
    */
    public function getUserList($by = 'all')
    {
        $sql = "SELECT u.userid, u.username FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }

        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to get the list of groups - male, female, english, etc
    *
    * @access public
    * @return int the number of staff
    */
    public function getGroupList($by = 'all', $group = 'gender')
    {
        $data = array();
        $select = '*';
        $orderby = '';
        switch($group){
            case 'language':
                $select = 'hu.language as groupid, hu.language as name';
                $groupby = 'GROUP BY hu.language ORDER BY hu.language';
                break;
            
            case 'course':
                $select = 'hu.course as groupid, hu.course as name';
                $groupby = 'GROUP BY hu.course ORDER BY hu.course';
                break;
                
            case 'study_year':
                $select = 'hu.study_year as groupid, hu.study_year as name';
                $groupby = 'GROUP BY hu.study_year ORDER BY hu.study_year';
                break;
                
            case 'sex':
            default:
                $objLanguage = $this->getObject('language', 'language');
                $lbFemale = $objLanguage->languageText('word_female');
                $lbMale = $objLanguage->languageText('word_male');
                $data[] = array('groupid' => 'M', 'name' => $lbMale);
                $data[] = array('groupid' => 'F', 'name' => $lbFemale);
                return $data;
        }
        
        
        $sql = "SELECT {$select} FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id ";
            
        if($by != 'all'){
            $sql .= "AND hu.staff_student = '{$by}' ";
        }
        
        $sql .= $groupby;

//        echo $sql; die();

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to get the number of staff at the institution
    *
    * @access public
    * @return int the number of staff
    */
    public function getStaffInfo()
    {
        $sql = "SELECT count(user_id) AS cnt FROM {$this->table}
            WHERE staff_student = 'staff'";
            
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }
    
    /**
    * Method to get the number of students at the institution
    *
    * @access public
    * @return int the number of students
    */
    public function getStudentInfo()
    {
        $sql = "SELECT count(user_id) AS cnt FROM {$this->table}
            WHERE staff_student = 'student'";
            
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }
    
    /**
    * Method to get the login history count from the database along with the users details
    *
    * @access public
    * @return array $data
    */
    public function getLoginHistory()
    {
        $sql = "SELECT *, count(l.userid) AS logins, max(l.lastlogindatetime) AS laston
            FROM {$this->tblLogin} AS l, {$this->tblUser} AS u 
            LEFT JOIN {$this->table} AS h ON (u.userid = h.user_id)
            WHERE l.userid = u.userid
            GROUP BY l.userid
            ORDER BY u.surname";
            
        $data  = $this->getArray($sql);
        return $data;
        
        $sql="SELECT count(tbl_userloginhistory.userid) 
          AS logins, max(lastlogindatetime) 
          AS lastOn, tbl_users.title, tbl_users.firstname,
          tbl_users.surname, tbl_users.country, 
          tbl_users.emailaddress, tbl_users.userid FROM  tbl_userloginhistory
          LEFT JOIN tbl_users  ON tbl_userloginhistory.userid = tbl_users.userid
          GROUP BY tbl_userloginhistory.userid
          ORDER BY " . $order;
        return $this->getArray($sql);
    }
    
    /**
    * Method to get the total logged in users / students
    *
    * @access public
    * @param string $by Get gender for all or students only
    * @return array $data
    */
    public function getTotalUsers($by = 'all')
    {
        $sql = "SELECT count(u.userid) AS cnt FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }
            
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }

    /**
    * Method to get the gender split - no of males vs females
    *
    * @access public
    * @param string $by Get gender for all or students only
    * @return array $data
    */
    public function getGenderSplit($by = 'all')
    {
        $sql = "SELECT count(sex) AS cnt, sex FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }
        $sql .= " GROUP BY sex";
            
        $data = $this->getArray($sql);
        return $data;
    }
        
    /**
    * Method to get the total logged in users / students
    *
    * @access public
    * @param string $by Get gender for all or students only
    * @return array $data
    */
    public function getHomeLanguages($by = 'all')
    {
        $sql = "SELECT count(language) as cnt, language FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }
        
        $sql .= " GROUP BY language ORDER BY language";
            
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to get the year of study
    *
    * @access public
    * @param string $by Get gender for all or students only
    * @return array $data
    */
    public function getYearStudy($by = 'all')
    {
        $sql = "SELECT count(study_year) as cnt, study_year FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }
        
        $sql .= " GROUP BY study_year ORDER BY study_year";
            
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to get the courses
    *
    * @access public
    * @param string $by Get gender for all or students only
    * @return array $data
    */
    public function getCourses($by = 'all')
    {
        $sql = "SELECT count(course) as cnt, course FROM tbl_users u, tbl_hivaids_users hu
            WHERE u.userid = hu.user_id";
            
        if($by != 'all'){
            $sql .= " AND hu.staff_student = '{$by}'";
        }
        
        $sql .= " GROUP BY course ORDER BY course";
            
        $data = $this->getArray($sql);
        
        return $data;
    }
}
?>