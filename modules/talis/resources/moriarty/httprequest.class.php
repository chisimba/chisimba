<?php

class HttpRequest {
  var $method;
  var $uri;
  var $headers = array();
  var $client;
  var $body;
  var $auth;

  function HttpRequest($method, $uri, $credentials = null) {
    $this->uri = $uri;
    $this->method = $method;
    if ( $credentials != null ) {
      $this->auth = $credentials->get_auth();
    }
    else {
       $this->auth = null;
    }
    
    $this->headers = array();
    $this->options = array();
    $this->body = null;

  }


  function execute($options=array()) {
    $poster = curl_init($this->uri);

    if ($this->auth != null) {
      curl_setopt($poster, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
      curl_setopt($poster, CURLOPT_USERPWD, $this->auth);
    }
   // curl_setopt($poster, CURLOPT_VERBOSE, 1);


    curl_setopt($poster, CURLOPT_FRESH_CONNECT,TRUE);

    curl_setopt($poster, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($poster, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($poster, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($poster, CURLOPT_TIMEOUT, 600);
    curl_setopt($poster, CURLOPT_HTTPHEADER, $this->get_headers() );
    curl_setopt($poster, CURLOPT_HEADER, 1);

    if ( defined( 'MORIARTY_PROXY' ) ) {
      curl_setopt($poster, CURLOPT_PROXY, MORIARTY_PROXY );
    }

    switch(strtoupper($this->method)) {
        case 'GET'  : break;
        case 'POST' : curl_setopt($poster, CURLOPT_POST, 1); break;
        default     : curl_setopt($poster, CURLOPT_CUSTOMREQUEST,strtoupper($this->method));
    }

    if ($this->body != null) {
      curl_setopt($poster, CURLOPT_POSTFIELDS, $this->body);
    }


    $raw_response = curl_exec($poster);
    $response_info = curl_getinfo($poster);
    $response_error = curl_error($poster);
    curl_close($poster);

    if ( $raw_response ) {
      list($response_code,$response_headers,$response_body) = $this->parse_response($raw_response);
    }
    else {
      $response_code = $response_info['http_code'];
      $response_body = "Request failed: " . $response_error;
      $response_headers = array();
    }
// if ( $response_code != 200) {
// echo  '<pre>' . htmlspecialchars($raw_response) . '</pre>';
// }

    $response = new HttpResponse();
    $response->status_code = $response_code;
    $response->headers = $response_headers;
    $response->body = $response_body;
    $response->info = $response_info;
    $response->request = $this;
    return $response;
  }

  function get_headers() {
    $flat_headers = array();
    foreach ($this->headers as $k=>$v) {
      $flat_headers[] = "$k: $v";
    }
    return $flat_headers;
  }

  function set_body($val) {
    $this->body = $val;
//    $this->headers['Content-Length'] = strlen($val);
  }

  function get_body() {
    return $this->body;
  }

  function set_accept($val) {
    $this->headers['Accept'] = $val;
  }

  function set_content_type($val) {
    $this->headers['Content-Type'] = $val;
  }


function parse_response($response){
   /*
   ***original code extracted from examples at
   ***http://www.webreference.com/programming
                           /php/cookbook/chap11/1/3.html

   ***returns an array in the following format which varies depending on headers returned

       [0] => the HTTP error or response code such as 404
       [1] => Array
       (
           [Server] => Microsoft-IIS/5.0
           [Date] => Wed, 28 Apr 2004 23:29:20 GMT
           [X-Powered-By] => ASP.NET
           [Connection] => close
           [Set-Cookie] => COOKIESTUFF
           [Expires] => Thu, 01 Dec 1994 16:00:00 GMT
           [Content-Type] => text/html
           [Content-Length] => 4040
       )
       [2] => Response body (string)
*/

   do
     {
      if ( strstr($response, "\r\n\r\n") == FALSE) {
        $response_headers = $response;
        $response = '';
      }
      else {
       list($response_headers,$response) = explode("\r\n\r\n",$response,2);
      }
       $response_header_lines = explode("\r\n",$response_headers);

   // first line of headers is the HTTP response code
       $http_response_line = array_shift($response_header_lines);
       if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line,
                     $matches)) {
         $response_code = $matches[1];
       }
       else
         {
           $response_code = "Error";
         }
     }
   while (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$response));

   $response_body = $response;

   // put the rest of the headers in an array
   $response_header_array = array();
   foreach ($response_header_lines as $header_line) {
       list($header,$value) = explode(': ',$header_line,2);
       $response_header_array[strtolower($header)] = $value;
   }

   return array($response_code,$response_header_array,$response_body);
  }

  function set_auth($auth_string) {
    $this->auth = $auth_string;
  }

  function to_string() {
    $ret = strtoupper($this->method) . ' ' . $this->uri . "\n";
    foreach ($this->headers as $k=>$v) {
      $ret .= "$k: $v\n";
    }
    $ret .= "\n";
    $ret .= $this->get_body();

    return $ret;
  }

}

class HttpResponse {
  var $status_code;
  var $headers = array();
  var $info = array();
  var $body;
  var $request;

  function __construct($status_code = null) {
    $this->status_code = $status_code;
  }

  function is_success() {
    return $this->status_code >= 200 && $this->status_code < 300;
  }


  function to_string() {
    $ret = $this->status_code . "\n";
    foreach ($this->headers as $k=>$v) {
      $ret .= "$k: $v\n";
    }
    $ret .= "\n";
    $ret .= $this->body;

    return $ret;
  }
}

?>
