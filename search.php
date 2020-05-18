<?
	// 半角英数チェック
	function is_alnum($text) {
		if (preg_match("/^[0-9A-ZÆÅØa-zæåø]+$/",$text)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// エスケープ処理
	function escape($s) {
		return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
	}

	include('header.php');

	$searchword=$_GET['searchword'];

	if(is_alnum($searchword)){
		$searchword=escape($searchword);

		require('app/init.php');

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

		if($stmt){
			//プレースホルダへ実際の値を設定する
			$stmt->bindValue(':allwild', '%'. $searchword. '%', PDO::PARAM_STR);			
			$stmt->bindValue(':same', $searchword, PDO::PARAM_STR);
			$stmt->bindValue(':rearwild', $searchword. '%', PDO::PARAM_STR);
			$stmt->bindValue(':frontwild', '%'. $searchword, PDO::PARAM_STR);

			if($stmt->execute()){
				//レコード件数取得
				$row_count = $stmt->rowCount();

				echo "<p>該当件数: ". $row_count. " 件 (空白の箇所は未登録か不使用)</p>";
				$word_count = 1;

				while($row = $stmt->fetch()){
					// 検索結果表示
					echo "<p>" . $word_count . ": " . $row['spell'] .(isset($row['pro']) ? " (発音: " . $row['pro'] . ")" : " (発音: 登録なし)"). "<br/>";
					//以下、テスト用
					//echo $row['con'];
					//echo $row['lemma_id'];

					// IDから得られる意味を取得
					$sql = "
					SELECT d.definisjon AS meaning
					FROM definisjon d
					WHERE 1=1
					AND d.lemma_id = (:lemma_id)";
		
					$meaning = $dbh->prepare($sql);
					//プレースホルダへ実際の値を設定する
					$meaning->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
					if(!$meaning->execute()){
						echo "SQLエラー<BR>" ;
						exit ;
					}

					$meaing_count = $meaning->rowCount();

					//意味が登録されていない場合
					if ($meaing_count == 0){
						echo "意味: (登録なし)</p>";
					}else{
						$meaning_count = 1;
						while($meaning_row = $meaning->fetch()){
							echo "意味" . $meaning_count  . ": " . $meaning_row['meaning'] . "</p>" ;
							$meaning_count++;
						}
					}

					$boy = null;
					// 活用がある場合
					if (!empty($row['con'])){

						$con_count = 1;

						// 該当する活用テーブルの名前を作成
						$boy = $row['con']."_boy";

						// 形容詞の場合
						if ($boy == 'adj_boy'){
							echo "品詞: 形容詞<br/>";
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
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}

							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>男性単数形: " . $con['m_en'] . "</li><li>女性単数形: ". $con['f_en'] . "</li><li>中性単数形: ". $con['n_en'] . "</li><li>既知単数形: ". $con['b_e'] . "</li><li>複数形: " . $con['fle'] . "</li><li>比較級: ". $con['sup_b'] . "</li><li>最上級: ". $con['sup'] . "</li><li>最上級既知形: " . $con['sup_b'] . "</li></ul>";
							$con_count++;
							}
						}
						// 形容動詞の場合
						if ($boy == 'adv_boy'){
							echo "品詞: 形容動詞<br/>";
							$sql = "
							SELECT pos AS pos 
							,positiv AS pos
							,komparativ AS kom
							,superlativ AS sup
							FROM adv_boy
							where lemma_id = (:lemma_id)";

							$con_boy = $dbh->prepare($sql);
							// プレースホルダへ実際の値を設定する
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}
							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>普通形: " . $con['pos'] . "</li><li>比較級: ". $con['kom'] . "</li><li>最上級: ". $con['sup'] . "</li></ul>";
							$con_count++;
							}
						}						
						// 名詞の場合	
						if ($boy == 'det_boy'){
							echo "品詞: 限定詞<br/>";
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
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}
							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>男性単数形: " . $con['m_en'] . "</li><li>女性単数形: ". $con['f_en'] . "</li><li>中性単数形: ". $con['n_en'] . "</li><li>既知単数形: ". $con['b_e'] . "</li><li>複数形: ". $con['fle'] . "</li></ul>";
							$con_count++;
							}
						}	
						// 代名詞の場合
						if ($boy == 'pron_boy'){
							echo "品詞: 代名詞<br/>";
							$sql = "
							SELECT pos AS pos 
							,subjektsform AS sub
							,objektsform AS obj
							FROM pron_boy
							where lemma_id = (:lemma_id)";

							$con_boy = $dbh->prepare($sql);
							// プレースホルダへ実際の値を設定する
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}
							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>主格: " . $con['sub'] . "</li><li>目的格: ". $con['obj'] . "</li></ul>";
							$con_count++;
							}
						}
						// 名詞の場合
						if ($boy == 'subst_boy'){
							echo "品詞: 名詞<br/>";
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
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}
							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>未知単数形: " . $con['ub_en'] . "</li><li>既知複数形: ". $con['be_en'] . "</li><li>未知複数形: ". $con['ub_fl'] . "</li><li>既知複数形: ". $con['be_fl'] . "</li></ul>";
							$con_count++;
							}
						}
						// 動詞の場合
						if ($boy == 'verb_boy'){
							echo "品詞: 動詞<br/>";
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
							$con_boy->bindValue(':lemma_id',$row['lemma_id'],PDO::PARAM_STR);
							if(!$con_boy->execute()){
								echo "SQLエラー<BR>" ;
								exit ;
							}
							while($con = $con_boy->fetch()){
							echo "活用パターン" . $con_count . ": " . $con['pos'] . "<ul><li>不定系: " . $con['inf'] . "</li><li>現在形: ". $con['pres'] . "</li><li>過去形: ". $con['pret'] . "</li><li>現在完了形: ". $con['pre_per'] . "</li><li>命令形: ". $con['imp'] . "</li><li>完了分詞男性・女性系: ". $con['per_m'] . "</li><li>完了分詞中性系: ". $con['per_n'] . "</li><li>完了分詞複数形: ". $con['per_f'] . "</li><li>現在分詞形: ". $con['par'] . "</li></ul>";
							$con_count++;
							}
						}	
					}
					// 次の検索結果レコードがある場合
					if ($word_count != $row_count){
						echo "<hr>";
					}
					$word_count++;
				}				
			}else{
				echo "SQLエラー<BR>" ;
				exit ;
			}
		}
		$dbh = null;
	}else{
		echo "<p>半角英数字で検索して下さい。</p>";
	}
	include('footer.php');
?>
