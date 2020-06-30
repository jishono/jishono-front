</div>
</div>
<footer>
  <div class='text-center my-4'>
    &copy; jisho.no 2020
    <br>
    <a href="https://github.com/jishono/japansk-norsk" target="_blank" class="badge badge-info">Github</a>
  </div> 　
</footer>

<!-- 検索候補表示方法の参考元：http://www.enjoyxstudy.com/javascript/suggest/ -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script src="suggest.js"></script>


<script>
  // windowのonloadイベントでSuggestを生成
  // (listは、list.js内で定義している)
  var suggestList = <?php echo json_encode($suggestionList); ?>;
  console.log(suggestList);
  var start = function() {
    new Suggest.Local("text", "suggest", suggestList, {
      dispMax: 0,
      dispAllKey: true,
      prefix: true,
      highlight: true
    });
  };
  window.addEventListener ?
    window.addEventListener('load', start, false) :
    window.attachEvent('onload', start);
</script>
</body>
<?php
$dbh = null;
?>

</html>