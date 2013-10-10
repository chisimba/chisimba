<?php
	$this->loadClass('icecast2');
	$icecast2 = new parse_icecast_info();
	$info = $icecast2->iceinfo('fsiu.uwc.ac.za','8000','admin','hello');
	//print_r($info);
	//echo '<br/>';
	echo "<table>";
	echo "<tr><td>Mount</td><td>{$info[0]->mount}</td></tr>";
	echo "<tr><td>Channels</td><td>{$info[0]->channels}</td></tr>";
	echo "<tr><td>Listeners</td><td>{$info[0]->listeners}</td></tr>";
	echo "<tr><td>ListenURL</td><td>{$info[0]->listenurl}</td></tr>";
	echo "<tr><td>Public</td><td>{$info[0]->public}</td></tr>";
	echo "<tr><td>Sample Rate</td><td>{$info[0]->samplerate}</td></tr>";
	echo "</table>";
	$this->loadClass('iframe','htmlelements');
	$iframe = new iframe();
	$iframe->src = 'http://fsiu.uwc.ac.za:8000/';
	echo $iframe->show();
?>