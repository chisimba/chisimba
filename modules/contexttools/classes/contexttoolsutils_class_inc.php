<?php
/**
 * Context tools utils
 *
 * This class contains utility functions for context tools
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
 * @author    David Wafula
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 =
 */
/* --------------------\----------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check

class contexttoolsutils extends object {
    function init() {
        $this->objConfig = $this->getObject ( 'altconfig', 'config' );
    }
    /**
     * this function reads filters.xml file and returns the results in json
     * format
     * @return <type>
     */
    public function readFiltersXml() {
        try {

            $this->_path = $this->objConfig->getModulePath()."contexttools/resources/filters.xml";
            $xml = simplexml_load_file($this->_path);
            $entries = $xml->xpath("//filter");
            $index=0;
            foreach ($entries as $filter) {
                $name = (string)$filter->name;
                $type=(string)$filter->type;
                $label=(string)$filter->label;
                $instructions=(string)$filter->instructions;
                $tag=(string)$filter->tag;
                $params=$filter->params;
                $input=$filter->input;
                $defaultvalue=(string)$filter->defaultvalue;
                $preinputvalue=(string)$filter->preinputvalue;
                $paramJson="";
                if(count($params) > 0) {
                    $paramJson='{"paramcount":"'.count($params).'","params":[';
                    foreach($params->param as $p) {
                        foreach($p[0]->attributes() as $a => $b) {
                            $paramJson.='{"name":"'.$b.'","value":"'.$p[0].'"},';
                        }
                    }
                    $lastChar = $paramJson[strlen($paramJson)-1];
                    $len=strlen($paramJson);
                    if($lastChar == ',') {
                        $paramJson=substr($paramJson, 0, (strlen ($paramJson)) - (strlen (strrchr($paramJson,','))));
                    }
                    $paramJson.="]}";
                }


                if (empty($name)) {
                    $name="unknown$index";
                }
                $result[] = array(
                    "name"=> $name,
                    "type"=> $type,
                    "label"=> $label,
                    "instructions"=> $instructions,
                    "tag"=>$tag,
                    "defaultvalue"=>$defaultvalue,
                    "params"=>$paramJson,
                    "preinputvalue"=>$preinputvalue,
                    "input"=>$inputParamJson
                );
                $index++;
            }


            $json='{"filtercount":"'.count($result).'","filters":[';
            foreach($result as $entry) {
                $json.='{"name":"'.$entry['name'].'",';
                $json.='"type":"'.$entry['type'].'",';
                $json.='"label":"'.$entry['label'].'",';
                $json.='"defaultvalue":"'.$entry['defaultvalue'].'",';
                $json.='"preinputvalue":"'.$entry['preinputvalue'].'",';
                $json.='"instructions":"'.$entry['instructions'].'",';
                $json.='"tag":"'.$entry['tag'].'"},';
            // $json.='"params":"'.$entry['params'].'"},';
            }
            $lastChar = $json[strlen($json)-1];
            $len=strlen($json);
            if($lastChar == ',') {
                $json=substr($json, 0, (strlen ($json)) - (strlen (strrchr($json,','))));
            }

            $json.="]}";
            echo $json;
            die();


        }catch (Exception $e ) {
            $this->errorCallback('Caught exception: '.$e->getMessage());
            exit();
        }
    }

    public function readFilterParams($filtername) {
        try {
            $this->_path = $this->objConfig->getModulePath()."contexttools/resources/filters.xml";
            $xml = simplexml_load_file($this->_path);
            $entries = $xml->xpath("//filter[name='$filtername']");
            $index=0;
            foreach ($entries as $filter) {
                $params=$filter->params;
                $paramJson="";
                if(count($params) > 0) {
                    $paramJson='{"paramcount":"'.count($params).'","params":[';
                    foreach($params->param as $p) {
                        foreach($p[0]->attributes() as $a => $b) {
                            $paramJson.='{"name":"'.$b.'","value":"'.$p[0].'"},';
                        }
                    }
                    $lastChar = $paramJson[strlen($paramJson)-1];
                    $len=strlen($paramJson);
                    if($lastChar == ',') {
                        $paramJson=substr($paramJson, 0, (strlen ($paramJson)) - (strlen (strrchr($paramJson,','))));
                    }
                    $paramJson.="]}";
                }

                echo $paramJson;
                die();
            }

        }catch (Exception $e ) {
            $this->errorCallback('Caught exception: '.$e->getMessage());
            exit();
        }
    }

    public function readFilterInput($filtername) {
        try {

            $this->_path = $this->objConfig->getModulePath()."contexttools/resources/filters.xml";
            $xml = simplexml_load_file($this->_path);
            $entries = $xml->xpath("//filter[name='$filtername']");
            $index=0;
            foreach ($entries as $filter) {
                $params=$filter->input;
                $paramJson="";
                if(count($params) > 0) {

                    foreach($params->inputparam as $p) {
                        foreach($p[0]->attributes() as $a => $b) {
                            $paramJson.=$b.":".$p[0].'#';
                        }
                    }
                }

                echo $paramJson;
                die();
            }

        }catch (Exception $e ) {
            $this->errorCallback('Caught exception: '.$e->getMessage());
            exit();
        }
    }
}

?>
