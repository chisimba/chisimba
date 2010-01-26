<?php
/**
*
* Login Box Sliding Panel
*
* The Login Sliding Panel using JQuery to do 
* user registration and login at the top
* of the web page by hiding/sliding the panel
*
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
* @package   userregistration
* @author    Administrative User <admin@localhost.local>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: colorbox_class_inc.php 11972 2008-12-29 22:03:01Z charlvn $
* @link      http://avoir.uwc.ac.za
*/

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check
/**
*
* Colorbox inserts text into coloured boxes
*
* Colorbox inserts text into coloured boxes, for example as used in
* the colorbox filter
*
* @category  Chisimba
* @author Wesley Nitsckie
* @copyright UWC and AVOIR under the GPL
* @package   userregistration
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id: colorbox_class_inc.php 11972 2008-12-29 22:03:01Z charlvn $
* @link      http://avoir.uwc.ac.za
*/
class loginbox extends object
{


    /**
     * Constructor method. It does nothing here
     *
     * @access public
     * @param void
     * @return VOID
     *
     */
    public function init()
    {
         $this->objAltConfig  = $this->getObject('altconfig','config');
         $this->objUser = $this->getObject('user', 'security');
         $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
         $this->isEnabled = $this->objSysConfig->getValue ( 'useloginpanel', 'userregistration' );
         
         if($this->isEnable()){
            $this->setInits();
         }
         
    }
    
    public function isEnable(){
       
        if(strtolower($this->isEnabled) == 'true'){
            return true;
        }else{
            return false;
        }
    }
    
    public function setInits(){
        $css = '<link rel="stylesheet" href="skins/_common/css/loginslide/slide.css" type="text/css" />';
       
       
       
       $js = '<script language="JavaScript" src="'.$this->getResourceUri('jquery-1.3.2.min.js', 'userregistration').'" type="text/javascript"></script>';
       $js .= '<script language="JavaScript" src="'.$this->getResourceUri('slide.js', 'userregistration').'" type="text/javascript"></script>';
	
       $this->setVar('SUPPRESS_PROTOTYPE', true); //Can't stop prototype in the public space as this might impact blocks
	   $this->setVar('SUPPRESS_JQUERY', true);
			//$this->setVar('JQUERY_VERSION', '1.3.2');
			
	   	$this->appendArrayVar('headerParams', '
    	<script type="text/javascript">	   
    	   var loginUrl = "'.$this->objAltConfig->getsiteRoot().'index.php?module=security&action=ajax_login";    		
    	   var successUrl = "'.$this->objAltConfig->getsiteRoot().'index.php?module=security&action=ajax_gotoPostlogin";    		
    	</script>');
       $this->appendArrayVar('headerParams', $css);
       $this->appendArrayVar('headerParams', $js);
       //var_dump($headerParams);
       //die;
    }

    public function show()
    {
       if($this->isEnable()){ 
           if($this->objUser->isLoggedIn()){
               return $this->getLogoutPanel();
           }else{
                return $this->getForms();
           }
       }
       
    }
    
    public function getLogoutPanel(){
        $name = $this->objUser->fullname();
        $forms = '<!-- Panel -->
        
        
<div id="toppanel">
    <div id="logoutpanel">
		<div class="content clearfix">
			
			<div class="left">				
				<li class="left">&nbsp;</li>
			</div>			
			
			<div class="left right">	
			<h2>Are you sure you want to Logout?</h2>		
				
				<button type="submit" id="logout" class="sexybutton sexysimple sexyxl sexyorange">Yes</button>
				<button type="cancel" id="cancel" class="sexybutton sexysimple sexyxl sexyyellow">No</button>
			</div>
		</div>
	</div> <!-- /login -->	
	
    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Hello '.$name.'!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="openl" class="open" href="#">Logout</a>
				<a id="closel" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->';
        
        return $forms;
    }
    
    public function getForms(){
        $forms = '<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			
			<div class="left">
				<!-- Login Form -->
				<form class="clearfix" action="#" method="post" id="login_form">
					<h1>Member Login</h1>
					<label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="pwd">Password:</label>
					<input class="field" type="password" name="password" id="password" size="23" />
	            	<label><input name="remember" id="remember" type="checkbox" checked="checked" value="forever" /> &nbsp;Remember me</label>
        			<div class="clear"></div>
					<!--input type="submit" name="submit" id="submit" value="Login" class="bt_login" /-->
					<!--button value="Login" id="submit" type="submit" name="submit" class="sexybutton"><span><span><span class="user">Login</span></span></span></button-->
					<button type="submit" id="submit" class="sexybutton sexysimple sexyxl sexyorange">Login</button>

					<a class="lost-pwd" href="#">Lost your password?</a>
				</form>
				
			</div>
			
			<!--div class="left">
				
				<button value="Login" id="msgbox" type="submit" name="submit" class="sexybutton "><span><span><span class="hourglass">Validating....</span></span></span></button>
			</div-->
			
			<div class="left right">			
				<!-- Register Form -->
				<form action="#" method="post">
					<h1>Not a member yet? Sign Up!</h1>				
					<label class="grey" for="signup">Username:</label>
					<input class="field" type="text" name="signup" id="signup" value="" size="23" />
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" size="23" />
					<label>A password will be e-mailed to you.</label>
					<!--input type="submit" name="submit" value="Register" class="bt_register" /-->
					<button type="submit" id="register" class="sexybutton sexysimple sexyxl sexyyellow">Register</button>
				</form>
			</div>
		</div>
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Hello Guest!</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#">Log In | Register</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->';
        
        return $forms;
    }
    
    public function getJS(){
        $js = '<script type="text/javascript">
                $(document).ready(function() {
	
            	// Expand Panel
            	$("#open").click(function(){
            		$("div#panel").slideDown("slow");
            	
            	});	
            	
            	// Collapse Panel
            	$("#close").click(function(){
            		$("div#panel").slideUp("slow");	
            	});		
            	
            	// Switch buttons from "Log In | Register" to "Close Panel" on click
            	$("#toggle a").click(function () {
            		$("#toggle a").toggle();
            	});		
            		
        });
        </script>';
        
        return $js;
    }
}
?>