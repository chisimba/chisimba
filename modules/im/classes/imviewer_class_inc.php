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
 * @author    Derke Keats dkeats@uwc.ac.za
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
 * @author Derek Keats
 * @package IM
 *
 */
class imviewer extends object {

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
        $this->objDBIM = $this->getObject('dbim');

        $this->objIcon->setIcon ( 'green_bullet' );
        //$this->objIcon->setAlt($this->objLanguage->languageText('mod_im_available', 'im'));
        $this->activeIcon = $this->objIcon->show ();
        $this->objIcon->setIcon ( 'grey_bullet' );
        $this->inactiveIcon = $this->objIcon->show ();
    }

    public function renderOutputForBrowser($msgs) {
        $objWashout = $this->getObject ( 'washout', 'utilities' );
	$this->objIcon->setIcon('reassign','png');
	$reassignIcon = $this->objIcon->show();
        $ret = "<tr>";

        $max = 1;
        $rownum = 0;
        //var_dump($msgs);
        foreach ( $msgs as $msg ) {
            $box = "";
            //log_debug($msg);
            // whip out a content featurebox and plak the messages in
            //$from = explode('/', $msg['person']);
            $fuser = $msg ['person'];
            $msgid = $msg ['id'];

            // get the presence info if it exists
            $objPres = $this->getObject ( 'dbimpresence' );
            if ($objPres->getPresence ( $msg ['person'] ) == "available") {
                $presence = $this->activeIcon;
            } else {
                $presence = $this->inactiveIcon;
            }

	    	$this->objLink->href = $this->uri(array('action' => 'viewreassign', 'patient' => $fuser), 'das');
	    	$this->objLink->link = $reassignIcon;
			$resassignLink = $this->objLink->show();
            $sentat = $this->objLanguage->languageText ( 'mod_im_sentat', 'im' );
            $fromuser = $this->objLanguage->languageText ( 'mod_im_sentfrom', 'im' );
            $prevmessages = "";
            foreach ( $msg ['messages'] as $prevmess ) {
                //get the message
                if($prevmess['parentid'] != "")
                {
                        $fromwho = "Counsellor";
                        $cssclass = "subdued";
                }else{
                        $fromwho = "User";
                        $cssclass = "";
                }
				$timeArr = split(" ", $prevmess['datesent']);
                $prevmessages .= '<span class="subdued" style="small">['. $timeArr[1].']</span> <span class="'.$cssclass.'">'.$objWashout->parseText ( nl2br ( htmlentities ( "$fromwho: ".$prevmess ['msgbody'] ) ) ) . '</span> <br/>';
                //get the reply(s) if there was any
                $replies = $this->objDBIM->getReplies($prevmess['id']);

                $lastmsgId = $prevmess ['id'];
            }

            $ajax = "<p class=\"im_source\" id=\"replydiv" . $lastmsgId . "\">[REPLY]</p>
                       <p class=\"im_source\">
			 <script charset=\"utf-8\">
                            new Ajax.InPlaceEditor('replydiv" . $lastmsgId . "', 'index.php', {okText:'Send', callback: function(form, value) { return 'module=im&action=reply&msgid=" . $lastmsgId . "&fromuser=" . $msg ['person'] . "&myparam=' + escape(value) }})
                        </script>
			</p><p class=\"im_reassign\">&nbsp;".$resassignLink.'</p>';

            $box .= '<td width="400px"><a name="'.$msg ['person'].'"></a><div class="im_default" >' . '<p class="im_source"><b>' . $msg ['person'] . '</b></p><p style ="height : 200px; overflow : auto;" class="im_message">' . $prevmessages . '</p><p>' . $ajax . '</p></div></td>';

            //var_dump($msg);
            //$box2 = $this->objFeatureBox->showContent($presence." <b>".$fromuser."</b>: ".$msg['person'].', &nbsp;&nbsp;<b>' . $sentat . '</b>: ' . $msg ['datesent'], $box ."<br />");
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
    * Nethod to show the quick links to conversations
    */
    public function renderLinkList($msgs)
	{
		if(count($msgs) > 0)
		{

			$anchor = $this->getObject('link', 'htmlelements');
			$str = '	<ul>';
			$class = ' class="first" ';

			foreach($msgs as $msg)
			{

				$anchor->href = '#'.$msg['person'];
				$anchor->link = $msg['person'];
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
		//number of live conversations

		$str = '<table><tr><td>Live Consersations</td><td>233</td></tr>';

		$str .= '<tr><td>Messages</td><td>2343</td></tr>';


		$str .= '</table>';
		return $this->objFeatureBox->show('Stats', $str);
	}


}
?>
