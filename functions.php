<?php
// This file contains all useful functions

// Setting the timezone
date_default_timezone_set('America/Los_Angeles');

function checkPermission($roleid, $url) {
  require_once "config.php";
  // connect db
  $db = configDB($roleid);
    // get roleid
    switch ($roleid) {
      case 2: // operator
        // switch 2 = operator
        // check if the url is in buttons_operator?
        $query = "SELECT * FROM Buttons_operator WHERE Link = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $url);
      break;
      case 3: // manager
        // switch 3 = manager
        // check if the url is in buttons_operator? and buttons_manager?
        $query = "SELECT * FROM Buttons_operator WHERE Link = ? UNION SELECT * FROM Buttons_manager WHERE Link = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $url, $url);
      break;
      case 4: // admin
        // switch 4 = admin
        // check if the url is in buttons_operator? and buttons_manager? and buttons_admin
        $query = "SELECT * FROM Buttons_operator WHERE Link = ? UNION SELECT * FROM Buttons_manager WHERE Link = ? UNION SELECT * FROM Buttons_admin WHERE Link = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sss", $url, $url, $url);
      break;
      default:
        $query = "SELECT * FROM Buttons_observer WHERE Link = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $url);
      break;
    }
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0) {
      return true;
    } else {
      return false;
    }
}


// Generating random password
function randPassword(){
  $password_special = '@#$%*-_+';
  $password_num = '0123456789';
  $password_lower = 'abcdefghijklmnpqrstuwxyz';
  $password_upper = 'ABCDEFGHIJKLMNPQRSTUWXYZ';
  $password_rand = '@#$%*-_+0123456789abcdefghijklmnpqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ';
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

function get_buttons($button_table) {
  $db = configDB($_SESSION["role"]);

  // Get buttons for observer permissions
  $button_name = "";
  $button_link = "";

  $query = "SELECT Name, Link FROM ".$button_table;

  // Checks if the user has permissions to access
  // button privileges for a table. If not, database returns a false
  // answer and the conditional is skipped
  if ($stmt = $db->prepare($query)){
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($button_name, $button_link);

    /*echo  "<div class=\"btn-group\">";
    while($stmt->fetch()) {
      echo "<a href=\"".$button_link."\">";
      echo    "<button>".$button_name."</button>";
      echo "</a>";
    }
    echo  "</div>";*/
    $count = 0;
    while($stmt->fetch()) {
      $button[$count]['buttonname'] = $button_name;
      $button[$count]['buttonlink'] = $button_link;

      // filter the keyword for nicer arrangement
      if (strpos(strtolower($button_name), 'player') !== false) {
        $button[$count]['type'] = 1;
      } else if (strpos(strtolower($button_name), 'team') !== false) {
        $button[$count]['type'] = 2;
      } else if (strpos(strtolower($button_name), 'coach') !== false) {
        $button[$count]['type'] = 3;
      } else if (strpos(strtolower($button_name), 'match') !== false) {
        $button[$count]['type'] = 4;
      } else if (strpos(strtolower($button_name), 'statistic') !== false) {
        $button[$count]['type'] = 5;
      } else if (strpos(strtolower($button_name), 'account') !== false) {
        $button[$count]['type'] = 6;
      } else {
        $button[$count]['type'] = 0;
      }

      $count++;
    }
    // return all buttons retrieved from query
    return $button;
  }
}

function get_all_buttons() {
    $button_observer = get_buttons("buttons_observer");
    $button_operator = get_buttons("buttons_operator");
    $button_manager = get_buttons("buttons_manager");
    $button_admin = get_buttons("buttons_admin");

    // some of these buttons may be null, make sure it is an array so that we can combine
    $buttons = array();
    if(is_array($button_observer)) {
        $buttons = array_merge($buttons, $button_observer);
    }
    if(is_array($button_operator)) {
        $buttons = array_merge($buttons, $button_operator);
    }
    if(is_array($button_manager)) {
        $buttons = array_merge($buttons, $button_manager);
    }
    if(is_array($button_admin)) {
        $buttons = array_merge($buttons, $button_admin);
    }


    $stack0 = array();
    $stack1 = array();
    $stack2 = array();
    $stack3 = array();
    $stack4 = array();
    $stack5 = array();
    $stack6 = array();

    // divide into 6 arrays for the arrangement
    foreach ($buttons as $button) {
      switch($button['type']) {
        case 0: array_push($stack0, $button);
        break;
        case 1: array_push($stack1, $button);
        break;
        case 2: array_push($stack2, $button);
        break;
        case 3: array_push($stack3, $button);
        break;
        case 4: array_push($stack4, $button);
        break;
        case 5: array_push($stack5, $button);
        break;
        case 6: array_push($stack6, $button);
        break;
      }
    }

    //$allbuttons = array_merge($stack0, $stack1,$stack2,$stack3,$stack4,$stack5,$stack6);
    /*foreach ($allbuttons as $allbutton) {
      echo $allbutton['type']." + ";
      echo $allbutton['buttonname']." + ";
      echo $allbutton['buttonlink']." --- ";
    }*/

    echo "<div class=\"btn-group\">";
    if (!empty($stack0)) {
      echo "<h3>Main Menu</h3>";
    }
    foreach($stack0 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack1)) {
      echo "<h3>Player</h3>";
    }
    foreach($stack1 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack2)) {
      echo "<h3>Team</h3>";
    }
    foreach($stack2 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack3)) {
      echo "<h3>Coach</h3>";
    }
    foreach($stack3 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack4)) {
      echo "<h3>Match</h3>";
    }
    foreach($stack4 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack5)) {
      echo "<h3>Statistics</h3>";
    }
    foreach($stack5 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

    echo "<div class=\"btn-group\">";
    if (!empty($stack6)) {
      echo "<h3>Privileges</h3>";
    }
    foreach($stack6 as $stack) {
      echo "<a href=\"".$stack['buttonlink']."\">";
      echo    "<button>".$stack['buttonname']."</button>";
      echo "</a>";
    }
    echo "</div><br/>";

}


?>
