<html>

<?php

echo "abc";
require_once("facebook.php");
$config = array();
$config['app_key'] = getenv('api_key');
$config['app_secret'] = getenv('api_secret');
$facebook = new Facebook($config);

echo $config['app_key']." ".$config['app_secret'];


?>

</html>