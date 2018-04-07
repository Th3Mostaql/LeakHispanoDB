<?php
//Class to make me easier coding
function cleanthis($data){
	$iclean = filter_var($data, FILTER_SANITIZE_STRING);
	$iclean = thisword($iclean);
	$iclean = htmlentities($iclean, ENT_QUOTES);
	return $iclean;
}
function thisword($word){
	$badword = array("drop", "insert", "update", "delete", "alter", "index", "truncate", "sleep", "'", '"');
	$badreplace = array("***", "***", "****", "***", "****", "***", "*****", "*****", "*", "*");
	$clean = str_replace($badword,$badreplace,$word);
	return $clean;
}
function formatSize ($bytes) {
	$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	$bytes /= pow(1024, $pow);

	return ceil($bytes) . ' ' . $units[$pow];
}

?>