<?php
/**
 *
 * Viewer class for rendering an array of messages to the browser
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
 * @category  Chisimba
 * @package   helloforms
 * @author    Wesley Nitsckie wesleynitsckie@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * Viewer class for rendering an array of messages to the browser
 *
 * @author Wesley Nitsckie
 * @package DAS
 *
 */
class viewrender extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
        $this->objIcon = $this->getObject ( 'geticon', 'htmlelements' );
		$this->objLink = $this->getObject ( 'link', 'htmlelements' );
        $this->objDBIM = $this->getObject('dbim', 'im');
		$this->objUser = $this->getObject('user', 'security');
		$this->objDbIm = $this->getObject ( 'dbim', 'im' );
        $this->objDbImPres = $this->getObject ( 'dbimpresence', 'im' );
        $this->objIcon->setIcon ( 'green_bullet' );
        $this->objModules = $this->getObject ( 'modules', 'modulecatalogue' );
        //$this->objIcon->setAlt($this->objLanguage->languageText('mod_im_available', 'im'));
        $this->activeIcon = $this->objIcon->show ();
        $this->objIcon->setIcon ( 'grey_bullet' );
        $this->inactiveIcon = $this->objIcon->show ();
		$this->objWashout = $this->getObject ( 'washout', 'utilities' );
		$this->objAilas = $this->getObject('dbalias', 'das');
    }

	/**
	* Method to render the output for the conversation
	* @param array $msgs
	* @return string
	*/
    public function renderOutputForBrowser($msgs) 
	{
        
		$objAlertBox = $this->getObject('alertbox', 'htmlelements');		
		
		$objPres = $this->getObject ( 'dbimpresence', 'im' );
		$this->objIcon->setIcon('archive','png');
		$archiveIcon = $this->objIcon->show();
		
		$this->objIcon->setIcon('feedback','png');
		$feedbackIcon = $this->objIcon->show();
		
		$this->objIcon->setIcon('refresh','png');
		$refreshIcon = $this->objIcon->show();
		$this->objIcon->setIcon('reassign','png');
		$reassignIcon = $this->objIcon->show();
        $ret = "<tr>";

        $max = 1;
        $rownum = 0;
	
        //loop the conversations
        foreach ( $msgs as $msg ) {
            $box = "";
            $replyIcon = "";

            $fuser = $msg ['person'];
            //$msgid = $msg ['id'];
           
			if ($this->objDbImPres->isHidden($fuser))
			{
			
				$this->objIcon->setIcon('plus');
				$hiddenIcon = $this->objIcon->show();
				
				$this->objLink->href = $this->uri(array('action' => 'showcontact', 'personid' => $fuser), 'das');
				$this->objLink->link = $hiddenIcon;
				$hidden = $this->objLink->show();
				
				$box .= '<td width="400px"><a name="'.$msg ['person'].'"></a><div class="im_hidden" >' ;
				$box .= '<p class="im_source">'.$hidden.'&nbsp;&nbsp;&nbsp;<b>' . $this->getAliasEditor($msg ['person'], $lastmsgId). '</b></p></td>';
			} else {
			
				$this->objIcon->setIcon('minus');
				$hiddenIcon = $this->objIcon->show();
				
				$this->objIcon->setIcon('icq_on');
				$replyIcon = $this->objIcon->show();
				
				$this->objLink->href = $this->uri(array('action' => 'hidecontact', 'personid' => $fuser), 'das');
				$this->objLink->link = $hiddenIcon;
				$hidden = $this->objLink->show();
				
				$this->objLink->href = '#';//$this->uri(array('action' => 'viewreassign', 'patient' => $fuser), 'das');
				$this->objLink->link = $archiveIcon;
				$archiveLink = $this->objLink->show();
				
				$archive = $objAlertBox->show($archiveIcon, $this->uri(array('action' => 'viewarchive', 'personid' => $fuser)));
				$feedback = $objAlertBox->show($feedbackIcon, $this->uri(array('action' => 'sendfeedback', 'personid' => $fuser)));
				
				$this->objLink->href = $this->uri(array('action' => 'viewreassign', 'patient' => $fuser));
				$this->objLink->link = $reassignIcon;
				$resassignLink = $this->objLink->show();
				
				$sentat = $this->objLanguage->languageText ( 'mod_im_sentat', 'im' );
				$fromuser = $this->objLanguage->languageText ( 'mod_im_sentfrom', 'im' );
				$prevmessages = "";
				
				//get the messages for the user
				foreach ( $msg ['messages'] as $prevmess ) {
					//get the message
					if($prevmess['parentid'] != "")
					{
						$fromwho = $prevmess['msgtype'];
						$cssclass = "subdued";
					}else{
						$fromwho = "User";
						$cssclass = "";
					}
							$timeArr = split(" ", $prevmess['datesent']);
					$prevmessages .= '<span class="subdued" style="small">['. $timeArr[1].']</span> <span class="'.$cssclass.'">'.$this->objWashout->parseText ( nl2br ( htmlentities ( "$fromwho: ".$prevmess ['msgbody'] ) ) ) . '</span> <br/>';
					//get the reply(s) if there was any
					$replies = $this->objDBIM->getReplies($prevmess['id']);
			
					$lastmsgId = $prevmess ['id'];
				}
			
			
				$ajax = "<p class=\"im_source\" id=\"replydiv" . $lastmsgId . "\">Reply..</p>
					   <p class=\"im_source\">
								<script charset=\"utf-8\">
								
						new Ajax.InPlaceEditor('replydiv" . $lastmsgId . "', 'index.php', { 	okText:'Send', 	
														cancelText: 'Cancel',
														savingControl: 'link',  
														savingText: 'Sending the message...',
														size:40,
														callback: function(form, value) { return 'module=im&action=reply&msgid=" . $lastmsgId . "&fromuser=" . $msg ['person'] . "&myparam=' + escape(value) }})
						</script>
									</p><p class=\"im_reassign\">&nbsp;".$resassignLink."&nbsp;&nbsp;&nbsp;".$archive."&nbsp;&nbsp;&nbsp;".$feedback."</p>";
						
				
				$pres = $this->getPresenceIndicator($msg['person']);
				
				//if ($this->objDbImPres->needsReply($msg['person']))
				//{
				//	$divClass = "im_replyneeded";
				//} else {
					$divClass = "im_default";
				//}
				$box .= '<td width="400px"><a name="'.$msg ['person'].'"></a><div class="'.$divClass.'" >' ;
				$box .= '<p class="im_source">'.$replyIcon."&nbsp;".$hidden.'&nbsp;&nbsp;&nbsp;'.$pres.'&nbsp;<b>' .$this->getAliasEditor($msg ['person'], $lastmsgId). '</b></p>';
				$box .= '<p style ="height : 200px; overflow : auto;" class="im_message">'   . $prevmessages . '</p><p>' . $ajax . '</p></div>';
				$box .= '</td>';
			}
		  
		//try to put 4 conversations in a row
		$rownum ++;
		if ($rownum == $max) {
			$box .= "</tr><tr>";
			$rownum = 0;
		}

			$ret .= $box;

        }
        header("Content-Type: text/html;charset=utf-8");
        return "<table>" . $ret . "</table>";
    }
	
	/**
	* Method to get the alias editor
	*
	*/
	public function getAliasEditor($personId, $lastmsgId)
	{
		$alias = $this->objAilas->getAlias($personId);
		
		$name = ($alias) ? $alias: $this->maskUser($personId);
			
		$aliasAjax = "<span  id=\"aliasdiv" . $lastmsgId . "\">$name</span>
			   
					    <script charset=\"utf-8\">
				new Ajax.InPlaceEditor('aliasdiv" . $lastmsgId . "', 'index.php', { 	okText:'Save', 	
											    cancelText: 'Cancel',
											    savingControl: 'link',  
											    savingText: 'Saving Alias...',
											    size:40,
											    callback: function(form, value) { return 'module=das&action=addalias&personid=" . $personId . "&myparam=' + escape(value) }})
			    </script> ";
				
		return $aliasAjax;
	}

    /**
     *Method to get the archived messages for a user
     *@param string $personId
     *@return string
     *@access public
     */
    public function getArchivedMessages($personId)
    {
	//get the users old messages
	$objDBArchive = $this->getObject('dbdas', 'das');
	$msgs = $objDBArchive->getArchivedMessages($personId);
	
	return $this->_formatMessages($msgs);
	
	}
	
	/**
	* Method to get the presence idicator for a user
	* @param string $person
	*/
	public function getPresenceIndicator($person)
	{
		$pres = $this->objDbImPres->getPresenceIndicator($person);

		switch(strtolower($pres[0]))
		{
			case 'none':
				if ($pres[1] == 'unavailable')
				{
					$icon = 'offline';
				} else {
					$icon = 'online';
				}
				break;
			case 'dnd':
				$icon = 'busy';
				break;
			case 'away':
				$icon = 'idle';
				break;
			case 'unavailable':
				$icon = 'offline';
				break;
			
		}
		$this->objIcon->setIcon ($icon, 'png');
		return $this->objIcon->show();
	}
	
	/**
     *Method to get the archived messages for a user
     *@param string $personId
     *@return string
     *@access public
     */
    public function getCurrentSessionMessages($personId)
    {
		//get the users messages
		$objDbIm = $this->getObject('dbim', 'im');		
		$msgs = $objDbIm->getPersonMessages($personId, 100);		
		return $this->_formatMessages($msgs);	
	}
	
	
	/**
	* Method to format messages
	* @param array $msg
	*/
	private function _formatMessages($msgs)
	{
	
	if (count($msgs) > 0)
	{
	    $prevmessages = "";
	    foreach ( $msgs as $prevmess )
	    {
                //get the message
                if($prevmess['parentid'] != "")
                {
                        $fromwho = $prevmess['msgtype'];
                        $cssclass = "subdued";
                }else{
                        $fromwho = "User";
                        $cssclass = "";
                }
		$timeArr = split(" ", $prevmess['datesent']);
                $prevmessages .= '<span class="subdued" style="small">['. $prevmess['datesent'].']</span> <span class="'.$cssclass.'">'.$this->objWashout->parseText ( nl2br ( htmlentities ( "$fromwho: ".$prevmess ['msgbody'] ) ) ) . '</span> <br/>';
                //get the reply(s) if there was any
                //$replies = $this->objDBIM->getReplies($prevmess['id']);

                //$lastmsgId = $prevmess ['id'];
            }
	    $str = '<div style="padding : 4px; height : 300px; overflow : auto; ">'.$prevmessages.'</div>';

	    //$str = $prevmessages;
	}else{
	    
	    $str = '<span class="warning">No Messages found in the archives for this person</span>';
	}
	return $str;
    }
    
    /**
    * Nethod to show the quick links to conversations
    */
    public function renderLinkList($msgs)
	{
		if(count($msgs) > 0)
		{
			
			$anchor = $this->getObject('link', 'htmlelements');
			$icon = $this->getObject('geticon', 'htmlelements');
			$icon->setIcon('icq_on');

			$str = '	<ul>';                                            
			$class = ' class="first" ';

			foreach($msgs as $msg)
			{

				$anchor->href = '#'.$msg['person'];
				if($this->objAilas->hasAlias($msg['person']))
				{
					$link = $this->objAilas->getAlias($msg['person']);
				} else {
					$link = $this->maskUser($msg['person']);
				}
				$anchor->link = $link;
				
				if ($this->objDbImPres->needsReply($msg['person']))
				{
					$replyIcon = $icon->show();
				} else {
					$replyIcon = "";
				}
				$str .="<li>".$anchor->show()."</li>";

				$class = "  class=\"personalspace\" ";

			}
			$str .= '</ul>';
			return $this->objFeatureBox->show('Quick Links', $str);
		} else {
			return "";
		}
	
	}

	/**
	* Method to render stats
	*/
	public function getStatsBox()
	{
		$objPres = $this->getObject ( 'dbimpresence' , 'im' );
		$objIMUsers = $this->getObject ( 'dbimusers', 'im' );
		//number of live conversations
		$cid = $this->objUser->userId();
		$outof = '/'.$this->objDbImPres->numOfUserAssigned ($cid);
		$msgs = $this->objDbIm->getMessagesByActiveUser ($cid);
		$numConv = $objPres->getRecordCount();
		
		$num = count($msgs);
		$myMessages = $num.$outof;
		

		$liveconv = $objPres->countActiveUsers();
		if((int)$liveconv > 0 ||  (int)$objIMUsers->countCounsellors())
		{
			$avg = (int)$liveconv / (int)$objIMUsers->countCounsellors();
		} else {
			$avg = 0;
		}
		$str = '<table>';

		$str .= '<tr><td>My Conversations</td><td> '.$myMessages.'</td></tr>';

		$str .= '<tr><td>Live Consersations</td><td>'.$liveconv."/".$numConv.'</td></tr>';
			
		$str .= '<tr><td>Avg Conversation per Counsellor</td><td>'.number_format($avg,2).'</td></tr>';

		$str .= '<tr><td>Messages</td><td> '.$objPres->countMessages().'</td></tr>';


		$str .= '</table>';
		return $this->objFeatureBox->show('Stats', $str);
	}
	
	/**
	 * Method to render a search form
	 */
	public function searchBox() {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
            'action' => 'searchmessages',
        )));
        $qseekform->addRule('keyword', $this->objLanguage->languageText("mod_das_phrase_searchtermreq", "das") , 'required');
        $qseekterm = new textinput('keyword');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_das_qseek", "das") , $this->objLanguage->languageText("mod_das_qseekinstructions", "das") . "<br />" . $qseekform);

        return $ret;
    }

    /**
    * Method to get the left blocks
    */
    public function getLeftBlocks()
    {
	$objBlocks = $this->getObject('blocks','blocks');
	$blocks = "";
	
	//Stats block
	$blocks .= $this->getStatsBox();
	
	//Conversations Block
	$blocks .= $this->getCounselorStatBox();
	
	// Search Block
	$blocks .= $this->searchBox();
	
	//Chat block
	//$blocks .= $this->getChatBlock();//$objBlocks->showBlock('contextchat', 'messaging', '', '', FALSE, FALSE);
        return $blocks;
	
    }
	

    /**
    * Method to get the right blocks
    */
    public function getRightBlocks()
    {
        
        $blocks = $this->renderLinkList($this->objDbIm->getMessagesByActiveUser(
                                 $this->objUser->userId()));
                                 
        $blocks .= $this->mxitDictionary();
        $blocks .= $this->advisorMassMessage();
        
        return $blocks;
    }


    /**
     *Method to to the contact
     *@param string $person The Person
     *@return string
     *@access public
     */
    public function maskUser($person)
    {
		return 'xxxxxxx'.substr($person, strpos($person, '@'));
	
    }
	
	
	/**
	* Method to see the number of 
	* live conversations of other counselors
	*/
	public function getCounselorStatBox()
	{
		$objPres = $this->getObject ( 'dbimpresence' , 'im' );
		$objIMUsers = $this->getObject ( 'dbimusers', 'im' );
		$objUser = $this->getObject('user', 'security');
		//get all the live counselors
		$activeCounselors = $objIMUsers->getAll();
		if(count($activeCounselors) > 0)
		{
			$str = '<table>';
			$myUserId = $objUser->userId();
			foreach($activeCounselors as $counselor)
			{
				if ($myUserId != $counselor['userid'])
				{
					$name = $objUser->fullname($counselor['userid']);
					$msgs = $objPres->countActiveUsers($counselor['userid'])."/".$this->objDbImPres->numOfUserAssigned ($counselor['userid']);;
					
					$str .= '<tr><td>'.$name.'</td><td> '.$msgs.'</td></tr>';
				}
			}



			$str .= '</table>';
		} else {
			$str = '<span class="subdued">No Records Found</span>';
		}
		return $this->objFeatureBox->show('Other Counselors', $str);
		
	}
	
	/**
	 * Method to get the chat block for the counselor
	 * chat
	 * @return string
	 * @access public
	 */
	public function getChatBlock()
	{
		$this->objIcon->setIcon('loader');
		$str = '<div style="padding:5px">
					<div id="screen" style="padding:3px;width:170px;height:200px;overflow:auto">'.
						$this->objIcon->show().
					'</div>';
		$str .= ' 	<br> 
					<input type="text" size="15" id="message">
					  <button id="button"> Send </button>
				 </div>';
		return $this->objFeatureBox->show('Advisor Chat', $str);
		
	}
	
	/**
	 * Method to format the chat messages
	 * 
	 */
	public function formatChat($messages)
	{
		if(count($messages) > 0)
		{
			$str = "";
			foreach($messages as $message)
			{
				
				$str .= '<span class="subdued" style="font-size:xx-small">['.$this->objUser->fullname($message['userid']).']</span>&nbsp;';
				$str .= '<span class="warning" style="font-size:xx-small">'.$message['body'].'</span><br>';
			}
			
			return $str;
		} else {
			return '<span class="subdued">No messages found</span>';
		}
		
		
	}
	
	/**
	* Method to post a massage
	*/
	public function advisorMassMessage()
	{
		$objFB = $this->getObject('featurebox', 'navigation');
		 $ajax = "<p class=\"im_source\" id=\"massdiv\">Ready...</p>
            
			 <script charset=\"utf-8\">
                            new Ajax.InPlaceEditor('massdiv', 'index.php', {rows:10,cols:13, callback: function(form, value) { return 'module=das&action=advisormassmessage&msg=' + escape(value) }})
                        </script>
			";

		return '<p>'.$objFB->show("Send a message to everyone that you are chatting to", $ajax).'</p>';
	}
	
	public function mxitDictionary()
	{
		if($this->objModules->checkIfRegistered('mxitdictionary')){		
			
			$anchor = $this->getObject('link', 'htmlelements');
			$anchor->href = $this->uri(null, 'mxitdictionary');				
			$anchor->link = 'View Mxit Dictionary';
			return $this->objFeatureBox->show('Mxit Dictionary', $anchor->show());
		}
		
	}

}
?>
