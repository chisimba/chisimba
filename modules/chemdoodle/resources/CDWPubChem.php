<?php

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//  
//  $Revision: 2787 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:54:59 -0400 (Thu, 12 Aug 2010) $
//

class XMLParser
{
  var $tag_name;
  var $tag_data;
  var $tag_prev_name;
  var $tag_parent_name;

  function XMLParser()
  {
    $tag_name = NULL;
    $tag_data = array();
    $tag_prev_name = NULL;
    $tag_parent_name = NULL;
  }

  function startElement($parser, $name, $attrs)
  {
    if ($this->tag_name != NULL)
    {
      $this->tag_parent_name = $this->tag_name;
    }
    $this->tag_name = $name;
  }

  function endElement($parser, $name)
  {
    if ($this->tag_name == NULL)
    {
      $this->tag_parent_name = NULL;
    }
    $this->tag_name = NULL;
    $this->tag_prev_name = NULL;
  }

  function characterData($parser, $data)
  {
    if ($this->tag_name == $this->tag_prev_name)
    {
      $data = $this->tag_data[$this->tag_name] . $data;
    }
    $this->tag_data[$this->tag_name] = $data;
    if ($this->tag_parent_name != NULL)
    {
      $this->tag_data[$this->tag_parent_name . "." . $this->tag_name] = $data;
    }
    $this->tag_prev_name = $this->tag_name;
  }

  function parse($data)
  {
    $xml_parser = xml_parser_create();
    xml_set_object($xml_parser, $this);
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");
    $success = xml_parse($xml_parser, $data, true);
    if (!$success)
    {
      $this->tag_data['error'] =  sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
    }
    xml_parser_free($xml_parser);
    return ($success);
  }

  function getElement($tag)
  {
    return ($this->tag_data[$tag]);
  }
}

function GetPubChemMolecule()
{
  $q = $_POST['q'];
  $dim = empty($_POST['dim']) ? 2 : $_POST['dim'];
  if ($dim != 3)
  {
    $dim = 2;
  }

  if (empty($q))
  {
    echo "Query error. Empty query.";
    exit;
  }

  $localPubChemFile = sprintf("./PubChemLocal/%s_%sD.mol", $q, $dim);
  if (file_exists($localPubChemFile))
  {
    $mol = file_get_contents($localPubChemFile);
  }
  else
  {
    if (is_numeric($q))
    {
      $cid = $q;
    }
    else
    {
      $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pccompound&field=ALL&retmax=1&tool=ChemDoodleWeb&email=customerservice@ichemlabs.com&term='.htmlentities($q);
      $idListXml = file_get_contents($url);
  
      $xml = new XMLparser();
      if (!$xml->parse($idListXml))
      {
        $log = sprintf('ERROR: XML parsing error ==> '.$xml->getElement('error'));
        error_log($log);
        exit;
      }
  
      $cid = $xml->getElement('Id');
    }
  
    if ($cid)
    {
      if ($dim == 2)
      {
        $url = 'http://pubchem.ncbi.nlm.nih.gov/summary/summary.cgi?db=pccompound&disopt=DisplaySDF&tool=ChemDoodleWeb&email=customerservice@ichemlabs.com&cid='.$cid;
        $mol = file_get_contents($url);
        // Save a local copy to avoid load on PubChem servers
        file_put_contents($localPubChemFile, $mol);
      }
      else
      {
        $url = 'http://pubchem.ncbi.nlm.nih.gov/summary/summary.cgi?db=pccompound&disopt=3DDisplaySDF&tool=ChemDoodleWeb&email=customerservice@ichemlabs.com&cid='.$cid;
        $mol = file_get_contents($url);
  
        if (strstr($mol, "3D info is not available"))
        {
          echo "ChemDoodle Web Components Query Error. No 3D info for this molecule.";
          exit;
        }
        else
        {
          // Save a local copy to avoid load on PubChem servers
          file_put_contents($localPubChemFile, $mol);
        }
      }
    }
    else
    {
      echo "ChemDoodle Web Components Query Error. Molecule not found.";
      exit;
    }
  }
  echo "<pre>\n";
  echo $mol;
  echo "</pre>\n";
}

GetPubChemMolecule();
?>
