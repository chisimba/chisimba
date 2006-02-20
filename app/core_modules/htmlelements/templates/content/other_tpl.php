<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

$this->loadClass('mouseoverpopup','htmlelements');

$mop = new mouseoverpopup('url with caption','Content with caption , this tooltip uses a nice fade in ','asdfdsaf');
$mop2 = new mouseoverpopup('without caption','This is content without a caption');

$mop->caption='this is a caption';
$mop->url=$this->uri(array('action'=>'valform'),'htmlelements');


$mop3 = new mouseoverpopup('Go visit Google','Click on the link');
$mop3->iframeUrl = 'http://www.google.com/';
$mop3->iframeWidth = '500';
$mop3->iframeHeight = '300';
$mop3->iframeCaption='Google Search Engine';



echo $mop->show();
echo $mop2->show();
echo $mop3->show();
?>