<?php
/**
 * This class handles the permitted file types that can be uploaded
 *  PHP version 5
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
 * @package   podcaster
 * @author    Paul Mungai
 * @copyright 2011
 *
 */
class dbpermittedtypes extends dbtable {
    var $tablename = "tbl_podcaster_permittedtypes";
    
    public function init(){
        parent::init($this->tablename);
    }

    public function saveFileType($filetypedesc,$filetypeext) {
        // check if the file type exists
        if(count($this->getRow('ext', $filetypeext)) <= 0) {
            $data = array("name"=>$filetypedesc, "ext"=>$filetypeext);
            $this->insert($data);
        }
        return "";
    }

    public function getFileTypeData(){
        return $this->getAll();
    }

    public function deleteFileType($id) {
        $this->delete('id', $id);
    }

    public function getFileExtensions($jsonencode=true) {
        $sql = "select * from $this->tablename";
        $data =$this->getArray($sql);
        if($jsonencode){
        echo json_encode(array("exts"=>$data));
        die();
        }else{
            return $data;
        }
    }

    public function getFileDesc($filetype) {
        $data = $this->getRow('ext', $filetype);
        return $data['name'];
    }

    public function saveNewExt($data) {
        $this->insert($data);
    }
}
?>
