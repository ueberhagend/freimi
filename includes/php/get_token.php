<?php
$username = "YourUserName";
$password = "YOurPassword";
$data = "user%5Bemail%5D=" . urlencode($username) . "&user%5Bpassword%5D=" . urlencode($password);

$req = curl_init('https://auth.aiesec.org/users/sign_in');
curl_setopt($req, CURLOPT_POST, true);
curl_setopt($req, CURLOPT_POSTFIELDS, $data);
curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($req, CURLOPT_HEADER, 1);
curl_setopt($req, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($req, CURLOPT_COOKIEFILE, "");
$res = curl_exec($req);
curl_close($req);

// get token cookie;
$token = false;
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $res, $cookies);
foreach($cookies[1] as $c) {
	parse_str($c, $cookie);
	if(isset($cookie["expa_token"])) $token = trim($cookie["expa_token"]);
}
//$token = "7302018ee0ddc967dee285b3c3e97dd9ba8e068853a865646529fab652cce6ec";
if($token !== false && $token !== null) {
	$_SESSION['token'] = $token;
	echo $token;

} else  {
	//return $token;
	if(strpos($res, "<h2>Invalid email or password.</h2>") !== false) {
		throw new InvalidCredentialsException("Invalid email or password");
	} else {
		throw new InvalidAuthResponseException("The GIS auth response does not match the requirements.");
	}
}