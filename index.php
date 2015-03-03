<?php include '/home/mkstin/public_html/sahildua.com/inc/all.inc.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Local Tweet Map : Visualizer by Sahil Dua</title>
		<style>
			html, body, #map-canvas{height:100%;margin:0;padding:0;font-family:Calibri,sans-serif; }
		</style>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6oyKPyKj1JsXHLaKtvEFIlhOVgtfT8aE"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script>
			// global variables to be assigned values, later on, in functions
			var latitude=0, longitude=0, map;
			// queue to store the markers for purpose of deleting older ones when length exceeds 100
			var markersList=[];
			// variable for storing the value of latest tweet id_str for sending along with next API call.
			var since_id='0';
			var locationFound = false;
			
			function plotRecentTweets(){
				var URL = 'http://sahildua.com/tripoto-twitter-maps/search.php?since_id='+since_id+'&latitude='+latitude+'&longitude='+longitude;
				$.ajax({
					type: 'GET',
					url: URL,

					success: function(data){
						var x = JSON.parse(data);
						var len = x.length;
						console.log(x);
						for(var j in x){
							// change in index made to push the oldest tweet first in the queue `markersList`
							var i = len - j - 1;
							var longitude = x[i].coordinates.coordinates[0];
							var latitude = x[i].coordinates.coordinates[1];
							var screen_name = x[i].user.screen_name;
							var tweetText = x[i].text;
							var profileImageURL = x[i].user.profile_image_url;
							var myLatlng = new google.maps.LatLng(latitude, longitude);
							var infoWindow = new google.maps.InfoWindow({
								content: tweetText
							});
							// get tweet id of the latest tweet retrieved
							since_id = x[i].id_str;
							
							var marker = new google.maps.Marker({
								position: myLatlng,
								map: map,
								title: tweetText,
								icon: profileImageURL
							});
							markersList.push(marker);
							// <NOT WORKING>
							google.maps.event.addListener(markersList[markersList.length-1], 'click', function(){
								//infoWindow.open(map, markersList[markersList.length-1]);
							});
							// </NOT WORKING>
						}
						// remove the oldest tweets and their corresponding markers from map if length of queue exceeds 100.
						while(markersList.length>100){
							(markersList.shift()).setMap(null);
						}
					},
					error: function(){
						// show error message
						console.log("No response from the server!");
					}
				});
			}
			
			function initialize(){
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position){
						latitude = position.coords.latitude;
						longitude = position.coords.longitude;
						var myLatlng = new google.maps.LatLng(latitude, longitude);
						var mapOptions = {
							zoom: 14,
							center: myLatlng
						}
						map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
						
						var marker = new google.maps.Marker({
							position: myLatlng,
							map: map,
							title:"My Location"
						});
						var circleOptions = {
							strokeColor: '#B0C4DE',
							strokeOpacity: 0.8,
							strokeWeight: 2,
							fillColor: '#B0C4DE',
							fillOpacity: 0.35,
							map: map,
							center: myLatlng,
							radius: 1609.34
						};
						var circle = new google.maps.Circle(circleOptions);
					},
					function(msg){
						var s = document.getElementById("status");
						s.innerHTML = (typeof msg == "string") ? "<h1>"+msg+"</h1>" : "<h1>Failed to access your location!</h1>";
					});
				} else { 
					// show error message
				}
			}
			google.maps.event.addDomListener(window, 'load', initialize);

			setInterval(function(){
				plotRecentTweets();
			}, 10000);
			
		</script>
	</head>
	<body>
		<h1 style="text-align:center;">Local Tweet Map</h1>
		<p style="text-align:center;">Visualize the most recent tweets within 1-mile radius of your current location(Hover over the profile pic of the user to read the tweet text)</p>
		<div id="status"></div>
		<div id="map-canvas"></div>
	</body>
</html>