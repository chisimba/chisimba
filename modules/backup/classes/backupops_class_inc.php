<?php
/**
 *
 * Operations for the module to backup your site
 *
 * Operations for the module to backup your site. This provides the interface
 * elements as well as the behaviours that result in interface output.
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
 * @package   backup
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Operations for the module to backup your site
 *
 * Operations for the module to backup your site. This provides the interface
 * elements as well as the behaviours that result in interface output.
*
* @package   backup
* @author    Derek Keats derek@dkeats.com
*
*/
class backupops extends dbtable
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
    *
    * Intialiser 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     *
     * Get the link to do the backup.
     *
     * @access public
     * @return string The rendered link
     *
     */
    public function getBackupLink()
    {
        $linkText = $this->objLanguage->languageText(
          "mod_backup_backupnow","backup","Backup now");
        $ret = '<a href="#" id="backuplink">' . $linkText . '</a>';
        return $ret;
    }
    
    /**
     *
     * Execute the backup and return results for Ajax
     * 
     * @return string The output of the backup command
     * @access public
     * 
     */
    public function doBackup()
    {
        $objAltConfig = $this->getObject('altconfig', 'config');
        $siteRootPath = $objAltConfig->getSiteRootPath();
        $usrFilesPath =  $siteRootPath . 'usrfiles/';
        $configPath =  $siteRootPath . 'config/';
        $userImagesPath =  $siteRootPath . 'user_images/';
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $backupPath = $objSysConfig->getValue('BACKUP_PATH', 'backup');
        $this->guessDatabaseDetails();
        $cmd = "bash /var/chisimba/scripts/backup.sh "
          . "$usrFilesPath $configPath $userImagesPath $backupPath "
          . "$this->dbUser $this->dbPassword $this->dbName";
        //$cmd = 'ls -l';
        $res = "<pre>" . trim(shell_exec($cmd)) . "</pre>";
        return $res;
    }
    
    public function buildMainOutputDiv()
    {
        $ret = "<div id='results_area'>"
         . "<span class='warning'>"
         . $this->objLanguage->languageText(
          "mod_backup_description","backup")
         . "</span></div><div id='results_appended'></div>";
        return $ret;
    }
    
    /**
     * 
     * Get the database details by parsing the dbdetails file 
     * in the config directory.
     * 
     */
    private function guessDatabaseDetails()
    {
        $objAltConfig = $this->getObject('altconfig', 'config');
        $siteRootPath = $objAltConfig->getSiteRootPath();
        $configFile =  $siteRootPath . 'config/dbdetails_inc.php';
        $dbFile = file_get_contents($configFile);
        // Extract the database user.
        $arDb = explode("://", $dbFile);
        $dbStr = $arDb[1];
        $arDb = explode(":", $dbStr);
        $this->dbUser = $arDb[0];
        // Extract the database password.
        $dbStr = $arDb[1];
        $arDb = explode("@", $dbStr);
        $this->dbPassword = $arDb[0];
        // Extract the database name.
        $dbStr = $arDb[1];
        $arDb = explode("/", $dbStr);
        $dbStr = $arDb[1];
        $arDb = explode("'", $dbStr);
        $this->dbName = $arDb[0];
        //echo "USER: $this->dbUser<br />";
        //echo "PASSWORD: $this->dbPassword<br />";
        //echo "DBNAME: $this->dbName<br />";
    }

}
?>