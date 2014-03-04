<?php
/**
 *
 *  Sphinxsearch operations class
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
 * @package   sphinxsearch
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
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
 * Sphinxsearch operations class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package Sphinxsearch
 *
 */
class sphinxops extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $objConfig;
    public $objSysConfig;
    public $objWashout;
    public $objUser;
    
    /**
     * Constructor
     *
     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objDbSphinx   = $this->getObject('dbsphinx');
    }
    
    /**
     * Run the search query. Here the first argument is the search term
     * and the second is the name of the index to search in.
     *
     * Search term can come from a search form
     *
     * @param string $term
     * @param string $index
     * @param boolean $asJSON
     * @param string constant $matchmode
     * @return array $results
     * @access public
     */
    public function doSearch($term, $index, $table, $asJSON = FALSE, $matchmode = SPH_MATCH_ALL) {
        // results are pulled via a SQL query, so we need to tell it which table to use
        $this->objDbSphinx->setTable($table);
        // Connect to sphinx server
        $sp = new SphinxClient(); 
        // Set the server
        // get the server IP and searchd port from dbsysconfig
        $server = $this->objSysConfig->getValue('sphinxserver', 'sphinx');
        $port = $this->objSysConfig->getValue('sphinxport', 'sphinx');
        $port = intval($port);
        
        $sp->SetServer($server, $port);
        
        // SPH_MATCH_ALL will match all words in the search term
        $sp->SetMatchMode($matchmode);
        
        // We want an array with complete per match information including the document ids
        $sp->SetArrayResult(true);
        $results = $sp->Query($term, $index);
        if ( $results === false ) {
            return $sp->GetLastError();
        }
        else {
            if ( $sp->GetLastWarning() ) {
                return $sp->GetLastWarning();
            }
            if ( ! empty($results["matches"]) ) {
                foreach ( $results["matches"] as $doc ) {
                    $res[] = $this->objDbSphinx->getResultRow($doc["id"]);
                }
                if($asJSON === TRUE) {
                    $json = $this->objDbSphinx->jsonify($res);
                    // header("Content-Type: application/json");
                    return $json;
                }
                else {
                    return $res;
                }
            }
            else {
                return FALSE;
            }
        }
    }
    
    /**
	 * Method to render a search form
	 */
	public function searchBox() {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
            'action' => 'search',
        )));
        $qseekform->addRule('term', $this->objLanguage->languageText("mod_sphinx_phrase_searchtermreq", "sphinx") , 'required');
        $qseekterm = new textinput('term');
        $qseekterm->setValue($this->objLanguage->languageText("mod_sphinx_qseek", "sphinx"));
        $qseekterm->size = 15;
        $qseekform->addRule('index', $this->objLanguage->languageText("mod_sphinx_phrase_searchindexreq", "sphinx") , 'required');
        $qseekindex = new textinput('index');
        $qseekindex->setValue('*');
        $qseekindex->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $qseekform->addToForm($qseekindex->show());
        
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_sphinx_qseek", "sphinx") , $this->objLanguage->languageText("mod_sphinx_qseekinstructions", "sphinx") . "<br />" . $qseekform);

        return $ret;
    }

}
?>
