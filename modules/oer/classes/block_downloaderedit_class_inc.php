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
 * this block is used for rendering downloader edit form
 *
 * @author pwando
 */
class block_downloaderedit extends object {
    private $objDownloaderEdit;

    function init() {
        $this->title = "";
        $this->objDownloaderEdit = $this->getObject("downloaderedit", "oer");
    }
    /**
     * Function renders the downloader edit form
     *
     * @return form
     */

    function show() {        
        $data = explode("|", $this->configData);
     
        $productId = Null;
        $id = Null;
        $producttype = "adaptation";
        $step = '1';
        if (count($data) == 3) {
            $id = $data[0];
            $productId = $data[1];
            $producttype = $data[2];
        } else if (count($data) == 1){
            $productId = $data[0];
        }
        return $this->objDownloaderEdit->show($productId, $id, $producttype);
    }
}
?>