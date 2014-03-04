<?php

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//  
//  $Revision: 2787 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:54:59 -0400 (Thu, 12 Aug 2010) $
//

function GetProteinDataBankMolecule()
{
  $q = $_POST['q'];

  if (empty($q))
	{
    echo "Query error. Empty query.";
    exit;
	}

  $url = 'http://www.rcsb.org/pdb/files/'.htmlentities($q).'.pdb';
  $content = file_get_contents($url);
  
  if ($content)
	{
    echo $content;
	}
  else
  {
    echo "ChemDoodle Web Components Query Error. Molecule not found.";
  }
}

GetProteinDataBankMolecule();
?>
