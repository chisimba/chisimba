<?php
include("XMLNode_class_inc.php");

class MathMLNode extends XMLNode
{
	public $_name;	
	
	public function MathMLNode($id = NULL)
	{
		parent::XMLNode($id);
	}
	
	public function removeBrackets()
	{
		if ($this->_name == 'mrow') {
			if ($c_node_0 = $this->getFirstChild()) {
				$c_node_0->isLeftBracket() ? $this->removeFirstChild() : 0;
			}
			
			if ($c_node_0 = $this->getLastChild()) {
				$c_node_0->isRightBracket() ? $this->removeLastChild() : 0;
			}
		}
	}
	
	public function isLeftBracket()
	{
		switch ($this->_content) {
			case '{':
			case '[':
			case '(':
				return(TRUE);
				break;
		}
		return(FALSE);
	}
	
	public function isRightBracket()
	{
		switch ($this->_content) {
			case '}':
			case ']':
			case ')':
				return(TRUE);
				break;
		}
		return(FALSE);
	}
}
?>