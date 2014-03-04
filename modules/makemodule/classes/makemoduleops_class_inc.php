<?php
/**
 *
 * Make module ops class
 *
 * Enable developers to quickly build a module that complies with Chisimba
 * development standards. The ops class adds some interface capability to
 * avoid putting too much code into the controller. It is a controller helper
 * class.
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
 * @package   makemodule
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbmakemodule.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
 * Make module ops class
 *
 * Enable developers to quickly build a module that complies with Chisimba
 * development standards. The ops class adds some interface capability to
 * avoid putting too much code into the controller. It is a controller helper
 * class.
*
* @author Derek Keats
* @package makemodule
*
*/
class makemoduleops extends object
{

    /**
    *
    * Intialiser for the makemodule database connector
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSvars = $this->getObject('serializevars', 'utilities');
    }

    /**
     *
     * Load the javascript that assists the functionality and interface
     * elements of this module
     *
     * @access public
     * @return VOID
     *
     */
    public function loadHelperScript()
    {
        // get relative module path.
        $modulePath = $this->objConfig->getModuleURI();
        $arrayVars = array();
        $arrayVars['packages'] = $modulePath;
       
        // pass module path through to javascript.
        $this->objSvars->varsToJs($arrayVars);
        
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('js/makemodule.js',
          'makemodule'));
    }

    /**
     *
     * Render the start button for the module creation
     *
     * @return string Rendered start button
     * @access public
     * 
     */
    public function showStartButton()
    {
        $start = "<a id='start' class='start_button' href=''>Get started</a>";
        return $start;
    }

    /**
     *
     * Find out if a user is a member of the Developers group.
     *
     * @return boolean TRUE | FALSE
     * @access public
     * 
     */
    public function isDeveloper()
    {
        $objGroupOps=$this->getObject('groupops','groupadmin');
        $objGroupAdminModel = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroupAdminModel->getId('Developers');
        $userId = $this->objUser->userId();
        $isMember=$objGroupOps->isGroupMember($groupId, $userId);
        if ($isMember) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Create the module.
     *
     * @return string The status result of creating the module.
     * @access public
     *
     */
    public function createModule()
    {
        $templateType = $this->getParam('templatetype', 'error');
        //die($templateType);
        $moduleCode = $this->getParam('modulecode', FALSE);
        if ($moduleCode) {
            $moduleCode = strtolower($moduleCode);
            // Create the module directory.
            $baseDir = $this->objConfig->getModuleURI() . $moduleCode;
            $workingDir = $baseDir . "/";
            if (!mkdir($baseDir, 0777)) {
                return 'failedtocreate';
            }
            $this->writeRegisterConf($workingDir, $templateType);
            $this->writeController($workingDir, $templateType);

            // Create the classes directory.
            $classesDir = $workingDir . 'classes';
            if (!mkdir($classesDir, 0777)) {
                return 'failedtocreate';
            }
            // Add the class files.
            $this->writeBlocks($classesDir . "/", $moduleCode, $templateType);
            $this->writeDbClass($classesDir . "/", $moduleCode);

            // Create the templates directory.
            $templatesDir = $workingDir . 'templates';
            if (!mkdir($templatesDir, 0777)) {
                return 'failedtocreate';
            }
            $ctDir = $templatesDir . "/content";
            if (!mkdir($ctDir, 0777)) {
                return 'failedtocreate';
            }
            // Add the content templates.
            $this->writeContentTemplate($ctDir . "/", $moduleCode);
            // There are no page templates, but make the dir anyway.
            $ptDir = $templatesDir . "/page";
            if (!mkdir($ptDir, 0777)) {
                return 'failedtocreate';
            }
            $ltDir = $templatesDir . "/layout";
            if (!mkdir($ltDir, 0777)) {
                return 'failedtocreate';
            }
            // Add the layout template.
            $this->writeLayoutTemplate($ltDir . "/", $moduleCode);

            
            // Create the resources directory
            $resourcesDir = $workingDir . 'resources';
            if (!mkdir($resourcesDir, 0777)) {
                return 'failedtocreate';
            }
            //Add the Javascript helper to the resources directory.
            $this->writeJsHelper($resourcesDir . "/", $moduleCode);
            $this->writeSample($resourcesDir . "/", $moduleCode);


            // Create the SQL directory.
            $sqlDir = $workingDir . 'sql';
            if (!mkdir($sqlDir, 0777)) {
                return 'failedtocreate';
            }
            $this->writeSql($sqlDir . "/", $moduleCode);

            // Create the dynamic canvas specific code
            //$templateType = $this->getParam('templatetype', 'json');
            switch ($templateType) {
                case 'userlevel':
                    $this->writeAllDyn($workingDir, $moduleCode, 'userlevel');
                    break;
                case 'pagelevel':
                    $this->writeAllDyn($workingDir, $moduleCode, 'pagelevel');
                    break;
                case 'modulelevel':
                    $this->writeAllDyn($workingDir, $moduleCode, 'modulelevel');
                    break;
                case 'json':
                default:
                    // Nothing to do here.
                    break;
            }

            return 'ok';
        } else {
            return 'nomodulecode';
        }
    }

    /**
     *
     * Write the main template and register.conf files for dyanamic canvas.
     *
     * @param string $workingDir The starting directory for the source files
     * @param string $moduleCode The module code for the module we are creating
     * @param string $level  The dynamic canvas level (userlevel, pagelevel, modulelevel)
     * @access public
     * @return VOID
     * 
     */
    public function writeAllDyn($workingDir, $moduleCode, $level)
    {

        $targetFile = $workingDir . '/templates/content/main_tpl.php';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/' . $level
          . '/main_tpl.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);

        // The data sql file for the page/user/module blocks
        $blockType = str_replace('level', '', $level);
        $targetFile = $workingDir . '/sql/tbl_' .  $moduleCode 
          . '_' . $blockType . 'blocks.sql';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/' . $level
          . '/tbl_MODULECODE_blocks.sql.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
    }

    /**
     *
     * Write the register.conf file
     *
     * @param string $targetDir The directory to put the file into.
     * @access public
     * @return VOID
     *
     */
    public function writeRegisterConf($targetDir, $templateType)
    {
        $targetFile = $targetDir . 'register.conf';
        $fPath = $this->objConfig->getModulePath()
           . 'makemodule/resources/source/'
           . $templateType . '/register.conf.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
    }

    /**
     *
     * Write the controller.php file
     *
     * @param string $targetDir The directory to put the file into.
     * @access public
     * @return VOID
     *
     */
    public function writeController($targetDir, $templateType)
    {
        $targetFile = $targetDir . 'controller.php';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/'
          . $templateType . '/controller.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
    }

    /**
     *
     * Write out the blocks classes for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
    public function writeBlocks($targetDir, $moduleCode, $templateType)
    {
        // Do the middle block.
        $targetFile = $targetDir . 'block_' . $moduleCode . 'middle_class_inc.php';
       // $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/common/block_middle_class_inc.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);

        // Do the left block.
        $targetFile = $targetDir . 'block_' . $moduleCode . 'left_class_inc.php';
        $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/common/block_left_class_inc.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);

        // Do the right block.
        $targetFile = $targetDir . 'block_' . $moduleCode . 'right_class_inc.php';
        $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/common/block_right_class_inc.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
    }

    /**
     *
     * Write out the database access class for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeDbClass($targetDir, $moduleCode)
     {
        $targetFile = $targetDir . 'db' . $moduleCode . '_class_inc.php';
        $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/common/dbmodel_class_inc.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }

    /**
     *
     * Write out the content template for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeContentTemplate($targetDir, $moduleCode)
     {
        $targetFile = $targetDir . 'main_tpl.php';
        $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/'
          . $templateType . '/main_tpl.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }

    /**
     *
     * Write out the layout template for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeLayoutTemplate($targetDir, $moduleCode)
     {
        $targetFile = $targetDir . 'layout_tpl.php';
        $templateType = $this->getParam('templatetype', 'json');
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/common/layout_tpl.php.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }

    /**
     *
     * Write out the Javascript helper for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeJsHelper($targetDir, $moduleCode)
     {
        $targetFile = $targetDir . $moduleCode . '.js';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/resources/jshelper.js.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }

    /**
     *
     * Write out some sample text that is loaded via Ajax by the sample javascript
     * for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeSample($targetDir, $moduleCode)
     {
        $targetFile = $targetDir . 'sample.txt';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/resources/sample.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }

    /**
     *
     * Write out the SQL for the module being created.
     *
     * @param string $targetDir The directory we are writing to
     * @param string $moduleCode  The module code for the module being created
     * @access public
     * @return VOID
     *
     */
     public function writeSql($targetDir, $moduleCode) {
        $targetFile = $targetDir . 'tbl_' . $moduleCode . '_text.sql';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/sql/tbl_modulename_text.sql.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);

        $targetFile = $targetDir . 'defaultdata.xml';
        $fPath = $this->objConfig->getModulePath()
          . 'makemodule/resources/source/sql/defaultdata.xml.txt';
        $fText = file_get_contents($fPath);
        $this->writeFile($targetFile, $fText);
     }


    /**
     *
     * Write out one of the module files after replacing the placeholders
     * with their actual values.
     *
     * @param string $targetFile The file path to write to
     * @param string $fText The contents to write to the file
     * @return VOID
     * @access public
     * 
     */
    public function writeFile($targetFile, $fText)
    {
        $fText = $this->replaceContents($fText);
        file_put_contents($targetFile, $fText);
    }

    /**
     *
     * Replace the placeholders, such as _SHORTDESCRIPTION, _MODULECODE, etc. 
     * with their actual values.
     *
     * @param string $fText The text to replace.
     * @return string The replaced text
     * @access public
     *
     */
    public function replaceContents($fText)
    {
        // Set up the values of the items to be replaced.
        $moduleCode = $this->getParam('modulecode', "nomodulecodefound");
        $moduleCode = strtolower($moduleCode);
        $moduleName = $this->getParam('modulename', "nomodulenamefound");
        $description = $this->getParam('description', "nodescriptionfound");
        $authorName = $this->objUser->fullName();
        $email = $this->objUser->email();
        $creationDate = date("F j, Y, g:i a");
        // Do the replacements.
        $fText = str_replace('_MODULECODE', $moduleCode, $fText);
        $fText = str_replace('_SHORTDESCRIPTION', $moduleName, $fText);
        $fText = str_replace('_LONGDESCRIPTION', $description, $fText);
        $fText = str_replace('_AUTHORNAME', $authorName, $fText);
        $fText = str_replace('_EMAIL', $email, $fText);
        $fText = str_replace('_DATE', $creationDate, $fText);
        // Send back the parsed text.
        return $fText;

    }

}
?>