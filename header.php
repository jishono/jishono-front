<!DOCTYPE html>
<html>
<head>
	<meta charset = “UTF-8”>
	<title>ノルウェー語・日本語辞書</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>	
	<h1>ノルウェー語・日本語辞書</h1>
<div class="stitch">
<p>検索ワード (半角英数字)</p>
		<form action = "search.php" method = "GET">
			<input id="text" type="text" name="searchword" pattern="^[0-9A-ZÆÅØa-zæåø]+$" title="半角英数字で入力して下さい。" value="<?php echo isset($searchword) ? $searchword : "" ?>" autocomplete="off" required>
			<div id="suggest" style="display:none;" tabindex="-1"></div>
			<input type = "submit" value = "検索">
		</form>
