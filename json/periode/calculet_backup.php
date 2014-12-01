<?php
ob_start();
session_start();
$_SESSION['message'] = '';
include '../config/dbconnect.php';
$query1 = mysql_query("SELECT * from stockbin where periode ='" . $_SESSION['per'] . "'");
while($row = mysql_fetch_array($query1)){
	$id =  $row['id'];
	$fisik = $row['stocksap'];
	$opnam = $row['countedqty'];
	if ($fisik == $opnam)
	{
		$query = "UPDATE stockbin SET deff=(stocksap) WHERE id=" . $id;
		$rs = mysql_query($query);    

	}elseif ($opnam == 0)
	{
		$query = "UPDATE stockbin SET deff=((stocksap) - ((stocksap * 2))) WHERE id=" . $id;
		$rs = mysql_query($query);
	}
	elseif ($fisik < $opnam) 
	{
		$query = "UPDATE stockbin SET deff=((stocksap) - (countedqty)) WHERE id=" . $id;
		$rs = mysql_query($query);    

	}elseif ($fisik > $opnam)
	{
		$query = "UPDATE stockbin SET deff=((stocksap) - (countedqty)) WHERE id=" . $id;
		$rs = mysql_query($query);    

	}
}
	echo $fisik;
	echo $opnam;
//`header('Location: gondola.php');
//mysql_close();
?>
