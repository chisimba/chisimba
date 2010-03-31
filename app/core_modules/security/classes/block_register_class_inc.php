cd ..<?php
 /**
 * block_register class for Chisimba
 * 
 * A block class to produce a registration block.
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
 * @package   security
 * @author Nic Appleby
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a registration block
*
* @author Nic Appleby
* 
* $Id$
*
*/
class block_register extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

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
            $this->objConf = $this->getObject('altconfig', 'config');
            if($this->objConf->getallowSelfRegister() == 'FALSE')
            {
                $this->blockType="invisible";
            }
            if($this->objUser->isLoggedIn()) {
                $this->blockType="invisible";
            } else { 
                $this->title = $this->objLanguage->languageText("word_registration");
            }
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
        try {
            $allowRegistration =  strtolower($this->objSysConfig->getValue('MOD_SECURITY_ALLOWREGISTRATION', 'security'));
            if ($allowRegistration !== "false") {
                if($this->objUser->isLoggedIn()) {
                    return NULL;
                } else {
                    $regModule = $this->objSysConfig->getValue('REGISTRATION_MODULE', 'security');
                    if(empty($regModule)){
                        $regModule = 'userregistration';
                    }
                    $registrationSize = $this->objSysConfig->getValue('registrationsize', 'userregistration');
                    if(strtolower($registrationSize) == 'big')
                    {
                       return $this->getBigForm($regModule); 
                    }else{
                        return $this->getRegForm();                   
                    }
								
                }
            } else {
                return NULL;
            }
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
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
