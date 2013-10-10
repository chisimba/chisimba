<?php
 $this->loadClass('link', 'htmlelements');
 $objListArchives = $this->getObject('dbarchivesstories');
 $results = $objListArchives->getArchivedStories();

if (count($results > 0)){
     echo '<h1>Archive List</h1>';
     echo '<ul>';
     foreach ($results as $newsItem){
         if ($newsItem['status'] == 'archive') {
		$newsLink = new link ($this->uri(array('action'=>'restorearchives','id'=>$newsItem['id'])));
             $newsLink->link = $newsItem['storytitle'];
echo '<li style="color:red";>ARCHIVED - '.$newsLink->show().'</li>';

         }
         elseif ($newsItem['status'] == 'restored') {
             echo "<li style='color:green;'>RESTORED - ".$newsItem['storytitle']."</li>";
         }
//         echo '<li>'.$newsLink->show().'</li>';
         //echo '<li>'.$newsItem['storytitle'].'&nbsp;<a href="restore_archive.php?id=""><img src="" alt="restore me" title="restore me" border="0" /></a></li>';
     }
     echo '</ul>';
}?>
