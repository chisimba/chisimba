<?php
//ini_set('error_reporting', 'E_ALL & ~E_NOTICE');
/**
 * IM controller class
 *
 * Class to control the IM module
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  chisimba
 * @package   im
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11311 2008-11-04 12:29:43Z wnitsckie $
 * @link      http://avoir.uwc.ac.za
 * @see       xmpphp
 */

class das extends controller {

    public $objImOps;
    public $objUser;
    public $objUserParams;
    public $userJid;
    public $objLanguage;
    public $objBack;

    public $objDbIm;
    public $conn;
    public $objDbImPres;

    public $objTwitterLib = NULL;

    public $objSysConfig;
    public $jserver;
    public $jport;
    public $juser;
    public $jpass;
    public $jclient;
    public $jdomain;

    public $objModules;

    /**
     *
     * Standard constructor method to retrieve the action from the
     * querystring, and instantiate the user and lanaguage objects
     *
     */
    public function init() {
        try {
            // Include the needed libs from resources
            include ($this->getResourcePath ( 'XMPPHP/BOSH.php', 'im' ));
            include ($this->getResourcePath ( 'XMPPHP/XMPPHP_Log.php', 'im' ));
            $this->objImOps = $this->getObject ( 'dasops', 'das' );
            $this->objViewRender = $this->getObject ( 'viewrender', 'das' );
            $this->objUser = $this->getObject ( "user", "security" );
            $this->objUserParams = $this->getObject ( 'dbuserparamsadmin', 'userparamsadmin' );
            $this->userJid = $this->objUserParams->getValue ( 'Jabber ID', 'im' );
            //Create an instance of the language object
            $this->objLanguage = $this->getObject ( "language", "language" );
            $this->objBack = $this->getObject ( 'background', 'utilities' );
            $this->objDbIm = $this->getObject ( 'dbim', 'im' );
            $this->objDbImPres = $this->getObject ( 'dbimpresence', 'im' );
            $this->objIMUsers = $this->getObject ( 'dbimusers', 'im' );
            $this->objModules = $this->getObject ( 'modules', 'modulecatalogue' );
			$this->objConfig = $this->getObject ( 'altconfig', 'config' );
			$this->objChat = $this->getObject ( 'dbchat', 'das' );
           

            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->jserver = $this->objSysConfig->getValue ( 'jabberserver', 'im' );
            $this->jport = $this->objSysConfig->getValue ( 'jabberport', 'im' );
            $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'im' );
            $this->jpass = $this->objSysConfig->getValue ( 'jabberpass', 'im' );
            $this->jclient = $this->objSysConfig->getValue ( 'jabberclient', 'im' );
            $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'im' );
			$this->timeLimit = $this->objSysConfig->getValue ( 'imtimelimit', 'im' );
            $this->conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
           
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
		
    }

    /**
     * Standard dispatch method to handle adding and saving
     * of comments
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
			case 'viewall' :

            case NULL :
				$this->setLayoutTemplate('das_layout_tpl.php');
				return 'viewall_tpl.php';
				break;
			case 'viewreassign':
				$this->setLayoutTemplate('das_layout_tpl.php');
				return 'view_reassign_tpl.php';
				break;
			
			case 'getconversations':
				$msgs = $this->objDbIm->getMessagesByActiveUser ($this->objUser->userId());
				
				echo $this->objViewRender->renderOutputForBrowser($msgs);
				break;
					
			case 'reassign':
				$this->objDbImPres->reAssignCounsellor($this->getParam('patient'), $this->getParam('counsellorbox'));
				return $this->nextAction('viewall');
			case 'resetcounsillors':
				$this->objDbImPres->resetCounsillors();
			
				$this->nextAction('viewcounsilors');
				break;
			case 'startsession':
				$this->objImOps->startSession();
				return $this->nextAction('viewcounsilors');
			case 'endsession':
				$this->objImOps->endSession($this->juser.'@'.$this->jdomain);
				return $this->nextAction('viewcounsilors');

            case 'viewcounsilors':
                $this->setVar('users', $this->objUser->getAll());
                //die('here');
                return 'counsilors_tpl.php';
            case 'addcounsilor':
                $this->objIMUsers->addCounsilor($this->getParam('userid'));
                return $this->nextAction('viewcounsilors');
            case 'removecounsilor':
                $this->objIMUsers->removeCounsilor($this->getParam('userid'));
                return $this->nextAction('viewcounsilors');
            case 'messageview' :
                // echo "booyakasha!";
                $msgs = $this->objDbIm->getMessagesByActiveUser ();

                $this->setVarByRef ( 'msgs', $msgs );
                header("Content-Type: text/html;charset=utf-8");
                return 'messageview_tpl.php';
                break;
            
            case 'getchatcontent':
            	echo $this->objViewRender->formatChat($this->objChat->getMessages());
            	exit(0);
            	break;
            	
            case 'addchatmessage':
            	$this->objChat->addMessage($this->getParam('message'), $this->objUser->userId());
            	exit(0);
            	break;
            		
			case 'viewarchive':
				echo $this->objViewRender->getArchivedMessages($this->getParam('personid'));
				exit(0);
				break;
				
            case 'reply' :
				//reply via ajax
                $msg = $this->getParam ( 'myparam' );
                $user2send = $this->getParam ( 'fromuser' );
                $msgid = $this->getParam ( 'msgid' );

                $conn2 = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
                $conn2->connect ();

                $conn2->processUntil ( 'session_start' );
                $conn2->message ( $user2send, $msg );
                $conn2->disconnect ();

                // update the message now with the reply for bookkeeping purposes
                $this->objDbIm->saveReply ( $msgid, $msg );
                

                echo $this->objLanguage->languageText ( 'mod_im_msgsent', 'im', 'Message Sent!' );
                exit(0);
                break;

            case 'massmessage' :
                //this will send to all user for the current session
            	$msg = $this->getParam('msg');
            	
            	$sendToAll = $this->getParam('sendtoall');
                $msg = strip_tags($msg);

                $conn2 = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
                $conn2->connect ();
                $conn2->processUntil ( 'session_start' );

                $time_start = microtime ( TRUE );

				//get all the users that was active in the last x minutes
                $users = $this->objDbImPres->getAll();//ActiveUsers(); 
                srand();
                $rcnt = rand(1, 5);
                $rsleep = rand(1, 3);
                $cnt = 0;
                foreach ( $users as $user ) {
                    $conn2->message ( $user ['person'], $msg );
                    usleep(3000);
                    $cnt++;
                    //put the random counter in between 1 - 10 users
                    if($cnt == $rcnt)
                    {
                    	//radomize the sleep also betwee 1 - 3 secs
                    	sleep($rsleep);
                    	
                    	//reset both counters
                    	$rcnt = rand(1, 5);
                		$rsleep = rand(1, 3);
                		$cnt = 0;
                	}
                }
                $time_end = microtime ( TRUE );
                $time = $time_end - $time_start;
                $conn2->disconnect ();

                echo "Messages were sent to ".count($users)." users <br> Time taking ".number_format($time, 2, '.', '')." seconds";
				exit(0);
                break;
            case 'sendtoall':
            	$this->objImOps->sendToAll($this->getParam('message'));
            	echo "Message has been sent to all subscribed users";
            	exit(0);
            	break;
            	
            case 'savestatus':
            	$this->objSysConfig->changeParam('jabberstatus', 'im', $this->getParam('status'));
				$this->objSysConfig->changeParam('jabbershow', 'im', $this->getParam('show'));
				/*try {
				    $conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );				
				    $conn->connect();
				    $conn->processUntil('session_start');
					$status = $this->getParam('status');
					$show = $this->getParam('show');
					$type = "available";
				    $conn->presence($status,$show,"wesleynitsckie@gmail.com");
				    //$conn->message('wesleynitsckie@gmail.com', 'Chaning status to '.$status);
				    $conn->disconnect();
				} catch(XMPPHP_Exception $e) {
				    die($e->getMessage());
				}*/
				return  $this->nextAction('viewcounsilors');
				break;

			case 'savesettings':
				
				$this->objSysConfig->changeParam('jabberuser', 'im', $this->getParam('dasusername'));
				$this->objSysConfig->changeParam('jabberuser', 'im', $this->getParam('dasusername'));
				$this->objSysConfig->changeParam('jabberpass', 'im', $this->getParam('daspassword'));
				$this->objSysConfig->changeParam('jabberdomain', 'im', $this->getParam('domain'));
				$this->objSysConfig->changeParam('imtimelimit', 'im', $this->getParam('idletime'));
				$this->objSysConfig->changeParam('feedbackemail', 'das', $this->getParam('dasfeedbackemail'));
				return  $this->nextAction('viewcounsilors');
				break;

				
			case 'togglereassign':
				$this->objIMUsers->setManualAssignment($this->getParam('userid'));
				return $this->nextAction('viewcounsilors');
				break;

			case 'showcontact':
				$this->objDbImPres->showContact($this->getParam('personid'));
				return $this->nextAction(null);
			
			case 'hidecontact':
				$this->objDbImPres->hideContact($this->getParam('personid'));
				return $this->nextAction(null);
				
			case 'addalias':
				$objAlias = $this->getObject('dbalias', 'das');
				$objAlias->addAlias($this->getParam('personid'), $this->getParam('myparam'));
				echo $this->getParam('myparam');
				exit(0);
				break;
				
			case "sendfeedback":				
				if ($this->objImOps->sendFeedBack($this->getParam('personid')))
				{					
					echo "Feedback was successfully sent ";
				} else {
					echo "There was an error send the feedback. Please information the site Administrator";
				}
				exit(0);
				break;
				
			case 'advisormassmessage' :
                //this will send to all user for the current session
            	$msg = $this->getParam('msg');
            	
            	$sendToAll = $this->getParam('sendtoall');
                $msg = strip_tags($msg);

                $conn2 = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
                $conn2->connect ();
                $conn2->processUntil ( 'session_start' );

                $time_start = microtime ( TRUE );

				//get all the users that was active in the last x minutes
                $users = $this->objDbImPres->getUsers($this->objUser->userId());//ActiveUsers(); 
                srand();
                $rcnt = rand(1, 5);
                $rsleep = rand(1, 3);
                $cnt = 0;
                foreach ( $users as $user ) {
                    $conn2->message ( $user ['person'], $msg );
                    usleep(3000);
                    $cnt++;
                    //put the random counter in between 1 - 10 users
                    if($cnt == $rcnt)
                    {
                    	//radomize the sleep also betwee 1 - 3 secs
                    	sleep($rsleep);
                    	
                    	//reset both counters
                    	$rcnt = rand(1, 5);
                		$rsleep = rand(1, 3);
                		$cnt = 0;
                	}
                }
                $time_end = microtime ( TRUE );
                $time = $time_end - $time_start;
                $conn2->disconnect ();

                echo "Messages were sent to ".count($users)." users <br> Time taking ".number_format($time, 2, '.', '')." seconds";
				exit(0);
                break;
                
            case 'searchmessages':
                $keyword = $this->getParam('keyword', NULL);
                $this->objDbDas = $this->getObject ( 'dbdas', 'das' );
                $data = $this->objDbDas->searchMessages($keyword);
                $this->setVarByRef('data', $data);
                return 'searchresults_tpl.php';
                break;
                
			default :
					die ( "unknown action" );
					break;
        }
    }

   
}
