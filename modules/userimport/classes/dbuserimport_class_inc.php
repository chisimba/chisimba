<?php
/* ----------- data class extends dbTable for tbl_importusers------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Model class for the table tbl_importusers
* @author James Scoble
*/
class dbuserimport extends dbTable
{
    // Standard Handles for user, sqlusers and config objects
    var $objUser;
    var $objUserAdmin;
    var $objConfig;

    // Output variables
    var $export;
    var $exportName;

    // Groups class and XML class
    var $objContextGroups;
    var $objSerialXML;

    /**
    * Constructor method to define the table
    */
    function init()
    {
        parent::init('tbl_importusers');
        $this->objUser=$this->getObject('user','security');
        $this->objUserAdmin=$this->getObject('useradmin_model2','security');
        $this->objConfig=$this->getObject('altconfig','config');

    }

    /**
    * method to load up a batch of new users
    * @param string $adminId
    * @param string $courseCode
    * @param array $newInfo
    * @param string $importMethod
    * @param string $batchCode
    * @returns string $batchCode
    */
    function addBatch($adminId,$courseCode,$newInfo,$importMethod,$batchCode='auto')
    {
        $this->objContextGroups=$this->getObject('managegroups','contextgroups');
        $this->objAdminGroups=$this->getObject('groupadminmodel','groupadmin'); 
        $this->objGroupOps=$this->getObject('groupops','groupadmin');

        $contextId=$this->objAdminGroups->getId($courseCode.'^Students');
        $now=date('Y-m-d');
        if ($batchCode=='auto'){
            $batchCode=$importMethod.date('Ymdhis').rand(10,99);
        }
        $students=array();
        foreach ($newInfo as $line)
        {
            $sql=array(
             'userId'=>$line['userId'],
             'adminId'=>$adminId,
             'contextCode'=>$courseCode,
             'creationDate'=>$now,
             'importMethod'=>$importMethod,
             'batchId'=>$batchCode
             );
            $returnId=$this->insert($sql);
            $students['Students'][]=$line['userId'];
        }
        if ($courseCode!='lobby'){
            //$this->objContextGroups->importGroupMembers($courseCode,$students);
            // Add to context
            foreach ($students['Students'] as $line){
                $usrdata = $this->objGroupOps->getUserByUserId($line);
                $permUserId = $usrdata['perm_user_id'];
                $this->objAdminGroups->addGroupUser( $contextId, $permUserId);
            }
        }
        // Uncomment below when doing a bulk-import of users
        //$this->addToUserGroup($students['Students'],'Students');
        return $batchCode;
    }

    /**
    * This method deletes all users imported as a specific 'batch'
    * @param string $batchCode
    */
    function deleteBatch($batchCode)
    {
        $this->objContextGroups=$this->getObject('managegroups','contextgroups');
        $this->objAdminGroups=$this->getObject('groupadminmodel','groupadmin');
        $this->objGroupOps=$this->getObject('groupops','groupadmin');
        $sql="where batchId='$batchCode'";
        $list=$this->getAll($sql);
        $groupId=NULL;
        foreach ($list as $line)
        {
            if (!$this->objUser->inAdminGroup($line['userid'])){
                // Delete from the import-batch
                $this->delete('id',$line['id']);
                // now from tbl_users...
                // Don't delete a user that wasn't added by the userimport method!
                // Or if in more than one context!
                $userPK=$this->objUser->PKId($line['userid']);
                if (((trim($this->objUser->getItemFromPkId($userPK,'howcreated'))=='userimport')||
                    (trim($this->objUser->getItemFromPkId($userPK,'howcreated'))=='import'))&&
                    ( count($this->objContextGroups->userContexts($line['userid'],array('contextcode')))<2) ){
                    //$this->objUserAdmin->setUserDelete($line['userid']);
                    $this->objUserAdmin->batchProcessOption(array($userPK),'delete');
                } else {
                // Here they didn't cascade-delete, so we have to remove them specifically.
                    if ($groupId==NULL){
                        $groupId=$this->objAdminGroups->getLeafId( array( $line['contextcode'], 'Students' ));
                    }
                    $usrdata = $this->objGroupOps->getUserByUserId($line['userid']);
                    $permUserId = $usrdata['perm_user_id'];
                    //$this->objAdminGroups->deleteGroupUser( $groupId, $userPK );
                    $this->objAdminGroups->deleteGroupUser( $groupId, $permUserId );
                }
            }
        }
    }

    /**
    * This method returns a list of all the 'batches' available to the user
    * @param string $contextCode
    * @returns array $info
    */
    function listBatch($contextCode)
    {
        $sql1="select distinct batchId,creationDate,contextCode from tbl_importusers";
         // jsc says: removing feature of Admin user seeing ALL imports for now - might put it back later.
        //if ($this->objUser->isAdmin()){
        //    $sql2=" order by batchId";
        //} else {
            $sql2=" where contextCode='$contextCode' order by batchId";
        //}
        $info=$this->getArray($sql1.$sql2);
        return $info;
    }


    /**
    * This method returns an array of all users imported as a specific 'batch'
    * @param string $batchCode
    * @returns array $data
    */
    function showBatch($batchCode)
    {
        $sql="select tbl_users.userId, tbl_users.username, firstname,surname,title,sex,emailAddress from tbl_users,tbl_importusers "
        ."where tbl_importusers.batchId='$batchCode' and tbl_users.userId=tbl_importusers.userId";
        $list=$this->getArray($sql);
        $data2=$this->getAll("where batchId='$batchCode' LIMIT 1");
        $contextCode=$data2[0]['contextcode'];
        $data=array();
        $data['batchCode']=$batchCode;
        $data['courseCode']=$contextCode;
        foreach ($list as $line)
        {
            extract($line);
            $data['users'][]=array($userid,$username,$firstname,$surname,$title,$sex,$emailaddress);
        }
        return $data;
    }

    /**
    * This method exports all users imported as a specific 'batch'
    * @param string $batchCode
    */
    function exportCSV($batchCode)
    {
        $this->export='';
        $sql="select tbl_users.userId, tbl_users.username, firstname,surname,title,sex,emailAddress from tbl_users,tbl_importusers "
        ."where tbl_importusers.batchId='$batchCode' and tbl_users.userId=tbl_importusers.userId";
        $list=$this->getArray($sql);
        foreach ($list as $line)
        {
            // Now we add the line to the .csv file
            $this->export.=implode(',',$line)."\n";
        }
        $this->exportName=$batchCode.".csv";
    }

    /**
    * This method exports all users imported as a specific 'batch'
    * @param string $batchCode
    */
    function exportXML($batchCode)
    {
        $this->export='';
        // Getting the data via an SQL cross-table lookup
        $sql="select tbl_users.userId, tbl_users.username, firstname,surname,title,sex,emailAddress from tbl_users,tbl_importusers "
        ."where tbl_importusers.batchId='$batchCode' and tbl_users.userId=tbl_importusers.userId";
        if ($batchCode=='ALL'){
            $sql="select userId, username, pass as cryptpassword, firstname,surname,title,sex,emailAddress from tbl_users";
        }
        $list=$this->getArray($sql);

        if ($batchCode!='ALL'){
            $data=$this->getAll("where batchId='$batchCode' LIMIT 1");
            @$contextCode=$data[0]['contextCode'];
        } else {
            $contextCode='ALL';
        }
        // Now we build up the array for the XML class to work with.
        $dataArray=array();
        $xml="<batch>\n";
        $dataArray['batchcode']=$batchCode;
        $xml.="<batchcode>$batchCode</batchcode>\n";
        $dataArray['contextcode']=$contextCode;
        $xml.="<contextcode>$contextCode</contextcode>\n";
        foreach ($list as $line)
        {
            $student=array();
            $xml.="<student>\n";
            foreach ($line as $key=>$value)
            {
                $student[$key]=$value;
                $xml.="<$key>$value</$key>\n";
            }
            $dataArray['student'][]=$student;
            $xml.="</student>\n";
        }
        $xml.="</batch>\n";
        $this->export=$xml;
        $this->exportName=$batchCode.".xml";
        return TRUE;

        // Call the XML class and put the output in the class variable.
        // This code is not used at the moment - there is a return before it.
        $this->objSerialXML=$this->getObject('xmlserial','utilities');
        $this->export=$this->objSerialXML->writeXML($dataArray);
    }


    /**
    * This method exports all students in the specified context,
    * whether imported in a batch or not.
    * @param string $batchCode
    */
    function exportClassXML($contextCode,$role='Students')
    {
        $this->objContextGroups=$this->getObject('managegroups','contextgroups');
        $this->export='';
        // Getting the data via a call to the groupmanagement classes
        $fields=array("tbl_users.userId AS userId", "username", "pass as cryptpassword", "firstname",
          "surname","title","sex","emailAddress");

        $list=$this->objContextGroups->contextUsers($role,$contextCode,$fields);

        // Now we build up the array to turn into XML.
        $dataArray=array();
        $xml="<batch>\n";
        $dataArray['batchcode']=$contextCode;
        $xml.="<batchcode>$contextCode</batchcode>\n";
        $dataArray['contextcode']=$contextCode;
        $xml.="<contextcode>$contextCode</contextcode>\n";
        foreach ($list as $line)
        {
            $student=array();
            $xml.="<student>\n";
            foreach ($line as $key=>$value)
            {
                $student[$key]=$value;
                $xml.="<$key>$value</$key>\n";
            }
            $dataArray['student'][]=$student;
            $xml.="</student>\n";
        }
        $xml.="</batch>\n";
        $this->export=$xml;
        $this->exportName=$contextCode.".xml";
        return $xml;
    }

    /**
    * method to add a list of imported users to a specified group
    * (used for the Nettel import)
    * @param array $users
    * @param string $groupId
    */
    function addToUserGroup($users,$groupName)
    {
        $objGroup=$this->getObject('groupadminmodel','groupadmin');
        $groupId=$objGroup->getLeafId(array($groupName));
        foreach($users as $line)
        {
            $objGroup->addGroupUser($groupId,$line);
        }
    }

}
?>
