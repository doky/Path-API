<?php

include('class.path.php');

$api = new path_wrapper();

$user = $api->login('YOUR_PATH_USERNAME', 'YOUR_PATH_PASSWORD');

echo $api->user->id;

$data = $api->getHome();

print_r($data);

?>