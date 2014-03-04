<?php

	$this->loadClass('windowpop','htmlelements');
  	$this->objFont=&new windowpop;
  	$this->objFont->set('location','http://www.mozilla.org/projects/mathml/fonts');
  	$this->objFont->set('linktext','Click to download fonts');
  	$this->objFont->set('width','800');
  	$this->objFont->set('height','600');
  	$this->objFont->set('left','300');
  	$this->objFont->set('top','400');
  	$this->objFont->putJs(); 
  
  	$this->objHelp=&new windowpop;
  	$this->objHelp->set('location','http://en.wikipedia.org/wiki/MathML');
  	$this->objHelp->set('linktext','Click here to learn more about MathML');
  	$this->objHelp->set('width','800');
  	$this->objHelp->set('height','600');
  	$this->objHelp->set('left','300');
  	$this->objHelp->set('top','400');
  	$this->objHelp->putJs();
  	

 	print $ar."<br />".$this->objFont->show()."<br />".$this->objHelp->show();

    echo '<br />'.$image;
?>