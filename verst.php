<?
	require_once 'db.php';
	$query = "SELECT * FROM test WHERE dateAndTime='2020-08-02 13:36:00' LIMIT 1";
	$res = mysqli_query($db, $query);
	$row = mysqli_fetch_row($res);

	$date = substr("2020-08-02 13:36:00", 17, 2);

	echo $date;


?>