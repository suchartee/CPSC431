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

function get_buttons($button_table) {
  $db = configDB($_SESSION["role"]);

  // Get buttons for observer permissions
  $button_name = "";
  $button_link = "";

  // Even or odd counter to determine <div class>
  // as smallboxleft or smallboxright
  $counter = 0;

  $query = "SELECT Name, Link FROM ".$button_table;

  // Checks if the user has permissions to access
  // button privileges for a table. If not, database returns a false
  // answer and the conditional is skipped
  if ($stmt = $db->prepare($query)){
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($button_name, $button_link);

    while($stmt->fetch()) {
      echo "<a href=\"".$button_link."\">";
      echo  "<div class=\"";

      if ($counter % 2 != 0) {
        echo "smallboxright";
      } else {
        echo "smallboxleft";
      }

      echo    "\">";
      echo    "<h3>".$button_name."</h3>";
      echo    "<hr/>";
      // Button descriptions
      //echo    "<h6>";
      //echo      "All the player related is here.";
      //echo    "</h6>";
      echo  "</div>";
      echo "</a>";

      ++$counter;
    }
  }
}

function get_all_buttons() {
    get_buttons("buttons_observer");
    get_buttons("buttons_operator");
    get_buttons("buttons_manager");
    get_buttons("buttons_admin");
}
?>
