<?php

/**
 * This is a DB layer that manages umbrella themes
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
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author davidwaf
 */
class dbthemes extends dbtable {

    private $tableName = 'tbl_oer_themes';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * this selects themes
     */
    function getThemes() {
        $sql = "select th.id as id,th.theme as theme,uth.theme as umbrellatheme from tbl_oer_themes th, tbl_oer_umbrella_themes uth where th.umbrellatheme = uth.id";
        return $this->getArray($sql);
    }

    /**
     * selects and formats themes
     * @return string 
     */
    function getThemesFormatted() {
        $sql = "select th.id,th.theme as theme,uth.theme as umbrellatheme from tbl_oer_themes th, tbl_oer_umbrella_themes uth where th.umbrellatheme = uth.id";
        $data = $this->getArray($sql);
        $themes = array();
        foreach ($data as $row) {
            $themes[] = array(
                "id" => $row['id'],
                "theme" => $row['theme'] . ' (' . $row['umbrellatheme'] . ')'
            );
        }
        return $themes;
    }

    /**
     * inserts a new theme
     * @param type $title
     * @return type 
     */
    function addTheme($title, $umbrellaTheme) {
        $data = array("theme" => $title, 'umbrellatheme' => $umbrellaTheme);
        return $this->insert($data);
    }

    /**
     * Returns theme data for a supplied id
     * @param type $id
     * @return type 
     */
    function getTheme($id) {
        $sql = "select * from tbl_oer_themes where id = '" . $id . "'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return null;
        }
    }

    /**
     * get the theme together with umbrella theme, formatted
     * @param type $id
     * @return type 
     */
    function getThemeFormatted($id) {
        $sql = "select th.id,th.theme as theme,uth.theme as umbrellatheme from 
              tbl_oer_themes th, tbl_oer_umbrella_themes uth where th.umbrellatheme = uth.id and th.id='$id'";

        $data = $this->getArray($sql);
        if (count($data) > 0) {
            $row = $data[0];
            return $row['umbrellatheme'] . '|' . $row['theme'];
        } else {
            return null;
        }
    }

}

?>
