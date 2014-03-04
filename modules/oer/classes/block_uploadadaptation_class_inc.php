<?php
/**
 * This class contains util methods for displaying full original product details
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
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

/**
 * Handles files upload
 *
 * @author pwando
 */
class block_uploadadaptation extends object {

    function init() {
        $this->title = "";
    }
    /*
     *  Function displays the download form
     */

    function show() {
        $id=  $this->configData;
        //Load objects
        $this->addJS();
        $this->loadClass('link', 'htmlelements');
        $objLanguage = $this->getObject('language', 'language');
        $content = $objLanguage->languageText('mod_oer_attachingfile', 'oer');
        $this->loadClass('iframe', 'htmlelements');
        $objAjaxUpload = $this->newObject('ajaxuploader', 'oer');
        //Load Ajax Upload form
        $content.= $objAjaxUpload->show($id,'uploadproductthumbnail');

        //Back button
        $backButton = new button('back', $objLanguage->languageText('word_back'));
        $backUri = $this->uri(array("action" => "editadaptationstep3", "id" => $id));
        $backButton->setOnClick('javascript: window.location=\'' . $backUri . '\'');
        $content.= $backButton->show();

        //Finish button
        $button = new button('finish', $objLanguage->languageText('mod_oer_finish', 'oer'));
        $uri = $this->uri(array("action" => "adaptationlist"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        
        $content.= "&nbsp;".$button->show();
        return $content;
    }
    /**
     *  Contains required javascript functions for the upload
     */

    function addJS() {
        $this->appendArrayVar('headerParams', "

<script type=\"text/javascript\">
    //<![CDATA[

    function loadAjaxForm(fileid) {
        window.setTimeout('loadForm(\"'+fileid+'\");', 1000);
    }

    function loadForm(fileid) {
        var pars = \"module=oer&action=ajaxprocess&id=\"+fileid;
        new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || \"no response text\";
                $('updateform').innerHTML = response;
            },
            onFailure: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert('Could not download module: '+response);
            }
        });
    }

    function processConversions() {
        window.setTimeout('doConversion();', 2000);
    }

    function doConversion() {

        var pars = \"module=oer&action=ajaxprocessconversions\";
        new Ajax.Request('index.php',{
            method:'get',
            parameters: pars,
            onSuccess: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert(response);
            },
            onFailure: function(transport){
                var response = transport.responseText || \"no response text\";
                //alert('Could not download module: '+response);
            }
        });
    }
    //]]>
</script>            
");
    }

}

?>
