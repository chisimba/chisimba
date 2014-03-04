<?php
 /**
 *
 * Create an account block
 *
 * A block to provide for the creation of a user account (user registration)
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
 * 
 * @category  Chisimba
 * @package   createaccount
 * @author Derek Keats
 * @copyright 2011, AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   001
 * @link      http://www.chisimba.com
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* 
*
* Create an account block
*
* A block to provide for the creation of a user account (user registration)
*
*/
class block_createaccount extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
     *
     * @var object $objLanguage String to hold the language object
     *
     * @access private
     *
     */
    private $objLanguage;
    private $objUser;
    private $objSysConfig;
    private $objConf;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            // Get the altconfig and see if we are allowing self-registration.
            $this->objConf = $this->getObject('altconfig', 'config');
            $this->allowSelfRegister = strtolower(
              $this->objConf->getallowSelfRegister()
            );
            if($this->allowSelfRegister == 'false')  {
                $this->blockType="invisible";
            }
            if($this->objUser->isLoggedIn()) {
                $this->blockType="invisible";
            }
            // The title needs to be visible for it to be available for adding as a block.
            $this->title = $this->objLanguage->languageText(
               "mod_userregistration_regtitle",
               "userregistration", "Create an account");
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
     *
     * Standard block show method.  All it does is render a register link
     * which it obtains from the getRegLink method with assistance from the
     * user registration ops class.
     *
     * @access public
     * @return string The formatted registration link or NULL
     *
     */
    public function show()
    {
        try {
            if ($this->allowSelfRegister !== "false") {
                if($this->objUser->isLoggedIn()) {
                    return NULL;
                } else {
                    return $this->getRegLink();
								
                }
            } else {
                return NULL;
            }
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    private function getRegLink()
    {
        $objOps = $this->getObject('registerops', 'createaccount');
        return '<div class="reglink"><a id="reglinkbutton" href="javascript:void(0);">'
          . $this->title . '</a></div><div id="putregisterform">'
          . $objOps-> getBasicRegForm('Register now') . '</div>';

    }










    
    public function getBigForm($regModule){
        $objAlertBox = $this->getObject('alertbox', 'htmlelements'); 
	 	$regLink = $this->newObject('link','htmlelements'); 
 	    $regLink->link = $this->objLanguage->languageText('word_register'); 
	 	$regLink->link($this->uri(array('action' => 'showregister'), $regModule)); 
 	    return  $objAlertBox->show(
 	                  $this->objLanguage->languageText('word_register'), 
 	                  $this->uri(array('action' => 'showregister'), $regModule)); 
	 	return $regLink->show(); 
    }
    
    public function getRegForm(){
        die("HERE");
        $this->setScripts();
		
		$captchaLabel = $this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request');
		$objCaptcha = $this->getObject('captcha', 'utilities');
        
            $form = '
                        <div id="demo" class=""ui-dialog-content ui-widget-content">
                            <div id="dialog" title="'.$this->title.'">
                                <div id="message"></div>
                            	
                            
                            	<form id="dialog_form">
                            	<p class="validateTips">All form fields are required.</p>
                            	<fieldset>
                            		<label for="name">'.$this->objLanguage->languageText('word_username', 'system').'</label>
                            		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
                            		<label for="email">'.$this->objLanguage->languageText('word_email', 'system', 'Email').'</label>
                            		<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
                            		<label for="password">'.$this->objLanguage->languageText('word_password', 'system').'</label>
                            		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /> 
                            		<label for="request_captcha">'.$captchaLabel.'</label>
                            		<div id="captchaDiv" style="padding-top:3px;padding-bottom:3px">
                            		  '.$objCaptcha->show().'
                            		</div>
                            		<input type="text" name="request_captcha" id="request_captcha" value="" class="text ui-widget-content ui-corner-all" /> 
                            		<a id="redraw">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>
                            	</fieldset>
                            	</form>
                            </div>
                        
                        </div>
                        
                        <button id="create-user" >
				           
				               '. $this->objLanguage->languageText('word_register').'
				           
				         </button>
    					       
					        ';
        
        return $form;
    }
    
    
    /**
     * Setting the init scripts
     *
     */
    public function setScripts(){
        $objSysConfig  = $this->getObject('altconfig','config');
        //$this->setVar('SUPPRESS_PROTOTYPE', true);
		 $this->setVar('SUPPRESS_JQUERY', true);
		 //$this->appendArrayVar('headerParams',$css);
		 $this->appendArrayVar('headerParams','<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>');
		 $this->appendArrayVar('headerParams','<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>');
		// $this->appendArrayVar('headerParams','<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />');
		 $this->appendArrayVar('headerParams','<link rel="stylesheet" href="http://jquery-ui.googlecode.com/svn/tags/latest/themes/base/jquery-ui.css" type="text/css" media="all" />');
		 $this->appendArrayVar('headerParams',$this->getJavascriptFile('register.js', 'userregistration'));
		 $str = '<script type="text/javascript">
		  registrationUrl = "'.$this->uri(array('action'=>'register')).'";
		  var baseUri = "'.$objSysConfig->getsiteRoot().'index.php";
		 </script>';
		 $this->appendArrayVar('headerParams',$str);
					 
    }
}
?>
