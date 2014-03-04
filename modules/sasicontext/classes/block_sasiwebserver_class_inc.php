<?php

/**
 * Extended Sasi Context Block
 *
 * This class generates a block to show the sasi context info
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
 * @package   sasicontext
 * @author    Qhamani Fenama <qfenama@gmail.com>
 * @copyright 2010 PQhamani Fenama
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
/* -------------------- dbTable class ----------------*/
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
 * Extended Sasi Context Block
 *
 * This class generates a block to show the sasi context info
 *
 * @category  Chisimba
 * @package   sasicontext
 * @author    Qhamani Fenama <qfenama@gmail.com>
 * @copyright 2010 PQhamani Fenama
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class block_sasiwebserver extends object {

    /**
     * @var bool $requiresAdmin Specifies only admin usage
     */
    public $requiresAdmin = TRUE;

    /**
     * @var object $objContext : The Context Object
     */
    public $objContext;

    /**
     * @var object $objLanguage : The Language Object
     */
    public $objLanguage;

    /**
     * @var object $dbSasicontexte : The DBSasicontext Object
     */
    public $dbSasicontext;


    /**
     *Initialize by send the table name to be accessed
     */
    public function init() {
        $this->dbSasicontext = $this->getObject('dbsasicontext', 'sasicontext');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->objSysConfig     = $this->getObject('dbsysconfig', 'sysconfig');
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = ucwords($this->objLanguage->code2Txt('mod_sasicontext_extendedinfo', 'sasicontext'));
    }

    /**
     * Method to render the block
     */
    public function show() {
        // Check Context Code is Valid
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return '';
        }

        $alertBox = $this->getObject('alertbox', 'htmlelements');
        $alertBox->putJs();

        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();

        $wsdlenable = $this->objSysConfig->getValue('ENABLE_SASIWS', 'sasicontext');
        $wsdl = $this->objSysConfig->getValue('WSDL', 'sasicontext');

        // Get Context Details
        $contextDetails = $this->dbSasicontext->getSasicontextByField('contextcode', $this->contextCode);

        $link = new link ($this->uri(array('action'=>'showfac'), 'sasicontext'));
        $link->link = ucwords($this->objLanguage->code2Txt('mod_sasicontext_linkup', 'sasicontext'));
        $link->rel = 'facebox';

        $link2 = new link ($this->uri(array(NULL), 'sasicontext'));
        $link2->link = ucwords($this->objLanguage->code2Txt('mod_sasicontext_managesasi', 'sasicontext'));
        if ($wsdlenable == 'FALSE' or $wsdl == 'FALSE') {
            $norec = ucwords($this->objLanguage->code2Txt('mod_sasicontext_wsdlnotset', 'sasicontext'));
            $table->addCell($norec, NULL, NULL, 'center', 'noRecordsMessage', ' colspan="15"');
            $table->endRow();
            return $table->show().'<p>'.$link->show().'</p>';
        }
        // Check that Context Exists
        else if ($contextDetails == FALSE) {
            $norec = ucwords($this->objLanguage->code2Txt('mod_sasicontext_nodata', 'sasicontext'));
            $table->addCell($norec, NULL, NULL, 'center', 'noRecordsMessage', ' colspan="15"');
            $table->endRow();
            return $table->show().'<p>'.$link->show().'</p>';
        }
        else  {
            $str = '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_sasicontext_faculty', 'sasicontext')).'</strong>: '.$contextDetails['facultytitle'].'</p>';
            $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_sasicontext_department', 'sasicontext')).'</strong>: '.$contextDetails['departmenttitle'].'</p>';
            $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_sasicontext_subject', 'sasicontext')).'</strong>: '.$contextDetails['subjecttitle'].'</p>';
            $table->addCell($str);
            $table->endRow();
            return $table->show().'<p>'.$link->show().'</p>'.'<p>'.$link2->show().'</p>';
        }

    }
}
?>
