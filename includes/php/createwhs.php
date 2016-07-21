<?php
error_reporting(0);
if(isset($_GET["id"])){
}
?>
<!doctype html>
<head>
	<script>
		var token = "YourDBToken";
		$(function() {
			$( "#startDate" ).datepicker({dateFormat:'yy-mm-dd'});
		});
		$(function() {
			$( "#endDate" ).datepicker({dateFormat:'yy-mm-dd'});
		});
		$('#body').ready(function() {

			//get parameters
			var url = location.search;
			var id = url.split("&")[1];
			if (typeof id != "undefined") {
				id = id.split("=")[1];
				var startDate = url.split("&")[2].split("=")[1];
				var endDate = url.split("&")[3].split("=")[1];
				var lc = unescape(url.split("&")[5].split("=")[1]).split(";")[0];

				//hide button and make divs visible
				document.getElementById('buttonWHSCreate').style.visibility = "hidden";
				document.getElementById('addParticipants').style.visibility = "visible";
				document.getElementById('FileUpload').style.visibility = "visible";

				//save WHS ID
				document.getElementById("whsID").value = id;
				document.getElementById("hidden_id").value = id;

				//store Values
				document.getElementById('startDate').value = startDate;
				document.getElementById('endDate').value = endDate;
				//add option to select and select it
				$('#lc').append($('<option>', {
					value: 1,
					text: lc
				}));
				$("select#lc").val("1");
				//load EPs
				saveEPs();
				reloadFiles();
			}
		});
		function createWHS(){
			var call = Utils.loadcreateWHS();
			document.getElementById("WHS").innerHTML = call;

		}
		function loadWHS(){
			var call = Utils.loadloadWHS();
			document.getElementById("WHS").innerHTML = call;
		}
		function startUpload(){
			return true;
		}
		function updateParticipant(id, whsID){
			//delete participation id
			var data = null;

			var xhr = new XMLHttpRequest();
			xhr.withCredentials = false;

			xhr.addEventListener("readystatechange", function () {
				if (xhr.readyState === 4){

					showParticipants(whsID);
				}


			});
			xhr.open("DELETE", "https://noob.aiesec.de/reintegrationActivityParticipations/"+id+"?access_token="+token,true);
			xhr.setRequestHeader("content-type", "application/json");
			xhr.send(data);
			//load participants again
			showParticipants(whsID);
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
			var whsID = document.getElementById("whsID").value;
			if(whsID != ""){
				document.getElementById("documents").innerHTML = "";
				setTimeout(myFunction, 200)
				function myFunction(){
					var call=Utils.loadFile(whsID);
					document.getElementById("documents").innerHTML = call;
				}
			}
		}

		function test(){
			document.getElementById('first_name').value = "NAME!!!!";
		}
		function storeWHSID(response) {
			json = JSON.parse(response);
			var whsID = json.id;
			document.getElementById('whsID').value = whsID;
			document.getElementById('hidden_id').value = whsID;
			document.getElementById('buttonWHSCreate').style.visibility = "hidden";
			document.getElementById('addParticipants').style.visibility = "visible";
			document.getElementById('FileUpload').style.visibility = "visible";

		}
		function saveEPs(){
			var whsID = document.getElementById('whsID').value;
			var EpIDs = $(".enterEpID");
			for (i = 1; i < EpIDs.length; i++) {
				var ep_id = $("#enterEpID_"+i).val();
				var exchangeID = $("#selectExchange_"+i).val();
				if(ep_id === undefined || ep_id == "" || exchangeID == "Select Exchange"){

				}
				else{
					var callEP = Utils.loadParticipationsByEP(exchangeID);
					json = JSON.parse(callEP);
					var totalItems = json.totalItems;
					if(totalItems > 0){
						$('#missing_field_id').append("<div>EP "+ep_id+" already did WHS for exchange "+exchangeID+"</div>");
					}
					else{
						//saving ep ids
						var data = {
							'confirmed': true,
							'exchange': exchangeID,
							'reintegrationActivity': whsID,
						};
						var xhr = new XMLHttpRequest();
						xhr.withCredentials = false;

						xhr.addEventListener("readystatechange", function () {
							if (xhr.readyState === 4) {
								console.log(xhr.responseText);
								showParticipants(whsID);
							}
						});
						xhr.open("POST", "https://noob2.aiesec.de/reintegrationActivityParticipations?access_token="+token,true);
						xhr.setRequestHeader("content-type", "application/json");
						xhr.send(JSON.stringify(data));
					}
				}
			}
			//load saved eps
			function f() {

			}
			showParticipants(whsID);
		}
		function confirmEPs(){
			whsID = document.getElementById('whsID').value;
			//get ep ids
			eps = $( ".enterEpID" );
			for (i = 0; i < 10; i++) {
				var z = i+1;
				ep_id = document.getElementById(eps[i].id).value;
				if(ep_id != ""){
					$("select#selectExchange_"+z)
						.empty()
						.append('<option selected="selected" value="Select">Select Exchange</option>')
					;
					//Activate the dropdown boxes
					$("#selectExchange_"+z).prop( "disabled", false );
					//get number of exchanges
					var call=Utils.getExchanges(ep_id);
					json = JSON.parse(call);
					exchanges = json.payload;
					if(typeof exchanges == "undefined"){
						alert("EP "+ep_id+" doesn't exist in the database!");
					}
					else{
						for (j = 0; j < exchanges.length; j++) {
							//if participation link exists, WHS is already confirmed
							reintegrationActivityParticipations = exchanges[j]._links.reintegrationActivityParticipations;
							console.log(reintegrationActivityParticipations);
							if(typeof reintegrationActivityParticipations == "undefined"){
								exchangeID = exchanges[j].id;
								applicationID = exchanges[j].applicationID;
								var exchange_name = "Exchange ID: "+exchangeID;
								if(applicationID != null){
									//get Application
									call = Utils.getApplication(applicationID);
									json = JSON.parse(call);
									oppID = json.opportunity.id;
									var exchange_name = "Opportunity ID: "+oppID;
								}

								this.$("select#selectExchange_"+z).append('<option value='+exchangeID+'>'+exchange_name+'</option>');

							}

						}
					}
				}
			}
			document.getElementById('saveEPs').style.visibility = "visible";
		}
		function showParticipants(whsID){
			whsID = document.getElementById('whsID').value;
			document.getElementById('showParticipants').style.visibility = "visible";
			var call=Utils.loadParticipationsByWHS(whsID);
			json = JSON.parse(call);
			participants = json.payload;
			$("#participants").empty();
			for (i = 0; i < participants.length; i++) {
				person = participants[i]._links.exchange.href;
				ep = person.split("/");
				ep_id = ep[2];
				exchange_id = ep[4];
				console.log(ep_id, exchange_id);
				var id = participants[i].id;
				$("#participants").prepend("<div><div style='width:200px;float:left;'>EP "+ep_id+" Exchange "+exchange_id+"</div><span class='status' id='removeMe' onclick='updateParticipant("+id+","+whsID+")'>Confirmed</span></div>")
			}
			//$('#missing_field_id').append("<div>EP "+ep_id+" doesn't exist</div>");
		}


		function WHStoDatabase() {
			var startDate = document.getElementById("startDate").value;
			var endDate = document.getElementById("endDate").value;
			var dropdown = document.getElementById("lc");
			var lc = dropdown.options[dropdown.selectedIndex].text;
			if(startDate == "" || endDate == "" || lc == "Select"){
				document.getElementById("missing_field").innerHTML = "Please enter all fields!";
			}
			var response = "";
			if(startDate != "" && endDate != "" && lc != "Select") {
				//save WHS to Database
				var response = "";

				var data = {
					'type': "welcomeHomeSeminar",
					'startDate': startDate,
					'endDate': endDate,
					'lc': lc,
				};

				var xhr = new XMLHttpRequest();
				xhr.withCredentials = false;

				xhr.addEventListener("readystatechange", function () {
					if (xhr.readyState === 4) return;

					response = (xhr.responseText);
					storeWHSID(response);


				});
				xhr.open("POST", "https://noob.aiesec.de/reintegrationActivities?access_token="+token,true);
				xhr.setRequestHeader("content-type", "application/json");
				xhr.send(JSON.stringify(data));



			}

		}

		//getting the token
		function Utils(){}


		/*Deklarierung einer asynchronen Methode, welche die Person mir der übergebenen ID zurück gibt*/
		Utils.getWHS=function (type,startDate,endDate,lc,successFunction) {
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/reintegrationActivities?access_token="+token, async: false}).responseText;
		}
		Utils.loadFile=function (whsID,successFunction) {
			return $.ajax({type: "GET", url: "includes/php/load_files.php?type=whs&id="+whsID, async: false}).responseText;
		}
		Utils.loadloadWHS=function (successFunction) {
			return $.ajax({type: "GET", url: "includes/php/loadwhs.php?load&page=1", async: false}).responseText;
		}
		Utils.loadcreateWHS=function (successFunction) {
			return $.ajax({type: "GET", url: "includes/php/createwhs.php", async: false}).responseText;
		}
		Utils.loadParticipationsByEP=function(exchangeID,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/reintegrationActivityParticipations?exchange="+exchangeID+"&access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}
		Utils.loadParticipationsByWHS=function(oppID,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/reintegrationActivityParticipations?reintegrationActivity="+oppID+"&access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}
		Utils.getExchanges=function(ep_id,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/people/"+ep_id+"/exchanges?access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}
		Utils.getApplication=function(applicationID,successFunction){
			return $.ajax({type: "GET", url: "https://gis-api.aiesec.org:443/v1/applications/"+applicationID+".json?access_token="+Utils.getToken(), async: false}).responseText;
		}
		Utils.getToken=function () {
			return $.ajax({type: "GET", url: "includes/php/get_token.php", async: false}).responseText;
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
			<label>Welcome Home Seminar Info</label>
			<div class="half" style="width:700px;!important">
				<!-- hidden whs ID -->
				<input type="text" hidden="hidden" id="whsID">
				<select <?php if(isset($lc)){echo "disabled";}?> name="lc" id="lc" style="width:300px;">
					<?php if(isset($lc)){ echo "<option value=$lc>$lc</option>";}?>
					<option value="Select">Select LC...</option>
					<option value="648">REGENSBURG</option>
					<option value="663">PADERBORN</option>
					<option value="677">DRESDEN</option>
					<option value="1443">WUERZBURG</option>
					<option value="1653">FREIBURG</option>
					<option value="1441">HANNOVER</option>
					<option value="1414">LUENEBURG</option>
					<option value="686">AUGSBURG</option>
					<option value="1415">GOETTINGEN</option>
					<option value="699">HEIDELBERG</option>
					<option value="1448">DUESSELDORF</option>
					<option value="1420">GIESSEN-MARBURG</option>
					<option value="1520">MANNHEIM</option>
					<option value="1440">NUERNBERG</option>
					<option value="1394">MUENCHEN</option>
					<option value="1442">BOCHUM</option>
					<option value="60">MAINZ - WIESBADEN</option>
					<option value="664">BIELEFELD</option>
					<option value="680">BERLIN HU</option>
					<option value="667">DARMSTADT</option>
					<option value="657">FRANKFURT (MAIN)</option>
					<option value="1523">HAMBURG</option>
					<option value="696">JENA</option>
					<option value="1421">KOELN</option>
					<option value="713">AACHEN</option>
					<option value="647">STUTTGART &amp; HOHENHEIM</option>
					<option value="1410">LEIPZIG</option>
					<option value="665">BREMEN</option>
					<option value="1447">MAGDEBURG</option>
					<option value="1438">KIEL</option>
					<option value="757">HALLE</option>
					<option value="1533">KAISERSLAUTERN</option>
					<option value="678">BRAUNSCHWEIG</option>
					<option value="693">BONN</option>
					<option value="1454">BAYREUTH</option>
					<option value="1484">MUENSTER</option>
					<option value="643">KARLSRUHE</option>
					<option value="1403">PASSAU</option>
					<option value="708">BERLIN TU</option>
				</select>

				<div id='missing_field_type' style=font-size:10pt;color:orangered;></div>
			</div>
			<div class="half">
			</div>
		</div>
		<div class="fields">
			<div class="">
				<label><p style="font-size:10pt;">Start & End Date</p></label>
				<input type="text" class="form_element" <?php if(isset($startDate)){ echo "value=$startDate disabled style=width:150px;color:darkgrey;";}?> name="startDate" id="startDate" placeholder="YYYY-MM-DD" style="width:150px; placeholder="Start Date">
				<input type="text" class="form_element" <?php if(isset($endDate)){ echo "value=$endDate disabled style=width:150px;color:darkgrey;";}?> name="endDate" id="endDate" placeholder="YYYY-MM-DD" style="width:150px; placeholder="End Date">
				<div id='missing_field' style=font-size:10pt;color:orangered;></div>
			</div>
		</div>
		<div class="fields">
			<p href class="btn confirm" id="buttonWHSCreate" onclick="WHStoDatabase()" value="confirm" style="width:120px;background-color:#3c3;">Save WHS</p>
			<p href class="btn cancel" id="buttonWHSExists" style="width:120px;visibility:hidden">WHS exists</p>
		</div>
		<div><br></div>
		<div><br></div>
		<div class="fields" id="addParticipants" style="visibility:hidden;">
			<label>Add participants</label>
			<div><br></div>
			<?php
			for($i=1;$i <= 5;$i++){
				?>
			<div class="half" style="width:400px;">
				<div class="fields">
					<div style="float:left;width:100px;"><input type="text" class="enterEpID" id="enterEpID_<?php echo $i;?>" placeholder="EP ID"></div>
					&nbsp;
					<div style="float:left;width:200px;">
						<select type="text" disabled class="selectExchange" id="selectExchange_<?php echo $i;?>" placeholder="Exchange ID">
							<option id="Select">Select Exchange</option>
						</select>
					</div>
				</div>
			</div>
				<?php }
			for($i=6;$i <= 10;$i++){
				?>
				<div class="half" style="width:400px;">
					<div class="fields">
						<div style="float:left;width:100px;"><input type="text" class="enterEpID" id="enterEpID_<?php echo $i;?>" placeholder="EP ID"></div>
						&nbsp;
						<div style="float:left;width:200px;">
							<select type="text" disabled class="selectExchange" id="selectExchange_<?php echo $i;?>" placeholder="Exchange ID">
								<option id="Select">Select Exchange</option>
							</select>
						</div>
					</div>
				</div>
			<?php }?>
		<div class="fields"></div>
			<div id='missing_field_id' style=font-size:10pt;color:orangered;></div>
			<div id='confirm_ep' style=font-size:10pt;color:green;></div>
			<br>
			<button class="btn confirm" id="confirmEPs" onclick="confirmEPs()" name="submitBtn" type = "submit" value="Confirm EPs" style="background-color:#3c3;font-size:12pt;border:0">Confirm EPs</button>
			<button class="btn confirm" id="saveEPs" onclick="saveEPs()" name="submitBtn" type = "submit" value="Save EPs" style="visibility:hidden;background-color:#3c3;font-size:12pt;border:0">Save EPs</button>

		</div>
		<div class="fields" id="showParticipants" style="visibility:hidden;">
			<label>Participants</label>
			<div><br></div>
			<div id="participants" class="half" style="width:700px;">

				<br>
			</div>
			<div class="half"></div>
		</div>
		<div id="FileUpload" class="fields" style="visibility:hidden;">
			<label>File Upload</label>
			<form action="includes/php/upload.php?type=whs" id="myForm" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
				<div class="fields">
					<div class="third">
						<input type="text" class="form_element" name="filename" id="filename" style="width:200px;" ng-model="vm.ep_id" placeholder="Filename" class="ng-pristine ng-untouched ng-valid">
					</div>

				</div>

				<input name="myfile" type="file" size="30" />

				<input type = "text" id="hidden_id" hidden name="hidden_id" >
				<input type = "text" id="hidden_email" hidden name="hidden_email" >
				<div id='missing_field_file' style=font-size:10pt;color:orangered;><?php if(isset($no_file) && $no_file == "true") {?>Please select a file!<?php }?></div>
				<div><br></div>
				<input class="btn confirm" onclick="reloadFiles()" name="submitBtn" value="Upload" type = "submit" style="width:100px;background-color:#3c3;border:0"/>
				<div><br></div>
				<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
			</form>
			<div style="min-height:200px;!important">
				<label>WHS Online EP Documents</label>
				<div><br></div>
				<div id='documents' style=font-size:10pt;><p style="color:orangered;"></p></div>


