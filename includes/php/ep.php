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
		function confirmEP(){
			var ep_id = document.getElementById("ep_id_confirm").value;
			if(ep_id == ""){
				document.getElementById("missing_field_id").innerHTML = "Please enter an ID!";
			}
			else{
				var call=Utils.getPerson(ep_id);
				json = JSON.parse(call);
				var error = json.error;
				if(typeof error != "undefined"){
					document.getElementById("missing_field_id").innerHTML = "EP doesn't exist!";
				}
				else{
					//email email = json.email;
					document.getElementById("email").value = json.email;
					//OPS Info
					//check OPS
					opsOnlineBookingDate = json.opsOnlineBookingDate;
					call = Utils.loadParticipationsByEP(ep_id);
					json = JSON.parse(call);
					if(json.totalItems == 0){
						//no physical
						if(typeof opsOnlineBookingDate == "undefined"){
							ops = "No OPS Confirmed";
						}
						else{
							opsOnlineBookingDate = opsOnlineBookingDate.split("T")[0];
							ops = "OPS Online confirmed on "+opsOnlineBookingDate;
						}
					}
					else{
						link = json.payload[0]._links.outgoerPreparation.href;
						//links.outgoerPreparation.href;
						call = Utils.getByLink(link);
						json = JSON.parse(call);
						name = json.type+" "+json.lc+" "+json.startDate.split("T")[0]+"-"+json.endDate.split("T")[0];
						ops = "Physical OPS: "+name;
					}
					document.getElementById("opsLabel").innerHTML = "OPS Information";
					document.getElementById("opsInfo").innerHTML = ops;
					document.getElementById("missing_field_id").innerHTML = "";
					//get exchanges
					call = Utils.getExchanges(ep_id);
					json = JSON.parse(call);
					//console.log(json);
					count = json.totalItems;
					exchanges = json.payload;
					document.getElementById("count_exchanges").innerHTML = "Exchanges: "+count;
					if(count > 0){
						var lines = ""
						for (i = 0; i < exchanges.length; i++) {
							j = i+1;
							console.log(exchanges[i]);
							var id = exchanges[i].id;
							var applicationID = exchanges[i].applicationID;
							//get Reintegration Activity
							link = exchanges[i]._links.reintegrationActivityParticipations;
							if(typeof link == "undefined"){
								reintegration = "No Reintegration Activity Found";
							}
							else{
								call = Utils.loadWHSParticipationsByEP(id);
								json = JSON.parse(call);
								link = json.payload[0]._links.reintegrationActivity.href;
								call = Utils.getByLink(link);
								json = JSON.parse(call);
								reintegration = json;
								if(json.type == "welcomeHomeSeminar"){
									type = "WHS";
								}
								else{
									type = "Returnee Talk";
								}
								reintegration = type+" "+json.lc+" "+json.startDate.split("T")[0]+" - "+json.endDate.split("T")[0];
							}
							if(applicationID == null){
								applicationID = "";
							}
							else{
								applicationID = "Application ID: "+applicationID;
							}
							line = "<p class='btn' style='font-style: italic;'>#"+j+" </p> Exchange ID: "+id+" | "+applicationID+" | "+reintegration+"<br><br>";
							console.log(line);
							lines = lines+line;

						}

					}
					document.getElementById("exchange_information").innerHTML = lines;

				}
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
		Utils.getExchanges=function(ep_id,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/people/"+ep_id+"/exchanges?access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}
		Utils.loadParticipationsByEP=function(ep_id,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/outgoerPreparationParticipations?person="+ep_id+"&access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}
		Utils.getByLink=function (link,successFunction) {
			return $.ajax({type: "GET", url: "https://noob.aiesec.de"+link+"?access_token="+token, async: false}).responseText;
		}
		Utils.loadWHSParticipationsByEP=function(exchangeID,successFunction){
			return $.ajax({type: "GET", url: "https://noob.aiesec.de/reintegrationActivityParticipations?exchange="+exchangeID+"&access_token="+token+"&page=1&limit=100", async: false}).responseText;
		}





	</script>
</head>
<div><br></div><div><br></div>
<div class="crm-content" style="margin-left:70px;margin-top:50px;width:1000px;border:1px solid #dddddd;background-color:#f9fbfd;border-radius:25px;">
	<div style="padding-left:30px; padding-top:30px;height:500px;" class="modal-content">


		<div class="fields">
			<label>EP Information</label>
			<div class="third">
				<input type="text" class="form_element" name="ep_id_confirm" id="ep_id_confirm" style="width:200px;" ng-model="vm.ep_id" placeholder="EP ID" class="ng-pristine ng-untouched ng-valid">
				<p href class="btn cancel" onclick="confirmEP()" ng-click="" value="confirm">Confirm</p>
				<div id='missing_field_id' style=font-size:10pt;color:orangered;></div>
			</div>
			<div class="third">
				<input readonly="readonly" style="color:darkgrey;" type="text" style="width:200px;" id="email" name="email" ng-model="vm.ep_id" placeholder="EP Account Mail" class="ng-pristine ng-untouched ng-valid">
				<div id='missing_field_email' style=font-size:10pt;color:orangered;><?php if(isset($no_email) && $no_email == "true") {?>Confirm EP ID! <?php } ?></div>
			</div>
		</div>
		<div class="fields">
			<label id ="opsLabel"></label>
			<div class="">
				<div style="font-size:10pt;" id="opsInfo"></div>
			</div>
		</div>
		<div class="fields">
			<label id ="count_exchanges"></label>
		</div>
		<div class="third">
			<div id="exchange_information"></div>
		</div>
	</div>
</div>
<div><br></div>
<div><br></div>
