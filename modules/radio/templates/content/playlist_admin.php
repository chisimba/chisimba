<?php

//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

//initiate objects
$this->loadClass('label', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlarea', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
//Main table
$table =$this->newObject('htmltable', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');

//getParam
$mode = $this->getParam('mode',NULL);
$playlist_n = $this->getParam('playlist_n',null);

if($mode != "edit")
{
	if($mode == "del"){$this->playlist->del_playlist($_SESSION['station'],$playlist_n);}
	$data = explode("&",$this->playlist->get($_SESSION['station']));
	$stop = "0";
	$teller = "0";

	$table->startRow();
	$table->addCell($this->objLanguage->languageText('mod_radio_playlistName','radio'),null,null,null,null,'');
	$table->addCell($this->objLanguage->languageText('mod_radio_Options','radio'),null,null,null,null,'');
	$table->endRow();

	$rowcount = 0;

	while($stop == "0")
	{
		//Set odd even row colour
		$oddOrEven = ($rowcount == 0) ? "even" : "odd";
		$playlist_n = $data[$teller];
		if($playlist_n != "")
		{
			//create Edit and Delete icons
			//Create delete icon
			$delArray = array('action' =>'playlist','mode'=>'del', 'confirm'=>'yes', 'playlist_n'=>$playlist_n);
			$deletephrase = $this->objLanguage->languageText('mod_radio_admin_delete', 'radio');
			$delIcon = $objIcon->getDeleteIconWithConfirm($playlist_n, $delArray,'radio',$deletephrase);

			//edit icon
			$editIcon = $objIcon->getEditIcon($this->uri(array('action' =>'playlist','mode'=>'edit','playlist_n' => $playlist_n)));
			$tableRow = array();
			$tableRow[] = $playlist_n;
			$tableRow[] = '<nobr>'.$editIcon.$delIcon.'</nobr>';
			$table->addRow($tableRow, $oddOrEven);

			$rowcount = ($rowcount == 0) ? 1 : 0;



		}else{ $stop ="yes";}
		$teller++;
	}
	echo $table->show();
}
if($mode == "edit")
{
	$playlist_name = $this->getParam('playlist_n');

	if($_SESSION['station'] != "" or $station != ""){

		if( $_SESSION['station'] == ""){$station_n = $station;}else{$station_n = $_SESSION['station'];}
	}else{$station_n = $this->getParam('station');}

	if($station_n != "" && $playlist_name != ""){
		$req = $this->getParam('req',null);
		$up = $this->getParam('up',null);
		$down = $this->getParam('down',null);
		$max_q = $this->getParam('max',null);
		$nr = $this->getParam('nr',null);
		$del = $this->getParam('req',null);

		if($req == "up")
		{

			$this->playlist->move_songs($station,$playlist_name,$this->playlist->build_list($up,$nr,$max_q));
		}
		if($req == "down")
		{
			$this->playlist->move_songs($station,$playlist_name,$this->playlist->build_list($down,$nr,$max_q));
		}
		if($del == "del"){$this->playlist->del_songs($nr,$playlist_name,$station_n);}
		$list_info = explode(";", $this->playlist->get_playlist_info($station_n,$playlist_name));
		$max = count($list_info);
		$max = $max -2;
		$stop = "0";
		$teller = "0";
		$table->startRow();
		$table->addCell($this->objLanguage->languageText('mod_radio_NR','radio'),null,null,null,null,'');
		$table->addCell($this->objLanguage->languageText('mod_radio_Song','radio'),null,null,null,null,'');
		$table->addCell($this->objLanguage->languageText('mod_radio_Bitrate','radio'),null,null,null,null,'');
		$table->addCell($this->objLanguage->languageText('mod_radio_Time','radio'),null,null,null,null,'');
		$table->addCell($this->objLanguage->languageText('mod_radio_Options','radio'),null,null,null,null,'');
		$table->endRow();

		$rowcount = 0;

		while($stop == "0")
		{
			//Set odd even row colour
			$oddOrEven = ($rowcount == 0) ? "even" : "odd";
			$out = explode("&",$list_info[$teller]);
			$song_name = isset($out[0]) ? $out[0] : 0;
			$bitrate = isset($out[1]) ? $out[1] : 0;
			$start = isset($out[2]) ? $out[2] : 0;
			$end = (isset($out[3]) ? $out[3] : '-');
			$song_name = substr($song_name, 0, 20);
			$next = $teller + 1;
			$back = $teller -1;
			$tableRow = array();

			if($next >= $max+1){$do_next = false;}else{$do_next = true;}
			if($back <= "0" && $back != "0"){$do_back = false;}else{$do_back = true;}
			if($song_name != "")
			{
				if($end == ""){
					$tableRow[] = '['.$teller.']';
					$tableRow[] = $song_name;
					$tableRow[] = $bitrate;
					$tableRow[] = $start;

					if($do_back){

						$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=up&nr=$teller&up=$back&max=$max>UP</a> | ";
					}
					if($do_next){
						$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=down&nr=$teller&down=$next&max=$max>DOWN</a> | ";
					}

					$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=del&nr=$teller>Delete</a></td></tr>";
					$table->addRow($tableRow, $oddOrEven);

					$rowcount = ($rowcount == 0) ? 1 : 0;
				}else{
					$tableRow[] = '['.$teller.']';
					$tableRow[] = $song_name;
					$tableRow[] = $bitrate;
					$tableRow[] = "$start / $end";

					if($do_back){
						$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=up&nr=$teller&up=$back&max=$max>UP</a> | ";
					}
					if($do_next){
						$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=down&nr=$teller&down=$next&max=$max>DOWN</a> | ";
					}
					$tableRow[] = "<a href=?module=radio&action=playlist&mode=edit&playlist_n=$playlist_name&req=del&nr=$teller>Delete</a></td></tr>";
					$table->addRow($tableRow, $oddOrEven);

					$rowcount = ($rowcount == 0) ? 1 : 0;
				}
			}else{$stop = "yes";}
			$teller++;
		}
		echo $table->show();
		//$page_to = "?page=songlist";
	}
}

?>