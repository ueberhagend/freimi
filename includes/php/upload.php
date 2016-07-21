<?php
	if(isset($_GET["type"]) && $_GET["type"] == "opso"){
		if(isset($_POST["hidden_id"]) && $_POST["hidden_id"] != ""){
			$destination = "opso";
			$ep_id  = $_POST["hidden_id"];
			if($ep_id != ""){
				$id = $ep_id;
			}
		}
	}
	if(isset($_GET["type"]) && $_GET["type"] == "ops" ){
		//if(isset($_POST["hidden_id"]) && $_POST["hidden_id"] != ""){
			$destination = "ops";
			$opsID  = $_POST["hidden_id"];
			if($opsID != ""){
				$id = $opsID;
			}
			else{
				$id = "empty";
			}
		echo $id;
		//}
	}
if(isset($_GET["type"]) && $_GET["type"] == "whs" ){
	//if(isset($_POST["hidden_id"]) && $_POST["hidden_id"] != ""){
	$destination = "whs";
	$whsID  = $_POST["hidden_id"];
	if($whsID != ""){
		$id = $whsID;
	}
	else{
		$id = "empty";
	}
	echo $id;
	//}
}
	if(isset($id)){
		//pass EP ID
		
		//pass new filename
		$filename = $_POST["filename"];
		//pass old filename
		$file_name = $_FILES['myfile']['name'];
		if($filename == ""){
			$filename = $file_name;
		}
		else{
			//get file extension
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			//create new filename
			$filename = "$filename.$ext";
		}
		//create folder
		mkdir("../../uploads/$destination/$id/", 0777);
		 //set dest. path
		 $destination_path = "../../uploads/$destination/$id/";
		
		$result = 0;
		
		$target_path = $destination_path . basename( $filename);
		
		if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
		   $result = 1;
		}
		
		sleep(1);
   		?>
		<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>);</script>   
		<?php
	}

?>
