<?php


	$dateien = array();
	$dateien['content'] = 'content.php';
	$dateien['opso'] = 'opso.php';
	$dateien['loadops'] = 'loadops.php';
	$dateien['createops'] = 'createops.php';
	$dateien['loadwhs'] = 'loadwhs.php';
	$dateien['createwhs'] = 'createwhs.php';
	$dateien['ep'] = 'ep.php';
	
	if (isset($_GET['site'], $dateien[$_GET['site']])) {

		if (file_exists("includes/php/".$dateien[$_GET['site']])) {
			include $dateien[$_GET['site']]; 
		} else {
			echo "Include-Datei konnte nicht geladen werden: ".$dateien[$_GET['site']]."'";
		}
	} else {
    // default bereich laden, news
    include $dateien['content'];
}
	

?>
