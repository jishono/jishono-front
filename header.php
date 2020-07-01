<!DOCTYPE html>
<html>

<head>
	<meta charset=“UTF-8”>
	<meta name=viewport content="width=device-width,initial-scale=1">
	<link rel=stylesheet href=https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css
        integrity=sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh crossorigin=anonymous>
	<title>jisho.no ノルウェー語・日本語辞書</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body>
	<h1>jisho.no ノルウェー語・日本語辞書 (beta)</h1>
	<div class="stitch">
		<p>検索ワード (半角英数字)</p>
		<form action="search.php" method="GET">
			<input id="text" type="text" name="searchword" pattern="^[0-9A-ZÆÅØa-zæåø\s\.]+$" title="半角英数字で入力して下さい。" value="<?php echo isset($searchword) ? $searchword : "" ?>" autocomplete="off" required>
			<div id="suggest" style="display:none;" tabindex="-1"></div>
			<input type="submit" value="検索">
		</form>
