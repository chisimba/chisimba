<?php
/**
 *
 * Execute shell scripts
 *
 * This module provides a helper class to execute shell scripts stored in /var/chisimba/scripts/
 * as well as a user interface for the system administrator to execute administrative scripts
 * from a web browser.
 *
 * Note: When safe mode is enabled, you can only execute executables within the safe_mode_exec_dir,
 * so this dir must e set to /var/chisimba/scripts or the config param COMMANDEXEC_SCRIPTDIR changed
 * to correspond to the safe_mode_exec_dir. For practical reasons it is currently not allowed to
 * have .. components in the path to the executable in accordance to PHP exec() requirements.
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
 * @package   Commandscript
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: commandexec_class_inc.php 11946 2008-12-29 21:25:46Z charlvn $
 * @link      http://avoir.uwc.ac.za
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
* Controller class for Chisimba for the module _MODULECODE
*
* @author Derek Keats
* @package Commandshell
*
*/
class commandexec extends object
{

    /**
    *
    * @var string $bashDir String to store the directory
    * where the bash scripts are located
    *
    */
    private $bashDir;
    /**
    *
    * @var string $cmd String to store the command to be
    * executed by the class
    *
    */
    private $cmd;
    /**
    *
    * @var string or string array $params String to store the list
    * of parameters of the command to be executed
    *
    */
    private $params;

    /**
    *
    * Intialiser for the commandexec class
    * @access public
    *
    */
    public function init()
    {
        //Create an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Get an instance of the config object
        $objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        //Get the value of the Bash directory and set object property
        $this->bashDir = $objConfig->getValue('COMMANDEXEC_SCRIPTDIR', 'commandshell');
    }

    /**
    *
    * Method to set the commmand to be executed, which must correspond
    * to one of the scripts stored in /var/chisimba/sctripts or whatever
    * is the path stored in the configuration.
    *
    * @param string $cmd The name of the script to execute without the .sh part
    *
    */
    public function setCommand($cmd)
    {
        $cmdExecute = $this->bashDir .  $cmd . ".sh";
        if ( $this->__validateCmd($cmdExecute) ) {
            $this->cmd = $cmdExecute;
            return TRUE;
        } else {
            $this->cmd = NULL;
            return FALSE;
        }
    }

    /**
    *
    * Method to execute shell commands stored in cmdArray. All commands must be
    * scripts stored in /var/chisimba/sctripts or whatever is the path stored
    * in the configuration, and all must be executable bash scripts, and must end
    * with the .sh extension.
    *
    * @return String The results of the execution of the command
    *
    */
    public function doCommand()
    {
        return shell_exec($this->cmd);
    }

    /**
    *
    * Method to validate that the command exists as a bash script
    * in the bash scripts directory for Chisimba execution (usually
    * /var/chisimba/scripts/. It also sets the
    *
    * @access Private
    * @param string $cmd The full filesystem path to the command
    * @return boolean TRUE | FALSE
    *
    */
    private function __validateCmd($cmd)
    {
        if ( $cmd=="" || $cmd==NULL ){
            $this->error = $this->objLanguage->languageText("mod_commandshell_errCommandWasEmpty", "commandshell");
            return FALSE;
        } else {
            if (file_exists($cmd)) {
                return TRUE;
            } else {
                $this->error = $this->objLanguage->languageText("mod_commandshell_errCommandNotFound", "commandshell") . "&nbsp;&nbsp;&nbsp;" . $cmd;
                return FALSE;
            }
        }

    }

}
?>