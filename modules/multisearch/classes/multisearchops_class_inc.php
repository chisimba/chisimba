<?php
/**
 * Multisearch operations class
 *  
 * This class is based on code by:
 * GooHooBi API by Christian Heilmann
 * @link http://github.com/codepo8/GooHooBi
 *
 * PHP version 5.1.0+
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
 * @package   multisearch
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * multisearch operations class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package twitterizer
 *
 */
class multisearchops extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $objWashout;
    public $objCurl;
    
    /**
     * Constructor
     *
     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
    }
    
    public function queryForm() {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('query', $this->uri(array(
        'action' => 'lookup',
        )));
        $qseekform->addRule('query', $this->objLanguage->languageText("mod_multisearch_phrase_searchtermreq", "multisearch") , 'required');
        $qseekterm = new textinput('query');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setIconClass('find');
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_multisearch_qseek", "multisearch") , $qseekform);

        return $ret;
    }

    public function buildQuery($query, $limit=20) {
        $queries = NULL;
        $queries[] = 'select Title,Description,Url,DisplayUrl '.'from microsoft.bing.web('.$limit.') where query="'.$query.'"';
        $queries[] = 'select title,clickurl,abstract,dispurl '.'from search.web('.$limit.') where query = "'.$query.'"';
        $queries[] = 'select titleNoFormatting,url,content,visibleUrl '.'from google.search('.$limit.') where q="'.$query.'"';
        $url = "select * from query.multi where queries='".join($queries,';')."'";
        $api = 'http://query.yahooapis.com/v1/public/yql?q='.urlencode($url).'&format=json&env=store'.'%3A%2F%2Fdatatables.org%2Falltableswithkeys&diagnostics=false';
        
        return $api;
    }
    
    public function doQuery($builtQuery) {
        $data = json_decode($this->objCurl->exec($builtQuery));
        return $data;
    }
    
    public function formatQuery($data, $format='html') {
        if($data->query) {
            if($data->query->results->results[0]) {
                $res = $data->query->results->results[0]->WebResult;
                switch ($format) {
                    case 'html':
                        $bing = '<h2>Bing</h2><ul>';
                        break;
                    default:
                        $bing = "=== Bing ===\n\n";
                }
                $all = sizeof($res);
                for($i=0;$i<$all;$i++) {      
                    switch ($format) {
                        case 'html':
                            $bing .= '<li><h3><a href="'.$res[$i]->Url.'" target ="_blank">'.$res[$i]->Title.'</a></h3><p>'.@$res[$i]->Description.'<span>('.$res[$i]->DisplayUrl.')</span></p></li>';
                            break;
                        default:
                            $bing .= sprintf("%s\n%s\n%s (%s)\n\n", $res[$i]->Title, $res[$i]->Url, $res[$i]->Description, $res[$i]->DisplayUrl);
                    }
                }
                if ($format === 'html') {
                    $bing .= '</ul>';
                }
            } 
            else {
                switch ($format) {
                    case 'html':
                        $bing = "<h2>Bing</h2><h3>".$this->objLanguage->languageText("mod_multisearch_noresults", "multisearch")."</h3>";
                        break;
                    default:
                        $bing = sprintf("=== Bing ===\n\n%s\n\n", $this->objLanguage->languageText("mod_multisearch_noresults", "multisearch"));
                }
            }
            if($data->query->results->results[1]) {
                $res = $data->query->results->results[1]->result;
                switch ($format) {
                    case 'html':
                        $yahoo = '<h2>Yahoo</h2><ul>';
                        break;
                    default:
                        $yahoo = "=== Yahoo ===\n\n";
                }
                $all = sizeof($res);
                for($i=0;$i<$all;$i++) {
                    switch ($format) {
                        case 'html':
                            $yahoo .= '<li><h3><a href="'.$res[$i]->clickurl.'" target ="_blank">'.$res[$i]->title.'</a></h3><p>'.$res[$i]->abstract.'<span>('.$res[$i]->dispurl.')</span></p></li>';
                            break;
                        default:
                            $yahoo .= sprintf("%s\n%s\n%s (%s)\n\n", $res[$i]->title, $res[$i]->clickurl, $res[$i]->abstract, $res[$i]->dispurl);
                    }
                }
                if ($format === 'html') {
                    $yahoo .= '</ul>';
                }
            } 
            else {
                switch ($format) {
                    case 'html':
                        $yahoo = "<h2>Yahoo</h2><h3>".$this->objLanguage->languageText("mod_multisearch_noresults", "multisearch")."</h3>";
                        break;
                    default:
                        $yahoo = sprintf("=== Yahoo ===\n\n%s\n\n", $this->objLanguage->languageText("mod_multisearch_noresults", "multisearch"));
                }
            }
            if($data->query->results->results[2]) {
                $res = $data->query->results->results[2]->results;
                switch ($format) {
                    case 'html':
                        $google = '<h2>Google</h2><ul>';
                        break;
                    default:
                        $google = "=== Google ===\n\n";
                }
                $all = sizeof($res);
                for($i=0;$i<$all;$i++) {
                    switch ($format) {
                        case 'html':
                            $google .= '<li><h3><a href="'.$res[$i]->url.'" target ="_blank">'.$res[$i]->titleNoFormatting.'</a></h3><p>'.$res[$i]->content.'<span>('.$res[$i]->visibleUrl.')</span></p></li>';
                            break;
                        default:
                            $google .= sprintf("%s\n%s\n%s (%s)\n\n", $res[$i]->titleNoFormatting, $res[$i]->url, $res[$i]->content, $res[$i]->visibleUrl);
                    }
                }
                if ($format === 'html') {
                    $google .= '</ul>';
                }
            } 
            else {
                switch ($format) {
                    case 'html':
                        $google = "<h2>Google</h2><h3>".$this->objLanguage->languageText("mod_multisearch_noresults", "multisearch")."</h3>";
                        break;
                    default:
                        $google = sprintf("=== Google ===\n\n%s\n\n", $this->objLanguage->languageText("mod_multisearch_noresults", "multisearch"));
                }
            }
  
            $out = array('google' => $google, 'yahoo' => $yahoo, 'bing' => $bing);
        } 
        else {
           $out = $this->objLanguage->languageText("mod_multisearch_noresults", "multisearch");
        }
        if ($format === 'plaintext') {
            foreach ($out as &$i) {
                $i = iconv('UTF-8', 'UTF-8//IGNORE', html_entity_decode(strip_tags($i), ENT_QUOTES, 'UTF-8'));
            }
        }
        return $out;
    }
    
}
