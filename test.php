<?php
date_default_timezone_set('America/Los_Angeles');
$timenow = date("Y-m-d H:i:s");
$nextTime = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$nextTime = date("D F d, Y \a\\t g:i:s A", strtotime($nextTime));

echo $timenow . " and " . $nextTime;

/*
$email = 'p2inglez@gmail.com';
$subject = 'Hellooooo';
$message = 'hey this is a test';


$result = mail($email, $subject, $message);
if ($result){
  echo 'yay';
} else {
  echo 'nay';
}*/

?>
