<?php
/**
 * This class provides capabilities to serialize MemModels to json strings.
 *
 * @package syntax
 * @author Philipp Frischmuth <philipp@frischmuth24.de>
 * @version $Id: JsonSerializer.php 555 2007-12-11 20:27:34Z p_frischmuth $
 */
class JsonSerializer extends Object {
	
	/**
	 * This method takes a MemModel object ad parameter and serializes all contained triples to rdf/json format.
	 * 
	 * @see http://n2.talis.com/wiki/RDF_JSON_Specification#rdf.2Fjson
	 * @param MemModel $model
	 * @return string Returns a string containing the serialized model.
	 */
	public function serialize(MemModel $model) {
		
		// create the root json object (root object)
		$jsonString = '{';
		$subjects = array();
		
		// sort triples by subject
		foreach ($model->triples as $triple) {
			$subjects[$triple->toStringSubject()][] = $triple;
		}
		
		// sort alphabetically
		ksort($subjects);
			
		// triples are sorted by subject now, each key is a subject uri, containing all triples with this subject uri
		$i = 0;
		foreach ($subjects as $predicatesArray) {
			$predicates = array();
			
			if ($i > 0) {
				$jsonString .= ',';
			}
			$i++;
			
			$subj = $predicatesArray[0]->getSubject();
			
			// add special _: sequence for blank node only
			if ($subj instanceof BlankNode) {
				$jsonString .= '"_:' . $this->_escapeValue($subj->getLabel()) . '":';
			} else {
				$jsonString .= '"' . $this->_escapeValue($subj->getLabel()) . '":';
			}
			
			
			
			// create a json object for each subject (subject object)
			$jsonString .= '{';
			
			// sort triples with current subject by predicate
			foreach ($predicatesArray as $triple) {	
				$predicates[$triple->toStringPredicate()][] = $triple;
			}
			
			// sort alphabetically
			ksort($predicates);
			
			$j = 0;
			foreach ($predicates as $valueArray) {
				
				if ($j > 0) {
					$jsonString .= ',';
				}
				$j++;
				
				$jsonString .= '"' . $this->_escapeValue($valueArray[0]->getLabelPredicate()) . '":';
				
				// create a json array (value array) 
				$jsonString .= '[';
				
				$k = 0;
				foreach ($valueArray as $triple) {
					if ($k > 0) {
						$jsonString .= ',';
					}
					$k++;
					
					// create json value object (value object)
					$jsonString .= '{';
					
					$obj = $triple->getObject();
					
					// add special _: sequence for blank nodes only
					if ($obj instanceof BlankNode) {
						$jsonString .= '"value":"_:' . $this->_escapeValue($obj->getLabel()) . '",';
					} else if ($obj instanceof Literal) {
						$jsonString .= '"value":"' . $this->_escapeValue($obj->getLabel()) . '",';
					} else {
						$jsonString .= '"value":"' . $this->_escapeValue($obj->getLabel()) . '",';
					}
					
					// add type of object
					if ($obj instanceof Literal) {
						$jsonString .= '"type":"literal"';
					} else if ($obj instanceof BlankNode) {
						$jsonString .= '"type":"bnode"';
					} else {
						$jsonString .= '"type":"uri"';
					}
					
					if ($obj instanceof Literal) {
						if ($obj->getLanguage() != '') {
							$jsonString .= ',"lang":"' . $this->_escapeValue($obj->getLanguage()) . '"';
						}
						if ($obj->getDatatype() != '') {
							$jsonString .= ',"datatype":"' . $this->_escapeValue($obj->getDatatype()) . '"';
						}
						
					}
					
					// close value object
					$jsonString .= '}';
				}
				
				// close the value array
				$jsonString .= ']';
			}
			
			// close the json object (for the subject) (subject object)
			$jsonString .= '}';
		}
		
		// close root json object (root object)
		$jsonString .= '}';
		
		return $jsonString;
	}
	
	/*
	 * Escapes the following chars as specified at json.org:
	 * 
	 * " -> \"
	 * \ -> \\
	 * / -> \/
	 * \b -> \\b
	 * \f -> \\f
	 * \n -> \\n
	 * \r -> \\r
	 * \t -> \\t
	 * \uXXXX -> \\uXXXX
	 */
	protected function _escapeValue($value) {
		
		
		$value = str_replace("\\", '\\\\', $value);
		#$value = str_replace("/", '\/', $value);
		$value = str_replace("\n", '\\n', $value);
		$value = str_replace("\t", '\\t', $value);
		$value = str_replace("\r", '\\r', $value);
		$value = str_replace("\b", '\\b', $value);
		$value = str_replace("\f", '\\f', $value);
		$value = str_replace('"', '\"', $value);
		
		return $value;
	}
}
?>
