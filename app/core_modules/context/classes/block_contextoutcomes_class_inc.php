<?php

/**
 * Learner Outcomes block
 *
 * This class generates a block that displays the about learner outcomes of a course
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
 * @package   context
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2009 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: block_aboutcontext_class_inc.php 14775 2009-09-11 11:47:31Z davidwaf $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 * Learner Outcomes block
 *
 * This class generates a block that displays the about learner outcomes of a course
 *
 * @category  Chisimba
 * @package   context
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2009 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class block_contextoutcomes extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {

        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->title = ucWords($this->objLanguage->code2Txt('mod_context_learneroutcomes', 'context'));
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
    * Standard block show method.
    */
    public function show()
    {
        $objWashout = $this->getObject('washout', 'utilities');
        $objContext = $this->getObject('dbcontext');
        $contextCode = $objContext->getContextCode();
        $objDBLearnerOutcomes = $this->getObject('dbcontext_learneroutcomes', 'context');
        $contextLO = $objDBLearnerOutcomes->getContextOutcomes($contextCode);
        $s = "";
        foreach ($contextLO as $LO){
            $s .= "<p>" . $LO["learningoutcome"] . "</p>";
        }
        return $objWashout->parseText($s); //$objContext->getGoals()
    }
}
?>
