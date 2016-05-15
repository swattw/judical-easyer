<?php
$courts = array(
    'TPC' => '司法院－刑事補償',
    'TPU' => '司法院－訴願決定',
    'TPJ' => '司法院職務法庭',
    'TPS' => '最高法院',
    'TPA' => '最高行政法院',
    'TPP' => '公務員懲戒委員會',
    'TPH' => '臺灣高等法院',
    'TPB' => '臺北高等行政法院',
    'TCB' => '臺中高等行政法院',
    'KSB' => '高雄高等行政法院',
    'IPC' => '智慧財產法院',
    'TCH' => '臺灣高等法院 臺中分院',
    'TNH' => '臺灣高等法院 臺南分院',
    'KSH' => '臺灣高等法院 高雄分院',
    'HLH' => '臺灣高等法院 花蓮分院',
    'TPD' => '臺灣臺北地方法院',
    'SLD' => '臺灣士林地方法院',
    'PCD' => '臺灣新北地方法院',
    'ILD' => '臺灣宜蘭地方法院',
    'KLD' => '臺灣基隆地方法院',
    'TYD' => '臺灣桃園地方法院',
    'SCD' => '臺灣新竹地方法院',
    'MLD' => '臺灣苗栗地方法院',
    'TCD' => '臺灣臺中地方法院',
    'CHD' => '臺灣彰化地方法院',
    'NTD' => '臺灣南投地方法院',
    'ULD' => '臺灣雲林地方法院',
    'CYD' => '臺灣嘉義地方法院',
    'TND' => '臺灣臺南地方法院',
    'KSD' => '臺灣高雄地方法院',
    'HLD' => '臺灣花蓮地方法院',
    'TTD' => '臺灣臺東地方法院',
    'PTD' => '臺灣屏東地方法院',
    'PHD' => '臺灣澎湖地方法院',
    'KMH' => '福建高等法院金門分院',
    'KMD' => '福建金門地方法院',
    'LCD' => '福建連江地方法院',
    'KSY' => '臺灣高雄少年及家事法院',
    'TPE' => '臺北簡易庭',
    'STE' => '新店簡易庭',
    'SLE' => '士林簡易庭',
    'NHE' => '內湖簡易庭',
    'PCE' => '板橋簡易庭',
    'SJE' => '三重簡易庭',
    'TYE' => '桃園簡易庭',
    'CLE' => '中壢簡易庭',
    'SCD' => '新竹簡易庭',
    'CPE' => '竹北簡易庭(含竹東)',
    'MLD' => '苗栗簡易庭',
    'TCE' => '臺中簡易庭',
    'SDE' => '沙鹿簡易庭',
    'FYE' => '豐原簡易庭',
    'CHE' => '彰化簡易庭',
    'OLE' => '員林簡易庭',
    'PDE' => '北斗簡易庭',
    'NTE' => '南投簡易庭(含埔里)',
    'TLE' => '斗六簡易庭',
    'HUE' => '虎尾簡易庭',
    'CYE' => '嘉義簡易庭(含朴子)',
    'PKE' => '北港簡易庭',
    'TNE' => '臺南簡易庭',
    'SYE' => '柳營簡易庭',
    'SSE' => '新市簡易庭',
    'KSE' => '高雄簡易庭',
    'GSE' => '岡山簡易庭',
    'CSE' => '旗山簡易庭',
    'FSE' => '鳳山簡易庭',
    'PTE' => '屏東簡易庭',
    'CCE' => '潮州簡易庭',
    'TTE' => '臺東簡易庭',
    'HLE' => '花蓮簡易庭(含鳳林,玉里)',
    'ILE' => '宜蘭簡易庭',
    'LTE' => '羅東簡易庭',
    'KLD' => '基隆、瑞芳簡易庭',
    'MKE' => '馬公簡易庭',
    'KME' => '金城簡易庭',
);
$types = array(
    'M' => '刑事',
    'V' => '民事',
    'A' => '行政',
    'P' => '公懲',
);

if (in_array($_SERVER['REQUEST_URI'], array('/js/judge_parse.js', '/js/text_parse.js'))) {
    readfile(__DIR__ . ltrim($_SERVER['REQUEST_URI']));
    exit;
}
if (in_array($_SERVER['REQUEST_URI'], array('/js/text.css'))) {
    header('Content-Type: text/css');
    readfile(__DIR__ . ltrim($_SERVER['REQUEST_URI']));
    exit;
}

if ($_SERVER['REQUEST_URI'] == '/') {
    include('mainpage.php');
    exit;
}

list(, $court_id, $court_type, $year, $case_word, $case_no, $jdate, $jcheck, $method) = explode('/', urldecode($_SERVER['REQUEST_URI']));
if (!$courts[$court_id]) {
    $title = "找不到法院 {$court_id}";
    $link = null;
} elseif (!$types[$court_type]) {
    $title = "找不到案件種類 {$court_type}";
    $link = null;
} else {
    $title = "{$courts[$court_id]}{$types[$court_type]} {$year}年度{$case_word}字第{$case_no}號";
    $link = "http://jirs.judicial.gov.tw/FJUD/FJUDQRY02_1.aspx?cw=1&v_court=" . urlencode($court_id . ' ' . $courts[$court_id]) . "&v_sys={$court_type}&jud_year={$year}&jud_case=" . urlencode($case_word) . "&jud_no={$case_no}&jud_title=&keyword=&sdate=19110101&edate=99991231&searchkw=";
}

if ($method == 'editor' or $method == 'viewer') {
    $params = array();
    $params[] = 'jrecno=' . urlencode(implode(',', array($year, $case_word, $case_no, $jdate + 19110000, $jcheck)));
    $params[] = 'v_court=' . urlencode($court_id . ' ' . $courts[$court_id]);
    $params[] = 'v_sys=' . urlencode($court_type);
    $params[] = 'jyear=' . urlencode($year);
    $params[] = 'jcase=' . urlencode($case_word);
    $params[] = 'jno=' . intval($case_no);
    $params[] = 'jdate=' . intval($jdate);
    $params[] = 'jcheck=' . intval($jcheck);
    $url = 'http://jirs.judicial.gov.tw/FJUD/PrintFJUD03_0.aspx?' . implode('&', $params);

    include($method . '.php');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>裁判書 - <?= htmlspecialchars($title) ?></title>
<meta property="og:description" 
content="本網址將會導向至 jirs.judicial.gov.tw 司法院法學資料檢索系統" />
</head>
<body>
<?php if (!is_null($link)) { ?>
<script>
document.location = <?= json_encode($link) ?>;
</script>
<?php } else { ?>
<h1>失敗: <?= htmlspecialchars($title) ?></h1>
<?php } ?>
</body>
</html> 

