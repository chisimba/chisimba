<?php
/**
 * Class to handle blog imports.
 *
 * Import legacy blog code from KINKY
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
 * @version    $Id: blogimporter_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @package    blog
 * @subpackage blogimporter
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to facilitate import of existing blog content from a remote server
 *
 * This class should allow a connection to a remote database on a remote server to get all content items within
 * that database table in order to process and import the content back into a Chisimba installation.
 * Please note that due to the way that this class acts, it is only necessary to supply a username/userid to
 * the function calls in order to get an Associative array of values back to be returned.
 *
 * @author    Paul Scott
 * @copyright 2006-2007 AVOIR
 * @access    public
 */
class blogimporter extends object
{
    /**
     * The DSN to the database to import FROM
     *
     * @var    mixed
     * @access public
     */
    public $dsn;
    /**
     * Table name of the tables that we need to connect to
     *
     * @var    string
     * @access public
     */
    protected $_tableName;
    /**
     * The database (remote) connection object
     *
     * @var    object
     * @access public
     */
    protected $objDb;
    /**
     * Language Object
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    /**
     * Standard init function for the object and controller class
     *
     * @access public
     * @return NULL
     */
    public function init() 
    {
        // language object
        $this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * Method to create a form to import the blog data from a remote
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function showImportForm($featurebox = TRUE) 
    {
        $this->objUser = $this->getObject('user', 'security');
        $imform = new form('importblog', $this->uri(array(
            'action' => 'importblog'
        )));
        // start a fieldset
        $imfieldset = $this->getObject('fieldset', 'htmlelements');
        // $imfieldset->setLegend($this->objLanguage->languageText('mod_blog_importblog', 'blog'));
        $imadd = $this->newObject('htmltable', 'htmlelements');
        $imadd->cellpadding = 5;
        // server dropdown
        $servdrop = new dropdown('server');
        $servdrop->addOption("fsiu", $this->objLanguage->languageText("mod_blog_fsiu", "blog"));
        $servdrop->addOption("elearn", $this->objLanguage->languageText("mod_blog_elearn", "blog"));
        $servdrop->addOption("santec", $this->objLanguage->languageText("mod_blog_santec", "blog"));
        // $servdrop->addOption("freecourseware", $this->objLanguage->languageText("mod_blog_freecourseware", "blog"));
        // $servdrop->addOption("5ive", $this->objLanguage->languageText("mod_blog_5ive", "blog"));
        // $servdrop->addOption("pear", $this->objLanguage->languageText("mod_blog_peardemo", "blog"));
        // $servdrop->addOption("dfx", $this->objLanguage->languageText("mod_blog_dfx", "blog"));
        $imadd->startRow();
        $servlabel = new label($this->objLanguage->languageText('mod_blog_impserv', 'blog') . ':', 'input_importfrom');
        $imadd->addCell($servlabel->show());
        $imadd->addCell($servdrop->show());
        $imadd->endRow();
        // username textfield
        $imadd->startRow();
        $imulabel = new label($this->objLanguage->languageText('mod_blog_impuser', 'blog') . ':', 'input_impuser');
        $imuser = new textinput('username');
        $usernameval = $this->objUser->username();
        if (isset($usernameval)) {
            $imuser->setValue($this->objUser->username());
        }
        $imadd->addCell($imulabel->show());
        $imadd->addCell($imuser->show());
        $imadd->endRow();
        // add rules
        // $imform->addRule('server', $this->objLanguage->languageText("mod_blog_phrase_imserverreq", "blog") , 'required');
        // $imform->addRule('username', $this->objLanguage->languageText("mod_blog_phrase_imuserreq", "blog") , 'required');
        // end off the form and add the buttons
        $this->objIMButton = &new button($this->objLanguage->languageText('word_import', 'system'));
        $this->objIMButton->setValue($this->objLanguage->languageText('word_import', 'system'));
        $this->objIMButton->setToSubmit();
        $imfieldset->addContent($imadd->show());
        $imform->addToForm($imfieldset->show());
        $imform->addToForm($this->objIMButton->show());
        $imform = $imform->show();
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_importblog", "blog") , $imform);
            return $ret;
        } else {
            return $imform;
        }
    }
    /**
     * Pseudo constructor method.
     *
     * We have not yet used the standard init() function here, or extended dbTable, as we are not really
     * interested in connecting to the local db with this object.
     *
     * @param string $server The name of the server to connect to (predefined)
     *
     * @return string, set DSN
     * @access public
     */
    public function setup($server) 
    {
        switch ($server) {
            case 'localhost':
                $this->dsn = 'mysql://root:@localhost/nextgen4';
                return $this->dsn;
                break;

            case 'fsiu':
                $this->dsn = 'mysql://reader:reader@172.16.203.173/fsiu';
                return $this->dsn;
                break;

            case 'elearn':
                $this->dsn = 'mysql://reader:reader@172.16.203.210/nextgen';
                return $this->dsn;
                break;

            case 'santec':
                $this->dsn = 'mysql://reader:reader@172.16.203.173/santec';
                return $this->dsn;
                break;

            case 'freecourseware':
                $this->dsn = 'mysql://next:n3xt@172.16.203.178/ocw';
                return $this->dsn;
                break;

            case '5ive':
                $this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/chisimba';
                return $this->dsn;
                break;

            case 'pear':
                $this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/chisimbademo';
                return $this->dsn;
                break;

            case 'dfx':
                $this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/dfx';
                return $this->dsn;
                break;
        }
    }
    /**
     * Build and instantiate the database object for the remote
     *
     * @return object
     * @access private
     */
    public function _dbObject() 
    {
        require_once 'MDB2.php';
        // MDB2 has a factory method, so lets use it now...
        $this->objDb = &MDB2::factory($this->dsn);
        // Check for errors on the factory method
        if (PEAR::isError($this->objDb)) {
            throw new customException($this->objLanguage->languageText("mod_blog_import_noconn", "blog"));
        }
        // set the options
        $this->objDb->setOption('portability', MDB2_PORTABILITY_FIX_CASE);
        // load the date and iterator MDB2 Modules.
        MDB2::loadFile('Date');
        MDB2::loadFile('Iterator');
        // Check for errors
        if (PEAR::isError($this->objDb)) {
            throw new customException($this->objLanguage->languageText("mod_blog_import_noconn", "blog"));
        }
        return $this->objDb;
    }
    /**
     * Method to query an arbitrarary remote table
     *
     * @param string $table  The table name
     * @param string $filter can be full SQL Query
     *
     * @return resultset
     * @access public
     */
    public function queryTable($table, $filter) 
    {
        $this->_tableName = $table;
        $res = $this->objDb->query($filter);
        // set the return mode to return an associative array
        return $res->fetchAll(MDB2_FETCHMODE_ASSOC);
    }
    /**
     * Method to get the blog contents per user (username) into an array
     *
     * @param string $username the users username
     *
     * @return array
     * @access public
     */
    public function importBlog($username) 
    {
        $this->objUser = $this->getObject('user', 'security');
        // set the table
        $this->_tableName = 'tbl_users';
        // set up the query to check userid and username
        $fil1 = "SELECT * FROM tbl_users WHERE username = '$username'";
        $res1 = $this->objDb->query($fil1);
        if (PEAR::isError($res1)) {
            throw new customException($res1->getMessage());
            exit;
        }
        $ures = $res1->fetchAll(MDB2_FETCHMODE_ASSOC);
        $fname = $ures[0]['firstname'] . " " . $ures[0]['surname'];
        // lets check that the users name is the same, or else drop his ass
        $locname = trim($this->objUser->fullname());
        $fname = trim($fname);
        if ($fname == $locname) {
            // now get the info we need
            // set the userid as in the blog
            $userid = $ures[0]['userid'];
            // set the table to the blog table
            $this->_tableName = 'tbl_blog';
            // query the blog table
            $fil2 = "SELECT * FROM tbl_blog WHERE userid = '$userid'";
            $res2 = $this->objDb->query($fil2);
            if (PEAR::isError($res2)) {
                // uh oh.... blog not installed, or cannot be found on remote
                throw new customException($this->objLanguage->languageText("mod_blog_import_noblog", "blog"));
            }
            // return the associative array of fetched values.
            $bres = $res2->fetchAll(MDB2_FETCHMODE_ASSOC);
            if (empty($bres)) {
                throw new customException($this->objLanguage->languageText("mod_blog_import_unoblog", "blog"));
            } else {
                return $bres;
            }
        } else {
            throw new customException($this->objLanguage->languageText("mod_blog_import_unomatch", "blog"));
        }
    }
    /**
     * Method to get the blog contents from a site
     *
     * @return array
     * @access public
     */
    public function importAllBlogs() 
    {
        $sql = "SELECT b.*, u.firstname, u.surname, u.userid FROM tbl_blog AS b, 
            tbl_users AS u 
            WHERE u.userid = b.userid";
        $result = $this->objDb->query($sql);
        if (PEAR::isError($result)) {
            // uh oh.... blog not installed, or cannot be found on remote
            throw new customException($this->objLanguage->languageText("mod_blog_import_noblog", "blog"));
        }
        // return the associative array of fetched values.
        $bres = $result->fetchAll(MDB2_FETCHMODE_ASSOC);
        if (empty($bres)) {
            throw new customException($this->objLanguage->languageText("mod_blog_import_unoblog", "blog"));
        } else {
            return $bres;
        }
    }
}
?>