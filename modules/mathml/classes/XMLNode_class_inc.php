<?php

class XMLNode
{
	// Private variables
	 private $_id = "";
	 private $_name;
	 private $_content;
	 private $_mt_elem_flg;
	 private $_attr_arr;
	 private $_child_arr;
	 private $_nmspc;
	 private $_nmspc_alias;
	 private $_parent_id;
	 private $_parent_node;
	
	public function XMLNode($id = NULL)
	{
		$this->_id = isset($id) ? $id : md5(uniqid(rand(),1));
		$this->_name = '';
		$this->_content = '';
		$this->_mt_elem_flg = FALSE;
		$this->_attr_arr = array();
		$this->_child_arr = array();
		$this->_nmspc = '';
		$this->_nmspc_alias = '';
		$this->_parent_id = FALSE;
		$this->_parent_node = NULL;
	}
	
	public function addChild($node)
	{
		$this->_child_arr[$node->getId()] = $node;
		$node->setParentId($this->_id);
		$node->setParentNode($this);
	}
	
	public function addChildArr($node_arr)
	{
		$key_arr = array_keys($node_arr);
		$num_key = count($key_arr);
		
		for ($i = 0; $i < $num_key; $i++) {
			$node =& $node_arr[$key_arr[$i]];
			$this->addChild($node);
		}
	}
	
	public function insertChildBefore($idx,$node)
	{
		$key_arr = array_keys($this->_child_arr);
		$num_key = count($key_arr);
		$tmp_arr = arry();
		
		for ($i = 0;$i < $num_key;$i++) {
			if ($i == $idx) {
				$tmp_arr[$node->getId()] = $node;
			}
			$tmp_arr[$key_arr[$i]] = $this->_child_arr[$key_arr[$i]];
		}
		$this->_child_arr =& $tmp_arr;
	}
	
	public function insertChildAfter($idx,$node)
	{
		$key_arr = array_keys($this->_child_arr);
		$num_key = count($key_arr);
		$tmp_arr = arry();
		
		for ($i = 0;$i < $num_key;$i++) {
			$tmp_arr[$key_arr[$i]] = $this->_child_arr[$key_arr[$i]];
			if ($i == $idx) {
				$tmp_arr[$node->getId()] = $node;
			}
		}
		$this->_child_arr = $tmp_arr;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
	}
	
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	public function setNamepace($nmspc)
	{
		$this->_nmspc = $nmspc;
	}
	
	public function setNamespaceAlias($nmspc_alias)
	{
		$this->_nmspc_alias = $nmspc_alias;
	}
	
	public function setContent($content)
	{
		$this->_content = $content;
	}
	
	public function setEmptyElem($mt_elem_flg)
	{
		$this->_mt_elem_flg = $mt_elem_flg;
	}
	
	public function setAttr($attr_nm,$attr_val)
	{
		$this->_attr_arr[$attr_nm] = $attr_val;
	}
	
	public function setAttrArr($attr_arr)
	{
		$this->_attr_arr = $attr_arr;
	}
	
	public function setParentId($id)
	{
		$this->_parent_id = $id;
	}
	
	public function setParentNode($node)
	{
		$this->_parent_node = $node;
	}
	
	public function getId()
	{
		return($this->_id);
	}
	
	public function getName()
	{
		return($this->_name);
	}
	
	public function getNamespace()
	{
		return($this->_nmspc);
	}
	
	public function getNamespaceAlias()
	{
		return($this->_nmspc_alias);
	}
	
	public function getContent()
	{
		return($this->_content);
	}
	
	public function getAttr($attr_nm)
	{
		if (isset($this->_attr_arr[$attr_nm])) {
			return($this->_attr_arr[$attr_nm]);
		} else {
			return(NULL);
		}
	}
	
	public function getAttrArr()
	{
		return($this->_attr_arr);
	}
	
	public function getParentId()
	{
		return($this->parent_id);
	}
	
	public function getParentNode()
	{
		return($this->_parent_node);
	}
	
	public function getChild($id)
	{
		if (isset($this->_child_arr[$id])) {
			return($this->_child_arr[$id]);
		} else {
			return(FALSE);
		}
	}
	
	public function getFirstChild()
	{
		$id_arr = array_keys($this->_child_arr);
		$num_child = count($id_arr);
		
		if ($num_child > 0) {
			return($this->_child_arr[$id_arr[0]]);
		} else {
			return(FALSE);
		}
	}
	
	public function getLastChild()
	{
		$id_arr = array_keys($this->_child_arr);
		$num_child = count($id_arr);
		
		if ($num_child > 0) {
			return($this->_child_arr[$id_arr[$num_child - 1]]);
		} else {
			return(FALSE);
		}
	}
	
	public function getChildByIdx($idx)
	{
		$id_arr = array_keys($this->_child_arr);
		
		if (isset($this->_child_arr[$id_arr[$idx]])) {
			return($this->_child_arr[$id_arr[$idx]]);
		} else {
			return(FALSE);
		}
	}
	
	public function getNumChild()
	{
		return(count($this->_child_arr));
	}
	
	function removeChild($id)
	{
		unset($this->_child_arr[$id]);
	}
	
	public function removeChildByIdx($idx)
	{
		$key_arr = array_keys($this->_child_arr);
		unset($this->_child_arr[$key_arr[$idx]]);
	}
	
	public function removeFirstChild()
	{
		$key_arr = array_keys($this->_child_arr);
		unset($this->_child_arr[$key_arr[0]]);
	}
	
	public function removeLastChild()
	{
		$key_arr = array_keys($this->_child_arr);
		unset($this->_child_arr[$key_arr[count($key_arr)-1]]);
	}
	
	public function dumpXML($indent_str = "\t")
	{
		$attr_txt = $this->_dumpAttr();
		$name = $this->_dumpName();
		$xmlns = $this->_dumpXmlns();
		$lvl = $this->_getCurrentLevel();
		$indent = str_pad('',$lvl,$indent_str);
		
		if ($this->_mt_elem_flg) {
			$tag = "$indent<$name$xmlns$attr_txt />";
			return($tag);
		} else {
			$key_arr = array_keys($this->_child_arr);
			$num_child = count($key_arr);
			
			$tag = "$indent<$name$xmlns$attr_txt>$this->_content";
			
			for ($i = 0;$i < $num_child;$i++) {
				$node = $this->_child_arr[$key_arr[$i]];
				
				$child_txt = $node->dumpXML($indent_str);
				$tag .= "\n$child_txt";
			}
			
			$tag .= ($num_child > 0 ? "\n$indent</$name>" : "</$name>");
			return($tag);
		}
	}
	
	public function _dumpAttr()
	{
		$id_arr = array_keys($this->_attr_arr);
		$id_arr_cnt = count($id_arr);
		$attr_txt = '';
		
		for($i = 0;$i < $id_arr_cnt;$i++) {
			$key = $id_arr[$i];
			$attr_txt .= " $key=\"{$this->_attr_arr[$key]}\"";
		}
		
		return($attr_txt);
	}
	
	public function _dumpName()
	{
		$alias = $this->getNamespaceAlias();
		if ($alias == '') {
			return($this->getName());
		} else {
			return("$alias:" . $this->getName());
		}
	}
	
	public function _dumpXmlns()
	{
		$nmspc = $this->getNamespace();
		$alias = $this->getNamespaceAlias();
		
		if ($nmspc != '') {
			if ($alias == '') {
				return(" xmlns=\"" . $nmspc . "\"");
			} else {
				return(" xmlns:$alias=\"" . $nmspc . "\"");
			}
		} else {
			return('');
		}
	}
	
	public function _getCurrentLevel()
	{
		if ($this->_parent_id === FALSE) {
			return(0);
		} else {
			$node = $this->getParentNode();
			$lvl = $node->_getCurrentLevel();
			$lvl++;
			return($lvl);
		}
	}
}
?>