<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";

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

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
  ?>

<div class="header">
  Welcome, <?php echo $_SESSION["username"]?>!
</div>

<?php
  echo "<div class=\"container\">";

  get_all_buttons();

  echo "</div>";
?>

</body>
</html>
