<?php
/**
 *
 * collectionsman helper class
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
 * @package   collectionsman
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
 * collectionsman helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package collectionsman
 *
 */
class collectionops extends object {

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
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objMongodb    = $this->getObject ('mongoops', 'mongo');
    }

    /**
     * Method to show a form to insert a record to a Mongo collection
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
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'collectionsman', 'Required').'</span>';
        //$headermsg = new htmlheading();
        //$headermsg->type = 1;
        //$headermsg->str = $this->objLanguage->languageText('phrase_addrecord', 'collectionsman');
        $ret = NULL;
        //$ret .= $headermsg->show();
        // start the form
        $form = new form ('add', $this->uri(array('action'=>'addrec'), 'collectionsman'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $defmsg = $this->objLanguage->languageText("mod_collections_defmsg", "collectionsman");
        $table->startRow();
        // add some rules
        // $form->addRule('', $this->objLanguage->languageText("mod_collectionsman_needsomething", "collectionsman"), 'required');

        // dropdown collection field
        $coll = new dropdown('coll');
        $list = $this->objMongodb->getCollectionNames();
        foreach($list as $item) {
            $coll->addOption($item, $item);
        }
        $collLabel = new label($this->objLanguage->languageText('mod_collectionsman_collection', 'collectionsman').'&nbsp;', 'input_coll');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($coll->show());
        $table->endRow();

        // accession number
        $ano = new textinput();
        $ano->name = 'ano';
        $ano->width ='50%';
        $anoLabel = new label($this->objLanguage->languageText('mod_collectionsman_accno', 'collectionsman').'&nbsp;', 'input_ano');
        $table->addCell($anoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($ano->show());
        $table->endRow();

        // title
        $title = new textinput();
        $title->name = 'title';
        $title->width ='50%';
        $titleLabel = new label($this->objLanguage->languageText('mod_collectionsman_title', 'collectionsman').'&nbsp;', 'input_title');
        $table->addCell($titleLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($title->show());
        $table->endRow();

        // description
        $desc = $this->newObject('htmlarea', 'htmlelements');
        $desc->name = 'desc';
        $desc->value = $defmsg;
        $desc->width ='50%';
        $descLabel = new label($this->objLanguage->languageText('mod_collectionsman_description', 'collectionsman').'&nbsp;', 'input_desc');
        $table->addCell($descLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $msg->toolbarSet = 'simple';
        $table->addCell($desc->show());
        $table->endRow();

        // date created
        $dc = new textinput();
        $dc->name = 'datecreated';
        $dc->width ='50%';
        $dcLabel = new label($this->objLanguage->languageText('mod_collectionsman_datecreated', 'collectionsman').'&nbsp;', 'input_datecreated');
        $table->addCell($dcLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($dc->show());
        $table->endRow();

        // media
        $media = new textinput();
        $media->name = 'media';
        $media->width ='50%';
        $mediaLabel = new label($this->objLanguage->languageText('mod_collectionsman_media', 'collectionsman').'&nbsp;', 'input_media');
        $table->addCell($mediaLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($media->show());
        $table->endRow();

        // comment
        $comment = new textinput();
        $comment->name = 'comment';
        $comment->width ='50%';
        $commentLabel = new label($this->objLanguage->languageText('mod_collectionsman_comment', 'collectionsman').'&nbsp;', 'input_comment');
        $table->addCell($commentLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($comment->show());
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_collectionsman_addrecord", "collectionsman"));
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
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $ret = NULL;
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();

        $collLabel = new label($this->objLanguage->languageText('mod_collectionsman_collection', 'collectionsman').'&nbsp;', 'input_coll');
        $table->addCell($collLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['collection']);
        $table->endRow();

        // accession number
        $anoLabel = new label($this->objLanguage->languageText('mod_collectionsman_accno', 'collectionsman').'&nbsp;', 'input_ano');
        $table->addCell($anoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['accession number']);
        $table->endRow();

        // title
        $titleLabel = new label($this->objLanguage->languageText('mod_collectionsman_title', 'collectionsman').'&nbsp;', 'input_title');
        $table->addCell($titleLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['title']);
        $table->endRow();

        // description
        $descLabel = new label($this->objLanguage->languageText('mod_collectionsman_description', 'collectionsman').'&nbsp;', 'input_desc');
        $table->addCell($descLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $msg->toolbarSet = 'simple';
        $table->addCell($this->objWashout->parseText($record['description']));
        $table->endRow();

        // date created
        $dcLabel = new label($this->objLanguage->languageText('mod_collectionsman_datecreated', 'collectionsman').'&nbsp;', 'input_datecreated');
        $table->addCell($dcLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['date created']);
        $table->endRow();

        // media
        $mediaLabel = new label($this->objLanguage->languageText('mod_collectionsman_media', 'collectionsman').'&nbsp;', 'input_media');
        $table->addCell($mediaLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['media']);
        $table->endRow();

        // comment
        $commentLabel = new label($this->objLanguage->languageText('mod_collectionsman_comment', 'collectionsman').'&nbsp;', 'input_comment');
        $table->addCell($commentLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($record['comment']);
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();

        $ret .= $fieldset->show();

        return $ret;
    }

}
?>
