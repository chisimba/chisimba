<?php
$objStories = $this->getObject('sitestories', 'stories');
$allStories =  $objStories->fetchPreLoginCategory('prelogin');

echo $allStories;
?>