<?php
//////////////////////////////////////////////////////////////
///   phPie() by James Heinrich <info@silisoftware.com>     //
//        available at http://www.silisoftware.com         ///
//////////////////////////////////////////////////////////////
///        This code is released under the GNU GPL:         //
//           http://www.gnu.org/copyleft/gpl.html          ///
//////////////////////////////////////////////////////////////

error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('phPie.class.php');

$phPie = new phPie();
foreach (@$_REQUEST as $key => $value) {
	switch ($key) {
		case 'data':
			if (is_array($value)) {
				// you can pass data to phPie via the GETstring as a text array:
				// echo '<img src="phPie.php?data[male]=15&data[female]=25">';
				$phPie->$key = $value;
			} else {
				// you can pass data to this file via a serialized array:
				// $data = array('male'=>15; 'female'=>25);
				// echo '<img src="phPie.php?data='.urlencode(serialize($data)).'">';
				if (get_magic_quotes_gpc()) {
					$phPie->$key = unserialize(stripslashes($_REQUEST['data']));
				} else {
					$phPie->$key = unserialize($_REQUEST['data']);
				}
			}
			break;

		case 'width':
		case 'height':
		case 'CenterX':
		case 'CenterY':
		case 'DiameterX':
		case 'DiameterY':
		case 'MinDisplayPercent':
		case 'FontNumber':
		case 'LineColor':
		case 'BackgroundColor':
		case 'SaveFilename':
		case 'StartAngle':
			$phPie->$key = $value;
			break;

		case 'DisplayColors':
			if (is_array($value)) {
				$phPie->$key = $value;
			} else {
				die('DisplayColors is not an array');
			}
			break;

		case 'Legend':
		case 'LegendOnSlices':
		case 'SortData':
			$phPie->$key = (bool) $value;
			break;

		default:
			break;
	}
}
if (($phPie->width > 8192) || ($phPie->height > 8192) || ($phPie->width <= 0) || ($phPie->height <= 0)) {
	die('Image size limited to between 1x1 and 8192x8192 for memory reasons');
}
$phPie->DisplayPieChart();

?>