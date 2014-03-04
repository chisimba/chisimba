<?php

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//  
//  $Revision: 2787 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:54:59 -0400 (Thu, 12 Aug 2010) $
//

function file2js($f)
{
  if (ereg('/', $f))
  {
    $file = $f;
  }
  else
  {
    $file = "./molecules/$f.mol";
  }

  if (empty($file))
  {
    echo "File error. File not found.";
  }
  else
  {
    $fileContents = file_get_contents($file);
    if ($fileContents)
    {	
      // We don't need REMARKs for our examples.  If REMARKS are needed, comment out the following line.
      $fileContents = preg_replace('/REMARK.*\n/', '', $fileContents);
      echo "'".str_replace(array("\r\n", "\n", "\r", "'"), array("\\n", "\\n", "\\n", "\\'"), $fileContents)."'";
	  }
	  else
	  {
      echo "File error. Empty file.";
	  }
  }
}
?>
