<?php
/**
The Talis Platform organises authentication into a number of capabilities. A user can have any or all of these capabilities assigned to them.
Each service provided by the platform may require one of these capabilities to be used.
This class can be passed to other platform classes to supply any necessary authentication information. The class can be constructed
with a single username and password which will be used for all authentication requests. This is a the most normal mode of operation.
However, the username and password for each type of capability can be overridden on an individual basis.

See http://n2.talis.com/wiki/Capabilities for more information on Capabilities including which services require the user to
have which capability.
*/
class Credentials {
  var $username;
  var $password;

  function Credentials($username, $password) {
    $this->username = $username;
    $this->password = $password;
  }
  
  function get_auth() {
    return $this->username . ':' . $this->password;
  }

}
?>
