<?php
/**
* Class to parse a string (e.g. page content) that contains a
* tag for displaying a metaweblog API based blog post at the point of the
* tag.
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
* @package   filters
* @author    Paul Scott <pscott@uwc.ac.za>
* @copyright 2007 Paul Scott
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: parse4adsense_class_inc.php 11998 2008-12-29 22:35:37Z charlvn $
* @link      http://avoir.uwc.ac.za
*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 * XML-RPC Client class provided by filters
 *
 * @category  Chisimba
 * @package   filters
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: parse4adsense_class_inc.php 11998 2008-12-29 22:35:37Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */
class parse4blogpost extends object
{
    /**
     * Language Object
     *
     * @var object
     */
    public $objLanguage;

    /**
     * Config object
     *
     * @var object
     */
    public $objConfig;

    /**
     * Sysconfig object
     *
     * @var object
     */
    public $sysConfig;
    
    /**
     * Port
     */
    public $port = 80;
    
    /**
     * Proxy info
     */
    public $proxy;
    
    /**
     * Proxy object
     */
    public $objProxy;
    

    /**
     * Standard init function
     *
     * @param void
     * @return void
     */
    public function init()
    {
        require_once($this->getPearResource('XML/RPC.php'));
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objProxy = $this->getObject('proxy', 'utilities');
        
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        
        // get the proxy info if set
        $proxyArr = $this->objProxy->getProxy(NULL);
        if (!empty($proxyArr)) {
            $this->proxy = array(
                'proxy_host' => $proxyArr['proxyserver'],
                'proxy_port' => $proxyArr['proxyport'],
                'proxy_user' => $proxyArr['proxyusername'],
                'proxy_pass' => $proxyArr['proxypassword']
            );
        }
        else {
            $this->proxy = array(
                'proxy_host' => '',
                'proxy_port' => '',
                'proxy_user' => '',
                'proxy_pass' => '',
            );
        }
    }

    
    /**
    * Parse the string in the form of [BLOGPOST: server=127.0.0.1,
    * endpoint=/cmysql, postid=gen14Srv13Nme33_44423_1260037572]
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        preg_match_all('/\\[BLOGPOST:(.*?)\\]/', $txt, $results);
        $counter = 0;

        foreach ($results[1] as $item) {
            //Parse for the parameters
            $str = trim($results[1][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            $ar = $this->objExpar->getArrayParams($str, ",");
            $val = $this->getBlogPost($ar);
            //$replacement = $val;
            $replacement = $this->parseContent($val);
            $txt = str_replace($replaceable, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }
    
    /**
     * Method to get a blog post from the metaweblog api
     *
     * @param string array $ar An array of parseable parameters
     * @param void
     * @return string
     */
    private function getBlogPost($ar)
    {
        // get the server and url from the params
        if (isset($ar['server'])) {
            $this->server = $ar['server'];
        } else {
            $this->server = '127.0.0.1';
        }
        
        if (isset($this->objExpar->endpoint)) {
            $this->url = $this->objExpar->endpoint.'/index.php?module=api';
        } else {
            $this->url = '/index.php?module=api';
        }
        
        // OK now get the post ID from the filter text.
        if (isset($ar['postid'])) {
            $this->postid = $ar['postid'];
        } else {
            $this->postid = NULL;
        }

        $params = array(new XML_RPC_Value($this->postid, "string"), 
          new XML_RPC_Value('username', "string"),
          new XML_RPC_Value('password', "string"));
        
        // Create the message.
        $msg = new XML_RPC_Message('metaWeblog.getPost', $params);
        $cli = new XML_RPC_Client($this->url, $this->server, $this->port, 
          $this->proxy['proxy_host'], $this->proxy['proxy_port'],
          $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
        $cli->setDebug(0);
        
        // Send the request message.
        $resp = $cli->send($msg);
        if (!$resp) {
            throw new customException($this->objLanguage->languageText("mod_packages_commserr",
              "packages").": ".$cli->errstr);
            exit;
        }
        if (!$resp->faultCode()) {
            $val = $resp->value();
            $xml = $val->serialize($val);
            $data = simplexml_load_string($xml);
            return $data->struct->member->value->string;
        } else {
            // Display problems that have been gracefully caught and
            //   reported by the xmlrpc server class.
            throw new customException($this->objLanguage->languageText("mod_packages_faultcode",
              "packages").": ".$resp->faultCode() .
               $this->objLanguage->languageText("mod_packages_faultreason",
               "packages").": ".$resp->faultString());
        }
    }

    /**
     *
     * Parse the output for any contained filters
     *
     * @param string $val The content being parsed
     * @return string The parsed content
     * @access private
     * 
     */
    private function parseContent($val)
    {
        $objWashout = $this->getObject('washout', 'utilities');
        // Avoid parsing the Ajax-based filters.
        return $objWashout->parseText($val, TRUE,array('blog', 'filepreview'));
    }
}
?>