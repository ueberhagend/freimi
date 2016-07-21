<?php
error_reporting(0);
//holt sich die Adresse aus der URI, findet raus, ob man sich auf ZAV/PBogen, etc... befindet. Dadurch kann später im Select/Dropdown der richtige Wert angezeigt werden
$uri = $_SERVER[REQUEST_URI];		//zieht sich die URI
$uri = explode("finance/", $uri);	//spaltet nach finance/, auf jeden Fall in zwei Teile
$uri = $uri[1];						//dadurch findet man raus, auf welcher Seite man sich befindet
if(isset($_POST) && $_POST != Array()){
	if($_POST["hidden_id"] == ""){
		$no_id = "true";
	}
	if($_POST["hidden_email"] == ""){
		$no_email = "true";
	}
	if(!isset($_FILES['image'])){
		$no_file = "true";
	}
	$ep_id = $_POST["hidden_id"];
	$ep_mail = $_POST["hidden_email"];
}

?>
<!doctype html>
<head>
	<script>
		var token = "YourDBToken";
		$(function() {
			$( "#opsOnlineBookingDate" ).datepicker({dateFormat:'yy-mm-dd'});
		});
		function startUpload(){
			return true;
		}

		function stopUpload(success){
			var result = '';
			if (success == 1){
				result = '<span class="msg">The file was uploaded successfully!<\/span><br/><br/>';
			}
			else {
				result = '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
			}
			return true;

		}
		function reloadFiles(){
			//load files
			var ep_id = document.getElementById("ep_id_confirm").value;
			if(ep_id != ""){
				document.getElementById("documents").innerHTML = "";
				setTimeout(myFunction, 200)
				function myFunction(){
					var call=Utils.loadFile(ep_id);
					document.getElementById("documents").innerHTML = call;
				}
			}
		}

		function test(){
			document.getElementById('first_name').value = "NAME!!!!";
		}
		function setCheckbox() {
			var checked = document.getElementById("opsOnline").checked;
			var opsOnlineBookingDate = document.getElementById("opsOnlineBookingDate").value;
			var ep_id = document.getElementById("ep_id_confirm").value;
			if(checked == "false"){
				checked = false;
			}
			else{
				checked = true;
			}
						console.log(ep_id, checked, opsOnlineBookingDate);

			var data = {
				'opsOnline': checked,
				'opsOnlineBookingDate': opsOnlineBookingDate,
			};

			var xhr = new XMLHttpRequest();
			xhr.withCredentials = false;

			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					console.log(this.responseText);
				}
			});

			xhr.open("PATCH", "https://noob.aiesec.de/people/"+ep_id+"?access_token="+token);
			xhr.setRequestHeader("content-type", "application/json");

			xhr.send(JSON.stringify(data));
		}
		function confirmEP(){
			var ep_id = document.getElementById("ep_id_confirm").value;
			if(ep_id == ""){
				document.getElementById("missing_field_id").innerHTML = "Please enter an ID!";
			}
			else{
				var call=Utils.getPerson(ep_id);
				json = JSON.parse(call);
				email = json.email;
				checked = json.opsOnline;
				opsOnlineBookingDate = json.opsOnlineBookingDate;
				if(checked == true){
					document.getElementById("opsOnline").checked = true;
				}
				else{
					document.getElementById("opsOnline").checked = false;
				}
				if(email == undefined){
					document.getElementById("missing_field_id").innerHTML = "No EP found!";
					document.getElementById("missing_field_email").innerHTML = "";
					email = "";
				}
				else{
					document.getElementById("missing_field_id").innerHTML = "";
				}
				document.getElementById("email").value = email;

				document.getElementById("hidden_email").value = email;
				document.getElementById("hidden_id").value = ep_id;
				if(opsOnlineBookingDate != null){
					opsOnlineBookingDate = opsOnlineBookingDate.split("T")[0];
				}
				console.log(opsOnlineBookingDate);
				document.getElementById("opsOnlineBookingDate").value = opsOnlineBookingDate;

				//load files
				var call=Utils.loadFile(ep_id);
				document.getElementById("documents").innerHTML = call;
			}

		}

		//getting the token
		function Utils(){}


		/*Deklarierung einer asynchronen Methode, welche die Person mir der übergebenen ID zurück gibt*/
		Utils.getPerson=function (ep_id,successFunction) {
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/people/"+ep_id+"?access_token="+token, async: false}).responseText;
		}
		Utils.loadFile=function (ep_id,successFunction) {
			return $.ajax({type: "GET", url: "includes/php/load_files.php?type=opso&id="+ep_id, async: false}).responseText;
		}

		var fileSelect = document.getElementById('file-select');
		function submit(){
			/*
			 // get ids
			 //get values;
			 var ep_id = document.getElementById("ep_id_confirm").value;
			 var email = document.getElementById("email").value;

			 var fileSelect = document.getElementById('file-select');
			 var uploadButton = document.getElementById('upload-button');

			 // Update button text.
			 uploadButton.innerHTML = 'Uploading...';
			 var file = document.getElementById('file-select').files[0]; //Files[0] = 1st file
			 var reader = new FileReader();
			 reader.readAsText(file, 'UTF-8');
			 reader.onload = shipOff;
			 function shipOff(event) {
			 var result = event.target.result;
			 var fileName = document.getElementById('file-select').files[0].name; //Should be 'picture.jpg'
			 $.post('/handler.php', { data: result, name: fileName }, continueSubmission);
			 }
			 */
		}




	</script>
</head>
<div><br></div><div><br></div>
<div class="crm-content" style="margin-left:70px;margin-top:50px;width:1000px;border:1px solid #dddddd;background-color:#f9fbfd;border-radius:25px;">
	<div style="padding-left:30px; padding-top:30px;" class="modal-content">


		<div class="fields">
			<label>EP Information</label>
			<div class="third">
				<input type="text" class="form_element" name="ep_id_confirm" id="ep_id_confirm" style="width:200px;" ng-model="vm.ep_id" placeholder="EP ID" class="ng-pristine ng-untouched ng-valid">
				<p href class="btn cancel" onclick="confirmEP()" ng-click="" value="confirm">Confirm</p>
				<div id='missing_field_id' style=font-size:10pt;color:orangered;><?php if(isset($no_id) && $no_id == "true") {?>EP ID is missing! <?php } ?></div>
			</div>
			<div class="third">
				<input readonly="readonly" style="color:darkgrey;" type="text" style="width:200px;" id="email" name="email" ng-model="vm.ep_id" placeholder="EP Account Mail" class="ng-pristine ng-untouched ng-valid">
				<div id='missing_field_email' style=font-size:10pt;color:orangered;><?php if(isset($no_email) && $no_email == "true") {?>Confirm EP ID! <?php } ?></div>
			</div>
		</div>
		<div class="fields">
			<label>First OPS Login Date</label>
			<div class="third">
				<input type="text" style="width:200px;" name="opsOnlineBookingDate" onchange="setCheckbox()" id="opsOnlineBookingDate" placeholder="YYYY-MM-DD">
			</div>
		</div>
		<div class="fields">
			<label>Confirm OPSo</label>
			<span class="btn cancel">
				<div class="third" style="padding-right:5em; !important">
					Checked?
				</div>
				<div class="third">
					<input <?php if($checked == "on"){ echo "checked"; } ?> name="checked" onclick="setCheckbox()" id="opsOnline" type="checkbox">
				</div>
			</span>
		</div>
		<label>&nbsp;</label>
		<form action="includes/php/upload.php?type=opso" id="myForm" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
			<div class="fields">
				<div class="third">
					<input type="text" class="form_element" name="filename" id="filename" style="width:200px;" ng-model="vm.ep_id" placeholder="Filename" class="ng-pristine ng-untouched ng-valid">
				</div>

			</div>

			<input name="myfile" type="file" size="30" />

			<input type = "text" id="hidden_id" hidden name="hidden_id" >
			<input type = "text" id="hidden_email" hidden name="hidden_email" >
			<div id='missing_field_file' style=font-size:10pt;color:orangered;><?php if(isset($no_file) && $no_file == "true") {?>Please select a file! <?php } ?></div>
			<div><br></div>
			<input class="btn confirm" onclick="reloadFiles()" name="submitBtn" type = "submit" style="width:100px;background-color:#3c3;"/>
			<div><br></div>
			<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		</form>
		<div style="min-height:200px;!important">
			<label>OPS Online EP Documents</label>
			<div><br></div>
			<div id='documents' style=font-size:10pt;><p style="color:orangered;">Submit form first!</p></div>

		</div>
	</div>
</div>
<div><br></div>
<div><br></div>
