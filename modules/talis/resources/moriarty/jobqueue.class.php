<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'simplegraph.class.php';

class JobQueue {

  var $uri;
  var $request_factory;
  var $credentials;

  function __construct($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }


  function schedule_reset_data($time = null, $label = null) {
    return $this->schedule_job($this->make_job_request(BF_RESETDATAJOB, $time, $label));
  }

  function schedule_snapshot($time = null, $label = null) {
    return $this->schedule_job($this->make_job_request(BF_SNAPSHOTJOB, $time, $label));
  }

  function schedule_reindex($time = null, $label = null) {
    return $this->schedule_job($this->make_job_request(BF_REINDEXJOB, $time, $label));
  }

  function schedule_restore($snapshot_uri, $time = null, $label = null) {
    $job = $this->make_job_request(BF_RESTOREJOB, $time, $label);
    $job->add_resource_triple('_:job', BF_SNAPSHOTURI, $snapshot_uri);
    return $this->schedule_job($job);
  }

  function make_job_request($jobtype, $time = null, $label = null) {
    $time = $time == null ? gmmktime() : $time;

    $formatted_time = gmdate("Y-m-d\TH:i:s\Z", $time);
    $label = $label == null ? 'Reset data job submitted ' . $formatted_time : $label;

    $job = new SimpleGraph();
    $job->add_resource_triple('_:job', BF_JOBTYPE,   $jobtype);
    $job->add_resource_triple('_:job', RDF_TYPE,     BF_JOBREQUEST);
    $job->add_literal_triple( '_:job', BF_STARTTIME, $formatted_time);
    $job->add_literal_triple( '_:job', RDFS_LABEL ,  $label);
    return $job;
  }

  function schedule_job($job) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $body = $job->to_rdfxml();

    $uri = $this->uri;
    $mimetype = MIME_RDFXML;

    $request = $this->request_factory->make( 'POST', $uri);
    $request->set_accept("*/*");
    $request->set_content_type($mimetype);
    $request->set_body( $body );
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }
    return $request->execute();
  }

}

?>
