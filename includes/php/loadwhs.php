<?php
error_reporting(0);
$token = "YourDBToken";

$query = "https://noob.aiesec.de/outgoerPreparations?access_token=$token&page=100000&limit=10";
$answer = file_get_contents($query);
$json = json_decode($answer);
$totalItems = $json->totalItems;
//divide by 50
$pages = $totalItems/50;
//get page number
if(isset($_GET["page"])){
	$page = $_GET["page"];
}
else{
	$page = 1;
}
?>


<!doctype html>
<head>
</head>
<div><br></div><div><br></div>
<div class="crm-content" style="margin-left:70px;margin-top:50px;width:1000px;border:1px solid #dddddd;background-color:#f9fbfd;border-radius:25px;">
	<div style="padding-left:30px; padding-top:30px; min-height:700px; !important;" class="modal-content">
		<div class="fields">
			<div class="half"></div>
		</div>
		<div class="fields">
			<label>Outgoer Preparation Seminars</label>
			<div class="half" style="width:900px;!important">
				<div style="width:800px;">
					<div style="float:left;width:200px;margin-right: 20px;"><p class="btn cancel">Type</p></div>
					<div style="float:left;width:200px;"><p class="btn cancel">Local Committee</p></div>
					<div style="float:left;width:300px;"><p class="btn cancel">Start Date - End Date</p></div>
				</div>
				<div>&nbsp;</div><br>
				<div>&nbsp;</div><br>
				<?php
				function sortFunction($a,$b){
					return strtotime($a->startDate) - strtotime($b->startDate);
				}
				$query = "https://noob.aiesec.de/reintegrationActivities?access_token=$token&page=$page&limit=50";
				$answer = file_get_contents($query);
				$json = json_decode($answer);
				$json = $json->payload;
				usort($json,"sortFunction");
				$json = array_reverse($json);
				foreach($json as $key => $ops){
					$id = $ops->id;
					$type = $ops->type;
					if($type == "standardReintegrationActivity"){continue;}
					$lc = $ops->lc;
					$startDate = $ops->startDate;
					$startDate = explode("T",$startDate);
					$startDate = $startDate[0];
					$endDate = $ops->endDate;
					$endDate = explode("T",$endDate);
					$endDate = $endDate[0];
					?>
					<a href="index.php?site=createwhs<?php echo "&id=$id&startDate=$startDate&endDate=$endDate&type=$type&lc=$lc;"?>" class="status" style="font-size:10pt;">
						<div style="float:left;width:200px;margin-right: 20px;"><?php echo $type; ?></div>
						<div style="float:left;width:200px;"><?php echo $lc; ?></div>
						<div style="float:left;width:300px;"><?php echo "$startDate - $endDate"; ?></div>
					</a>
					<?php

				}
				?>
			</div>
			<div class="half"></div>
		</div>
	</div>
</div>

