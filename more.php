<?php
	include 'db.php';
	session_start();
	if (isset($_POST['start']) && is_numeric($_POST['start'])) {
		$start = $_POST['start'];
		$queryResult = mysqli_query($db, $_SESSION['query']." LIMIT {$start}, 20");
		$numOfRows = mysqli_num_rows($queryResult);
		for ($i = 0; $i < $numOfRows; $i++) {
			$row = mysqli_fetch_row($queryResult);
			$article = '<div class="article">';
			if($row[2] != '')
			    $article .= '<img class="article-img" src="'.$row[2].'"><br>'.'<div class="article-content"><a class="article-href" target="_blank" href="'.$row[4].'"><h2 class="article-title">'.$row[1].'</h2></a><br>'.'<div class="article-text">'.$row[3].'</div><br><div class="article-date">'.$row[0].'</div></div>';
			else
			    $article .= '<div class="article-content"><a class="article-href" target="_blank" href="'.$row[4].'"><h2 class="article-title">'.$row[1].'</h2></a><br><div class="article-text">'.$row[3].'</div><br><div class="article-date">'.$row[0].'</div></div>';
			$article .= '</div>';
			$articles .= $article;
		}
	}
	echo $articles;
?>