<?php
	require_once 'func.php';
	require_once 'db.php';
	session_start();
	if($_SESSION['query'] == "") {
		$_SESSION['query'] = "SELECT * FROM test ORDER BY dateAndTime DESC";
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Tempus</title>
	<script type="text/javascript" src="js/masonry.min.js"></script>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.lazyload.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.2.0/imagesloaded.pkgd.min.js"></script>
	<link rel="stylesheet" href="style_main.css">
</head>
<body>
	<div class="preloader"></div>
	<div class="button"><span>Источники</span></div>
	<div class="filter">
		<form class="form" action="" method="POST">
			<div class="checkbox-box-box">
				<?php
					//вывод чекбоксов
					$query = 'SELECT name FROM articlesSources';
					$queryResult = mysqli_query($db, $query);
					$numOfRows = mysqli_num_rows($queryResult);
					for($i = 0; $i < $numOfRows; $i++) {
						echo '<div class="checkbox-box"><input class="checkbox" type="checkbox" name="checkbox' . $i . '"' . /*$_SESSION['checked'][$i]*/ '' . '> <span class="nameSource">' . mysqli_fetch_row($queryResult)[0] . '</span></div>';
					}
				?>
			</div>
			<input class="submit" type="submit" name="submit" value="Применить">
		</form>
	</div>

	<script type="text/javascript">
		let button = document.querySelector('.button');
		let filter = document.querySelector('.filter');
		button.addEventListener('click', function () {
			filter.style.display = "block";
		});
	</script>

	<?php
		//обработчик кнопки
		if(isset($_POST['submit'])) {
			//echo '<script type="text/javascript">var preloader = document.querySelector(".preloader");preloader.style.display = "block";</script>';
			$query = 'SELECT name FROM articlesSources';
			$queryResult = mysqli_query($db, $query);
			$numOfRows = mysqli_num_rows($queryResult);
			$_SESSION['query'] = "SELECT * FROM test ";
			//формирование запроса с фильтрами для сесии
			for($i = 0; $i < $numOfRows; $i++) {
				$srcName = mysqli_fetch_row($queryResult)[0];
				if(isset($_POST['checkbox'.$i]) && stristr($_SESSION['query'], "WHERE") === FALSE) {
					$_SESSION['query'] .= "WHERE srcName='" . $srcName . "'";
					//$_SESSION['checked'][$i] = "checked";
				} elseif (isset($_POST['checkbox'.$i])) {
					$_SESSION['query'] .= "OR srcName='" . $srcName . "'";
					//$_SESSION['checked'][$i] = "checked";
				}
				else {
					//$_SESSION['checked'][$i] = "";
				}
			}
			$_SESSION['query'] .= ' ORDER BY dateAndTime DESC';
			//echo '<meta http-equiv="refresh" content="0">';
		}
	?>

	<header class="header">
		<div class="uzor-near uzor-left"><span>Агрегатор новостей</span></div>
		<div class="uzor"><img class="uzor-img" src="res/uzor.png" alt=""></div>
		<div class="uzor-near uzor-right"><span>www.ssilkanasait.ru</span></div>
		<hr class="hr-4" size="4">
		<h1 class="main-title">Tempus</h1>
		<hr class="hr-8" size="8">
		<hr class="hr-4" size="4">
		<div class="description-title"><span class="description-title-top">Самые свежие новости</span><br><span class="description-title-bottom">за последнее время</span></div>
		<hr class="hr-4" size="4">
		<hr class="hr-8" size="8">
		<div class="date">
			<script type="text/javascript" src="js/printDate.js"></script>
		</div>
		<div class="fraza">
			<script type="text/javascript" src="js/holidays.js"></script>
		</div>
		<hr class="hr-4" size="4">
	</header>

	<section class="section">
		<div class="container">
		    <div class="gutter-sizer"></div>
			<?php
				//вывод статей
				$query = $_SESSION['query'].' LIMIT 20';
				$queryResult = mysqli_query($db, $query);
				$numOfRows = mysqli_num_rows($queryResult);
				for ($i = 0; $i < $numOfRows; $i++) {
			        $row = mysqli_fetch_row($queryResult);
			        echo '<div class="article">';
			        if($row[2] != '')
			        	echo '<img class="article-img" src="'.$row[2].'"><br>'.'<div class="article-content"><a class="article-href" target="_blank" href="'.$row[4].'"><h2 class="article-title">'.$row[1].'</h2></a><br>'.'<div class="article-text">'.$row[3].'</div><br><div class="article-date">'.$row[0].'</div></div>';
			        else
			        	echo '<div class="article-content"><a class="article-href" target="_blank" href="'.$row[4].'"><h2 class="article-title">'.$row[1].'</h2></a><br><div class="article-text">'.$row[3].'</div><br><div class="article-date">'.$row[0].'</div></div>';
			        echo '</div>';
			    }
			?>
		</div>
		<button class="more">Показать больше</button>
	</section>
	
	<script type="text/javascript">
		var container = document.querySelector('.container');
		var preloader = document.querySelector('.preloader');
		var more = document.querySelector('.more');
		preloader.style.display = "block";
		container.style.display = "none";
		//more.style.display = "none";
		$('.container').imagesLoaded(function() {
		//$('document').ready(function() {
			preloader.style.display = "none";
			container.style.display = "block";
			//more.style.display = "block";
			var msnry = new Masonry(container, {
			  itemSelector: '.article',
			  gutter: '.gutter-sizer',
			});
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			var startFrom = 20; // позиция с которой начинается вывод данных
		    $('.more').click(function() {
		        $.ajax({
		            url: 'more.php', // путь к ajax-обработчику
		            method: 'POST',
		            data: {
		                "start" : startFrom
		            },
		            success: function(data) {
					    //var container = document.querySelector('.container');
						var preloader = document.querySelector('.preloader');
						var more = document.querySelector('.more');
						preloader.style.display = "block";
						var scrollY = window.scrollY;
						//more.style.display = "none";
						$('.container').append(data);
						$('.container').imagesLoaded(function() {
							preloader.style.display = "none";
							//more.style.display = "block";
							var msnry = new Masonry(container, {
								itemSelector: '.article',
								gutter: '.gutter-sizer',
							});
							window.scrollTo(0, scrollY);
						});
						startFrom += 20;
					}
		        });
		    });
		});
	</script>
</body>
</html>

<!--
<script type="text/javascript">
		var container = document.querySelector('.container');
		var preloader = document.querySelector('.preloader');
		$(window).on('load', function() {
			preloader.style.display = "none";
			container.style.display = "block";
			var msnry = new Masonry(container, {
			  itemSelector: '.article',
			});
		});
	</script>
-->