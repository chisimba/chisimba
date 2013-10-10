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
 * Builds a form for creating add/edit-adaptation form
 *
 * @author pwando
 */
class block_makeadaptation extends object {

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title = "";
    }

    /**
     * Function constructs the form and returns it for display
     * 
     * @return string
     */
    public function show() {
        $objAdaptationManager = $this->getObject("adaptationmanager", "oer");
        $data = explode("|", $this->configData);
        $id = Null;
        $productid = Null;
        $mode = "new";
        
        if (count($data) == 3) {
            $productid = $data[0];
            $mode = $data[1];
            $id = $data[2];
        } else if (count($data) == 2) {
            $productid = $data[0];
            $mode = $data[1];
        } else if (!empty($data )) {
            $productid = $data[0];
        }
        return $objAdaptationManager->makeNewAdaptation($mode, $id, $productid);
        break;
    }
}
?>