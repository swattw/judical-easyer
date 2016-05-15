<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="/js/text.css" rel="stylesheet" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="/js/judge_parse.js"></script>
<script src="/js/text_parse.js"></script>
</head>
<body>
<div id="output"></div>
<div id="submit-form">
  <label class="form-label">標題：<input id="title"></input></label>
  <label class="form-label">摘要：<input id="summary"></input></label>
  <button id="submit">送出</button>
</div>
<div id="json-box">
<textarea id="json-text"></textarea>
<div class="import"><button id="import-json">匯入</button></div>
</div>
<script>
var url = <?= json_encode($url) ?> ;
var proxy_url = 'http://proxy.g0v.ronny.tw/proxy.php?url=' + encodeURIComponent(url);
$.get(proxy_url, function(text){
        var result = parse_from_print_page(text, url);
        var input = JSON.stringify(result, true, 2);
        parseText(input, $('#output'));
});
</script>
</body>
</html>
