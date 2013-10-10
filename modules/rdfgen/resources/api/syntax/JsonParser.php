<?php
/**
 * This class provides capabilities to parse json encoded rdf models.
 *
 * @package syntax
 * @author Philipp Frischmuth <philipp@frischmuth24.de>
 * @version $Id: JsonParser.php 555 2007-12-11 20:27:34Z p_frischmuth $
 */
class JsonParser extends Object {
	
	/**
	 * This method takes a json encoded rdf-model and a reference to aa (usually empty) MemModel, parses the json
	 * string and adds the statements to the given MemModel.
	 *
	 * @param string $jsonString The string that contains the rdf model, encoded as a json-string.
	 * @param MemModel $model A reference to the model, where to add the statements, usually an empty MemModel.
	 */
	public function generateModelFromString($jsonString, $model) {
		
		$jsonModel = array();
		$jsonModel = json_decode($jsonString, true);
		
		// throws an excpetion if json model was corrupt
		if (!is_array($jsonModel)) {
			throw new Exception('error in json string');
		}
		
		foreach ($jsonModel as $subject=>$remain) {
			foreach ($remain as $predicate=>$object) {
				$s = (strpos($subject, '_') === 0) ? new BlankNode(substr($subject, 2)) : new Resource($subject);
				$p = new Resource($predicate);
				
				foreach ($object as $obj) {
					if ($obj['type'] === 'uri') {
						$o = new Resource($obj['value']);
					} else if ($obj['type'] === 'bnode') {
						$o = new BlankNode(substr($obj['value'], 2));
					} else {
						$dtype = (isset($obj['datatype'])) ? $obj['datatype'] : '';
						$lang = (isset($obj['lang'])) ? $obj['lang'] : '';

						$oVal = $obj['value'];

						$o = new Literal($oVal, $lang);
						$o->setDatatype($dtype);
					}
					
					$model->add(new Statement($s, $p, $o));
				}
			}
		}
	}
}
?>
