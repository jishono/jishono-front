<?php
require('app/init.php');

$sql = "SELECT DISTINCT oppslag FROM oppslag WHERE lemma_id IN (SELECT lemma_id FROM definisjon)";

$sth = $dbh->prepare($sql);
$sth->execute();

$suggestionList = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset=“UTF-8”>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ノルウェー語・日本語辞書</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
	<div class='container-fluid'>
		<div class='row' id='header'>
			<div class='col'>
				<a href='/'>
					<img class='mx-auto d-block logo my-4' src='css/jisho_logo_1.png' />
				</a>
			</div>
		</div>
		<div class='row' id='searchbar'>
			<div class='col-lg'></div>
			<div class='col'>
				<form action="search.php" method="GET">
					<input class="input-lg form-control" placeholder='検索ワード (半角英数字)' id="text" type="text" name="searchword" pattern="^[0-9A-ZÆÅØa-zæåø\s\.\%]+$" title="半角英数字で入力して下さい。" value="<?php echo isset($searchword) ? $searchword : "" ?>" autocomplete="off" required>
					<div id="suggest" class='list-group' style="display:none;" tabindex="-1"></div>
				</form>
			</div>
			<div class='col-lg'></div>
		</div>