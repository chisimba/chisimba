<?php
/*************************************************************
*                         Display access class             *
*                  Created by: Fernando Martinez             *
*                        05/02/04                            *
*                  Last Modified: 29/05/04                   *
*                  Modified by: Fernando                     *
**************************************************************/

$siteSkin = "cil_blue_skin.php";
include_once('../config/'.$siteSkin);
?>
<link rel=StyleSheet href="../config/<? echo $skincss ?>" type="text/css">
<?
// this class should provide functionality to display a page
// ie, generate html to send to client
class Display
{
  var $webpage;
  
  function Display(&$webpage)
  {
    $this->webpage = &$webpage;
  }
  
  // display a string
  function String($s)
  {
    echo($s);
  }
  
  // display page header (title)
  function Header()
  {
    $s="<html><head><title>".$this->webpage->GetTitle()."</title>".
    $this->webpage->GetHead()."</head>";
    echo($s);
  }
  
  // display page body
  function Body()
  {
    $s.="<body>".$this->webpage->GetBody();
    echo($s);
  }
  
  // display page foot or bottom
  function Bottom()
  {
    $s=$this->webpage->GetBottom()."</body></html>";
    echo($s);
  }
  
  // display whole page
  function Page()
  {
    $this->Header();
    $this->Body();
    $this->Bottom();
  }
  
  // display fatal error
  function FatalError($err)
  {
    die($err);
  }
}
