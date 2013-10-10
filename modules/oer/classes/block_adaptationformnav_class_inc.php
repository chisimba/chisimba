<?php
/**
 * This class contains util methods for displaying full original product details
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
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

/**
 * This creates a navigation for quick moving in between forms when managing
 * adaptations
 *
 * @author pwando
 */
class block_adaptationformnav extends object {

    function init() {
        $this->title="";   
    }
    /**
     * Function creates forms for editing adaptations
     *
     * @return form
     */

    public function show() {
        $data = explode("|", $this->configData);
        $id = NULL;
        $step = '1';
        if (count($data) == 2) {
            $id = $data[0];
            $step = $data[1];
        } else if (count($data) == 1){
            $id = $data[0];
        }
        $objAdaptationManager = $this->getObject('adaptationmanager', 'oer');
        
        return $objAdaptationManager->buildAdaptationStepsNav($id,$step);
    }

}

?>
