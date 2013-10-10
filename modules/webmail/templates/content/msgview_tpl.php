<?php
echo nl2br($message);
echo "<br />";
if(!empty($attachments))
{
	//are there multiple attachments?
	if(is_array($attachments))
	{
		//print_r($attachments);
		foreach ($attachments as $files)
		{
			//print_r($files);
			$data = base64_decode($files['filedata']);
			$name = $files['filename'];
			//write the file to the users directory
			echo "wrote file $name to filemanager:" . $this->objConfig->getContentBasepath() . "/" . $name . "<br />";
			$fp = @fopen ($this->objConfig->getContentBasepath() . "/" . $name, 'wb');
				  @fwrite ($fp, $data);
				  @fclose($fp);
			//echo $this->objFiles->uploadFile($this->objConfig->getContentBasepath() . $name);
		}
	}
}
?>