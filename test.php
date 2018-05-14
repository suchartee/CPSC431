<?php
require_once "functions.php";
require_once "config.php";

$db = configDB(5);

 ?>
 <!DOCTYPE html>
 <html>
 <head>
   <title>Basketball Roster Project</title>
   <link rel="stylesheet" href="style.css">
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
   <script type="text/javascript">
       $(function () {
           $("#search").change(function () {
               if ($(this).val() == "searchByFirstName" || $(this).val() == "searchByLastName" || $(this).val() == "searchByTeam") {
                   $("#criteria").show();
                   $("#searchButton").show();
               } else {
                   $("#criteria").hide();
               }
           });
       });
   </script>
 </head>

 <body>

   <ul class="drop_menu">
     <?php
     $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

     // reformat for the user's ease of reading
     //$nextAttemptFormat = date("l F d, Y. g:i:s A", strtotime($nextAttempt));
     //echo $nextAttemptFormat;

     // Hash a new password for storing in the database.
     // The function automatically generates a cryptographically safe salt.
     $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

     if (password_verify($password, $hashToStoreInDb)) {
      echo " YAY ";
    }

     // Check if the hash of the entered login password, matches the stored hash.
     // The salt and the cost factor will be extracted from $existingHashFromDb.
     //$isPasswordCorrect = password_hash("admin", PASSWORD_DEFAULT);
     //echo " \\n".$isPasswordCorrect;



     /*
     // select all button that this role can see
     $query = "SELECT Button.ID, Button.Name, Button.Link, Permission.MenuType FROM Permission JOIN Button ON Permission.ButtonID = Button.ID AND RoleID = 2 AND Permission != 0 ORDER BY Button.ID";
     $stmt = $db->prepare($query);
     //$stmt->bind_param("s", $username);
     $stmt->execute();
     $stmt->store_result();
     $stmt->bind_result($buttonID, $buttonName, $link, $menu);
     $stmt->data_seek(0);
     while( $stmt->fetch() ) {
      if ($menu == 0) {
        echo "<li><a href=\"".$link."\">". $buttonName ."</a></li>";
      } else if ($menu == 1) {  // is headmenu
        echo "<li><a href=\"".$link."\">". $buttonName ."</a><ul>";
      } else if ($menu == 2){ // is last submenu
        echo "<li><a href=\"".$link."\">". $buttonName ."</a></li></ul></li>";
      }
    }*/
     ?>
   </ul>

<form action="test.php" method="post">
   <div class="header">
     <select name="search" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
       <option value="viewAllPlayer">View All Player</option>
       <option value="searchByFirstName">Search By First Name</option>
       <option value="searchByLastName">Search By Last Name</option>
       <option value="searchByTeam">Search By Team Name</option>
     </select>
       <input type="date" name="datepicker" id="datepicker" />
     <input type="submit" name="searchButton" id="searchButton" value="Search"/>
   </div>
 </form>

<?php
if (isset($_POST["searchButton"])) {
  $date = date("Y-m-d", strtotime($_POST["datepicker"]));
  echo $date;
} else {
  echo "Yike";
}

?>
</body>
</html>



<!--ref: http://www.cssterm.com/css-menus/horizontal-css-menu/simple-drop-down-menu>
