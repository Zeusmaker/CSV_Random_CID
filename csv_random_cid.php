<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="file" name="file">

<?php

require("dbconnect_mysqli.php");
require("functions.php");

if (isset($_GET["list_id_override"]))			{$list_id_override=$_GET["list_id_override"];}
	elseif (isset($_POST["list_id_override"]))	{$list_id_override=$_POST["list_id_override"];}
  
$list_id_override = preg_replace('/[^0-9]/','',$list_id_override);

$stmt="SELECT list_id, list_name FROM vicidial_lists ORDER BY list_id;";
$rslt=mysql_to_mysqli($stmt, $link);
$num_rows = mysqli_num_rows($rslt);

echo "<select name='list_id_override'>";
$count=0;
while ( $num_rows > $count ) {
	$row = mysqli_fetch_row($rslt);
	echo "<option value=\'$row[0]\'>$row[0] - $row[1]</option>\n";
	$count++;
}
echo "</select>";
echo "<input type='submit' name='btn_submit' value='Upload And Process' />";

$lines = array();
if (isset($_FILES['file'])) {
  $fh = fopen($_FILES['file']['tmp_name'], 'r+');
  while( ($row = fgetcsv($fh, 8192)) !== FALSE ) {
  	$lines[] = $row;
  }
  #var_dump($lines);
}

if (isset($list_id_override)) {
  $stmt = "SELECT lead_id FROM vicidial_list WHERE list_id=$list_id_override";
  $rslt = mysql_to_mysqli($stmt, $link);
  $num_rows = mysqli_num_rows($rslt);
  
  $count=0;
  echo "<br />";
  while ( $num_rows > $count ) {
  	$row = mysqli_fetch_row($rslt);
    shuffle($lines);
    $rand_cid = $lines[0];
	#echo $rand_cid[0];
  	echo "Updating security_phrase of lead: $row[0] with: $rand_cid[0]<br />";
    $stmt_update = "UPDATE vicidial_list SET security_phrase='$rand_cid[0]' WHERE lead_id=$row[0] LIMIT 1";
    $rslt_update = mysql_to_mysqli($stmt_update, $link);
  	$count++;
  }
}

?>
