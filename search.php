<?php
// 半角英数チェック
function is_alnum($text)
{
	$pattern = "/^[0-9A-ZÆÅØa-zæåø\s\.]+$/";
	if (preg_match($pattern, $text)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

// エスケープ処理
function escape($s)
{
	return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

include('header.php');

$searchword = $_GET['searchword'];


function getWordType($boyning)
{
	switch ($boyning) {
		case "verb":
			return "動詞";
			break;
		case "adj":
			return "形容詞";
			break;
		case "preposisjon":
			return "前置詞";
			break;
		case "interjeksjon":
			return "感動詞";
			break;
		case "prefiks":
			return "接頭辞";
			break;
		case "adv":
			return "副詞";
			break;
		case "det":
			return "限定詞";
			break;
		case "subjunksjon":
			return "関係詞";
			break;
		case "pron":
			return "代名詞";
			break;
		case "konjunksjon":
			return "接続詞";
			break;
		case "subst":
			return "名詞";
			break;
	}
}

if (is_alnum($searchword)) {
	$searchword = escape($searchword);

	//フォームで送られてきた条件を元にSELECT文を作成
	//ID、綴り、活用、発音抽出。発音が登録されていなくも結果を表示するよう、LEFT　JOIN使用。
	$sql = "
		SELECT o.lemma_id AS lemma_id
		,o.oppslag AS spell
		,o.boy_tabell AS con
		,u.transkripsjon AS pro
		FROM oppslag o
		LEFT JOIN uttale u
		ON o.lemma_id = u.lemma_id
		WHERE 1=1
		AND o.lemma_id IN 
			(SELECT lemma_id FROM definisjon)
		AND o.oppslag LIKE (:allwild)
		ORDER BY
		CASE
			WHEN lower(spell) LIKE (:same) THEN 1
			WHEN lower(spell) LIKE (:rearwild) THEN 2
			WHEN lower(spell) LIKE (:frontwild) THEN 3
			ELSE 4
		END,
		spell";

	$stmt = $dbh->prepare($sql);

	if ($stmt) {
		//プレースホルダへ実際の値を設定する
		$stmt->bindValue(':allwild', '%' . $searchword . '%', PDO::PARAM_STR);
		$stmt->bindValue(':same', $searchword, PDO::PARAM_STR);
		$stmt->bindValue(':rearwild', $searchword . '%', PDO::PARAM_STR);
		$stmt->bindValue(':frontwild', '%' . $searchword, PDO::PARAM_STR);

		if ($stmt->execute()) {
			//レコード件数取得
			$row_count = $stmt->rowCount();

			$treff = $row_count > 0 ? "JA" : "NEI";
			WriteResultToFile($searchword, $treff);

			echo "
			<div class='row my-4' id='number'>
				<div class='col text-center'>該当件数: " . $row_count . "件</div>
			</div>";
			echo "<div id='result-container'>";
			$word_count = 1;

			while ($row = $stmt->fetch()) {
				// 検索結果表示
				echo "<div class='row my-2'>";
				echo "<div class='col-lg-2'></div>";
				echo "<div class='col'>";
				echo "<div class='card box-shadow'>";

				$uttale = isset($row['pro']) ? "(発音: " . $row['pro'] . ")" : "(発音: 登録なし)";
				$ord = $row['spell'];
				$ordtype = getWordType($row['con']);

				echo "<div class='result-top-container card-header'>";
				echo "<div class='row'>";
				echo "<div class='result-top-item col-md-4 text-md-center'><h3 class='m-0'>$ord</h3></div>";
				echo "<div class='result-top-item col-md-4 text-md-center'>$uttale</div>";
				echo "<div class='result-top-item col-md-4 text-md-center'>$ordtype</div>";


				echo "</div>";
				echo "</div>";
				echo "</div>";

				// IDから得られる意味を取得
				$sql = "
					SELECT d.definisjon AS meaning
					FROM definisjon d
					WHERE 1=1
					AND d.lemma_id = (:lemma_id)";

				$meaning = $dbh->prepare($sql);
				//プレースホルダへ実際の値を設定する
				$meaning->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
				if (!$meaning->execute()) {
					echo "SQLエラー<BR>";
					exit;
				}

				$meaning_count = $meaning->rowCount();
				echo "<div class='imi my-3 mx-md-4 mx-sm-1'>";
				//意味が登録されていない場合
				if ($meaning_count == 0) {
					echo "意味: (登録なし)";
				} else {
					$meaning_count = 1;
					while ($meaning_row = $meaning->fetch()) {
						echo "意味" . $meaning_count  . ": " . $meaning_row['meaning'] . "<br>";
						$meaning_count++;
					}
				}
				echo "</div>";

				$boy = null;
				// 活用がある場合
				if (!empty($row['con'])) {

					$con_count = 1;

					// 該当する活用テーブルの名前を作成
					$boy = $row['con'] . "_boy";


					$btnClass = "btn mb-2 btn-outline-primary";

					// 形容詞の場合 V
					if ($boy == 'adj_boy') {
						$sql = "
							SELECT pos AS pos 
							,m_entall AS m_en
							,f_entall AS f_en
							,n_entall AS n_en
							,bestemt_entall AS b_e
							,flertall AS fle
							,komparativ AS kom
							,superlativ AS sup
							,superlativ_bestemt AS sup_b
							FROM adj_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}

						while ($con = $con_boy->fetch()) {

							$generateId = $row['con'] . $ord . $con_count;

							echo "<button 
								class='$btnClass' 
								data-toggle='collapse' 
								data-target='#$generateId' 
								role='button' 
								aria-expands='false' 
								aria-controls='$generateId'>
								活用パターン" . $con_count . ": " . $con['pos'] . "
								</button>";

							echo "
								<div class='collapse' id='$generateId'>
								<table class='table table-sm table-bordered text-center'>
									<thead >
										<tr>
											<th colspan='3'>単数形</th>
											<th rowspan='2'>複数形</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<th>男性・女性</th>
											<th>中性</th>
											<th>既知形</th>
											<th></th>
										</tr>
										<tr>
											<td>" . $con['m_en'] . "</td>
											<td>" . $con['n_en'] . "</td>
											<td>" . $con['b_e'] . "</td>
											<td>" . $con['fle'] . "</td>
										</tr>
									</tbody>
								</table>
								<table class='table table-sm table-bordered text-center'>
									<tbody>
										<tr>
											<th>比較級</th>
											<th>最上級</th>
											<th>最上級既知形</th>
										</tr>
										<tr>
											<td>" . $con['sup_b'] . "</th>
											<td>" . $con['sup'] . "</th>
											<td>" . $con['sup_b'] . "</th>
										</tr>
									</tbody>
								</table>
								";
							echo "</div>";
							$con_count++;
						}
					}
					// 形容動詞の場合
					if ($boy == 'adv_boy') {
						$sql = "
							SELECT pos AS pos 
							,positiv AS pos
							,komparativ AS kom
							,superlativ AS sup
							FROM adv_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}
						while ($con = $con_boy->fetch()) {
							$generateId = $row['con'] . $ord . $con_count;
							echo "<button 
							class='$btnClass'
							data-toggle='collapse' 
							data-target='#$generateId' 
							role='button' 
							aria-expands='false' 
							aria-controls='$generateId'>
							活用パターン" . $con_count . ": " . $con['pos'] . "
							</button>";

							echo "
							<div class='collapse' id='$ord$con_count'>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th>普通形</th>
										<th>比較級</th>
										<th>最上級</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $con['pos'] . "</td>
										<td>" . $con['kom'] . "</td>
										<td>" . $con['sup'] . "</td>
									</tr>
								</tbody>
							</table>
							";
							echo "</div>";

							$con_count++;
						}
					}
					// 名詞の場合	
					if ($boy == 'det_boy') {
						$sql = "
							SELECT pos AS pos 
							,m_entall AS m_en
							,f_entall AS f_en
							,n_entall AS n_en
							,bestemt_entall AS b_e
							,flertall AS fle
							FROM det_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}
						while ($con = $con_boy->fetch()) {
							$generateId = $row['con'] . $ord . $con_count;
							echo "<button 
							class='$btnClass'
							data-toggle='collapse' 
							data-target='#$generateId' 
							role='button' 
							aria-expands='false' 
							aria-controls='$generateId'>
							活用パターン" . $con_count . ": " . $con['pos'] . "
							</button>";

							echo "
							<div class='collapse' id='$ord$con_count'>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th>男性単数形</th>
										<th>女性単数形</th>
										<th>中性単数形</th>
										<th>既知形</th>
										<th>複数形</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $con['m_en'] . "</td>
										<td>" . $con['f_en'] . "</td>
										<td>" . $con['n_en'] . "</td>
										<td>" . $con['b_e'] . "</td>
										<td>" . $con['fle'] . "</td>
									</tr>
								</tbody>
							</table>
							";
							$con_count++;
						}
					}
					// 代名詞の場合
					if ($boy == 'pron_boy') {
						$sql = "
							SELECT pos AS pos 
							,subjektsform AS sub
							,objektsform AS obj
							FROM pron_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}

						while ($con = $con_boy->fetch()) {
							$generateId = $row['con'] . $ord . $con_count;
							echo "<button 
							class='$btnClass'
							data-toggle='collapse' 
							data-target='#$generateId' 
							role='button' 
							aria-expands='false' 
							aria-controls='$generateId'>
							活用パターン" . $con_count . ": " . $con['pos'] . "
							</button>";

							echo "
							<div class='collapse' id='$ord$con_count'>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th>主格</th>
										<th>目的格</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $con['sub'] . "</td>
										<td>" . $con['obj'] . "</td>
									</tr>
								</tbody>
							</table>
							";
							$con_count++;
						}
					}
					// 名詞の場合
					if ($boy == 'subst_boy') {
						$sql = "
							SELECT pos AS pos 
							,ubestemt_entall AS ub_en
							,bestemt_entall AS be_en
							,ubestemt_flertall AS ub_fl
							,bestemt_flertall AS be_fl
							FROM subst_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}
						while ($con = $con_boy->fetch()) {
							$generateId = $row['con'] . $ord . $con_count;
							echo "<button 
							class='$btnClass'
							data-toggle='collapse' 
							data-target='#$generateId' 
							role='button' 
							aria-expands='false' 
							aria-controls='$generateId'>
							活用パターン" . $con_count . ": " . $con['pos'] . "
							</button>";

							echo "
							<div class='collapse' id='$generateId'>
							<table class='table table-sm table-bordered text-center'>
							<thead>
								<tr>
									<th>未知単数形</th>
									<th>既知単数形</th>
									<th>未知複数形</th>
									<th>既知複数形</th>
								</tr>
							</thead>
							<tbody>
									<tr>
										<td>" . $con['ub_en'] . "</td>
										<td>" . $con['be_en'] . "</td>
										<td>" . $con['ub_fl'] . "</td>
										<td>" . $con['be_fl'] . "</td>
									</tr>
								</tbody>
							</table>
							";
							echo "</div>";
							$con_count++;
						}
					}
					// 動詞の場合
					if ($boy == 'verb_boy') {
						$sql = "
							SELECT pos AS pos 
							,infinitiv AS inf
							,presens AS pres
						    ,preteritum AS pret
						    ,presens_perfektum AS pre_per
						    ,imperativ AS imp
						    ,perf_part_mf AS per_m
						    ,perf_part_n AS per_n
						    ,perf_part_bestemt AS per_b
						    ,perf_part_flertall AS per_f
						    ,presens_partisipp AS par
				 			FROM verb_boy
							where lemma_id = (:lemma_id)";

						$con_boy = $dbh->prepare($sql);
						// プレースホルダへ実際の値を設定する
						$con_boy->bindValue(':lemma_id', $row['lemma_id'], PDO::PARAM_STR);
						if (!$con_boy->execute()) {
							echo "SQLエラー<BR>";
							exit;
						}
						while ($con = $con_boy->fetch()) {
							$generateId = $row['con'] . $ord . $con_count;
							echo "<button 
							class='$btnClass'
							data-toggle='collapse' 
							data-target='#$generateId' 
							role='button' 
							aria-expands='false' 
							aria-controls='$generateId'>
							活用パターン" . $con_count . ": " . $con['pos'] . "
							</button>";

							echo "
							<div class='collapse' id='$generateId'>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th>不定形</th>
										<th>現在形</th>
										<th>過去形</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $con['inf'] . "</td>
										<td>" . $con['pres'] . "</td>
										<td>" . $con['pret'] . "</td>
									</tr>
								</tbody>
							</table>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th>現在完了形</th>
										<th>現在分詞形</th>
										<th>命令形</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $con['pre_per'] . "</td>
										<td>" . $con['par'] . "</td>
										<td>" . $con['imp'] . "</td>
									</tr>
								</tbody>
							</table>
							<table class='table table-sm table-bordered text-center'>
								<thead>
									<tr>
										<th colspan='4'>完了分詞形</th>
									</tr>
								</thead>
								<tbody>
								<tr>
										<th>男性・女性形</th>
										<th>中性形</th>
										<th>既知形</th>
										<th>複数形</th>
									</tr>
									<tr>
										<td>" . $con['per_m'] . "</td>
										<td>" . $con['per_n'] . "</td>
										<td>" . $con['per_b'] . "</td>
										<td>" . $con['per_f'] . "</td>
									</tr>
								</tbody>
							</table>
							";
							echo "</div>";
							$con_count++;
						}
					}
				}
				echo "</div>";
				echo "<div class='col-lg-2'></div>";
				// 次の検索結果レコードがある場合
				if ($word_count != $row_count) {

					echo "</div>";
				}
				$word_count++;
			}
		} else {
			echo "SQLエラー<BR>";
			exit;
		}
	}
} else {
	echo "<div class='alert alert-danger text-center my-3'>半角英数字で検索して下さい。</div>";
}
echo "</div>";
echo "</div>";
include('footer.php');

function WriteResultToFile($word, $treff)
{
	$date = date("d.m.Y H:i");
	$separator = "\t";
	$line = $word . $separator . $treff . $separator . $date . PHP_EOL;
	file_put_contents("treff.txt", $line, FILE_APPEND);
}
