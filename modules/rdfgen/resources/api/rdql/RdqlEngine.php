<?php

// ----------------------------------------------------------------------------------
// Class: RdqlEngine
// ----------------------------------------------------------------------------------

/**
 * Some general methods common for RdqlMemEngine and RdqlDbEngine
 *
 * @version  $Id: RdqlEngine.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Radoslaw Oldakowski <radol@gmx.de>
 *
 * @package rdql
 * @access public
 */

Class RdqlEngine extends Object{

	

/**
 * Prints a query result as HTML table.
 * You can change the colors in the configuration file.
 *
 * @param array $queryResult [][?VARNAME] = object Node
 * @access private
 */
 function writeQueryResultAsHtmlTable($queryResult) {
 	  // Import Package Utility
   	 include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

   if (current($queryResult[0]) == NULL) {
      echo 'no match<br>';
      return;
   }

        echo '<table border="1" cellpadding="3" cellspacing="0"><tr><td><b>No.</b></td>';
   foreach ($queryResult[0] as $varName => $value)
     echo "<td align='center'><b>$varName</b></td>";
     echo '</tr>';

   foreach ($queryResult as $n => $var) {

   		
     echo '<tr><td width="20" align="right">' .($n + 1) .'.</td>';
     foreach ($var as $varName => $value) {
       echo INDENTATION . INDENTATION . '<td bgcolor="';
   	   echo RDFUtil::chooseColor($value);
       echo '">';
       echo '<p>';

       $lang  = NULL;
       $dtype = NULL;
       if (is_a($value, 'Literal')) {
    	   if ($value->getLanguage() != NULL)
               $lang = ' <b>(xml:lang="' . $value->getLanguage() . '") </b> ';
		   if ($value->getDatatype() != NULL)
  			  $dtype = ' <b>(rdf:datatype="' . $value->getDatatype() . '") </b> ';
       }
  	   echo  RDFUtil::getNodeTypeName($value) .$value->getLabel() . $lang . $dtype .'</p>';
     }
     echo '</tr>';
   }
   echo '</table>';
 }
 
} // end: Class RdqlEngine

?>