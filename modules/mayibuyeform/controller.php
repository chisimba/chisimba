<?php

/**
 *
 *mayibuyeform

 * mayibuyeform  application to produce material of the robben island museum archives
 * @category  Chisimba
 * @package   mayibuyeform
 * @Author  Brenda Mayinga
 */



class mayibuyeform extends controller {

    public $objLanguage;
    protected $objMail;
    var $date;
    var $nameofreseacher;
    var $tellno;
    var $faxxno;
    var $email;
    var $jobtitles;
    var $organization;
    var $postaladd;
    var $physicaladd;
    var $vatno;
    var $jobnno;
    var $telephone;
    var $faxnumber2;
    var $email2;
    var $nameofresi;
    var $jotitle;
    var $organizationname;
    var $postadd;
    var $tel;
    var $faxx;
    var $stuno;
    var $staffnum;
    var $colection;
    var $captcha;
    var $image;
    var $project;
    var $time;


    public function init() {
        //Instantiate the language object
        $this->objLanguage = $this->getObject('language', 'language');

        $this->objMail = $this->getObject('mailer', 'mail');

        $this->dbresearchform = $this->getObject('researchform', 'mayibuyeform');

        $this->dbresearchft = $this->getObject('researchft','mayibuyeform');

	$this->dbresearchstud = $this->getObject('researchstud','mayibuyeform');
 
	$this->dbresearchlast = $this->getObject('researchlast','mayibuyeform');

    }

    public function dispatch($action) {
        $this->setLayoutTemplate('research_tpl.php');

        switch ($action) {

            default:
                return 'research_tpl.php';

        case 'send_researchform':
                $this->SavestudentRecord();
		return "researchft_tpl.php";
		
	
	case 'send_researchft':
		$this->SaveResearchRecord();
		return "researchstudent_tpl.php";

	
	case 'send_researchstud':
		$this->SaveResearchStudRecord();
		return "researchlast_tpl.php";

        case 'send_researchlast':
		$this->SaveResearchLastRecord();
		return "confirm_tpl.php";
    
               
        }
    }

    public function SavestudentRecord() {
        $date = $this->getParam('date');
        $nameofreseacher = $this->getParam('name_resign');
        $tellno = $this->getParam('tellno');
        $faxxno = $this->getParam('faxno');
        $email = $this->getParam('emailaddress');
         
        // insert into database
        $pid = $this->dbresearchform->insertStudentRecord($date, $nameofreseacher, $tellno, $faxxno, $email);

	$subject = "New Reseacher First form";
	$this->sendEmailNotification($subject, $message = ' ***Researcher Details*** '. "\n". ' Date:' . $date . '  ' . "\n" . 'name:' .
                $nameofreseacher . '   ' . "\n" . ' Telephone Number: ' . $tellno . '   ' . "\n" . 'Fax no: ' .
                $faxxno . '  ' . "\n" . ' Email Adddress: ' . $email);

}

	public function SaveResearchRecord()
		{
		$nameofsign = $this->getParam('resignatorname');
        	$jobtitles = $this->getParam('job_title');
        	$organization = $this->getParam('organization');
        	$postaladd = $this->getParam('postal_address');
        	$physicaladd = $this->getParam('phyiscal_address');
        	$vatno = $this->getParam('vat_no');
        	$jobnno = $this->getParam('job_no');
        	$telephone = $this->getParam('tell_no');
        	$faxnumber2 = $this->getParam('faxno_2');
        	$email2 = $this->getParam('emails');

 		// insert into database
		$id = $this->dbresearchft->insertResearchRecord($nameofsign, $jobtitles, $organization, $postaladd, $physicaladd, $vatno,
						 	$jobnno, $telephone, $faxnumber2, $email2);
	$subject = "New Reseacher second form";

	$this->sendEmailNotification($subject, $message = ' ***Signatory Details*** '. "\n". ' Name of Signatory: ' .$nameofsign . '  ' . "\n" . 
		' Job Title: ' .$jobtitles . '   ' . "\n" . ' Organization Name: ' . $organization . '   ' . "\n" . 'Postal Address: ' .             			 $postaladd . '  ' . "\n" . ' Physical Address: ' . $physicaladd. '   ' . "\n" . ' Vat No:'. $vatno . '   ' . "\n" . 'Job No: ' .
		 $jobnno . '  ' . "\n" .'Telephone: '. $telephone . '  ' . "\n" . ' Fax Number: ' .
		 $faxnumber2 .	'   ' . "\n" . ' Email Address: ' . $email2 );


	}


  public function SaveResearchStudRecord()
	{     
	$nameofresi = $this->getParam('name');
        $jotitle = $this->getParam('jobtitle');
        $organizationname = $this->getParam('orgranization2');
        $postadd = $this->getParam('postaladdress');
        $tel = $this->getParam('tellno_3');
        $faxx = $this->getParam('faxno_3');

	// inserting researchstud record into database

	$id = $this->dbresearchstud->insertResearchStudRecord($nameofresi,$jotitle, $organizationname, $postadd, $tel, $faxx);

	$subject = "Next of Kin Details";

	$this->sendEmailNotification($subject, $message = ' ***Next of Kin Details*** '. "\n". ' Name: ' .$nameofresi . '  ' . "\n" . 
		' Job Title: ' .$jotitle . '   ' . "\n" . ' Organization Name: ' . $organizationname . '   ' . "\n" . 'Postal Address: ' .             			 $postadd . '  ' . "\n" . ' Telephone: ' . $tel. '   ' . "\n" . ' Fax Number:'. $faxx);


}

	public function SaveResearchLastRecord()
	{

        $stuno = $this->getParam('uwc');
        $staffnum = $this->getParam('staffno');
        $colection = $this->getParam('dept');
        $image = $this->getParam('subheading3');
        $project = $this->getParam('publication');
        $time = $this->getParam('project');
	$captcha = $this->getParam('research_captcha');

	$errormsg[] = array();

        if ((md5(strtoupper($captcha)) != $this->getParam('research_captcha'))) {
            $errormsg[] = 'badcaptcha';
        }

        //if form entry is in corect or invavalid
        if (count($errormsg) > 0) {
            $this->setVarByRef('errormsg', $errormsg);
            $this->setVarByRef('insarr', $insarr);

            }

// inserting researchlast record into database

	$id =$this->dbresearchlast->insertResearchlastRecord($stuno, $staffnum, $colection, $image, $project, $time);

 	$subject = "New Reseacher Fouth form";

        $this->sendEmailNotification($subject, $message = ' ***Researcher Details*** '. "\n".'Student No:' .
                $stuno . '  ' . "\n" . 'Staff num:' . $staffnum . '  ' . "\n" . 'Colection:' . 
		$colection. ' '. "\n". 'Image: ' . $image . ' '. "\n". 	'Project:'. $project. ' '. "\n". ' Time: '. $time );

        return "researchft_tpl.php";
    }

    public function sendEmailNotification($subject, $message) {

        $objMail = $this->getObject('mailer', 'mail');
        //send to multiple addressed   
        $list = array("pmahinga@uwc.ac.za");
        $objMail->to = ($list);
        // specify to whom the email is coming from
        $objMail->from = "no-reply@uwc.ac.za";
        $objMail->from = "no-reply";
        //Give email subject and body
        //$objMail->subject=$emaill;
        $objMail->subject = $subject;
        $objMail->body = $message;
        $objMail->AltBody = $message;
        // send email
        $objMail->send();
    }

public function RequiresLogin()
{
	return FALSE;
}

}

?>
