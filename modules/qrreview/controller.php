<?php
/**
 * QRreview controller class
 *
 * Class to control the QRreview module
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
 * @package   qrreview
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * QRreview controller class
 *
 * Class to control the QRreview module.
 *
 * @category  Chisimba
 * @package   qrreview
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class qrreview extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objQrOps;
    public $sysType = 'wine';

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            $this->objQrOps      = $this->getObject('qrops', 'qrcreator');
            $this->objDbQr       = $this->getObject('dbqr', 'qrcreator');
            $this->objReviewOps  = $this->getObject('reviewops');
            $this->objDbReview   = $this->getObject('dbqrreview');
            $this->objTu         = $this->getObject("tinyurlapi", "utilities");
            $this->objSysConfig  = $this->getObject('dbsysconfig', 'sysconfig');
            
            $this->sysType       = $this->objSysConfig->getValue('type', 'qrreview');
            $this->apionly       = strtolower($this->objSysConfig->getValue('apionly', 'qrreview'));
            if($this->apionly == 'true') {
                $this->apionly = TRUE;
            }
            else {
                $this->apionly = FALSE;
            }
			
            if($this->objModuleCat->checkIfRegistered('activitystreamer'))
            {
                $this->objActStream = $this->getObject('activityops','activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActStream, 'postmade' ));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:

            case 'viewtop' :
                // $this->requiresLogin('viewtop');
                return 'front_tpl.php';
                break;
            
            case 'new' :
                $createbasic = $this->objReviewOps->addForm();
                //echo $createbasic;
                if($this->apionly == TRUE) {
                    echo $createbasic;
                    break;
                }
                $this->setVarByRef('createbasic', $createbasic);
                return 'newproduct_tpl.php';
                break;

            case 'addprod' :
                $longdesc = $this->getParam('longdesc');
                $prodname = $this->getParam('prodname');
                $farmid = $this->getParam('farmid');
                if($this->apionly == TRUE) {
                    $userid = 1;
                }
                else {
                    $userid = $this->objUser->userId();
                }
                
                $recarr = array('longdesc' => $longdesc, 'prodname' => $prodname, 'userid' => $userid, 'farmid' => $farmid);
                $recid = $this->objDbReview->insertRecord($recarr);
                
                $url = $this->uri(array('id' => $recid, 'action' => 'mobireview'), 'qrreview');
                // tinyurl the url as i-nigma doesn't pick up anything after ?
                $url = str_replace('&amp;', '&', $url);
                $url = $this->objTu->createTinyUrl($url);
                $data = $this->objReviewOps->genBasicQr($userid, $url);
                // update the table with the code details too.
                $fileurl = $data['filename'];
                $fileurl = array('qr' => $fileurl);
                $this->objDbReview->updateQR($recid, $fileurl);
                $this->nextAction('details', array('id' => $recid));
                break;
                
            case 'details' :
                $id = $this->getParam('id');
                $row = $this->objDbReview->getRecord($id);
                if(!isset($row[0])) {
                    return 'notfound_tpl.php';
                    break;
                }
                else {
                    $row = $row[0];
                }
                if($this->apionly == TRUE) {
                    $this->nextAction('makepdf', array('id' => $id));
                }
                else {
                    $this->setVarByRef('row', $row);
                    $this->setVarByRef('filename', $filename);
                    return 'detailview_tpl.php';
                }
                break;

            case 'json_getdata' :
                $id = $this->getParam('id');
                $row = $this->objDbReview->getRecord($id);
                if(!isset($row[0])) {
                    echo "Not found";
                    break;
                }
                else {
                    $row = $row[0];
                }
                if($this->apionly == TRUE) {
                    header("Content-Type: application/json");
                    echo json_encode($row); 
                }
                break;

                
            case 'mobireview' :
                // mobile clients will come here via the QR code
                $id = $this->getParam('id');
                $row = $this->objDbReview->getRecord($id);
                if(!isset($row[0])) {
                    return 'notfound_tpl.php';
                    break;
                }
                else {
                    $row = $row[0];
                }
                if($this->apionly == TRUE) {
                    echo $this->objReviewOps->showReviewFormMobi($row);
                }
                else {
                    echo $this->objReviewOps->showReviewFormMobi($row);
                }
                break;
                
            case 'addreview' :
                // for either mobi or site based reviews, end point is the same
                $prodrate = $this->getParam('prodrate');
                $prodrate = intval($prodrate)*2;
                $prodcomm = $this->getParam('prodcomm');
                $phone = $this->getParam('phone');
                $prodid = $this->getParam('prodid');
                $product = $this->objDbReview->getRecord($prodid);
                $farmid = $product[0]['farmid'];
                // quick double check in case a phone browser doesn't support JS
                if(strlen($phone) != 10 || $prodrate == '' || intval($phone) == 0) {
                    $out = $this->objLanguage->languageText("mod_qrreview_missingtext", "qrreview");
                    $homelink = new href($this->uri('', 'qrreview'),$this->objLanguage->languageText("mod_qrreview_home", "qrreview"));
                    $out .= $homelink->show();
                    echo $out;
                    break;
                }
                // make up the data array
                $data = array('prodid' => $prodid, 'prodrate' => $prodrate, 'prodcomm' => $prodcomm, 'phone' => $phone, 'farmid' => $farmid);
                $this->objDbReview->addComment($data);
                // increment the scores
                $this->objDbReview->updateScores($prodid, $prodrate);
                // upstream to wine times if this is a wine review site
                if($this->sysType == 'wine') {
                    $this->objReviewOps->wineUpstream($data);
                    // upstream to twitter too
                    
                    return 'thanks_tpl.php';
                }
                return 'thanks_tpl.php';
                break;
                
            case 'review' :
                // case for on site reviews
                $id = $this->getParam('id');
                $row = $this->objDbReview->getRecord($id);
                if(!isset($row[0])) {
                    return 'notfound_tpl.php';
                    break;
                }
                else {
                    $row = $row[0];
                }
                $form = $this->objReviewOps->showReviewFormMobi($row);
                if($this->apionly == TRUE) {
                    echo $form;
                }
                else {
                    $this->setVarByRef('form', $form);
                    $this->setVarByRef('row', $row);
                    return 'webreview_tpl.php';
                }
                break;
                
            case 'makepdf':
                $id = $this->getParam('id');
                $row = $this->objDbReview->getRecord($id);
                if(!isset($row[0])) {
                    return 'notfound_tpl.php';
                    break;
                }
                else {
                    $row = $row[0];
                }
                // var_dump($row);
                
                //create the pdf and send it out
                $header = stripslashes($row['prodname']);
                $body = '<br /><br /><center><img src="'.$row['qr'].'" /></center><br />'.stripslashes($row['longdesc']);
                $postdate = $row['creationdate'];
                //put it all together
                //get the pdfmaker classes
                //$objPdf = $this->getObject('tcpdfwrapper', 'pdfmaker');
                $text = $header . "  " . $postdate . "\r\n\r\n" .$body;
                //$objPdf->WriteHTML($text, NULL, NULL, 'qrreview_'.$row['prodname'].'.pdf');
                //$this->nextAction('');
                echo $text;
                break;
            

            default:
                $this->nextAction('');
                break;
        }
    }

    /**
     * Method to turn off login for selected actions
     *
     * @access public
     * @param string $action Action being run
     * @return boolean Whether the action requires the user to be logged in or not
     */
    function requiresLogin($action='viewtop') {
        $allowedActions = array('mobireview', 'details', 'addreview', 'viewtop', NULL, 'review', 'json_getdata');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
