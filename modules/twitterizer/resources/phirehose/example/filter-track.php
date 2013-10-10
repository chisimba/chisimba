<?php
require_once('../lib/Phirehose.php');
require_once('/var/www/cmysql/lib/pear/XML/RPC.php');
/**
 * Example of using Phirehose to display a live filtered stream using track words 
 */
class FilterTrackConsumer extends Phirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process. 
     */
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
        // Send to chisimba API
        $params = array(
                          new XML_RPC_VALUE(urldecode($data['text']), 'string'), 
                          new XML_RPC_VALUE($data['created_at'], 'string'), 
                          new XML_RPC_VALUE(strtotime($data['created_at']), 'string'), 
                          new XML_RPC_VALUE(urldecode($data['user']['screen_name']), 'string'), 
                          new XML_RPC_VALUE(urldecode($data['user']['name']), 'string'),
                          new XML_RPC_VALUE($data['user']['profile_image_url'], 'string'),
                          new XML_RPC_VALUE(urldecode($data['user']['location']), 'string'),   
                      );
          $msg = new XML_RPC_Message('twitterizer.addTweet', $params);
          $cli = new XML_RPC_Client('/cmysql/index.php?module=api', '127.0.0.1');
          $cli->setDebug(0);
          $resp = $cli->send($msg);
          if(!$resp) {
              echo "Communication err: ".$cli->errstr;
              exit;
          }
          if(!$resp->faultCode()) {
              $val = $resp->value();
              $val = XML_RPC_decode($val);
              // var_dump($val);
          }
          else {
              echo "Fault Code: ".$resp->faultCode()."\n";
              echo "Fault Reason: ".$resp->faultString()."\n";
          }
    }
  }
}

// Start streaming
$sc = new FilterTrackConsumer('paulscott56', 'pongid56', Phirehose::METHOD_FILTER);
$sc->setTrack(array('#WC2010', '#wc2010', 'wc2010', 'WC2010', 'SWC2010', '#SWC2010', 'vuvuzela', '#vuvuzela'));
$sc->consume();
