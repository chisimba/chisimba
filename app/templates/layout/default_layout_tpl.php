<?php

echo $this->getContent().$this->footerStr;
/*
//get the side navigation
if (isset($leftSideColumn))
{
	$side1 = '<div id="sidebar">'.$leftSideColumn.'</div>';
}


//get the feature boxes (formally blocks)
if (isset($rightSideColumn))
{
	$side2 = '<div id="utility">'.$rightSideColumn.'</div>';
}

//get the footer string 
if (isset($footerStr))
{
	$footer = '<div  id="footer">'.$footerStr.'</div>';
}

//get the bread crumbs
$breadcrumbs = '<div id="breadcrumb">
			<a href="homepage.cfm">Home</a> / <a href="devtodo">Section Name</a> / <strong>Page Name</strong>
			</div>';

$middleContent = '<div id="content">'.$breadcrumbs.$this->getContent().$footer.'</div>';  //

//echo '<div id="content-wrap">'.$middleContent.$side1.$side2.'</div>';

*/
?>