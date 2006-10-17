<?php
class parse4mathml extends object {
	function parseAll($str)
	{
		//$this->loadClass('mouseoverpopup','htmlelements');
		//$str = mouseoverpopup::showInit() . $str;
		$this->loadClass('iframe','htmlelements');
		$this->objMathMLParser = $this->getObject("mathmlparser",'mathml');
		$search = '/\[MATH\](.*)\[\/MATH\]/U';
		preg_match_all($search, $str, $matches, PREG_PATTERN_ORDER);
		if (!empty($matches)) {
		    foreach ($matches[1] as $match) {
				//$replace = $this->objMathMLParser->mathmlreturn($match);
				//echo "[$match]<br/>";
				//echo "[$replace]<br/>";
				/*
				$popup = new mouseoverpopup('Equation','Click on link to view formula.','MathML');
				$popup->iframeCaption = 'MathML';
				$popup->iframeWidth = 150;
				$popup->iframeHeight = 120;
				$popup->iframeUrl = $this->uri(array('action'=>'render','formula'=>$match),'mathml');
				$replace = $popup->show();
				*/
				$iframe = new iframe();
				$iframe->width = 150;
				$iframe->height = 120;
				$iframe->src = $this->uri(array('action'=>'render','formula'=>$match),'mathml');
			    $iframe->frameborder = "0";
				$replace = $iframe->show();
				$str = preg_replace('/'.preg_quote('[MATH]'.$match.'[/MATH]','/').'/', $replace, $str);
			}
		}
		return $str;
	}	
}
?>
