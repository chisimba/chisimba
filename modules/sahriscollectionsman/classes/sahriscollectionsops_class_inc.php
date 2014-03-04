<?php
/**
 *
 * sahris collectionsman helper class
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
 * @package   sahriscollectionsman
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
 * sahriscollectionsman helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package sahriscollectionsman
 *
 */
class sahriscollectionsops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage    = $this->getObject('language', 'language');
        $this->objConfig      = $this->getObject('altconfig', 'config');
        $this->objSysConfig   = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser        = $this->getObject('user', 'security');
        $this->objDbColl      = $this->getObject('dbsahriscollections');
        $this->objFileIndexer = $this->getObject('indexfileprocessor', 'filemanager');
    }

    /**
     * Method to show a form to create a collection
     *
     * @access public
     * @param void
     * @return string form
     */
    public function addCollectionForm(){
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
        $ret = NULL;
        // start the form
        $form = new form ('addcoll', $this->uri(array('action'=>'createcollection'), 'sahriscollectionsman'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $defmsg = $this->objLanguage->languageText("mod_sahriscollections_defcollmsg", "sahriscollectionsman");
        
        // collection name
        $cn = new textinput();
        $cn->name = 'cn';
        $cn->width ='50%';
        $cnLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collname', 'sahriscollectionsman').'&nbsp;', 'input_cn');
        $table->addCell($cnLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($cn->show());
        $table->endRow();
        
        // comment
        $comment = $this->newObject('htmlarea', 'htmlelements');
        $comment->name = 'comment';
        $comment->value = $defmsg;
        $comment->width ='100%';
        $commentLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_comment', 'sahriscollectionsman').'&nbsp;', 'input_comment');
        $table->addCell($commentLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($comment->show());
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = $this->objLanguage->languageText('mod_sahriscollectionsman_addcollection', 'sahriscollectionsman');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_addcollection", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();       
        
        return $ret;
    }
    
    /**
     * Method to show a form to insert a record to a collection
     *
     * @access public
     * @param void
     * @return string form
     */
    public function addRecordForm(){
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
        //$headermsg = new htmlheading();
        //$headermsg->type = 1;
        //$headermsg->str = $this->objLanguage->languageText('phrase_addrecord', 'collectionsman');
        $ret = NULL;
        //$ret .= $headermsg->show();
        // start the form
        $form = new form ('add', $this->uri(array('action'=>'addrec'), 'sahriscollectionsman'));
        $form->extra = 'enctype="multipart/form-data"';
        $table = $this->newObject('htmltable', 'htmlelements');
        $defmsg = $this->objLanguage->languageText("mod_sahriscollections_defmsg", "sahriscollectionsman");
        $table->startRow();
        // add some rules
        // $form->addRule('', $this->objLanguage->languageText("mod_collectionsman_needsomething", "collectionsman"), 'required');

        // dropdown collection field
        $coll = new dropdown('coll');
        $list = $this->objDbColl->getCollectionNames();
        // var_dump($list);
        if(empty($list)) {
            $list = array();
        }
        foreach($list as $item) {
            $coll->addOption($item['id'], $item['collname']);
        }
        $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collection', 'sahriscollectionsman').'&nbsp;', 'input_coll');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($coll->show());
        $table->endRow();

        // accession number
        $ano = new textinput();
        $ano->name = 'ano';
        $ano->width ='50%';
        $anoLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_accno', 'sahriscollectionsman').'&nbsp;', 'input_ano');
        $table->addCell($anoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($ano->show());
        $table->endRow();

        // title
        $title = new textinput();
        $title->name = 'title';
        $title->width ='50%';
        $titleLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_title', 'sahriscollectionsman').'&nbsp;', 'input_title');
        $table->addCell($titleLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($title->show());
        $table->endRow();

        // description
        $desc = $this->newObject('htmlarea', 'htmlelements');
        $desc->name = 'desc';
        $desc->value = $defmsg;
        $desc->width ='50%';
        $descLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_description', 'sahriscollectionsman').'&nbsp;', 'input_desc');
        $table->addCell($descLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $msg->toolbarSet = 'simple';
        $table->addCell($desc->show());
        $table->endRow();

        // media
        $objUpload = $this->newObject('selectfile', 'filemanager');
        $objUpload->name = 'media';
        // $objUpload->restrictFileList = array('mp3');
        
        //$media = new textinput();
        //$media->name = 'media';
        //$media->width ='50%';
        $mediaLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_media', 'sahriscollectionsman').'&nbsp;', 'input_media');
        $table->addCell($mediaLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objUpload->show());
        $table->endRow();

        // comment
        $comment = new textinput();
        $comment->name = 'comment';
        $comment->width ='50%';
        $commentLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_comment', 'sahriscollectionsman').'&nbsp;', 'input_comment');
        $table->addCell($commentLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($comment->show());
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_addrecord", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }
    
    /**
     * Method to show a form to upload a collection csv file
     *
     * @access public
     * @param void
     * @return string form
     */
    public function uploadCsvForm() {
        $this->loadClass('form', 'htmlelements');
        // $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        // $this->loadClass('htmlarea', 'htmlelements');
        // $this->loadClass('dropdown', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
        $ret = NULL;
        // start the form
        $form = new form ('uploadcsv', $this->uri(array('action'=>'importcsv'), 'sahriscollectionsman'));
        $form->extra = 'enctype="multipart/form-data"';
        $table = $this->newObject('htmltable', 'htmlelements');        
        // add some rules
        //$form->addRule('csv', $this->objLanguage->languageText("mod_sahriscollectionsman_needcsv", "sahriscollectionsman"), 'required');

        // csv file
        $table->startRow();
        $objUpload = $this->newObject('selectfile', 'filemanager');
        $objUpload->name = 'csv';
        $objUpload->restrictFileList = array('csv');
        $csvLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_csvfile', 'sahriscollectionsman').'&nbsp;', 'input_csv');
        $table->addCell($csvLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objUpload->show().$required);
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_uploadcsv", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }
    
    public function editRecordForm($record) {
        if(!isset($record[0])) {
            return "Empty Collection";
        }
        $record = $record[0];
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
        $ret = NULL;
        // start the form
        $form = new form ('recedit', $this->uri(array('action'=>'recedit', 'sitename' => $record['sitename'], 'gensite' => $record['gensite'], 'siteid' => $record['siteid'], 'collectionid' => $record['collectionid'], 'collectionname' => $record['collectionname'], 'recordid' => $record['id']), 'sahriscollectionsman'));
        $form->extra = 'enctype="multipart/form-data"';
        $table = $this->newObject('htmltable', 'htmlelements');
        $defmsg = $this->objLanguage->languageText("mod_sahriscollections_defmsg", "sahriscollectionsman");
        
        /*
        // dropdown collection field
        $table->startRow();
        $coll = new dropdown('collectionname');
        $list = $this->objDbColl->getCollectionNames();
        if(empty($list)) {
            $list = array();
        }
        foreach($list as $item) {
            $coll->addOption($item['id'], $item['collname']);
        }
        $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collection', 'sahriscollectionsman').'&nbsp;', 'input_collectionname');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($coll->show());
        $table->endRow();
        */
        // object name
        $objname = new textinput();
        $objname->name = 'objname';
        $objname->width ='50%';
        $objname->setValue($record['objname']);
        $objnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_objname', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($objnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objname->show());
        $table->endRow();
        
        // object type
        $objtype = new textinput();
        $objtype->name = 'objtype';
        $objtype->width ='50%';
        $objtype->setValue($record['objtype']);
        $objtypeLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_objtype', 'sahriscollectionsman').'&nbsp;', 'input_objtype');
        $table->addCell($objtypeLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objtype->show());
        $table->endRow();

        // accession number
        $accno = new textinput();
        $accno->name = 'accno';
        $accno->width ='50%';
        $accno->setValue($record['accno']);
        $anoLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_accno', 'sahriscollectionsman').'&nbsp;', 'input_accno');
        $table->addCell($anoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($accno->show());
        $table->endRow();

        // acquisition meth
        $acqmeth = new textinput();
        $acqmeth->name = 'acqmeth';
        $acqmeth->width ='50%';
        $acqmeth->setValue($record['acqmethod']);
        $acqmethLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqmeth', 'sahriscollectionsman').'&nbsp;', 'input_acqmeth');
        $table->addCell($acqmethLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($acqmeth->show());
        $table->endRow();
        
        // acq date
        $acqdate = new textinput();
        $acqdate->name = 'acqdate';
        $acqdate->width ='50%';
        $acqdate->setValue($record['acqdate']);
        $acdateLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqdate', 'sahriscollectionsman').'&nbsp;', 'input_acqdate');
        $table->addCell($acdateLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($acqdate->show());
        $table->endRow();
        
        // acq source
        $acqsrc = new textinput();
        $acqsrc->name = 'acqsrc';
        $acqsrc->width ='50%';
        $acqsrc->setValue($record['acqsrc']);
        $acqsrcLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqsrc', 'sahriscollectionsman').'&nbsp;', 'input_acqsrc');
        $table->addCell($acqsrcLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($acqsrc->show());
        $table->endRow();
        
        // common name
        $commname = new textinput();
        $commname->name = 'commname';
        $commname->width ='50%';
        $commname->setValue($record['commname']);
        $table->startRow();
        $commLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_commname', 'sahriscollectionsman').'&nbsp;', 'input_commname');
        $table->addCell($commLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($commname->show());
        $table->endRow();
        
        // local name
        $locname = new textinput();
        $locname->name = 'locname';
        $locname->width ='50%';
        $locname->setValue($record['localname']);
        $table->startRow();
        $locnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_locname', 'sahriscollectionsman').'&nbsp;', 'input_locname');
        $table->addCell($locnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($locname->show());
        $table->endRow();
        
        // classified name
        $classname = new textinput();
        $classname->name = 'classname';
        $classname->width ='50%';
        $classname->setValue($record['classname']);
        $table->startRow();
        $classnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_classname', 'sahriscollectionsman').'&nbsp;', 'input_classname');
        $table->addCell($classnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($classname->show());
        $table->endRow();
        
        // cat by form
        $catbyform = new textinput();
        $catbyform->name = 'catbyform';
        $catbyform->width ='50%';
        $catbyform->setValue($record['catbyform']);
        $table->startRow();
        $cbfLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_catbyform', 'sahriscollectionsman').'&nbsp;', 'input_catbyform');
        $table->addCell($cbfLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($catbyform->show());
        $table->endRow();
        
        // cat by tech
        $catbytech = new textinput();
        $catbytech->name = 'catbytech';
        $catbytech->width ='50%';
        $catbytech->setValue($record['catbytech']);
        $table->startRow();
        $cbtLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_catbytech', 'sahriscollectionsman').'&nbsp;', 'input_catbytech');
        $table->addCell($cbtLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($catbytech->show());
        $table->endRow();
        
        //material
        $material = new textinput();
        $material->name = 'material';
        $material->width ='50%';
        $material->setValue($record['material']);
        $table->startRow();
        $matLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_material', 'sahriscollectionsman').'&nbsp;', 'input_material');
        $table->addCell($matLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($material->show());
        $table->endRow();
        
        //technique
        $technique = new textinput();
        $technique->name = 'technique';
        $technique->width ='50%';
        $technique->setValue($record['technique']);
        $table->startRow();
        $techLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_technique', 'sahriscollectionsman').'&nbsp;', 'input_technique');
        $table->addCell($techLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($technique->show());
        $table->endRow();
        
        //dimensions
        $dimensions = new textinput();
        $dimensions->name = 'dimensions';
        $dimensions->width ='50%';
        $dimensions->setValue($record['dimensions']);
        $table->startRow();
        $dimLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_dimensions', 'sahriscollectionsman').'&nbsp;', 'input_dimensions');
        $table->addCell($dimLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($dimensions->show());
        $table->endRow();
        
        // normalloc
        $normalloc = new textinput();
        $normalloc->name = 'normalloc';
        $normalloc->width ='50%';
        $normalloc->setValue($record['normalloc']);
        $table->startRow();
        $nlocLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_normalloc', 'sahriscollectionsman').'&nbsp;', 'input_normalloc');
        $table->addCell($nlocLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($normalloc->show());
        $table->endRow();
        
        // currloc
        $currloc = new textinput();
        $currloc->name = 'currloc';
        $currloc->width ='50%';
        $currloc->setValue($record['currloc']);
        $table->startRow();
        $currlocLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currloc', 'sahriscollectionsman').'&nbsp;', 'input_currloc');
        $table->addCell($currlocLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($currloc->show());
        $table->endRow();
        
        // currloc reason
        $reason = new textinput();
        $reason->name = 'reason';
        $reason->width ='50%';
        $reason->setValue($record['reason']);
        $table->startRow();
        $locreasonLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currlocreason', 'sahriscollectionsman').'&nbsp;', 'input_reason');
        $table->addCell($locreasonLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($reason->show());
        $table->endRow();
        
        // remover
        $remover = new textinput();
        $remover->name = 'remover';
        $remover->width ='50%';
        $remover->setValue($record['remover']);
        $table->startRow();
        $moverLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_remover', 'sahriscollectionsman').'&nbsp;', 'input_remover');
        $table->addCell($moverLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($remover->show());
        $table->endRow();
        
        //physdesc
        $physdesc = new textinput();
        $physdesc->name = 'physdesc';
        $physdesc->width ='50%';
        $physdesc->setValue($record['physdesc']);
        $table->startRow();
        $pdescLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_physdesc', 'sahriscollectionsman').'&nbsp;', 'input_physdesc');
        $table->addCell($pdescLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($physdesc->show());
        $table->endRow();
        
        // distfeat
        $distfeat = new textinput();
        $distfeat->name = 'distfeat';
        $distfeat->width ='50%';
        $distfeat->setValue($record['distfeat']);
        $table->startRow();
        $distfeatLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_distfeat', 'sahriscollectionsman').'&nbsp;', 'input_distfeat');
        $table->addCell($distfeatLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($distfeat->show());
        $table->endRow();
        
        // currcond
        $currcond = new textinput();
        $currcond->name = 'currcond';
        $currcond->width ='50%';
        $currcond->setValue($record['currcond']);
        $table->startRow();
        $currcondLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currcond', 'sahriscollectionsman').'&nbsp;', 'input_currcond');
        $table->addCell($currcondLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($currcond->show());
        $table->endRow();
        
        // conservemeth
        $conservemeth = new textinput();
        $conservemeth->name = 'conservemeth';
        $conservemeth->width ='50%';
        $conservemeth->setValue($record['conservemeth']);
        $table->startRow();
        $cmethLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_conservemeth', 'sahriscollectionsman').'&nbsp;', 'input_conservemeth');
        $table->addCell($cmethLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($conservemeth->show());
        $table->endRow();
        
        // conservedate
        $conservedate = new textinput();
        $conservedate->name = 'conservedate';
        $conservedate->width ='50%';
        $conservedate->setValue($record['conservedate']);
        $table->startRow();
        $cdateLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_consdate', 'sahriscollectionsman').'&nbsp;', 'input_conservedate');
        $table->addCell($cdateLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($conservedate->show());
        $table->endRow();
        
        // conservator
        $conservator = new textinput();
        $conservator->name = 'conservator';
        $conservator->width ='50%';
        $conservator->setValue($record['conservator']);
        $table->startRow();
        $consLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_conservator', 'sahriscollectionsman').'&nbsp;', 'input_conservator');
        $table->addCell($consLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($conservator->show());
        $table->endRow();
        
        // histcomments
        $histcomments = new textinput();
        $histcomments->name = 'histcomments';
        $histcomments->width ='50%';
        $histcomments->setValue($record['histcomments']);
        $table->startRow();
        $hcLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_histcomm', 'sahriscollectionsman').'&nbsp;', 'input_histcomments');
        $table->addCell($hcLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($histcomments->show());
        $table->endRow();
        
        // maker
        $maker = new textinput();
        $maker->name = 'maker';
        $maker->width ='50%';
        $maker->setValue($record['maker']);
        $table->startRow();
        $prodLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_producer', 'sahriscollectionsman').'&nbsp;', 'input_maker');
        $table->addCell($prodLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($maker->show());
        $table->endRow();
        
        // prodplace
        $prodplace = new textinput();
        $prodplace->name = 'prodplace';
        $prodplace->width ='50%';
        $prodplace->setValue($record['prodplace']);
        $table->startRow();
        $ppLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_prodplace', 'sahriscollectionsman').'&nbsp;', 'input_prodplace');
        $table->addCell($ppLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($prodplace->show());
        $table->endRow();
        
        // prodperiod
        $prodperiod = new textinput();
        $prodperiod->name = 'prodperiod';
        $prodperiod->width ='50%';
        $prodperiod->setValue($record['prodperiod']);
        $table->startRow();
        $perLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_prodperiod', 'sahriscollectionsman').'&nbsp;', 'input_prodperiod');
        $table->addCell($perLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($prodperiod->show());
        $table->endRow();
        
        // histuser
        $histuser = new textinput();
        $histuser->name = 'histuser';
        $histuser->width ='50%';
        $histuser->setValue($record['histuser']);
        $table->startRow();
        $uLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_histuser', 'sahriscollectionsman').'&nbsp;', 'input_histuser');
        $table->addCell($uLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($histuser->show());
        $table->endRow();
        
        // placeofuse
        $placeofuse = new textinput();
        $placeofuse->name = 'placeofuse';
        $placeofuse->width ='50%';
        $placeofuse->setValue($record['placeofuse']);
        $table->startRow();
        $pouLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_placeofuse', 'sahriscollectionsman').'&nbsp;', 'input_placeofuse');
        $table->addCell($pouLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($placeofuse->show());
        $table->endRow();
        
        // periodofuse
        $periodofuse = new textinput();
        $periodofuse->name = 'periodofuse';
        $periodofuse->width ='50%';
        $periodofuse->setValue($record['periodofuse']);
        $table->startRow();
        $perLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_periodofuse', 'sahriscollectionsman').'&nbsp;', 'input_periodofuse');
        $table->addCell($perLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($periodofuse->show());
        $table->endRow();
        
        // provenance
        $provenance = new textinput();
        $provenance->name = 'provenance';
        $provenance->width ='50%';
        $provenance->setValue($record['provenance']);
        $table->startRow();
        $provLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_provenance', 'sahriscollectionsman').'&nbsp;', 'input_provenance');
        $table->addCell($provLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($provenance->show());
        $table->endRow();
        
        // collector
        $collector = new textinput();
        $collector->name = 'collector';
        $collector->width ='50%';
        $collector->setValue($record['collector']);
        $table->startRow();
        $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collector', 'sahriscollectionsman').'&nbsp;', 'input_collector');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($collector->show());
        $table->endRow();
        
        // collectdate
        $collectdate = new textinput();
        $collectdate->name = 'collectdate';
        $collectdate->width ='50%';
        $collectdate->setValue($record['collectdate']);
        $table->startRow();
        $cdLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collectdate', 'sahriscollectionsman').'&nbsp;', 'input_collectdate');
        $table->addCell($cdLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($collectdate->show());
        $table->endRow();
        
        // collmethod
        $collmethod = new textinput();
        $collmethod->name = 'collmethod';
        $collmethod->width ='50%';
        $collmethod->setValue($record['collmethod']);
        $table->startRow();
        $cmLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collectmethod', 'sahriscollectionsman').'&nbsp;', 'input_collmethod');
        $table->addCell($cmLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($collmethod->show());
        $table->endRow();
        
        // collnumber
        $collnumber = new textinput();
        $collnumber->name = 'collnumber';
        $collnumber->width ='50%';
        $collnumber->setValue($record['collnumber']);
        $table->startRow();
        $cnoLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collnumber', 'sahriscollectionsman').'&nbsp;', 'input_collnumber');
        $table->addCell($cnoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($collnumber->show());
        $table->endRow();
        
        // pubref
        $pubref = new textinput();
        $pubref->name = 'pubref';
        $pubref->width ='50%';
        $pubref->setValue($record['pubref']);
        $table->startRow();
        $prLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_pubref', 'sahriscollectionsman').'&nbsp;', 'input_pubref');
        $table->addCell($prLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($pubref->show());
        $table->endRow();
        
        $table->startRow();
        $objUpload = $this->newObject('selectfile', 'filemanager');
        $objUpload->name = 'media';
        $objUpload->restrictFileList = array('jpg', 'JPG', 'png', 'gif');
        $csvLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_mediafile', 'sahriscollectionsman').'&nbsp;', 'input_media');
        $table->addCell($csvLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($objUpload->show());
        $table->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_modify", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }

    /**
     * Method to format a single retrieved record and display it
     *
     * @access public
     * @param array $record
     * @return string
     */
    public function formatRecord($record)
    {
        if(!isset($record[0])) {
            return "Empty Collection";
        }
        $record = $record[0];
        // log_debug($record);
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $ret = NULL;
        
        // image
        $objFile = $this->getObject('dbfile', 'filemanager');
        $image = $objFile->getFilePath($record['media']);
        $alt = $objFile->getFileName($record['media']);
        $ret .= '<img src="'.$image.'" width="300" height="200" alt="'.$alt.'" />';

        
        $table = $this->newObject('htmltable', 'htmlelements');
        
        // site name
        $table->startRow();
        $snLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitename', 'sahriscollectionsman').'&nbsp;', 'input_sn');
        $table->addCell($snLabel->show(), 150, NULL, 'right');
        $sn = $record['sitename']; // $this->objDbColl->getCollById($record['collectionname']);
        // $collname = $collname[0]['collname'];
        $table->addCell('&nbsp;', 5);
        $table->addCell($sn);
        $table->endRow();
        
        // site abbr
        $table->startRow();
        $sabbrLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_siteabbr', 'sahriscollectionsman').'&nbsp;', 'input_sa');
        $table->addCell($sabbrLabel->show(), 150, NULL, 'right');
        $sa = $record['siteabbr']; // $this->objDbColl->getCollById($record['collectionname']);
        // $collname = $collname[0]['collname'];
        $table->addCell('&nbsp;', 5);
        $table->addCell($sa);
        $table->endRow();
        
        // site manager
        $table->startRow();
        $smLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitemanager', 'sahriscollectionsman').'&nbsp;', 'input_sm');
        $table->addCell($smLabel->show(), 150, NULL, 'right');
        $sm = $record['sitemanager']; // $this->objDbColl->getCollById($record['collectionname']);
        // $collname = $collname[0]['collname'];
        $table->addCell('&nbsp;', 5);
        $table->addCell($sm);
        $table->endRow();
        
        // collection name
        $table->startRow();
        $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collection', 'sahriscollectionsman').'&nbsp;', 'input_coll');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $collname = $record['collectionname']; // $this->objDbColl->getCollById($record['collectionname']);
        // $collname = $collname[0]['collname'];
        $table->addCell('&nbsp;', 5);
        $table->addCell($collname);
        $table->endRow();
        
        // object name
        $objnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_objname', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($objnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['objname']);
        $table->endRow();
        
        // object type
        $objtypeLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_objtype', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($objtypeLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['objtype']);
        $table->endRow();

        // accession number
        $anoLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_accno', 'sahriscollectionsman').'&nbsp;', 'input_ano');
        $table->addCell($anoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['accno']);
        $table->endRow();

        // acquisition meth
        $acqmethLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqmeth', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($acqmethLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['acqmethod']);
        $table->endRow();
        
        // acq date
        $acdateLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqdate', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($acdateLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['acqdate']);
        $table->endRow();
        
        // acq source
        $acqsrcLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_acqsrc', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($acqsrcLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['acqsrc']);
        $table->endRow();
        
        // gen site accession
        $gensiteLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_gensiteacc', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table->addCell($gensiteLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['gensite']);
        $table->endRow();
        
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = 'Object Identification'; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();

        // $ret .= $fieldset->show();
        
        /*****/
        
        
        
        $table2 = $this->newObject('htmltable', 'htmlelements');
        
        // common name
        $table2->startRow();
        $commLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_commname', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($commLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['commname']);
        $table2->endRow();
        
        // local name
        $table2->startRow();
        $locnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_locname', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($locnameLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['localname']);
        $table2->endRow();
        
        // classified name
        $table2->startRow();
        $classnameLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_classname', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($classnameLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['classname']);
        $table2->endRow();
        
        // cat by form
        $table2->startRow();
        $cbfLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_catbyform', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($cbfLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['catbyform']);
        $table2->endRow();
        
        // cat by tech
        $table2->startRow();
        $cbtLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_catbytech', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($cbtLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['catbytech']);
        $table2->endRow();
        
        //material
        $table2->startRow();
        $matLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_material', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($matLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['material']);
        $table2->endRow();
        
        //technique
        $table2->startRow();
        $techLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_technique', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($techLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['technique']);
        $table2->endRow();
        
        //dimensions
        $table2->startRow();
        $dimLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_dimensions', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($dimLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['dimensions']);
        $table2->endRow();
        
        // normal loc
        $table2->startRow();
        $nlocLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_normalloc', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($nlocLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['normalloc']);
        $table2->endRow();
        
        // curr loc
        $table2->startRow();
        $currlocLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currloc', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($currlocLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['currloc']);
        $table2->endRow();
        
        // currloc reason
        $table2->startRow();
        $locreasonLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currlocreason', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($locreasonLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['reason']);
        $table2->endRow();
        
        // remover
        $table2->startRow();
        $moverLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_remover', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($moverLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['remover']);
        $table2->endRow();
        
        //phys desc
        $table2->startRow();
        $pdescLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_physdesc', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($pdescLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['physdesc']);
        $table2->endRow();
        
        // dist feat
        $table2->startRow();
        $distfeatLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_distfeat', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($distfeatLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['distfeat']);
        $table2->endRow();
        
        // curr cond
        $table2->startRow();
        $currcondLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_currcond', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($currcondLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['currcond']);
        $table2->endRow();
        
        // conserve meth
        $table2->startRow();
        $cmethLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_conservemeth', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($cmethLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['conservemeth']);
        $table2->endRow();
        
        // cons date
        $table2->startRow();
        $cdateLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_consdate', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($cdateLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['conservedate']);
        $table2->endRow();
        
        // conservator
        $table2->startRow();
        $consLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_conservator', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table2->addCell($consLabel->show(), 150, NULL, 'right');
        $table2->addCell('&nbsp;', 5);
        $table2->addCell($record['conservator']);
        $table2->endRow();
        
        $fieldset2 = $this->newObject('fieldset', 'htmlelements');
        $fieldset2->legend = 'Object Details'; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset2->contents = $table2->show();

        //$ret .= $fieldset2->show();
        
        $table3 = $this->newObject('htmltable', 'htmlelements');
        
        // historical comments
        $table3->startRow();
        $hcLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_histcomm', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($hcLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['histcomments']);
        $table3->endRow();
        
        // producer
        $table3->startRow();
        $prodLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_producer', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($prodLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['maker']);
        $table3->endRow();
        
        // prodplace
        $table3->startRow();
        $ppLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_prodplace', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($ppLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['prodplace']);
        $table3->endRow();
        
        // prodperiod
        $table3->startRow();
        $perLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_prodperiod', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($perLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['prodperiod']);
        $table3->endRow();
        
        // user
        $table3->startRow();
        $uLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_histuser', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($uLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['histuser']);
        $table3->endRow();
        
        // placeuse
        $table3->startRow();
        $pouLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_placeofuse', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($pouLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['placeofuse']);
        $table3->endRow();
        
        // periodofuse
        $table3->startRow();
        $perLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_periodofuse', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($perLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['periodofuse']);
        $table3->endRow();
        
        // provenance
        $table3->startRow();
        $provLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_provenance', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($provLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['provenance']);
        $table3->endRow();
        
        // collector
        $table3->startRow();
        $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collector', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($collLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['collector']);
        $table3->endRow();
        
        // collection date
        $table3->startRow();
        $cdLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collectdate', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($cdLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['collectdate']);
        $table3->endRow();
        
        // collection method
        $table3->startRow();
        $cmLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collectmethod', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($cmLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['collmethod']);
        $table3->endRow();
        
        // collection no
        $table3->startRow();
        $cnoLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collnumber', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($cnoLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['collnumber']);
        $table3->endRow();
        
        // pubref
        $table3->startRow();
        $prLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_pubref', 'sahriscollectionsman').'&nbsp;', 'input_objname');
        $table3->addCell($prLabel->show(), 150, NULL, 'right');
        $table3->addCell('&nbsp;', 5);
        $table3->addCell($record['pubref']);
        $table3->endRow();
        
        $fieldset3 = $this->newObject('fieldset', 'htmlelements');
        $fieldset3->legend = 'Object History'; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset3->contents = $table3->show();

        // $ret .= $fieldset3->show();

        $table4 = $this->newObject('htmltable', 'htmlelements');
        $table4->startRow();
        $table4->addCell($fieldset->show().$fieldset3->show());
        $table4->addCell($fieldset2->show());
        $table4->endRow();
        
        // edit and delete link
        $e = $this->newObject('link', 'htmlelements');
        $e->href = $this->uri(array('action' => 'editrecord', 'collectionid' => $record['collectionid'], 'recordid' => $record['id']));
        $e->link = $this->objLanguage->languageText("mod_sahriscollectionsman_editrecord", "sahriscollectionsman");
        $e = $e->show();
        
        // edit and delete link
        $d = $this->newObject('link', 'htmlelements');
        $d->href = $this->uri(array('action' => 'deleterecord', 'collectionid' => $record['collectionid'], 'recordid' => $record['id']));
        $d->link = $this->objLanguage->languageText("mod_sahriscollectionsman_deleterecord", "sahriscollectionsman");
        $d = $d->show();
        
        $table4->startRow();
        $table4->addCell($e." ".$d);
        $table4->endRow();
        
        $ret .= $table4->show();
        
        header ( "Content-Type: text/html;charset=utf-8" );
        return $ret;
    }
    
    public function menuBox() {
        $ret = NULL;
        $menubox = $this->newObject('featurebox', 'navigation');
        
        // create a collection
        $createcoll = $this->newObject('link', 'htmlelements');
        $createcoll->href = $this->uri(array('action' => 'collform'));
        $createcoll->link = $this->objLanguage->languageText("mod_sahriscollectionsman_createcollection", "sahriscollectionsman");
        $createcoll = $createcoll->show();
        
        /* add a collection record
        $addrec = $this->newObject('link', 'htmlelements');
        $addrec->href = $this->uri(array('action' => 'addform'));
        $addrec->link = $this->objLanguage->languageText("mod_sahriscollectionsman_addrectocoll", "sahriscollectionsman");
        $addrec = $addrec->show(); */
        
        // site report link
        $sr = $this->newObject('link', 'htmlelements');
        $sr->href = $this->uri(array('action' => 'sitesreport'));
        $sr->link = $this->objLanguage->languageText("mod_sahriscollectionsman_sitesreport", "sahriscollectionsman");
        $sr = $sr->show();
        
        // objects report link
        $or = $this->newObject('link', 'htmlelements');
        $or->href = $this->uri(array('action' => 'objectreport'));
        $or->link = $this->objLanguage->languageText("mod_sahriscollectionsman_objectsreport", "sahriscollectionsman");
        $or = $or->show();
        
        // activity
        $su = $this->newObject('link', 'htmlelements');
        $su->href = $this->uri(array('action' => ''), 'logger');
        $su->link = $this->objLanguage->languageText("mod_sahriscollectionsman_userreport", "sahriscollectionsman");
        $su = $su->show();
        
        // search a collection record
        $searchrec = $this->newObject('link', 'htmlelements');
        $searchrec->href = $this->uri(array('action' => 'search'));
        $searchrec->link = $this->objLanguage->languageText("mod_sahriscollectionsman_searchrecords", "sahriscollectionsman");
        $searchrec = $searchrec->show();
        
        // import CSV
        $csvin = $this->newObject('link', 'htmlelements');
        $csvin->href = $this->uri(array('action' => 'uploadcsv'));
        $csvin->link = $this->objLanguage->languageText("mod_sahriscollectionsman_uploaddatafile", "sahriscollectionsman");
        $csvin = $csvin->show();
        
        // Sites list
        $sl = $this->newObject('link', 'htmlelements');
        $sl->href = $this->uri(array('action' => 'viewsites'));
        $sl->link = $this->objLanguage->languageText("mod_sahriscollectionsman_siteslist", "sahriscollectionsman");
        $sl = $sl->show();
        
        $txt = "<ul";
        $txt .= "<li>".$sl."</li>";
        $txt .= "<li>".$sr."</li>";
        $txt .= "<li>".$or."</li>";
        $txt .= "<li>".$su."</li>";
        if($this->objUser->inAdminGroup($this->objUser->userId())) {
            $txt .= "<li>".$createcoll."</li>";
        }
        // $txt .= "<li>".$addrec."</li>";
        $txt .= "<li>".$searchrec."</li>";
        if($this->objUser->inAdminGroup($this->objUser->userId())) {
            $txt .= "<li>".$csvin."</li>";
        }
        $txt .= "</ul>";
        
        $ret = $menubox->show($this->objLanguage->languageText("mod_sahriscollectionsman_menu", "sahriscollectionsman"), $txt);
        return $ret; 
    }
    
    public function formatSearchResults($results) {
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellspacing='1';
        $table->cellpadding='10';
            
        $table->startHeaderRow();
        $table->addHeaderCell('Accession Number');
        $table->addHeaderCell('Object Name');
        $table->addHeaderCell('Description');
        // $table->addHeaderCell('Comment');
        $table->addHeaderCell('Date Created');
        $table->addHeaderCell('Action');
        $table->endHeaderRow();
        
        foreach($results as $row) {
            $table->startRow();
            $table->addCell($row['accno']);
            $table->addCell($row['objname']);
            $table->addCell($row['physdesc']);
            // $table->addCell($row['comment']);
            $table->addCell($row['datecreated']);
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $url = $this->uri(array('action' => 'viewsingle', 'id' => $row['id']));
            $objIcon->setIcon('visible', 'gif');
            $v = $this->newObject('link', 'htmlelements');
            $v->href = $url;
            $v->link = $objIcon->show();
            $table->addCell($v->show());
            $table->endRow();
        }
        
        return $table->show();
        
    }
    
    public function searchForm() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
        $ret = NULL;
        $form = new form ('search', $this->uri(array('action'=>'search'), 'sahriscollectionsman'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $q = new textinput();
        $q->name = 'q';
        $q->width ='50%';
        $qLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_keyword', 'sahriscollectionsman').'&nbsp;', 'input_q');
        $table->addCell($qLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($q->show());
        $table->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_search", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center">'.$button->show().'</p>');
        $ret .= $form->show();
        
        return $ret;
    }
    
    public function parseCSV($file) {
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $rows[] = $data;
                $row++;
            }
            fclose($handle);
        }
        return $rows;
    }
    
    public function processMediaFromCSV($file, $username, $fname) {
        $userid = $this->objUser->getUserId($username);
        if(!file_exists($this->objConfig->getContentBasePath().'users/'.$userid."/"))
        {
            @mkdir($this->objConfig->getContentBasePath().'users/'.$userid."/");
            @chmod($this->objConfig->getContentBasePath().'users/'.$userid."/", 0777);
        }
        
        $objOverwriteIncrement = $this->getObject('overwriteincrement', 'filemanager');
        $fname = $objOverwriteIncrement->checkfile($fname, 'users/'.$userid);
        
        $localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$fname;
        file_put_contents($localfile, $file);

        $fmname = basename($fname);
        $fmpath = 'users/'.$userid.'/'.$fmname;

        // Add to users fileset
        $fileId = $this->objFileIndexer->processIndexedFile($fmpath, $userid);
        
        return $fileId;
    }
    
    public function processCsvData($collarr) {
        foreach($collarr as $rec) {
            $sitename = $rec[0];
            $siteabbr = $rec[1];
            $sitemanager = $rec[2];
            $collectionname = $rec[3];
            $objname = $rec[4];
            $objtype = $rec[5];
            $accno = $rec[6];
            $acqmethod = $rec[7];
            $acqdate = $rec[8];
            $acqsrc  = $rec[9];
            $origmedia = $rec[10];
            $commname = $rec[11];
            $localname = $rec[12];
            $classname = $rec[13];
            $catbyform = $rec[14];
            $catbytech = $rec[15];
            $material = $rec[16];
            $technique = $rec[17];
            $dimensions = $rec[18];
            $normalloc = $rec[19];
            $currloc = $rec[20];
            $reason = $rec[21];
            $remover = $rec[22];
            $physdesc = $rec[23];
            $distfeat = $rec[24];
            $currcond = $rec[25];
            $conservemeth = $rec[26];
            $conservedate = $rec[27];
            $conservator = $rec[28];
            $histcomments = $rec[29];
            $maker = $rec[30];
            $prodplace = $rec[31];
            $prodperiod = $rec[32];
            $histuser = $rec[33];
            $placeofuse = $rec[34];
            $periodofuse = $rec[35];
            $provenance = $rec[36];
            $collector = $rec[37];
            $collectdate = $rec[38];
            $collmethod = $rec[39];
            $collnumber = $rec[40];
            $pubref = $rec[41];
            $gensite = $rec[42];
            $media64 = $rec[43];
            $filename = $rec[44];
            $username = $rec[45];
            
            if($sitename == 'site name' || $sitename == 'sitename' || $sitename == NULL || $sitename == '') {
                continue;
            }
            else {
                if($media64 != NULL) {
                    $media = $this->processMediaFromCSV($media64, $username, $filename);
                }
                else {
                    $media = NULL;
                }
                    
                // parse the site name and optionally create a new one if needs be
                $sid = $this->objDbColl->getSiteByName($sitename);
                if($sid == NULL) {
                    // $siteabbr = metaphone($sitename, 3);
                    $siteins = array('userid' => $this->objUser->userId($username), 'sitename' => $sitename, 'siteabbr' => $siteabbr, 
                                     'sitemanager' => $sitemanager, 'sitecontact' => NULL, 'lat' => NULL, 'lon' => NULL, 'comment' => NULL);
                    $sid = $this->objDbColl->addSiteData($siteins);
                }
                    
                $sitedet = $this->objDbColl->getSiteDetails($sid);
                $siteaccabbr = $gensite; //$sitedet[0]['siteabbr'];
                $sitecount = $this->objDbColl->countItemsInSite($sid);
                  
                $siteacc = $gensite."_".$sitecount;
                    
                // get the collection id from name
                $collid = $this->objDbColl->getCollByName($collectionname);
                if($collid == NULL) {
                    // create a collection as it doesn't exist
                    $insarr = array('userid' => $this->objUser->userId($username), 'collname' => $collectionname, 'comment' => NULL, 
                                    'sitename' => $sitename, 'siteid' => $sid);
                    $collid = $this->objDbColl->insertCollection($insarr);
                }
                // and now the data     
                $insarr = array(
                'userid' => $this->objUser->userId($username),
                'sitename' => $sitename,
                'siteabbr' => $siteabbr,
                'sitemanager' => $sitemanager,
                'siteid' => $sid,
                'collectionname' => $collectionname,
                'objname' => $objname,
                'objtype' => $objtype,
                'accno' => $accno,
                'acqmethod' => $acqmethod,
                'acqdate' => $acqdate,
                'acqsrc' => $acqsrc,
                'origmedia' => $origmedia,
                'commname' => $commname,
                'localname' => $localname,
                'classname' => $classname,
                'catbyform' => $catbyform,
                'catbytech' => $catbytech,
                'material' => $material,
                'technique' => $technique,
                'dimensions' => $dimensions,
                'normalloc' => $normalloc,
                'currloc' => $currloc,
                'reason' => $reason,
                'remover' => $remover,
                'physdesc' => $physdesc,
                'distfeat' => $distfeat,
                'currcond' => $currcond,
                'conservemeth' => $conservemeth,
                'conservedate' => $conservedate,
                'conservator' => $conservator,
                'histcomments' => $histcomments,
                'maker' => $maker, 
                'prodplace' => $prodplace,
                'prodperiod' => $prodperiod,
                'histuser' => $histuser,
                'placeofuse' => $placeofuse,
                'periodofuse' => $periodofuse,
                'provenance' => $provenance,
                'collector' => $collector,
                'collectdate' => $collectdate,
                'collmethod' => $collmethod,
                'collnumber' => $collnumber,
                'pubref' => $pubref,
                'gensite' => $gensite,
                'media64' => $media64,
                'filename' => $filename,
                'username' => $username,
                'media' => $media,
                'collectionid' => $collid,
                );
            
                // var_dump($insarr); die();
                $res = $this->objDbColl->insertRecord($insarr);
                $insarr = NULL;
                $media = NULL;
            }
        }
    }
    
    public function formatSites($sites) {
        $ret = NULL;
        foreach($sites as $site) {
            $fb = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $edit = $this->newObject('geticon', 'htmlelements');
            $edit->setIcon('edit_sm');
            // edit link
            $ed = $this->newObject('link', 'htmlelements');
            $ed->href = $this->uri(array('action' => 'editsite', 'siteid' => $site['id']));
            $ed->link = $edit->show();
            
            $table->startRow();
            $smLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitemanager', 'sahriscollectionsman').'&nbsp;', 'input_sitemanager');
            $table->addCell($smLabel->show(), 150, NULL, 'left');
            $table->addCell('&nbsp;', 5);
            $table->addCell($site['sitemanager']);
            $table->endRow();
            
            $table->startRow();
            $scLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitecontact', 'sahriscollectionsman').'&nbsp;', 'input_sitecontact');
            $table->addCell($scLabel->show(), 150, NULL, 'left');
            $table->addCell('&nbsp;', 5);
            $table->addCell($site['sitecontact']);
            $table->endRow();
            
            // get the number of collections at the site
            $numcoll = $this->objDbColl->getCollCountBySite($site['id']);
            // collections link list
            $c = $this->newObject('link', 'htmlelements');
            $c->href = $this->uri(array('action' => 'viewcollection', 'siteid' => $site['id']));
            $c->link = $this->objLanguage->languageText("mod_sahriscollectionsman_viewcollectionsinsite", "sahriscollectionsman");
            $c = $c->show();
        
            $table->startRow();
            $ncLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_numcoll', 'sahriscollectionsman').'&nbsp;', 'input_numcoll');
            $table->addCell($ncLabel->show(), 150, NULL, 'left');
            $table->addCell('&nbsp;', 5);
            $table->addCell($numcoll." (".$c.")");
            $table->endRow();
            
            $ret .= $fb->show($site['sitename']." (".$site['siteabbr'].") ".$ed->show(), $table->show()); 
        }
        
        return $ret;
    }
    
    /**
     * Method to show a form to edit a site
     *
     * @access public
     * @param void
     * @return string form
     */
    public function editSiteForm($siteid) {
        $d = $this->objDbColl->getSiteDetails($siteid);
        $d = $d[0];
        
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'sahriscollectionsman', 'Required').'</span>';
      
        $ret = NULL;
        
        // start the form
        $form = new form ('add', $this->uri(array('action'=>'updatesitedata', 'id' => $d['id']), 'sahriscollectionsman'));
        $form->extra = 'enctype="multipart/form-data"';
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        // add some rules
        // $form->addRule('', $this->objLanguage->languageText("mod_collectionsman_needsomething", "collectionsman"), 'required');

        // Site name
        $sn = new textinput();
        $sn->name = 'sn';
        $sn->width ='50%';
        $sn->setValue($d['sitename']);
        $snLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitename', 'sahriscollectionsman').'&nbsp;', 'input_sn');
        $table->startRow();
        $table->addCell($snLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($sn->show());
        $table->endRow();
        
        // Site abbreviation
        $sa = new textinput();
        $sa->setValue($d['siteabbr']);
        $sa->name = 'sa';
        $sa->width ='50%';
        $saLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_siteabbr', 'sahriscollectionsman').'&nbsp;', 'input_sa');
        $table->startRow();
        $table->addCell($saLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($sa->show());
        $table->endRow();
        
        // Site Manager
        $sm = new textinput();
        $sm->setValue($d['sitemanager']);
        $sm->name = 'sm';
        $sm->width ='50%';
        $smLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitemanager', 'sahriscollectionsman').'&nbsp;', 'input_sm');
        $table->startRow();
        $table->addCell($smLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($sm->show());
        $table->endRow();

        // Site contact
        $sc = $this->newObject('htmlarea', 'htmlelements');
        $sc->name = 'sc';
        $sc->value = $d['sitecontact'];
        $sc->width ='80%';
        $scLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitecontact', 'sahriscollectionsman').'&nbsp;', 'input_sc');
        $table->startRow();
        $table->addCell($scLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $msg->toolbarSet = 'simple';
        $table->addCell($sc->show());
        $table->endRow();
        
        // Site comment
        $scom = new textinput();
        $scom->name = 'scom';
        $scom->setValue($d['comment']);
        $scom->width ='50%';
        $scomLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitecomment', 'sahriscollectionsman').'&nbsp;', 'input_scom');
        $table->startRow();
        $table->addCell($scomLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($scom->show());
        $table->endRow();
        
        // site location map
        $map = $this->geolocformelement($d);
        $gtlabel = new label($this->objLanguage->languageText("mod_sahriscollectionsman_geoposition", "sahriscollectionsman") . ':', 'input_geotags');
        $gtags = '<div id="map"></div>';
        $geotags = new textinput('geotag', NULL, NULL, '100%');
        if (isset($d['lat']) && isset($d['lon'])) {
            $geotags->setValue($d['lat'].", ".$d['lon']);
        }
        
        $table->startRow();
        $table->addCell($gtlabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($gtags.$geotags->show());
        $table->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_sahriscollectionsman_updatesite", "sahriscollectionsman"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }
    
    /**
     * Method used to set geolocation coordinates
     *
     * Users are able to set geographic coordinates by either completing a text input or clicking on a map
     *
     * @param array $editparams
     * @param boolean $eventform
     * @return string
     */
    public function geolocformelement($d) {
        $lat = $d['lat'];
        $lon = $d['lon'];
        if($lat == NULL || $lon == NULL) {
            //latlon defaults -29.113775395114,  26.2353515625
            $lat = -29.113775395114;
            $lon = 26.2353515625;
        }
        $zoom = 5;
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
        }
    </style>';

        $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = 17;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18} );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(new OpenLayers.LonLat($lon,$lat), $zoom);

            map.events.register(\"click\", map, function(e) {
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" +
                                          + lonlat.lon
            });

        }
    </script>";

        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
        $this->appendArrayVar('bodyOnLoad', "init();");
    } 
    
    public function formatCollections($colls) {
        $ret = NULL;
        foreach($colls as $coll) {
            //var_dump($coll);
            $this->objWashout = $this->getObject('washout', 'utilities');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('htmlarea', 'htmlelements');
            $ret = NULL;
            $table = $this->newObject('htmltable', 'htmlelements');
            
            $table->startRow();
            $snLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_sitename', 'sahriscollectionsman').'&nbsp;', 'input_coll');
            $table->addCell($snLabel->show(), 150, NULL, 'right');
            $sname = $coll['sitename'];
            $table->addCell('&nbsp;', 5);
            $table->addCell($sname);
            $table->endRow();
            
            $table->startRow();
            $collLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_collection', 'sahriscollectionsman').'&nbsp;', 'input_coll');
            $table->addCell($collLabel->show(), 150, NULL, 'right');
            $collname = $coll['collname'];
            $table->addCell('&nbsp;', 5);
            $table->addCell($collname);
            $table->endRow();
            
            // comment
            $table->startRow();
            $commLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_notes', 'sahriscollectionsman').'&nbsp;', 'input_coll');
            $table->addCell($commLabel->show(), 150, NULL, 'right');
            $comm = $coll['comment'];
            $table->addCell('&nbsp;', 5);
            $table->addCell($comm);
            $table->endRow();
            
            // date created
            $table->startRow();
            $dcLabel = new label($this->objLanguage->languageText('mod_sahriscollectionsman_datecreated', 'sahriscollectionsman').'&nbsp;', 'input_coll');
            $table->addCell($dcLabel->show(), 150, NULL, 'right');
            $dc = $coll['datecreated'];
            $table->addCell('&nbsp;', 5);
            $table->addCell($dc);
            $table->endRow();
            
            // view records link
            $r = $this->newObject('link', 'htmlelements');
            $r->href = $this->uri(array('action' => 'viewrecords', 'collid' => $coll['id']));
            $r->link = $this->objLanguage->languageText("mod_sahriscollectionsman_viewrecordsincoll", "sahriscollectionsman");
            $r = $r->show();
            $table->startRow();
            $table->addCell('');
            $table->addCell('&nbsp;', 5);
            $table->addCell($r);
            $table->endRow();
            
            $fieldset = $this->newObject('fieldset', 'htmlelements');
            $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
            $fieldset->contents = $table->show();

            $ret .= $fieldset->show();

        
        }
        return $ret;
    }  
    
    public function sitesReport($sites) {
        $ret = NULL;
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell('Site Name');
        $table->addHeaderCell('Coordinates');
        $table->addHeaderCell('Number of Collections');
        $table->addHeaderCell('Number of Objects');
        $table->endHeaderRow();
        foreach($sites as $site) {
            // tabulate the site data
            $numcolls = NULL;
            $numcolls .= $this->objDbColl->getCollCountBySite($site['id']);
            $numobjs = NULL;
            $numobjs .= $this->objDbColl->countItemsInSite($site['id']);
            $table->startRow();
            $table->addCell($site['sitename'], 150, NULL, 'left');
            $table->addCell($site['lat']." ".$site['lon'], 150, NULL, 'left');
            $table->addCell($numcolls, 150, NULL, 'center');
            $table->addCell($numobjs, 150, NULL, 'center');
            $table->endRow();
        }
        $ret .= $table->show();
        
        // coverage map
        $kml = $this->generateKML($sites);
        $ret .= $kml;
        return $ret;
        
        
    }
    
    public function generateKML($sites) {
        $kml = NULL;
        $kml .= '<?xml version="1.0" encoding="UTF-8"?>
                     <kml xmlns="http://www.opengis.net/kml/2.2">
                     <Document>';
        foreach($sites as $site) {
            $kml .= '<Placemark>
                         <name>'.$site['sitename'].'</name>
                         <description>
                             <![CDATA[
                                 <h1>'.$site['sitename'].'
                                 '.$site['sitecontact'].'</h1>
                             ]]>
                         </description>
                         <Point>
                             <coordinates>'.$site['lon'].','.$site['lat'].'</coordinates>
                         </Point>
                     </Placemark>';
        }
        $kml .= '</Document>
                 </kml>';
        file_put_contents($this->objConfig->getsiterootPath().'usrfiles/tmp.kml', $kml);
        $kfile = $this->objConfig->getsiteRoot().'usrfiles/tmp.kml';
        // bang up the map itself now
        $zoom = 15;
        
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 512px;
            /* border: 1px solid black;
            background-color: white;
            z-index:-5; */
        }
        </style>';

        $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        //$google = "<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>";
        // $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $js = '<script type="text/javascript">
    //<![CDATA[
      var map = new GMap2(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(-30,17), 5);

      // ==== Create a KML Overlay ====
    
      var kml = new GGeoXml("'.$kfile.'");
      map.addOverlay(kml);

    //]]>
    </script>';
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$google);
        //$this->appendArrayVar('bodyOnLoad', "init();");
        $gtags = '<div id="map"></div>';
        return $gtags.$js;
    }
    
    public function viewLocMap($lat, $lon, $zoom = 15) {
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
            z-index:-5;
        }
        </style>';

        $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
        //$google = "<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>";
        $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = $zoom;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18 } );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18 } );
            
            
            var markers = new OpenLayers.Layer.Markers( \"Markers\" );
            map.addLayer(markers);

            var size = new OpenLayers.Size(20,34);
            var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
            var icon = new OpenLayers.Icon('skins/_common/icons/marker.png',size,offset);

            var proj = new OpenLayers.Projection(\"EPSG:900913\");
            var point = new OpenLayers.LonLat($lon, $lat);
            point.transform(proj, map.getProjectionObject());


            markers.addMarker(new OpenLayers.Marker(point,icon));
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(point, $zoom);

            map.events.register(\"click\", map, function(e) {
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" +
                                          + lonlat.lon
            });

        }
        </script>";

        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
        $this->appendArrayVar('bodyOnLoad', "init();");
        $gtags = '<div id="map"></div>';
        return $gtags;
    }
    
    /**
     * Date manipulation method for getting posts by month/date
     *
     * @param  mixed selected date $sel_date
     * @return array
     */
    public function retDates($sel_date = NULL)
    {
        if ($sel_date == NULL) {
            $sel_date = mktime(0, 0, 0, date("m", time()) , 1, date("y", time()));
        }
        $t = getdate($sel_date);
        $start_date = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], 1, $t['year']);
        $start_date-= 86400*date('w', $start_date);
        $prev_year = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], $t['mday'], $t['year']-1);
        $prev_month = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon']-1, $t['mday'], $t['year']);
        $next_year = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon'], $t['mday'], $t['year']+1);
        $next_month = mktime($t['hours'], $t['minutes'], $t['seconds'], $t['mon']+1, $t['mday'], $t['year']);
        return array(
            'mbegin' => $sel_date,
            'prevyear' => $prev_year,
            'prevmonth' => $prev_month,
            'nextyear' => $next_year,
            'nextmonth' => $next_month
        );
    }
    
    /**
     * Method to get the archiveed posts array for manipulation
     *
     * @param  string  $userid
     * @return array
     * @access private
     */
    private function _archiveArr()
    {
        // add in a foreach for each year
        $allposts = $this->objDbColl->getAbsAllPosts();
        //print_r($allposts);
        // $revposts = array_reverse($allposts);
        $revposts = $allposts;
        $recs = count($revposts);
        
        if ($recs > 0) {
            $recs = $recs-1;
        }
        if (!empty($revposts)) {
            //echo count($revposts);
            $lastrec = $revposts[$recs]['obj_ts'];
            $firstrec = $revposts[0]['obj_ts'];
            
            $c1 = date("ym", $firstrec);
            $c2 = date("ym", $lastrec);
            $startdate = date("m", $firstrec);
            $enddate = date("m", $lastrec);
            // . " " .date("Y", $lastrec);
            // create a while loop to get all the posts between start and end dates
            $postarr = array();
             
             // echo $startdate, $enddate; die();
            foreach($revposts as $themonths) {
                $months[] = date("ym", $themonths['obj_ts']);
                $posts = array();
                $postarr[date("Ym", $themonths['obj_ts']) ] = $posts;
            }
            return $postarr;
        } else {
            return NULL;
        }
    }
    
    public function archive() {
        // var_dump($this->_archiveArr());
        $posts = $this->_archiveArr();
        // print_r($posts);die();
        if (!empty($posts)) {
            $yearmonth = array_keys($posts);
            $arks = NULL;
            foreach($yearmonth as $months) {
                $month = str_split($months, 4);
                $thedate = mktime(0, 0, 0, intval($month[1]) , 1, intval($month[0]));
                $arks[] = array(
                    'formatted' => date("F", $thedate) . " " . date("Y", $thedate) ,
                    'raw' => $month[1],
                    'rfc' => $thedate
                );
            }
            $thismonth = mktime(0, 0, 0, date("m", time()) , 1, date("y", time()));
            //var_dump($arks);
            $data = array();
            foreach ($arks as $ark) {
                $data['date'] = $ark['formatted']; 
                $data['objects'] = $this->objDbColl->getMonthCount($ark['rfc']);
                $info[] = $data;
            }
            
            return $info;
        } else {
            return NULL;
        }
    }
    
    public function graphObjects($data) {
        $ret = NULL;
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell('Date');
        $table->addHeaderCell('Number of Objects');
        $table->endHeaderRow();
        
        foreach($data as $obj) {
            $table->startRow();
            $table->addCell($obj['date'], 150, NULL, 'left');
            $table->addCell($obj['objects'], 150, NULL, 'left');
            $table->endRow();
        }
        $ret .= $table->show();
        
        return $ret;
    }
}
?>
