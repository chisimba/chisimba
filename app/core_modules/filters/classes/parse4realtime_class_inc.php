<?php

/**
*
* Class used to parse [REALTIME]room name[/REALTIME] filter into a link to an
instant virtual classroom
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
* @author    David Wafuala <Wanyonyi.Wafula@wits.ac.za>
* @copyright 2009 David Wafula
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
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
 *
 * Parse string for filter for displaying a virtual classroom
 *
 * Class to parse a string (e.g. page content) that contains a filter
 * code for including the all files in a user directory as links with descriptions
 * where descriptions exist.
 *
 */
class parse4realtime extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;



    /**
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objUser = $this->getObject('user', 'security');
        $this->filemanager =  $this->getObject('dbfile','filemanager');
        $this->_objConfig = $this->getObject('altconfig', 'config');

        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set params
        $isRegistered = $objModule->checkIfRegistered('realtime');
        if ($isRegistered){

        }
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('realtime');
        if ($isRegistered){
            //this takes the format [REALTIME]Room Name[/REALTIME]
            preg_match_all('/\\[REALTIME](.*?)\\[\/REALTIME]/', $txt, $results, PREG_PATTERN_ORDER);            $roomName = $results[1][0];

            $counter = 0;
            foreach ($results[0] as $item)
            {
                $content = $results[1][$counter];

                $replacement=$this->renderVirtualRoom($content);
                $txt = str_replace($item, $replacement, $txt);
                $counter++;
            }

        }else{
            //Commented out by JO'C
            //It breaks the content when the Realtime module is not installed.
            //$txt.=' Error: Realtime module not registered';
        }

        return $txt;
    }


    /**
     * loop through the albums, displaying each of them.
     * @todo: Its is always one album anyway, the loop might not
     * be necessary
     * @param <Array> $albums
     * @return well htm formated display of the album
     */
    private function renderVirtualRoom(
        $room,
        $slidesDir='/',
        $presentationId='/',
        $presentationName='/'
        ){

        $str = '';

        $siteRoot=$this->_objConfig->getSiteRoot();
        $imgLink='<img src="'.$siteRoot.'skins/_common/icons/webpresent/btn_ENTER.jpg" width="200" height="80">';

        $modPath=$this->_objConfig->getModulePath();
        $replacewith="";
        $docRoot=$_SERVER['DOCUMENT_ROOT'];
        $resourcePath=str_replace($docRoot,$replacewith,$modPath);
        $codebase="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/realtime/resources/';

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $servletURL=$objSysConfig->getValue('SERVLETURL', 'realtime');
        $openfireHost=$objSysConfig->getValue('OPENFIRE_HOST', 'realtime');
        $openfirePort=$objSysConfig->getValue('OPENFIRE_CLIENT_PORT', 'realtime');
        $openfireHttpBindUrl=$objSysConfig->getValue('OPENFIRE_HTTP_BIND', 'realtime');

        $username=$this->objUser->userName();
        $fullnames=$this->objUser->fullname();
        $email=$this->objUser->email();
        $ispresenter=$this->objUser->isLecturer()?"yes":"no";
        $inviteUrl=$this->_objConfig->getSiteRoot();
        $roomUrl='';
        $roomUrl.=$servletURL.'?';
        $roomUrl.='port='.$openfirePort.'&';
        $roomUrl.='host='.$openfireHost.'&';
        $roomUrl.='username='.$username.'&';
        $roomUrl.='roomname='.$room.'&';
        $roomUrl.='audiovideourl='.$openfireHttpBindUrl.'&';
        $roomUrl.='slidesdir='.$slidesDir.'&';
        $roomUrl.='ispresenter='.$ispresenter.'&';
        $roomUrl.='presentationid='.$presentationId.'&';
        $roomUrl.='presentationName='.$presentationName.'&';
        $roomUrl.='names='.$fullnames.'&';
        $roomUrl.='email='.$email.'&';
        $roomUrl.='inviteurl='.$inviteUrl.'&';
        $roomUrl.='useec2=false&';
        $roomUrl.='joinid=none&';
        $roomUrl.='codebase='.$codebase;
        $str.='<a href = "'.$roomUrl.'">'.$imgLink.'</a>';

        return $str;
    }
}
?>
