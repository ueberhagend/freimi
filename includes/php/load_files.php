<?php
error_reporting(0);
if(isset($_GET["id"]) && $_GET["type"]){
	$id = $_GET["id"];
	$type = $_GET["type"];
$array = scandir("../../uploads/$type/$id/");
if(count($array) < 3){?> <div id='missing_files' style=font-size:10pt;color:orangered;>No files found!</div> <?php }
$i = 0;
foreach ($array as $key => $value) {
	if ($value == "." or $value == "..") {
	} else {
		$i++;
		echo "$i.";
		?>
		<a href="../../uploads/$type/<?php echo "$id/$value"; ?>"><?php echo $value; ?></a><br>
		<?php
	}
}
}
?>