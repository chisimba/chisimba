<?php
/**
 * (c) 2010 phpgrease.net
 *
 * For licensing terms, plese see license.txt which should distribute with this source
 *
 * @package Pandra
 * @link http://www.phpgrease.net/projects/pandra
 * @author Michael Pearson <pandra-support@phpgrease.net>
 */

class PandraClauseRegex extends PandraClause {
    private $_pattern = NULL;

    private $_flags = NULL;

    private $_offset = NULL;

    public $matches = array();

    public function __construct() {
        $args = func_get_args();

        $this->_pattern = func_get_arg(0);

        if (isset($args[1])) $this->_flags = func_get_arg(1);
        if (isset($args[2])) $this->_offset = func_get_arg(2);       
    }

    public function match($value) {
        return preg_match($this->_pattern, $value, $this->matches, $this->_flags, $this->_offset);
    }
}
?>