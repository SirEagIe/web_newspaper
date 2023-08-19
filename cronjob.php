<?php
	require_once 'phpQuery/phpQuery.php';
	require_once 'func.php';
	require_once 'db.php';
	
	//Парсер
	function parser($DOM, $db) {
		$file = file_get_contents($DOM[url]);
		$doc = phpQuery::newDocument('<meta charset="utf-8">' . $file);	//Установка кодировки
		//Парсинг всех статей со страницы
		foreach($doc->find($DOM[articlesClass]) as $article) {
			$article = pq($article);
			$articleUrl = $article->find($DOM[aClass])->attr('href');	//Поиск ссылки на статью
			//Исключаем статьи с других ресурсов, если таковые имеются
			if((stristr($articleUrl, 'http') === FALSE) || ($DOM[_url] == "")) {
				//Проверка на наличие статьи в БД
				$articleUrl = $DOM[_url] . $articleUrl;
				$query = "SELECT src FROM test WHERE src = '" . $articleUrl . "'";
				$queryResult = mysqli_query($db, $query);
				if(mysqli_errno($db))
					echo "Ошибка " . mysqli_error($db);
				if(mysqli_num_rows($queryResult) == 0) {
					//Если статьи нет в БД
					$articleFile = file_get_contents($articleUrl);
					if(stristr($DOM[name], 'Pikabu') !== FALSE)
					    $articleFile = iconv('windows-1251', 'utf-8', $articleFile);
					$articleDoc = phpQuery::newDocument('<meta charset="utf-8">' . $articleFile);
					//Поиск заголовка
					$title = $articleDoc->find($DOM[titleClass])->attr('content');
					if($title == "")
						$title = $articleDoc->find($DOM[titleClass])->text();
					if(strlen($title) >= 1000) {
					    //Регулировка длины заголовка
						$title = substr($title, 0, 1000);
						$title = rtrim($title, "!,.-");
                        $title = substr($title, 0, strrpos($title, ' '));
						$title .= '... ';
					}
					$title = ecran($title);	//Экранирование кавычек
					//Поиск описания
					$text = $articleDoc->find($DOM[description])->attr('content');
					if($text == "")
						$text = $articleDoc->find($DOM[description])->html();
					if(strlen($text) >= 1000) {
					    //Регулировка длины описания
						$text = substr($text, 0, 1000);
						$text = rtrim($text, "!,.-");
                        $text = substr($text, 0, strrpos($text, ' '));
						$text .= '... ';
					}
					$text = ecran($text);	//Экранирование кавычек
					//Поиск изображения
					$articleImg = $articleDoc->find($DOM[img])->attr('content');
					if($articleImg == "")
						$articleImg = $articleDoc->find($DOM[img])->attr('src');
					//Поиск даты
					$articleDate = $articleDoc->find($DOM[date])->attr('content');
					if($articleDate == "")
						$articleDate = $articleDoc->find($DOM[date])->attr('datetime');
					if($articleDate == "")
						$articleDate = $articleDoc->find($DOM[date])->text();
					if($articleDate == "")
					    continue;
					//Доп. проверки на повтор
					if(substr($articleDate, 17, 2) != "00") {
						//Если у даты публикации есть секунды, ищем дубли по времени и имени источника
						$query = "SELECT * FROM test WHERE dateAndTime='".$articleDate."' AND srcName='".$DOM[name]."'";
						$queryResult = mysqli_query($db, $query);
						//Фромируем запрос на обнавление статьи в БД в случае, если статья будет дублем
						$query = "UPDATE test SET dateAndTime='" . $articleDate . "', title='" . $title . "', img='" . $articleImg . "', text='" . $text . "', src='" . $articleUrl . "', srcName='" . $DOM[name]. "' WHERE dateAndTime='".$articleDate."' AND srcName='".$DOM[name]."'";
					} else {
						//Иначе по дате и картинке
						$query = "SELECT * FROM test WHERE dateAndTime='".$articleDate."' AND img='".$articleImg."'";
						$queryResult = mysqli_query($db, $query);
						//Фромируем запрос на обнавление статьи в БД в случае, если статья будет дублем
						$query = "UPDATE test SET dateAndTime='" . $articleDate . "', title='" . $title . "', img='" . $articleImg . "', text='" . $text . "', src='" . $articleUrl . "', srcName='" . $DOM[name]. "' WHERE dateAndTime='".$articleDate."' AND img='".$articleImg."'";
					}
					//Добавлени/обнавление статьти в БД
					if(mysqli_num_rows($queryResult) != 0) {
						//Если дубль, отправляем сформированный запрос на обнавление статьи в БД
						mysqli_query($db, $query);
						//отладка
						$query = "INSERT INTO _test VALUES ('" . $articleDate . "', '" . $title . "', '" . $articleImg . "', '" . $text . "', '" . $articleUrl . "', '" . $DOM[name] . "')";
						mysqli_query($db, $query);
					} else {
						//Запись статьи в БД
						$query = "INSERT INTO test VALUES ('" . $articleDate . "', '" . $title . "', '" . $articleImg . "', '" . $text . "', '" . $articleUrl . "', '" . $DOM[name] . "')";
						mysqli_query($db, $query);
					}
				}
			}
		}
	}

	$query = 'SELECT * FROM articlesSources';
	$queryResult = mysqli_query($db, $query);
	$numOfRows = mysqli_num_rows($queryResult);
	for($i = 0; $i < $numOfRows; $i++) {
		$row = mysqli_fetch_row($queryResult);
		$DOM['name'] = $row[0];
		$DOM['url'] = $row[1];
		$DOM['articlesClass'] = $row[2];
		$DOM['aClass'] = $row[3];
		$DOM['titleClass'] = $row[4];
		$DOM['_url'] = $row[5];
		$DOM['description'] = $row[6];
		$DOM['img'] = $row[7];
		$DOM['date'] = $row[8];
		parser($DOM, $db);
	}
?>