<?php
$proxy_url = getenv('PROXY_URL') ?: 'http://proxy.g0v.ronny.tw/';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>判決書分享小幫手r</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="navbar-header">
        <a class="navbar-brand" href="/">判決書分享工具</a>
        <ul class="navbar-nav nav">
            <li>
            <a href="https://github.com/swattw/judicial-easyer">GitHub</a>
            </li>
            <li>
            <a href="https://blog.swat.tw">Swattw</a>
        </ul>
    </div>
</nav>
<div class="container">

<form method="get" id="form">
    URL: <input type="text" size="100" id="url" placeholder="請貼上判決的友善列印頁網址，或者是判決書第一行也就是像「臺灣臺北地方法院刑事判決 100年度某字5566號」">
    <button type="submit">GET</button>
</form>
<div id="message"></div>
分享網址: <input type="text" readonly="readonly" id="share-url" size="90"><br>
短網址: <input type="text" readonly="readonly" id="short-url" size="90"><br>
Viewer網址: <input type="text" readonly="readonly" id="viewer-url" size="90"><br>
Editor網址: <input type="text" readonly="readonly" id="editor-url" size="90"><br>
<a href="#" id="full-version">維基百科 InfoBox 及 JSON 轉換版</a>
<div id="full-area" style="display:none">
<div id="wiki-area">
Wiki Infobox:
<textarea style="width:100%; height: 300px" id="wiki"></textarea>
</div>
Result:
<textarea style="width:100%; height: 500px" id="textarea"></textarea>
</div>
</div>

<font face="微軟正黑體"> 特別感謝 <a href="http://ronny.tw/">@ronnywang</a> 以ＢＳＤ授權方式，本人進行簡單重製，並使用以下授權方式： </f>
<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="創用 CC 授權條款" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">判決書小幫手</span>由<a xmlns:cc="http://creativecommons.org/ns#" href="https://blog.swat.tw" property="cc:attributionName" rel="cc:attributionURL">Swattw</a>製作，以<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">創用CC 姓名標示-相同方式分享 4.0 國際 授權條款</a>釋出。<br />此作品衍生自<a xmlns:dct="http://purl.org/dc/terms/" href="http://ronny.tw/" rel="dct:source">http://ronny.tw/</a>。<br />超出此條款範圍外的授權可於<a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/ronnywang/judicial-easyer" rel="cc:morePermissions">https://github.com/ronnywang/judicial-easyer</a>查閱。</font>
<script src="js/judge_parse.js"></script>
<script>
$('#full-version').click(function(e){
    e.preventDefault();
    $('#full-area').toggle();
});
$('#form').submit(function(e){
        e.preventDefault();
        var url = $('#url').val();
        var proxy_url = <?= json_encode($proxy_url) ?> + '/proxy.php?url=' + encodeURIComponent(url);

        if (url.match('/FJUD/FJUDQRY03_1.aspx')) { // 單一判決頁
            var type = 'FJUDQRY03_1';
        } else if (!url.match('^http')) {
            try {
                var result = parse_court(url, empty_result());
            } catch (s) {
                $('#message').text('字號無法判斷: ' + s);
                return;
            }
            var url = 'http://jirs.judicial.gov.tw/FJUD/FJUDQRY02_1.aspx?cw=1&v_court=' + encodeURIComponent(result['法院'].ID + ' ' + court[result['法院'].ID]) + '&v_sys=' + result['裁判種類'].ID + '&jud_year=' + result['裁判字號']['年'] + '&jud_case=' + encodeURIComponent(result['裁判字號']['字']) + '&jud_no=' + result['裁判字號']['號'] + '&jud_title=&keyword=&sdate=19110101&edate=99991231&searchkw=';
            $('#share-url').val(url);
            url = '/' + encodeURIComponent(result['法院'].ID) + '/' + result['裁判種類'].ID + '/' + result['裁判字號']['年'] + '/' + encodeURIComponent(result['裁判字號']['字']) + '/' + result['裁判字號']['號'];
            $('#short-url').val(url);
            $('#message').text('已產生可分享此案件連結，詳細資訊可入內透過友善列印取出');
            $('#textarea').text(JSON.stringify(result, true, 2));
            $('#wiki-area').hide();
            return;
        } else if (url.match('/FJUD/PrintFJUD03_0.aspx')) { // 友善列印頁
            var type = 'PrintFJUD03_0';
        } else if (url.match('/FJUD/HISTORYSELF.aspx')) { // 歷審頁
            var type = 'HISTORYSELF';
        } else {
            $('#message').text('目前只允許處理單一判決頁、友善列印頁或是歷審案件查詢頁');
            return;
        }
        
        $('#message').text('讀取中...');
        $.get(proxy_url, function(text){
            var result;
            $('#message').text('');
            try {
                if ('FJUDQRY03_1' == type) {
                        result = parse_from_page(text);
                } else if ('PrintFJUD03_0' == type) {
                    result = parse_from_print_page(text, url);
                } else if ('HISTORYSELF' == type) {
                    result = parse_history(text);
                    $('#textarea').text(JSON.stringify(result, true, 2));
                    $('#wiki-area').hide();
                    return;
                }
            } catch (s) {
                $('#message').text('字號無法判斷: ' + s);
                throw s;
                return;
            }
            $('#textarea').text(JSON.stringify(result, true, 2));
            $('#wiki-area').show();
            $('#wiki').text(to_wiki_infobox(result));
            $('#share-url').val(result["連結"]["列表"]);
            $('#short-url').val(result["連結"]["列表短網址"]);
            $('#viewer-url').val(result["連結"]["列表短網址"] + '/' + result['裁判日期'].SOURCE + '/' + result['jcheck'] + '/viewer');
            $('#editor-url').val(result["連結"]["列表短網址"] + '/' + result['裁判日期'].SOURCE + '/' + result['jcheck'] + '/editor');
        });
});
</script>

</body>
</html>
