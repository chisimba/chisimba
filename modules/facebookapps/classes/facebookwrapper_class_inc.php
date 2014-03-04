<?php
/**
* Created on 24 Jun 2007
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
* 
* Chisimba class to wrap facebook API class
* 
* 
*/


/**
*
* Wrapper class for Facebook. This wrapper was generated
* using the generate module of the Chisimba framework as
* developed by Derek Keats on his birthday in 2006. For 
* further information about the class being wrapped, see
* the Facebook documentation.
* 
* @author Derek keats
* @package facebookapps
*
*/
class Facebookwrapper extends object
{
    public $api_client;
    public $api_key;
    public $secret;
    public $fb_params;
    public $user;

    
    /**
    * Standard init method to initialize the class 
    * (Facebook) being wrapped.
    *
    */
    public function init()
    {
        //Include the class file to wrap 
        require_once($this->getResourcePath('facebook.php', 'facebookapps'));
        //Instantiate the class
        $api_key="xx";
        $secret="xx";
        $this->$objFacebookwrapper = new Facebook($api_key, $secret);
    }

    /**
    *
    * Wrapper method for validate_fb_params in the Facebook
    * class being wrapped. See that class for details of the 
    * validate_fb_paramsmethod.
    *
    */
    public function validate_fb_params()
    {
        return $this->objFacebookwrapper->validate_fb_params();
    }

    /**
    *
    * Wrapper method for do_get_session in the Facebook
    * class being wrapped. See that class for details of the 
    * do_get_sessionmethod.
    *
    */
    public function do_get_session($auth_token)
    {
        return $this->objFacebookwrapper->do_get_session($auth_token);
    }

    /**
    *
    * Wrapper method for redirect in the Facebook
    * class being wrapped. See that class for details of the 
    * redirectmethod.
    *
    */
    public function redirect($url)
    {
        return $this->objFacebookwrapper->redirect($url);
    }

    /**
    *
    * Wrapper method for in_frame in the Facebook
    * class being wrapped. See that class for details of the 
    * in_framemethod.
    *
    */
    public function in_frame()
    {
        return $this->objFacebookwrapper->in_frame();
    }

    /**
    *
    * Wrapper method for in_fb_canvas in the Facebook
    * class being wrapped. See that class for details of the 
    * in_fb_canvasmethod.
    *
    */
    public function in_fb_canvas()
    {
        return $this->objFacebookwrapper->in_fb_canvas();
    }

    /**
    *
    * Wrapper method for get_loggedin_user in the Facebook
    * class being wrapped. See that class for details of the 
    * get_loggedin_usermethod.
    *
    */
    public function get_loggedin_user()
    {
        return $this->objFacebookwrapper->get_loggedin_user();
    }

    /**
    *
    * Wrapper method for current_url in the Facebook
    * class being wrapped. See that class for details of the 
    * current_urlmethod.
    *
    */
    public function current_url()
    {
        return $this->objFacebookwrapper->current_url();
    }

    /**
    *
    * Wrapper method for require_login in the Facebook
    * class being wrapped. See that class for details of the 
    * require_loginmethod.
    *
    */
    public function require_login()
    {
        return $this->objFacebookwrapper->require_login();
    }

    /**
    *
    * Wrapper method for require_install in the Facebook
    * class being wrapped. See that class for details of the 
    * require_installmethod.
    *
    */
    public function require_install()
    {
        return $this->objFacebookwrapper->require_install();
    }

    /**
    *
    * Wrapper method for require_add in the Facebook
    * class being wrapped. See that class for details of the 
    * require_addmethod.
    *
    */
    public function require_add()
    {
        return $this->objFacebookwrapper->require_add();
    }

    /**
    *
    * Wrapper method for require_frame in the Facebook
    * class being wrapped. See that class for details of the 
    * require_framemethod.
    *
    */
    public function require_frame()
    {
        return $this->objFacebookwrapper->require_frame();
    }

    /**
    *
    * Wrapper method for get_facebook_url in the Facebook
    * class being wrapped. See that class for details of the 
    * get_facebook_urlmethod.
    *
    */
    public function get_facebook_url($subdomain)
    {
        return $this->objFacebookwrapper->get_facebook_url($subdomain);
    }

    /**
    *
    * Wrapper method for get_install_url in the Facebook
    * class being wrapped. See that class for details of the 
    * get_install_urlmethod.
    *
    */
    public function get_install_url($next)
    {
        return $this->objFacebookwrapper->get_install_url($next);
    }

    /**
    *
    * Wrapper method for get_add_url in the Facebook
    * class being wrapped. See that class for details of the 
    * get_add_urlmethod.
    *
    */
    public function get_add_url($next)
    {
        return $this->objFacebookwrapper->get_add_url($next);
    }

    /**
    *
    * Wrapper method for get_login_url in the Facebook
    * class being wrapped. See that class for details of the 
    * get_login_urlmethod.
    *
    */
    public function get_login_url($next,$canvas)
    {
        return $this->objFacebookwrapper->get_login_url($next,$canvas);
    }

    /**
    *
    * Wrapper method for generate_sig in the Facebook
    * class being wrapped. See that class for details of the 
    * generate_sigmethod.
    *
    */
    public function generate_sig($params_array,$secret)
    {
        return $this->objFacebookwrapper->generate_sig($params_array,$secret);
    }

    /**
    *
    * Wrapper method for set_user in the Facebook
    * class being wrapped. See that class for details of the 
    * set_usermethod.
    *
    */
    public function set_user($user,$session_key,$expires)
    {
        return $this->objFacebookwrapper->set_user($user,$session_key,$expires);
    }

    /**
    *
    * Wrapper method for no_magic_quotes in the Facebook
    * class being wrapped. See that class for details of the 
    * no_magic_quotesmethod.
    *
    */
    public function no_magic_quotes($val)
    {
        return $this->objFacebookwrapper->no_magic_quotes($val);
    }

    /**
    *
    * Wrapper method for get_valid_fb_params in the Facebook
    * class being wrapped. See that class for details of the 
    * get_valid_fb_paramsmethod.
    *
    */
    public function get_valid_fb_params($params,$timeout,$namespace)
    {
        return $this->objFacebookwrapper->get_valid_fb_params($params,$timeout,$namespace);
    }

    /**
    *
    * Wrapper method for verify_signature in the Facebook
    * class being wrapped. See that class for details of the 
    * verify_signaturemethod.
    *
    */
    public function verify_signature($fb_params,$expected_sig)
    {
        return $this->objFacebookwrapper->verify_signature($fb_params,$expected_sig);
    }

}

?>