<style type="text/css">
#watermark {
  color: #d0d0d0;
  font-size: 100pt;
  -webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  position: absolute;
  width: 100%;
  height: 100%;
  margin: 0;
  z-index: -1;
  left:200px;
  top:-200px;
}
</style>

<div id="watermark">
<p>NOT COMPLETE</p>
</div>

<i>Source code can be found on <a href="https://github.com/sharmalakshay">my github</a></i>

<br><hr><br>

<form method="post" action="">
From: <input type="text" name="from"/> (Bus station number)
<br><br>
Route: <input type="text" name="route"/>
<br><br>
<input type="submit" name="submit" value="show me the right bus"/>
</form>

<br><hr><br>

<form action="" method="POST">
Current location: <input type="text" name="location"/> (GPS coordinates)
<input type="submit" name="submit2" value="Show the bus stations near me"/>
</form>

<br><hr><br>

<form action="" method="POST">
FROM: <input type="text" name="from"/>
TO: <input type="text" name="to"/> (Actual name or address)
<input type="submit" name="submit3" value="Tell me how to go"/>
</form>

<br><hr><br>

<?php

if(isset($_POST['submit'])){
	$url = "https://data.dublinked.ie/cgi-bin/rtpi/realtimebusinformation?stopid=".$_POST['from'];
	$json = file_get_contents($url);
	extract(json_decode($json, true));
	if($numberofresults<1){
		echo "unlucky you";
	}
	else{
		for ($i = 0; $i<$numberofresults; $i++){
			if($results[$i]['route']==$_POST['route']){
				echo "coming in ".$results[$i]['duetime']." minutes <br>";
			}
		}
	}
}










if(isset($_POST['submit2'])){
	$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".urlencode($_POST['location'])."&radius=500&type=bus_station&key=AIzaSyAXSAx-O4DR1h9GwcKCBW-9BcQF7UGvno8";
	$json = json_decode(file_get_contents($url),true);
	$count = count($json['results']);
	for($i=0; $i<$count; $i++){
		echo $json['results'][$i-1]['name'], "<br>";
	}
}









if(isset($_POST['submit3'])){
	
	//BELOW: make array of, print all stations near current location
	
	$from_url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($_POST['from']);
	$from_json = json_decode(file_get_contents($from_url),true);
	if($from_json['status']=="OK"){
		$from_coordinates = $from_json['results'][0]['geometry']['location']['lat'].",".$from_json['results'][0]['geometry']['location']['lng'];
		$from_coordinates_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".urlencode($from_coordinates)."&radius=500&type=bus_station&key=AIzaSyAXSAx-O4DR1h9GwcKCBW-9BcQF7UGvno8";
		$from_coordinates_json = json_decode(file_get_contents($from_coordinates_url),true);
		echo "<b>Bus stations in the range of 500m are:</b>  <br>";
		$from_stations = array();
		$count = count($from_coordinates_json['results']);
		for($i=0; $i<$count; $i++){
			$from_stations[$i-1] = $from_coordinates_json['results'][$i-1]['name'];
			echo $from_stations[$i-1], "<br>";
		}
	}
	else{
		echo "Cannot find the place you entered in FROM";
		exit;
	}	
	
	
	
	
	
	//BELOW: make array of, print all stations near current locations
	
	$to_url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($_POST['to']);
	$to_json = json_decode(file_get_contents($to_url),true);
	if($to_json['status']=="OK"){
		$to_coordinates = $to_json['results'][0]['geometry']['location']['lat'].",".$to_json['results'][0]['geometry']['location']['lng'];
		$to_coordinates_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".urlencode($to_coordinates)."&radius=500&type=bus_station&key=AIzaSyAXSAx-O4DR1h9GwcKCBW-9BcQF7UGvno8";
		$to_coordinates_json = json_decode(file_get_contents($to_coordinates_url),true);
		echo "<br><b>Bus stations in the range of 500m of destination are:</b>  <br>";
		$to_stations = array();
		for($i=count($to_coordinates_json['results']); $i>0; $i--){
			$to_stations[$i-1] = $to_coordinates_json['results'][$i-1]['name'];
			echo $to_stations[$i-1], "<br>";
		}
	}
	else{
		echo "Cannot find the place you entered in TO";
		exit;
	}
	
	
	
	
	//itterating over stations near destination
	 
	//for($i=count($to_stations); $i>0; $i--){
	//	$to_stop_ids_url = "https://data.dublinked.ie/cgi-bin/rtpi/busstopinformation?stopname=".urlencode($to_stations[$i-1]);
	//	$to_stop_ids_json = json_decode(file_get_contents($to_stop_ids_url),true);
	//	$to_stop_ids = array();
	//	for($j=$to_stop_ids_json['numberofresults']; $j>0; $j--){
	//		$to_stop_ids[$j-1] = $to_stop_ids_json['results'][$j-1]['stopid'];
	//		echo $to_stop_ids[$j-1], "<br>";
	//	}	
	//}
}
?>