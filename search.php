<?php
	session_start();
	require_once('library/twitteroauth.php');
	include('config.php');
	
?>

<?php
	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $OAUTH_TOKEN, $OAUTH_TOKEN_SECRET);
	if(isset($_GET['since_id']) && $_GET['since_id']!='0'){
		$tweets = $connection->get('https://api.twitter.com/1.1/search/tweets.json?q=&geocode='.$_GET['latitude'].','.$_GET['longitude'].',1mi&result_type=recent&since_id='.$_GET['since_id']);
	}
	else if(isset($_GET['since_id']) && $_GET['since_id']=='0'){
		$tweets = $connection->get('https://api.twitter.com/1.1/search/tweets.json?q=&geocode='.$_GET['latitude'].','.$_GET['longitude'].',1mi&result_type=recent');
	}
	else{
		$tweets = $connection->get('https://api.twitter.com/1.1/search/tweets.json?q=&geocode=28.602095600000002,77.03976709999999,1mi&result_type=recent');
	}
	
	foreach($tweets as $tweet){
		break;
	}
	echo json_encode($tweet);

?>
