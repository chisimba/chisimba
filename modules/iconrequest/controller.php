<?php
/* ------------iconrequest class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* Send request for new icon
* @author Nic Appleby
* $Id: controller.php,v 1.0 2005/12/13
*/
//iii  
class iconrequest extends controller
{
   public $objH;			//Main page heading
   public $objLanguage;	//Language object for language independant text
   public $dbReq;			//Database containing request data
   public $dbDev;			//Database containing developer data
   public $dbFile;			//Database containing example files
   public $config;			//Config object for content base path
   public $mailMsg;		//kngemail object to email request to developer
   public $objSys;
   public $sortBy;	//Used to sort the displayed icons
   /**
   * Function to initialise objects
   */
   public function init()
   {
	$this->objLanguage = &$this->getObject("language", "language");
	$this->objUser =& $this->getObject('user', 'security');
	//Get the activity logger class
    $this->objLog = &$this->newObject('logactivity', 'logger');
    //Log this module call
    $this->objLog->log();
	//initialise tables
    $this->dbReq = &$this->getObject('requestTable');
	$this->dbDev = &$this->getObject('developerTable');
	$this->dbFile = &$this->getObject('filestable');
	//get config object for content basse path
	$this->config = &$this->getObject('config','config');
	//setup the kngemail object
	$this->mailMsg = &$this->getObject('kngemail','utilities');
    $this->objSys = &$this->getObject('dbsysconfig','sysconfig');
   }

   /**
   * Main function
   */
   public function dispatch()
   {
	$this->action = $this->getParam("action", null);
        switch ($this->action) {

            	// Welcome screen displaying current requests
            	case null:
			$sortBy = "n";
			$this->setVar('pageSuppressXML', TRUE);
			$this->setVar('sortBy',$sortBy);
                    return "request_tpl.php";

	//Made change to viewing of icons by category
	//Author      	: Jarrett Jordaan
	//Email 	: 2111279@gmail.com

            	// Welcome screen displaying current requests sorted by priority
            	case sortByPriority:
			$sortBy = "p";
			$this->setVar('sortBy',$sortBy);
			$this->setVar('pageSuppressXML', TRUE);
                    return "request_tpl.php";

            	// Welcome screen displaying current requests sorted by description
            	case sortByDescription:
			$sortBy = "d";
			$this->setVar('sortBy',$sortBy);
			$this->setVar('pageSuppressXML', TRUE);
                    return "request_tpl.php";

                case "test":
                	$this->setVar('pageSuppressXML', TRUE);
                    return "test2_tpl.php";

            	// Edit an icon request
            	case "edit":
            		$this->setVar('pageSuppressXML', TRUE);
            		return "icon_form_tpl.php";

		        // Template to edit the icon developer details
		        case "developer":
		        	$this->setVar('pageSuppressXML', TRUE);
            		return "dev_edit_tpl.php";

            	// Template to get all the icon request data from the user
            	case "request":
            		$this->setVar('pageSuppressXML', TRUE);
			        return "icon_form_tpl.php";

		//content of iframe for dynamically uploading icon
		case "tempframe":
			//suppress layout templates for iframe
			$this->setLayoutTemplate(NULL);
			$this->setVar('pageSuppressIM', TRUE);
        		$this->setVar('pageSuppressToolbar', TRUE);
        		$this->setVar('pageSuppressBanner', TRUE);
        		$this->setVar('pageSuppressContainer', TRUE);
        		$this->setVar('suppressFooter', TRUE);
        		$this->setVar('pageSuppressXML', TRUE);
			return "upload_icon_tpl.php";

		// Submit a new request to the DB
		case "submit":
	   		//create new request to insert into db
	   		$this->loadClass('request');
	   		$icon = &new request($this->getParam('reqid'),
				$this->getParam('module_name'),$this->getParam('priority'),
	   		$this->getParam('icon_type'),$this->getParam('rdbtphptype'),
				$this->getParam('icon_name'),$this->getParam('icon_description'),
				$this->objUser->userId(),$this->getParam('idea_uri1'),
				$this->getParam('idea_uri2'));

			switch ($icon->priority) {
				case '1' : $iconPr = $this->objLanguage->languageText('word_yesterday','iconrequest');
					break;
				case '2' : $iconPr = $this->objLanguage->languageText('word_high','iconrequest');
					break;
				case '3' : $iconPr = $this->objLanguage->languageText('word_normal','iconrequest');
					break;

			switch ($icon->phpvers){
			}		
				case '4' : $iconPt = $this->objLanguage->languageText('word_php4','iconrequest');
					break;
				case '5' : $iconPt = $this->objLanguage->languageText('word_php5','iconrequest');
					break;
				case '1' : $iconPt = $this->objLanguage->languageText('word_phpunkn','iconrequest');
					break;
				default  : $iconPt = $this->objLanguage->languageText('word_phpunkn','iconrequest');
					break;
		
			}
			
			$iconTy = ($icon->type == 'm') ? $this->objLanguage->languageText('word_module','iconrequest') : $this->objLanguage->languageText('word_common','iconrequest');
			//try insert the record and return appropriate message
	   		if ($this->dbReq->insertRec($icon) == false) {
	   			return $this->nextAction(null,array('message'=>'fail'));
	   		} else {	//success, generate email and send
            $host = $this->objSys->getValue('KEWL_SERVERNAME');
	   		$this->mailMsg->setup($this->objUser->email(), $this->objLanguage->languageText('mod_name','iconrequest') .' '. $this->objUser->fullName(),$host);
            $email = $this->objUser->email($this->dbDev->getId());
				$subject = $this->objLanguage->languageText('mod_email_subject','iconrequest');
				$name = $this->objUser->fullname($this->dbDev->getId());
				$body = $this->objLanguage->languageText('mod_email_body','iconrequest');
				$body .= $this->objLanguage->languageText('form_label1','iconrequest').' '.$icon->modname.'<br>';
				$body .= $this->objLanguage->languageText('form_label2','iconrequest').' '.$iconPr.'<br>';
				$body .= $this->objLanguage->languageText('form_label3','iconrequest').' '.$iconTy.'<br>';
				/*
				*   Auhtor of changes Dean Van Niekerk
				*   Email address : dvanniekerk@uwc.ac.za
				*/
				$body .= $this->objLanguage->languageText('form_label11','iconrequest').' '.$icon->Phpversion.'<br>';
				$body .= $this->objLanguage->languageText('form_label4','iconrequest').' '.$icon->iconname.'<br>';
				$body .= $this->objLanguage->languageText('form_label5','iconrequest').' '.$icon->description.'<br>';
				$body .= $this->objLanguage->languageText('form_label8','iconrequest').' '.$icon->uri1.'<br>';
				$body .= $this->objLanguage->languageText('form_label9','iconrequest').' '.$icon->uri2.'<br>';
				$body .= $this->objLanguage->languageText('form_label10','iconrequest').' <a href="mailto:'.$this->objUser->email().'">'.$this->objUser->fullName($icon->uploaded).'</a><br>';
				$pic = $this->dbFile->getRow('reqid',$icon->reqid);
				if ($pic !=Null) {	//attachment exists
					$path = $this->config->contentBasePath().'assets/'.$pic['filename'];
					$attach = file_get_contents($path);
				} else {
					$attach = Null;
				}
				$this->mailMsg->sendMail($name,$subject,$email,$body,true,$attach,$pic['filename']);		

	   			return $this->nextAction(null,array('message'=>'confirm'));
	   		}

	   	// Update the percentage complete of the request
	   	case "update":
			$pt = $this->getParam('percentage');
			$icon = array('modname' =>$this->getParam('module_name'),'priority' =>$this->getParam('priority'),
					'type' =>$this->getParam('icon_type'),'phptype'=>$this->getParam('rdbtphptype'), 'iconname'=>$this->getParam('icon_name'),
					'description'=>$this->getParam('icon_description'),'uri1'=>$this->getParam('idea_uri1'),
					'uri2'=>$this->getParam('idea_uri2'),'complete'=>$pt);
	   		$pk = $this->getParam('pk');
			$this->dbReq->update('id',$pk,$icon);
	   		if ($pt==100) {
                if (!$this->dbDev->isEmpty()) {
                    $id = $this->dbDev->getId();
                    $email = $this->objUser->email($id);
                    $developer = $this->objUser->fullname($id);
                	  $host = $this->objSys->getValue('KEWL_SERVERNAME');
                	  $this->mailMsg->setup($email,$developer,$host);
                	  $d = $this->dbReq->getRow('id',$pk);
					$email = $this->objUser->email($d['uploaded']);
					$subject = $this->objLanguage->languageText('icondone_email_subject','iconrequest');
					$name = $this->objUser->fullName($d['uploaded']);
					$body = $this->objLanguage->languageText('phrase_icon','iconrequest');
					$body .= $d['iconname'].$this->objLanguage->languageText('phrase_icon2','iconrequest');
					$body .= $email.'">'.$developer;
					$body .= $this->objLanguage->languageText('phrase_icon3','iconrequest');
					$this->mailMsg->sendMail($name,$subject,$email,$body,true,null,null);
					return $this->nextAction('delete',array('reqid'=>$d['reqid'],'Id'=>$pk));
                }
			} else {
				$host = $this->objSys->getValue('KEWL_SERVERNAME');
	   			$this->mailMsg->setup($this->objUser->email(), $this->objLanguage->languageText('mod_name','iconrequest') .' '. $this->objUser->fullName(),$host);
                $email = $this->objUser->email($this->dbDev->getId());
				$subject = $this->objLanguage->languageText('mod_email_subject','iconrequest');
				$name = $this->objUser->fullname($this->dbDev->getId());
				$body = $this->objLanguage->languageText('mod_email_body','iconrequest');
				$body .= $this->objLanguage->languageText('form_label1','iconrequest').' '.$icon->modname.'<br>';
				$body .= $this->objLanguage->languageText('form_label2','iconrequest').' '.$icon->priority.'<br>';
				$body .= $this->objLanguage->languageText('form_label3','iconrequest').' '.$icon->type.'<br>';
				/*
				*   Auhtor of changes Dean Van Niekerk
				*   Email address : dvanniekerk@uwc.ac.za
				*/
				$body .= $this->objLanguage->languageText('form_label11','iconrequest').' '.$icon->Phpversion.'<br>';
				$body .= $this->objLanguage->languageText('form_label4','iconrequest').' '.$icon->iconname.'<br>';
				$body .= $this->objLanguage->languageText('form_label5','iconrequest').' '.$icon->description.'<br>';
				$body .= $this->objLanguage->languageText('form_label8','iconrequest').' '.$icon->uri1.'<br>';
				$body .= $this->objLanguage->languageText('form_label9','iconrequest').' '.$icon->uri2.'<br>';
				$body .= $this->objLanguage->languageText('form_label10','iconrequest').' <a href="mailto:'.$this->objUser->email().'">'.$this->objUser->fullName($icon->uploaded).'</a><br>';
				$pic = $this->dbFile->getRow('reqid',$icon->reqid);
				if ($pic !=Null) {	//attachment exists
					$path = $this->config->contentBasePath().'assets/'.$pic['filename'];
					$attach = file_get_contents($path);
				} else {
					$attach = Null;
				}
				$this->mailMsg->sendMail($name,$subject,$email,$body,true,$attach,$pic['filename']);
	   			return $this->nextAction(null);
			}
			break;

	   	// Change the developer information in the DB
	   	case "changedev":
	   		if (($this->objUser->userId() == $this->dbDev->getId()) || ($this->objUser->isAdmin())) {
				$this->dbDev->updateRec($this->getParam('dev_id'));
	   		}
	   		return $this->nextAction(null);

	   	// Delete a request from the DB
		case "delete":
			$reqId = $this->getParam('reqid');
			$example = $this->dbFile->getRow('reqid',$reqId);
			if ($example != Null) {	//if there is an assosciated entry in filestable delete this too
				$this->dbFile->deleteFile($reqId);
				$fName = $this->config->contentBasePath().'assets/'.$example['filename'];
				unlink($fName);	//delete from the server directory
			}
			$this->dbReq->deleteRec($this->getParam("id", null));
			return $this->nextAction(null);

		// Delete the icon developer information from the DB (debugging purposes only)
		case "deldev":
			$this->dbDev->deleteRec();
			return $this->nextAction(null);

		default:
			die("Action unknown");
                	break;
		} //switch
   }
}
?>
