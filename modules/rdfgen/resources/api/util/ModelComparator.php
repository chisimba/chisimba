<?php

// ----------------------------------------------------------------------------------
// Class: ModelComparator
// ----------------------------------------------------------------------------------


/**
 * This class compares to models. This comparator bases on the labelling algorithm 
 * described in <href="http://www.hpl.hp.com/techreports/2003/HPL-2003-142.pdf" >Signing RDF Graphs</href> 
 * by Jeremy J. Carroll.
 *
 *
 * @version  $Id$
 * @author Tobias Gauﬂ <tobias.gauss at web.de>
 *
 * @package utility
 * @access	public
 *
 **/ 
 class ModelComparator extends Object {
 	
 	/**
 	* Compares two models.
 	*
 	* @param $thisModel First Model.
 	* @param $thatModel Second Model.
 	*
 	* @return boolean
 	*/
 	function compare($thisModel, $thatModel){
 	
 	$thisModeltriples = null;
 	$thatModeltriples = null;
 	
 		if(is_a($thisModel,"DbModel")){
 			$thisModeltriples = 	$thisModel->getMemModel();
 		}else{
 			$thisModeltriples = 	$thisModel;
 		}
 		
 		if(is_a($thatModel,"DbModel")){
 			$thatModeltriples = 	$thatModel->getMemModel();
 		}else{
 			$thatModeltriples = 	$thatModel;
 		}
 			
 		$sortArray1 = ModelComparator::buildSortArray($thisModeltriples->triples);
		$sortArray2 = ModelComparator::buildSortArray($thatModeltriples->triples);	
		
		$renamedArray1 = ModelComparator::renameBlanks($sortArray1);
		$renamedArray2 = ModelComparator::renameBlanks($sortArray2);
		
		return ModelComparator::compareTriples($renamedArray1,$renamedArray2);
 	}
 		
 	
 	/**
 	* Builds a sorted array. 
 	*
 	* @param $tripleList A List that contains the models triples.
 	*
 	* @return Array
 	*/
 	function buildSortArray($tripleList){
 		$sortedArray = Array();
 		
 		foreach($tripleList as $index => $triple){
 			$sub = null;
 			$obj = null;
 			$orgSub  = $triple->getSubject();
 			$orgObj  = $triple->getObject();
 			
 			if(is_a($orgSub,"Blanknode")){
 				$sub = $orgSub->getID();
 				$triple->subj = new Blanknode("~");
 			}
 			if(is_a($orgObj,"Blanknode")){
 				$obj = $orgObj->getID();
 				$triple->obj = new Blanknode("~");
 			}
 			$sortedArray[$index]['string']  = $triple->toString();
 			$sortedArray[$index]['index']  = $index;
 			$sortedArray[$index]['triple'] = $triple;
 			$sortedArray[$index]['sub']    = $sub;
 			$sortedArray[$index]['obj']    = $obj;	
 		}
 		sort($sortedArray);
 		return $sortedArray;
 	}
 	
 	/**
 	* Renames the models Blanknodes. 
 	*
 	* @param $sortedArray A List that contains the models triples.
 	*
 	* @return Array
 	*/
 	function renameBlanks($sortedArray){
 	$i = 0;
 	$labelmap = Array();
 		
 		foreach ($sortedArray as $value){	
 			//new label
 			if($value['sub']!=null){
 				$label = null;
 				if(isset($labelmap[$value['sub']])){
 					$label = $labelmap[$value['sub']];
 					$value['triple']->subj = new BlankNode($labelmap[$value['sub']]);
 				}else{
 					$label = $i."Bnode";
 					$labelmap[$value['sub']]=$label;
 					$value['triple']->subj = new BlankNode($labelmap[$value['sub']]);
 					$i++;
 				}
 			}
 			
 			if($value['obj']!=null){
 				$label = null;
 				if(isset($labelmap[$value['obj']])){
 					$label = $labelmap[$value['obj']];
 					$value['triple']->obj = new BlankNode($labelmap[$value['obj']]);
 				}else{
 					$label = $i."Bnode";
 					$labelmap[$value['obj']]=$label;
 					$value['triple']->obj = new BlankNode($labelmap[$value['obj']]);
 					$i++;
 				}
 			}
 			
 		}
 		return $sortedArray;
 	
 	}
 	
 	/**
 	* Compares the Triples in the lists. 
 	*
 	* @param $tripleList A List that contains the models triples.
 	* @param $tripleList A List that contains the models triples.
 	*
 	* @return boolean
 	*/
 	function compareTriples($array1, $array2){
 		foreach($array1 as $key => $value){
 			if(!$value['triple']->equals($array2[$key]['triple']))
 				return false;
 		}
 		return true;
 	
 	}
 	
 
 }
 
 
 
 ?>