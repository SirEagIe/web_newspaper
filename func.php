<?php
    //Функция, экранирующая двойные и одинарные кавычки
	function ecran($str) {
		$str = str_replace("'", "\'",  $str);
		$str = str_replace('"', '\"',  $str);
		$str = strip_tags($str, '<p>');
		return $str;
	}

	// function printArticles($db, $numOfArticles) {
	// 	$queryResult = mysqli_query($db, $_SESSION['query'].$numOfArticles.', '.($numOfArticles + 20));
	// 	$numOfRows = mysqli_num_rows($queryResult);
	// 	for ($i = 0; $i < $numOfRows; $i++) {
	//         $row = mysqli_fetch_row($queryResult);
	//         if($row[2] != '')
	//         	echo "<td>$row[0]</td><br>".$row[1].'<br>'.'<img style="max-width: 420px; width: 420px; " src="'.$row[2].'"><br>'.$row[3].'<br>'.'<a target="_blank" href="'.$row[4].'">Источник</a>'.'<hr>';
	//         else
	//         	echo "<td>$row[0]</td><br>".$row[1].'<br>'.$row[3].'<br>'.'<a target="_blank" href="'.$row[4].'">Источник</a>'.'<hr>';
	//     }
	// }
?>