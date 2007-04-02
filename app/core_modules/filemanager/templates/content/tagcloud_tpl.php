<?php
echo '<h1>Tag Cloud</h1>';


if (count($tagCloudItems) > 0) {
    $tags = array();
    foreach ($tagCloudItems as $item)
    {
        $tag = array('name'=>$item['tag'], 'url'=>$this->uri(array('action'=>'viewbytag', 'tag'=>$item['tag'])), 'weight'=>$item['weight'], 'time'=>strtotime("now"));
        
        $tags[] = $tag;
    }
    
    $tagCloud = $this->newObject('tagcloud', 'utilities');
    echo $tagCloud->buildCloud($tags);
} else {

}


?>