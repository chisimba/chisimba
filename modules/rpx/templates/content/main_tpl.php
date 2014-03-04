<?php

$src  = 'https://chisimba.rpxnow.com/openid/embed?token_url=';
$src .= urlencode($this->objAltConfig->getsiteRoot() . 'index.php?module=rpx&action=token');

$iframe = $this->newObject('iframe', 'htmlelements');
$iframe->frameborder = 0;
$iframe->height = 240;
$iframe->src = $src;
$iframe->width = 400;
echo $iframe->show();
