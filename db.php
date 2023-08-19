<?php
    function db_connect($host, $user, $password, $database) {
		$link = mysqli_connect($host, $user, $password, $database);	//подключение бд
		if(mysqli_errno($link))	//чек ошибки
			echo "Ошибка " . mysqli_error($link);
		else
			return $link;
	}
	
	$db = db_connect('localhost', '***', '***', '***');
?>