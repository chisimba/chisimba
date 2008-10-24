<?php
/**
 * Annotate interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: ffmpegapi_class_inc.php 3183 2007-12-19 10:01:02Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Annotate XML-RPC Class
 * 
 * Class to provide Chisimba ADM XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class annoteapi extends object
{
    
    /**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        try {
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
    }
    
    
    public function grabXMLDoc($params)
    {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $doc = $param->scalarval();
        
        if(!file_exists($this->objConfig->getContentBasePath().'apitmp/'))
        {
            @mkdir($this->objConfig->getContentBasePath().'apitmp/');
            @chmod($this->objConfig->getContentBasePath().'apitmp/', 0777);
        }
        $localfile = $this->objConfig->getContentBasePath().'apitmp/annote'.rand(1,999);
        $ret = file_put_contents($doc, $localfile);
        
        $val = new XML_RPC_Value($ret, 'boolean');
        return new XML_RPC_Response($val);
        // Ooops, couldn't open the file so return an error message.
        return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
    }
}
?>