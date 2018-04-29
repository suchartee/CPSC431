<?php
  session_start();
  require_once "config.php"
?>

<!DOCTYPE html>
<html>
<head>
  <title>Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <!-- Navigation menu on top -->
  <ul class="drop_menu">
    <li><a href='index.php'>Home</a></li>
    <li><a href='about.php'>About</a></li>
  </ul>

  <form method="post" action="index.php">
    <div class="box">
      <h1>Login</h1>
      <input type="text" name="username" placeholder="Username" class="textbox" required/><br/>
      <input type="password" name="password" placeholder="Password" class="textbox" required/><br/>
      <input type="submit" class="btn_login" value="Login" name="login"/>
      <a href="register.php"><input type="button" class="btn_reg" value="Register" name="register"/></a>
    </div>
  </form>

  <p>Forgot your password? <a href="forgotPassword.php"><u style="color:#f1c40f;">Click Here!</u></a></p>

  <?php
  if ((isset($_POST["username"]) && !empty($_POST["username"])) && (isset($_POST["password"]) && (!empty($_POST["password"])))) {
    // sql injection
    $username = strtolower(strip_tags(htmlspecialchars($_POST["username"])));
    $password = strip_tags(htmlspecialchars($_POST['password']));

    // DONT FORGET TO HASH PASSWORD !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $db = configDB(5);

    $query = "SELECT Username, Email, RoleID FROM Account WHERE Username = ? AND Password = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username, $email, $role);

    if($stmt->num_rows > 0) {
        // there exists this username and password, login successfully
        $stmt->data_seek(0);
        while ($stmt->fetch()){
          $_SESSION["username"] = $username;
          $_SESSION["email"] = $email;
          $_SESSION["role"] = $role;
        }

        // update last login time
        $query = "UPDATE Account SET LastLogin = now() WHERE Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Previous db connection using accountauth does not Have
        // permission to access LoginAttempts table. Therefore,
        // we open a new db connection with manager credentials
        $db = configDB(3);

        // Reset record from the LoginAttempts table
        $nextAttempt = null;
        $attempt = 0;
        $query = "UPDATE LoginAttempts SET Attempt = ?, NextAttempt = ? WHERE Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iss", $attempt, $nextAttempt ,$username);
        $stmt->execute();

        echo "<script>window.location = 'dashboard.php';</script>";
    }
    /* Alice's part */
  }
    ?>


</body>
</html>
