<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

class parse_icecast_info{

	// connect to icecast and get the xml info.
	public function iceinfo($ip,$port,$user,$pass) {
		$this->infila = array();
		$this->temp=null;
		$this->objcomp=null;

		$this->ch = curl_init();
		
		curl_setopt($this->ch,CURLOPT_URL,"http://".$ip.":".$port."/admin/stats");
		curl_setopt($this->ch,CURLOPT_HEADER,false);
		//curl_setopt($this->ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
		curl_setopt($this->ch,CURLOPT_USERPWD,$user.":".$pass);
		curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
		
		$this->output=curl_exec($this->ch);
		
		if (curl_errno($this->ch))
			echo curl_error($this->ch);
		
		curl_close($this->ch);
		
		$this->parser = xml_parser_create();

		xml_set_object($this->parser,$this);
		xml_set_element_handler($this->parser,"inizio","fine");
		xml_set_character_data_handler($this->parser,"componi");
		xml_parse($this->parser,$this->output);
		xml_parser_free($this->parser);

		return $this->infila;
	}
	
	//functions to parse the icecast xml (inizio,componi,fine)
	public function inizio($parser,$name,$attr) {
		if ($name == "SOURCE") {
			// create nuovatemp empty
			$this->temp->mount=$attr["MOUNT"];
		}
		else  $this->objcomp = $name;
	}
	
	public function componi($parser,$dato) {
		if (is_null($this->objcomp) || is_null($this->temp)) 
			return;

		if ($this->objcomp == "AUDIO_INFO")
	 		$this->temp == "audio_info";
		elseif ($this->objcomp == "BITRATE")
			$this->temp->bitrate=$dato;
		elseif ($this->objcomp == "CHANNELS")
			$this->temp->channels=$dato;
		elseif ($this->objcomp == "LISTENERS")
			$this->temp->listeners=$dato;
		elseif ($this->objcomp == "LISTENURL")
			$this->temp->listenurl=$dato;
		elseif ($this->objcomp == "PUBLIC")
			$this->temp->public=$dato;
		elseif ($this->objcomp == "SAMPLERATE")
			$this->temp->samplerate=$dato;
	}
	
	public function fine($parser,$name) {
		if ($name == "SOURCE") {
			array_push($this->infila,$this->temp);
			$this->temp=null;
		}
		else $this->objcomp=null;
	}

}

?>
