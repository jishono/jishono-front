</div>
<footer>
  　<p>&copy; jisho@no. 2020.</p>
</footer>

<!-- 検索候補表示方法の参考元：http://www.enjoyxstudy.com/javascript/suggest/ -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<script src="suggest.js"></script>
<script src="./wordlist"></script>
<script>
  // windowのonloadイベントでSuggestを生成
  // (listは、list.js内で定義している)
  var start = function() {
    new Suggest.Local("text", "suggest", list, {
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

</html>
