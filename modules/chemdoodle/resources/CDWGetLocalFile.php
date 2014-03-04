<?php

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//  
//  $Revision: 2787 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:54:59 -0400 (Thu, 12 Aug 2010) $
//

$uploadedFile = '/var/tmp/f'.mt_rand();
if (move_uploaded_file($_FILES['f']['tmp_name'], $uploadedFile))
{
  $fileContents = file_get_contents($uploadedFile);
  if ($fileContents)
  {
    echo $fileContents;
	}
	else
	{
    echo "File Error. Empty file.";
	}
  unlink($uploadedFile);
}
else
{
  echo "File Error. File not found.";
}

?>
