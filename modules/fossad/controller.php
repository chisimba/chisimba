<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

class fossad extends controller {

    function init() {
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objLog = $this->getObject('logactivity', 'logger');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->utils = $this->getObject('simpleregistrationutils','fossad');
        $this->objLog->log();
    }

    public function dispatch($action) {
    /*
    * Convert the action into a method (alternative to
    * using case selections)
    */
        $method = $this->getMethod($action);
    /*
    * Return the template determined by the method resulting
    * from action
    */
        return $this->$method();
    }

  /**
  *
  * Method to convert the action parameter into the name of
  * a method of this class.
  *
  * @access private
  * @param string $action The action parameter passed byref
  * @return string the name of the method
  *
  */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__'.$action;
        }
        else {
            return '__home';
        }
    }

  /**
  *
  * Method to check if a given action is a valid method
  * of this class preceded by double underscore (__). If it __action
  * is not a valid method it returns FALSE, if it is a valid method
  * of this class it returns TRUE.
  *
  * @access private
  * @param string $action The action parameter passed byref
  * @return boolean TRUE|FALSE
  *
  */
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

   /**
    * default home page
    * @return <type>
    */
    function __home() {
        $firstname=$this->getParam('firstname');
        $lastname=$this->getParam('lastname');
        $company=$this->getParam('company');
        $email=$this->getParam('email');
        $mode=$this->getParam('mode');
        $this->setVarByRef('editfirstname',$firstname);
        $this->setVarByRef('editlastname',$lastname);
        $this->setVarByRef('editcompany',$company);
        $this->setVarByRef('editemail',$email);
        $this->setVarByRef('mode',$mode);
        return "home_tpl.php";
    }
    /**
     * save a new registration; incase email exists already ,return it back to
     * user
     */
    function __register() {
        $firstame=$this->getParam('firstname');
        $lastname=$this->getParam('lastname');
        $company=$this->getParam('company');
        $email=$this->getParam('emailfield');
        $reg = $this->getObject('dbregistration');
        if($reg->addRegistration($firstame,$lastname,$company,$email)){
            $this->sendMail($email);

            $this->nextAction("success",array('title1'=>$this->objLanguage->languageText('mod_fossad_registrationsuccess', 'fossad'),
   'title2'=>''));
        }
        else{
            $this->nextAction('home',array('firstname'=>$firstame,'lastname'=>$lastname,'company'=>$company,'email'=>$email,"mode"=>'edit'));
        }
    }

    function __expresssignin(){
        $reg = $this->getObject('dbregistration');
        if($this->objUser->email() == 'Anonymous user (not logged in)'){
            $this->objUser->logout();
            return  $this->nextAction('home',  array ('mode' => 'loginagain' ));

        }
        if($reg->emailExists($this->objUser->email())){
            $this->nextAction('success',array('title1'=>$this->objLanguage->languageText('mod_fossad_alreadysignedup', 'fossad'),
   'title2'=>''));
        }else{
            $this->nextAction('register',array('firstname'=>$this->objUser->getSurname(),
   'lastname'=>$this->objUser->getFirstName(),
   'company'=>$this->objConfig->getSiteName(),
   'emailfield'=>$this->objUser->email(),
   'title1'=>$this->objLanguage->languageText('mod_fossad_registrationsuccess', 'fossad'),
   'title2'=>$this->objLanguage->languageText('mod_fossad_success', 'fossad')));
        }
    }
   /**
    * For admin functions
    * @return <type>
    */
    function __admin() {
        return "memberlist_tpl.php";
    }

    function __deletemember(){
        $id=$this->getParam('id');
        $reg = $this->getObject('dbregistration');
        $reg->deleteMember($id);
        $this->nextAction('admin');
    }
    /**
     * Registration is a success, inform users
     */
    function __success(){
        $title1=$this->getParam('title1');
        $title2=$this->getParam('title2');
        $this->setVarByRef('rightTitle1',$title1);
        $this->setVarByRef('rightTitle2',$title2);
        return "success_tpl.php";
    }

    /**
     *  Sends the email to the newly registered member
     */
    function sendMail($to){


        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $contactemail=$objSysConfig->getValue('CONTACT_EMAIL', 'fossad');
        $subject=$objSysConfig->getValue('EMAIL_SUBJECT', 'fossad');
        $body=$objSysConfig->getValue('EMAIL_BODY', 'fossad');
        $emailName=$objSysConfig->getValue('EMAIL_NAME', 'fossad');
        $inviteattach=$objSysConfig->getValue('INVITE_ATTACH', 'fossad');
        $programattach=$objSysConfig->getValue('PROGRAM_ATTACH', 'fossad');

        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('to', array($to));
        $objMailer->setValue('from', $contactemail);
        $objMailer->setValue('fromName', $emailName);
        $objMailer->setValue('subject', $subject);
        $objMailer->setValue('body', $body);
        $objMailer->attach($inviteattach);
        $objMailer->attach($programattach);
        $objMailer->send();
    }
    function __download() {

        return "download_tpl.php";
    }
    function __xls(){
        $eventid=$this->getParam('eventid');
        $reg = $this->getObject('dbregistration');
        $dbdata=$reg->getRegistrations($eventid);
        $stringData='';

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $downloadfolder=$objSysConfig->getValue('DOWNLOAD_FOLDER', 'simpleregistration');

        $docRoot=$_SERVER['DOCUMENT_ROOT'].$downloadfolder;

        $myFile = $docRoot."listing.xls";
        unlink($myFile);
        /*
         * $fh = fopen($myFile, 'w') or die("can't open file");
        foreach($dbdata as $row){
            fwrite($fh,$row['first_name'].'    '.$row['last_name'].'   '.$row['email'].'   '.$row['company']);
        }*/
        $file = fopen($myFile, "a");
        //delete old one

        foreach($dbdata as $row){
            fputs($file, $row['first_name'].','.$row['last_name'].', '.$row['company']."\r\n");
        }
        fclose($file);
        //fclose($fh);

        $this->nextAction('download');
    }

      /**
     * Overridden method to determine whether or not login is required
     *
     * @return FALSE
     */
    public function requiresLogin() {
        switch ($this->getParam('action')) {
            case 'admin':
                return TRUE;
                case 'expresssignin':
                    return TRUE;
                    default:
                        return FALSE;
                    }
                }
            }
