<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';

class HttpCache {
  var $_directory;
  
  function __construct($directory) {
    $this->_directory = $directory;
    if (substr($this->_directory, -1) != DIRECTORY_SEPARATOR) {
      $this->_directory .= DIRECTORY_SEPARATOR;
    }
  }


  function make_conditional_request($request) {
    return $request;
  }
  
  function write($request, $response) {
    
  }
  
  function get_cached_response($request) {
    
  }
  
  function get_cache_filename($request) {
    $accept = $request->headers['Accept'];
    $accept_parts = split(',', $accept);
    sort($accept_parts);
    $accept = join(',', $accept_parts);
    return $this->_directory . md5('<' . $request->uri . '>' . $accept); 
  }
  

}
?>
