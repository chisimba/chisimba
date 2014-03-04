<?php
//////////////////////////////////////////////////////////////
///   phPie() by James Heinrich <info@silisoftware.com>     //
//        available at http://www.silisoftware.com         ///
//////////////////////////////////////////////////////////////
///        This code is released under the GNU GPL:         //
//           http://www.gnu.org/copyleft/gpl.html          ///
//////////////////////////////////////////////////////////////

require_once('phPie.class.php');

$phpie = new phPie;
$phpie->data = array();
$phpie->width= 200;
$phpie->height= 400;
srand(time());
for ($i = 1; $i < 10; $i++) {
	$phpie->AddItem('SampleData'.$i, rand(0, 1000));
}
$phpie->Legend         = TRUE;//(bool) @$_REQUEST['Legend'];
$phpie->LegendOnSlices = (bool) @$_REQUEST['LegendOnSlices'];
$phpie->DisplayPieChart();

?>