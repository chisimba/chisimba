<?php
class dbstories extends dbtable {
    var $tablename = "tbl_stories";

    public function init() {
        parent::init($this->tablename);

    }
    public function getStories() {
        $data=$this->getAll();
        $json='{"storycount":"'.count($params).'","stories":[';
        foreach($data as $row) {
        $json.='{"storyid":"'.$row['id'].'","title":"'.$row['title'].'"},';
        }

        $lastChar = $json[strlen($json)-1];
        $len=strlen($json);
        if($lastChar == ',') {
            $json=substr($json, 0, (strlen ($json)) - (strlen (strrchr($json,','))));
        }
        $json.="]}";
        echo $json;
        die();
    }
}

?>
