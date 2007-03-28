<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Background object
 * This class is a means to access the connection handling API.
 * Ported to Chisimba by Ryan Whitney, ryan@greenlikeme.org
 * @author Paul Scott
 * @copyright GNU/GPL AVOIR/UWC 2006
 * @package utilities
 *
 * @example
 * <?php
 * //include the class source
 * include('background_class_inc.php');
 *
 * $b = new background;
 *
 * //check the connection status
 * $status = $b->isUserConn();
 *
 * //keep the user connection alive even if the browser is closed
 * $callback = $b->keepAlive();
 *
 * //This is where you call your long running process
 * sleep(60); //fake a 60 second process
 *
 * //fork the process and create the child process and call the callback function when done
 * $call2 = $b->setCallBack("jdoe@example.com", "Your long thing is done", "The long running process that you requested is finished");
 * ?>
 *
 */
class background extends object
{
	/**
	 * @var $connStatus Property to hold user connection status
	 */
	private $connStatus;

	/**
	 * @var $callback Property to hold the callback function
	 */
	private $callback;

	/**
	 * @var $address Property to hold the users email address
	 */
	private $address;

	/**
	 * @var $subject Property to hold the email subject
	 */
	private $subject;

	/**
	 * @var $message Property to hold the message
	 */
	private $message;

	/**
	* Constructor
	*/
    public function init()
    {
    }

	/**
	 * Method to check users connection status
	 * @param void
	 * @return property $connStatus bool FALSE on user abort
	 */
	public function isUserConn()
	{
		//is the user still connected?
		if(connection_status()!=0)
		{
			//set a property saying that the user is dead
			$this->connStatus = FALSE;
			return FALSE;
		}
		else {
			$this->connStatus = TRUE;
			return TRUE;
		}
	}

	/**
	 * Method to keep the script going in the background
	 * @param void
	 * @return newline chars
	 */
	public function keepAlive()
	{
		set_time_limit(0);
		//this will force the script to keep running till the end
		ignore_user_abort(TRUE);
		//funny thing with PHP, it needs output to keep running(?)!
		while($this->connStatus != TRUE)
		{
			//this will save the loop
			print "\n";
   			flush(); //Now php will check the connection
   			sleep(1);
		}
	}

	/**
	 * Method to send mail as the callback function
	 * @param void
	 * @return void
	 */
	public function callBackFunc()
	{
		@mail($this->address, $this->subject ,$this->message);

	}

	/**
	 * Method for the callback function
	 * This is called dynamically be the shutdown function callback
	 * @param string $address - the users email addy
	 * @param string $subject - the subject
	 * @param string $message - the message to say the operation is done
	 * @return void
	 */
	public function setCallback($address,$subject,$message)
	{
		//set the properties
		$this->address = $address;
		$this->subject = $subject;
		$this->message = $message;

		//pop a mail off to the user when his operation completes
		//create the __Lambda Function
		$callback = create_function('$address, $subject, $message','@mail($address, $subject ,$message);');
		//execute the __Lambda function
		$callback($address, $subject, $message);
		//register the callback method
		register_shutdown_function(array(&$this, "callBackFunc"));
	}

}//end class
?>
