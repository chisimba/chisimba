<?php
/*
* This is the dbentirebook
* Module
*
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
 *
 * @package rimfhe
 * @version 0.1
 * @Copyright Aug2009
 * @author Ram
 */

class dbmastersstudents extends dbtable
{
    public $objUrl;
    public $mode;

    //method to define the table
    public function init()
    {
        parent::init('tbl_rimfhe_graduatemasters');
        $this->objUrl = $this->getObject('url', 'strings');
    }//end init()

    public function mastersStudents()
    {
        $id = $this->getParam('mastersid');//get record id when in edit mode
        $surname = $this->getParam('surname');
        $initials= $this->getParam('initials');
        $firstname= $this->getParam('firstname');
        $gender= $this->getParam('gender');
        $studnumber= $this->getParam('studnumber');
        $dept= $this->getParam('department');
        $faculty= $this->getParam('faculty');
        $thesis= $this->getParam('thesis');
        $supervisor1= $this->getParam('supervisor1');
        $supervisor2= $this->getParam('supervisor2');
        $supervisor3= $this->getParam('supervisor3');
        $affiliate1= $this->getParam('supaffiliate1');
        $affiliate2= $this->getParam('supaffiliate2');
        $affiliate3= $this->getParam('supaffiliate3');
        $degree= $this->getParam('degree');

        //check which author fieild is not empty
        if (!empty($supervisor1)){

            switch($affiliate1){
                case 'UWC Staff Member':
                    $supervisor1 ='<b>'.$supervisor1.'</b><br />';
                    break;
                case 'External Supervisor':
                    $supervisor1 = $supervisor1.'<br />';
                    break;
            }

        }
        if (!empty($supervisor2)){

            switch($affiliate2){
                case 'UWC Staff Member':
                    $supervisor2 ='<b>'.$supervisor2.'</b><br />';
                    break;
                case 'External Supervisor':
                    $supervisor2 = $supervisor2.'<br />';
                    break;
            }

        }
        if (!empty($supervisor3)){

            switch($affiliate3){
                case 'UWC Staff Member':
                    $supervisor3 ='<b>'.$supervisor3.'</b><br />';
                    break;
                case 'External Supervisor':
                    $supervisor3 = $supervisor3.'<br />';
                    break;
            }

        }

        $supervisorname = $supervisor1.$supervisor2.$supervisor3;
        $peerreview= $this->getParam('peerreview');

        $fields =array(
        'surname'=> $surname,
        'initials'=> $initials,
        'firstname' => $firstname,
        'gender'=> $gender,
        'regnumber'=> $studnumber,
        'deptschoool' => $dept,
        'faculty'=> $faculty,
        'thesistitle'=> $thesis,
        'supervisorname'=> $supervisorname,
        'degree' =>$degree
        );

        //if not edite mode, add record tp database
        if(empty($id)){
            //Cheeck if book with same title is already in the database
            $where = "WHERE thesistitle='".$thesis."'";
            $checkRecord = $this->getAll($where);
            if(count($checkRecord) > 0){
                return FALSE;
            }
            else{
                return $this->insert($fields);
            }
        }
        else{
            //update record
            return $this->update('id', $id, $fields);
        }
    }//end

    //This public method retrieves all the record form the table
    public function getAllMastersStudents()
    {
        return $this->getAll();
    }

    //This public method counts the Number of Masters Studenst in each Department
    public function displayByDepartment()
    {
        $query ="SELECT deptschoool, COUNT(*) AS countthesis FROM tbl_rimfhe_graduatemasters GROUP BY deptschoool";
        return $this->getArray($query);
    }

    //This public method counts the Number of Masters Studenst in each Faculty
    public function displayByFaculty()
    {
        $query ="SELECT faculty, COUNT(*) AS countthesis FROM tbl_rimfhe_graduatemasters GROUP BY faculty";
        return $this->getArray($query);
    }

    //This public method counts the totall number of Masters Graduates of the university
    public function totalMastersStudents()
    {
        $query ="SELECT COUNT(*) AS totalmastersstudents FROM tbl_rimfhe_graduatemasters";
        $result = $this->query($query);
        $return = $result[0]['totalmastersstudents'];
        return $return;
    }
}//end dbstaffmember
?>
