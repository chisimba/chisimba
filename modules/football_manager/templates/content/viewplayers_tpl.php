<?php
$info=$this->objDb->getInfo();

$str = "<table>
	<tr>
	<th>First Name</th><th>Surname</th><th>Age</th><th>Position</th><th>Transfer Fee</th><th>Other Info</th><th>Transfer Status</th>
	</tr>
	";
foreach ($info as $data)
{
	$fn = $data['firstname'];
	$ln = $data['lastname'];
	$age = $data['age'];
	$pos = $data['position'];
	$fee = $data['transferfee'];
	$other = $data['otherinfo'];
	$status = $data['status'];
	if ($status)
	{
	   $status="Listed";
	}
	else
	{
	   $status="Not Listed";
	}
	$str = $str."<tr><td>".$fn."</td><td>".$ln."</td><td>".$age."</td><td>".$pos."</td><td>".$fee."</td><td>".$other."</td><td>".$status."</td><td></tr>";
}
$str = $str."</table>";

$btnMenu = new button('menu','Menu');
$btnMenu->setOnClick("window.location='".$this->uri(NULL)."';");

echo "<h1>".$title."</h1>";
echo $str;
echo "<br>".$btnMenu->show();
?>
