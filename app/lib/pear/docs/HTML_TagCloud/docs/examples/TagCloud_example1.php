<?php
// $Id$

require_once 'HTML/TagCloud.php';

date_default_timezone_set('Asia/Tokyo');
$tags = new HTML_TagCloud();
// add Element
$tags->addElement('PHP','http://www.php.net', 39, strtotime('-1 day'));
$tags->addElement('XML','http://www.xml.org', 21, strtotime('-2 week'));
$tags->addElement('Perl','http://www.xml.org', 15, strtotime('-1 month'));
$tags->addElement('PEAR','http://pear.php.net', 32, time());
$tags->addElement('MySQL','http://www.mysql.com', 10, strtotime('-2 day'));
$tags->addElement('PostgreSQL','http://pgsql.com', 6, strtotime('-3 week'));
// output HTML and CSS
print $tags->buildALL();
show_source(__FILE__);
?>
