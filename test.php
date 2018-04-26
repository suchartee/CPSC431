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
 </head>

 <body>
   <ul class="drop_menu">
     <?php
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
    }
     ?>
   </ul>
</body>
</html>


<!--ref: http://www.cssterm.com/css-menus/horizontal-css-menu/simple-drop-down-menu>
