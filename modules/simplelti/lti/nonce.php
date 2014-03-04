<?php

require_once("debug.php");

function getNonce($secret) {
  $created = gmdate("Y-m-d")."T".gmdate("H:i:s")."Z";
  DPRT("Current Time:".$nonce);
  $nonce = uniqid();

  $presha1 = $nonce . $created . $secret;
  // DPRT("Presha1 " . $presha1);
  $x = sha1($presha1, TRUE);
  $digest = base64_encode($x);
  DPRT("postsha1 " . $digest );
  $arr = array("created" => $created, "nonce" => $nonce, "digest" => $digest );
  return $arr;
}

function checkTime($created, $seconds ) {
    $d = gmdate("Y-m-d")."T".gmdate("H:i:s")."Z";
    DPRT("Current Time:".$d);
    DPRT("Created Time:".$created);
    $dm = strtotime($d);
    $cm = strtotime($created);
    if ( ! ( $cm && $dm ) ) {
         DPRT("Bad Date format");
         return false;
    }
    $diff = abs($dm - $cm);
    DPRT("Time Difference in seconds actual:".$diff." acceptible:".$seconds);
    if ( $diff > $seconds ) {
        DPRT("Expired");
        return false;
    }
    return true;
}

function checkNonce($nonce, $created, $digest, $secret, $seconds = -1 ) {

    if ( ! ( $nonce && $created && $digest && $secret ) ) {
        DPRT("checkNonce() missing Required Parameter");
        DPRT("nonce=$nonce created=$created digest=$digest secret=*****\n");
        // DPRT("nonce=$nonce created=$created digest=$digest secret=$secret\n");
	return false;
    }

    // Check to see if the timestamp is in range...
    if ( $seconds > 0 ) {
        $d = gmdate("Y-m-d")."T".gmdate("H:i:s")."Z";
        DPRT("Current Time:".$d);
        DPRT("Created Time:".$created);
        $dm = strtotime($d);
        $cm = strtotime($created);
        if ( ! ( $cm && $dm ) ) {
             DPRT("Bad Date format");
             return false;
        }
        $diff = abs($dm - $cm);
        DPRT("Time Difference in seconds actual:".$diff." acceptible:".$seconds);
        if ( $diff > $seconds ) {
            DPRT("Expired");
            return false;
        }
    }

    // Check the nonce to match the secret
    $presha1 = $nonce . $created . $secret;
    // DPRT("Presha1 " . $presha1);
    $x = sha1($presha1, TRUE);
    $y = base64_encode($x);
    DPRT("postsha1 " . $y );
    DPRT("digest " . $digest );
    if ( $digest != $y ) {
      DPRT("No Match");
      return false;
    }
    DPRT("Match");
    return true;
}

?>
