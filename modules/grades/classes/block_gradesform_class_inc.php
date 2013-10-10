<?php
/**
 *
 * A middle block for grades.
 *
 * A middle block for grades. Module to hold grades - can be used in conjunction with the schools module.
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
 * @package    grades
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * A left block for grades.
 *
 * A left block for grades. Module to hold grades - can be used in conjunction with the schools module.
 *
 * @category  Chisimba
 * @author    Kevin Cyster kcyster@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_gradesform extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objOps = $this->getObject('gradesops', 'grades');
        
        $addGradeLabel = $this->objLanguage->code2Txt('mod_grades_addgrade', 'grades', NULL, 'ERROR: mod_grades_addgrade');
        $addSubjectLabel = $this->objLanguage->code2Txt('mod_grades_addsubject', 'grades', NULL, 'ERROR: mod_grades_addsubject');
        $addStrandLabel = $this->objLanguage->code2Txt('mod_grades_addstrand', 'grades', NULL, 'ERROR: mod_grades_addstrand');
        $addClassLabel = $this->objLanguage->code2Txt('mod_grades_addclass', 'grades', NULL, 'ERROR: mod_grades_addclass');
        $editGradeLabel = $this->objLanguage->code2Txt('mod_grades_editgrade', 'grades', NULL, 'ERROR: mod_grades_editgrade');
        $editSubjectLabel = $this->objLanguage->code2Txt('mod_grades_editsubject', 'grades', NULL, 'ERROR: mod_grades_editsubject');
        $editStrandLabel = $this->objLanguage->code2Txt('mod_grades_editstrand', 'grades', NULL, 'ERROR: mod_grades_editstrand');
        $editClassLabel = $this->objLanguage->code2Txt('mod_grades_editclass', 'grades', NULL, 'ERROR: mod_grades_editclass');

        $type = $this->getParam('type');
        $id = $this->getParam('id');
        
        switch ($type)
        {
            case 'g':
                $this->title = (empty($id)) ? $addGradeLabel : $editGradeLabel;
                break;
            case 's':
                $this->title = (empty($id)) ? $addSubjectLabel : $editSubjectLabel;
                break;
            case 'k':
                $this->title = (empty($id)) ? $addStrandLabel : $editStrandLabel;
                break;
            case 'c':
                $this->title = (empty($id)) ? $addClassLabel : $editClassLabel;
                break;
        }
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {        
        return $this->objOps->showForm();
    }
}
?>