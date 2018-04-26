<?php
// This file contains all useful functions

// Setting the timezone
date_default_timezone_set('America/Los_Angeles');

// Generating random password
function randPassword(){
  $password_special = '@#$%*-_+&';
  $password_num = '0123456789';
  $password_lower = 'abcdefghijklmnpqrstuwxyz';
  $password_upper = 'ABCDEFGHIJKLMNPQRSTUWXYZ';
  $password_rand = '@#$%*-_+&0123456789abcdefghijklmnpqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ';
  $password="";
  // generate 6 characters with atleast one lowercase, atleast one uppercase, atleast one number, and atleast one special character
  $password .= substr(str_shuffle($password_special), 0, 1);
  $password .= substr(str_shuffle($password_num), 0, 1);
  $password .= substr(str_shuffle($password_lower), 0, 1);
  $password .= substr(str_shuffle($password_upper), 0, 1);
  $password .= substr(str_shuffle($password_rand), 0, 2);

  $password = str_shuffle($password);
  return $password;
}




?>
