<?php 
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Michael Dransfield <mike@blueroot.net>                      |
// +----------------------------------------------------------------------+
//
// $Id$


/**
 * PEAR::HTML_Crypt
 *
 * The PEAR::HTML_Crypt provides methods to encrypt text, which 
 * can be later be decrypted using JavaScript on the client side
 *
 * This is very useful to prevent spam robots collecting email
 * addresses from your site, included is a method to add mailto 
 * links to the text being generated
 * 
 *  a basic example to encrypt an email address
 *  $c = new HTML_Crypt('yourname@emailaddress.com', 8);
 *  $c->addMailTo();
 *  $c->output();
 *
 * @author  Michael Dransfield <mike@blueroot.net>
 * @package HTML_Crypt
 * @version $Revision$
 */
class HTML_Crypt
{
    // {{{ properties

    /**
     * The unencrypted text
     *
     * @access public
     * @var    string
     * @see    setText()
     */
     var $text = '';
     
    /**
     * The full javascript to be sent to the browser
     *
     * @access public
     * @var    string
     * @see    getScript()
     */
     var $script = '';
     
     
    /**
     * The text encrypted - without any js
     *
     * @access public
     * @var    string
     * @see    cyrptText
     */
     var $cryptString = '';
     
     
    /**
     * The number to offset the text by
     *
     * @access public
     * @var    int
     */
     var $offset;
     
    /**
     * Whether or not to use JS for encryption or simple html
     *
     * @access public
     * @var    int
     */
     var $useJS; 
         
    /**
     * a preg expression for an <a href=mailto: ... tag
     *
     * @access public
     * @var    string
     */
     var $apreg;
     
         
    /**
     * a preg expression for an email
     *
     * @access public
     * @var    string
     */
     var $emailpreg;
     
    // }}}
    // {{{ HTML_Crypt()

    /**
     * Constructor
     *
     * @access public
     * @param string $text  The text to encrypt
     * @param int $offset  The offset used to encrypt/decrypt
     */
    function HTML_Crypt($text = '', $offset = 3, $JS = true)
    {
        $this->offset = $offset;
        $this->text = $text;
        $this->script = '';
        $this->useJS = $JS;
        $this->emailpreg = '[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}';
        $this->apreg = '\<[aA]\shref=[\"\']mailto:.*\<\/[aA]\>';
    }
    
    // }}}
    // {{{ setText()

    /**
     * Set name of the current realm
     *
     * @access public
     * @param  string $text  The text to be encrypted
     */
    function setText($text)
    {
        $this->text = $text;
    }
    
    // }}}
    // {{{ addMailTo()

    /**
     * Turns the text into a mailto link (make sure 
     * the text only contains an email)
     *
     * @access public
     */
    function addMailTo()
    {
        $email = $this->text;
        $this->text = '<a href="mailto:'.$email.'">'.$email.'</a>';
    }
    
    // }}}
    // {{{ cryptText()

    /**
     * Encrypts the text
     *
     * @access private
     */
    function cryptText()
    {
        $enc_string = '';
        $length = strlen($this->text);        

        for ($i=0; $i < $length; $i++) {
            $current_chr = substr($this->text, $i, 1);
            $inter = ord($current_chr)+$this->offset;
            $enc_char =  chr($inter);
            $enc_string .= ($enc_char == '\\' ? '\\\\' : $enc_char);
        }

        $this->cryptString = $enc_string;
    }
    
    // }}}
    // {{{ getScript()

    /**
     * Returns the script html source including the function to decrypt it
     *
     * @access public
     * @return string $script The javascript generated
     */
    function getScript()
    {
        if ($this->cryptString == '' && $this->text != '') {
            $this->cryptText();
        }
        // get a random string to use as a function name
        srand((float) microtime() * 10000000);
        $letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $rnd = $letters[array_rand($letters)] . md5(time());
        // the actual js (in one line to confuse)
        $script = "<script language=\"JavaScript\" type=\"text/JavaScript\">var a,s,n;function $rnd(s){r='';for(i=0;i<s.length;i++){n=s.charCodeAt(i);if(n>=8364){n=128;}r+=String.fromCharCode(n-".$this->offset.");}return r;}a='".$this->cryptString."';document.write ($rnd(a));</script>";
        $this->script = $script;
        return $script;
    }
    
    // }}}
    // {{{ output()

    /**
     * Outputs the full JS to the browser
     *
     * @access public
     */
    function output()
    {
        if ($this->useJS) {
            if ($this->script == '') {
                $this->getScript();
            }
            echo $this->script;
        } else {
            echo str_replace(array('@', '.'), array(' ^at^ ', '-dot-'), $this->text);
        }
    }
    
    function obStart()
    {
        ob_start();
    }
    
    function obEnd()
    {
        $text = ob_get_contents();
        $text = preg_replace_callback("/{$this->apreg}/", array($this, '_fly'), $text);
        ob_end_clean();
        echo $text;
    }
    
    function _fly($text)
    {
        $c = new HTML_Crypt($text[0]);
        $c->setText($text[0]);
        return $c->getScript();
    }
}
?>
