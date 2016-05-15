<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="/js/judge_parse.js"></script>
<style>
.text {
    white-space: pre;
    font-family: 細明體;
}

.text-summary {
background: yellow;
}

.line {
    width: 100px;
    text-align: right;
    padding-right: 20px;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.css">
</head>
<body>
<div class="container">
    <form id="summary-form">
        <textarea id="summary-data"></textarea>
        <button type="submit">OK</button>
    </form>
        
    <table id="case-table" >
    </table>
</div>
<script>
var url = <?= json_encode($url) ?> ;
var proxy_url = 'http://proxy.g0v.ronny.tw/proxy.php?url=' + encodeURIComponent(url);
$.get(proxy_url, function(text){
    $('#case-table').html('');
    var case_data = parse_from_print_page(text, url);
    lines = case_data['裁判全文'].split("\n");
    for (var i = 0; i < lines.length; i ++) {
        var tr_dom = $('<tr></tr>').attr('id', 'tr-line-' + i);
        tr_dom.append($('<td></td>').text(i + 1).addClass('line'));
        tr_dom.append($('<td></td>'));
        tr_dom.append($('<td></td>').text(lines[i]).addClass('text'));
        $('#case-table').append(tr_dom);
    }

    });

$('#summary-form').submit(function(e){
    e.preventDefault();
    summaries = JSON.parse($('#summary-data').val());
        for (var i = 0; i < summaries.length; i ++) {
            summary = summaries[i];
            var tr_dom = $('<tr></tr>').attr('id', 'tr-summary-' + i);
            tr_dom.append($('<td></td>').text((summary.line_start + 1) + ' ~ ' + (summary.line_end + 1)).addClass('line'));
            tr_dom.append($('<td></td>').text('+').addClass('btn-open').data('summary-id', i));
            tr_dom.append($('<td></td>').text(summary.title + "\n" + summary.summary).addClass('text-summary'));
            tr_dom.insertBefore($('#tr-line-' + summary.line_start));

            for (var j = summary.line_start; j <= summary.line_end; j ++) {
                $('#tr-line-' + j + ' td:eq(1)').text('-').addClass('btn-close').data('summary-id', i);
                $('#tr-line-' + j).hide().addClass('tr-summary-' + i);
            }
        }
});

$('#case-table').on('click', '.btn-open', function(e){
   var summary_id = $(this).data('summary-id');
   $('.tr-summary-' + summary_id).show();
   $('#tr-summary-' + summary_id).hide();
});
$('#case-table').on('click', '.btn-close', function(e){
   var summary_id = $(this).data('summary-id');
   $('.tr-summary-' + summary_id).hide();
   $('#tr-summary-' + summary_id).show();
});
</script>
</body>
</html>
