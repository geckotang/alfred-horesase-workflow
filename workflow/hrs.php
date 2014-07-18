<?php
define("WF_QUERY", $query);
require_once('workflows.php');
require_once('underscore.php');
$wf = new Workflows();
$filepath = "data.json";

#ファイルがないもしくは、1日以上経っている場合は更新する
if ( !file_exists($filepath) || (filemtime($filepath) <= time()-86400) ) {
	$url = "http://horesase-boys.herokuapp.com/meigens.json";
	$data = json_decode(@file_get_contents($url, 0, $context));
	if (count($data)) {
		file_put_contents($filepath, json_encode($data));
	}
}

$json = json_decode(file_get_contents($filepath));

if (constant('WF_QUERY')) {
	$dataList = __::filter($json, function($entry) {
		$findTitle = stripos($entry->title, constant('WF_QUERY')) !== false;
		$findCharacter = stripos($entry->character, constant('WF_QUERY')) !== false;
		$findBody = stripos($entry->body, constant('WF_QUERY')) !== false;
		return $findTitle || $findCharacter || $findBody;
	});
} else {
	$dataList = $json;
}

foreach($dataList as $data) {
	$wf_query = $data->image;
	$wf_title = urldecode($data->character);
	$wf_description = urldecode($data->body);
	$wf_icon = 'icon.png';
	# 無料記事の場合はアイコンを変更する
	$wf->result(
		time(),
		$wf_query,
		$wf_title,
		$wf_description,
		$wf_icon
	);
}

echo $wf->toxml();
?>
