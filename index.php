<?php
  session_start();
  require_once "config.php";

  if (isset($_SESSION["authenticated"])) {
      header("Location: dashboard.php");
      exit;
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php include_once "default_navbar.php" ?>

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
          $_SESSION["authenticated"] = 'yes';
          $_SESSION["lastlogin"] = date("Y-m-d H:i:s");
        }


        // update last login time
        $query = "UPDATE Account SET LastLogin = ? WHERE Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $_SESSION["lastlogin"], $username);
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

        // Close the db connection using manager credentials
        $db->close();

        echo "<script>window.location = 'dashboard.php';</script>";
    }
	   else {
    /* This is the beginning of Suchartee Kitisopakul's part */
    if (isset($_SESSION["timeout"]) && !empty($_SESSION["timeout"])) {
      if ($_SESSION["timeout"] > date("Y-m-d H:i:s")) {
        // cannot login
        echo '<script type="text/javascript"> alert("You still have to wait. Be patient!")</script>';
      } else {
        // can log in now
        $lastAttempt = $_SESSION["timeout"];
        $attempt = 0;
        unset($_SESSION["timeout"]);
        // update the attempt to 0
        $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
        $stmt->execute();

        if ($username == 'admin') {
          echo '<script type="text/javascript"> alert("You are tricky! Do not do this again.") </script>';
        } else {
          // check if the username is existed in the $database
          $query = "SELECT Username, Email FROM Account WHERE Username = ?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($username, $email);

          if($stmt->num_rows > 0) {
            $stmt->data_seek(0);
            while ($stmt->fetch()){
              $_SESSION["username"] = $username;
              $_SESSION["email"] = $email;
            }
            // check if that username has record in the loginAttempts or not
            $query = "SELECT Attempt, LastAttempt, NextAttempt FROM LoginAttempts WHERE Username = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($attempt, $lastAttempt, $nextAttempt);
            $stmt->data_seek(0);
            while($stmt->fetch()){
              // fetch the latest one
            }

            if($stmt->num_rows > 0) {
                // there exists this username in the database
                // check if the time is in 2 mins interval
                $lastAttemptCheck = date("Y-m-d H:i:s");
                $diff = strtotime($lastAttemptCheck) - strtotime($lastAttempt);

                if ($diff > 120) { // if different is > 2 minutes (120 seconds)
                  // not in 2 mins interval
                  $attempt = 1;
                  $disable = false;
                } else {
                    // within 2 mins interval
                    if ($attempt < 3) {
                      $attempt = $attempt + 1;
                      $disable = false;
                    }
                    else {
                      $disable = true;
                      // update the last attempt
                      $lastAttempt = $lastAttemptCheck;
                      $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                      // reformat for the user's ease of reading
                      $nextAttemptFormat = date("D F d, Y at g:i:s A", strtotime($nextAttempt));

                      $query = "UPDATE LoginAttempts SET LastAttempt = ?, NextAttempt = ? WHERE Username = ?";
                      $stmt = $db->prepare($query);
                      $stmt->bind_param("sss", $lastAttempt, $nextAttempt, $username);
                      $stmt->execute();
                    }
                }
                // update in database if < 4 invalid login
                if (!$disable) {
                  $lastAttempt = date("Y-m-d H:i:s");
                  $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
                  $stmt = $db->prepare($query);
                  $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
                  $stmt->execute();
                  echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
                } else {
                  // send email to notify user + send temporary password to user
                  // prepare for generating new password

                  $password = randPassword();

                  // store new password to database;
                  // dont forget to hash password!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

                  $query = "UPDATE Account SET Password = ? WHERE Email = ?";
                  $stmt = $db->prepare($query);
                  $stmt->bind_param("ss", $password, $_SESSION["email"]);
                  $stmt->execute();

                  // prepare for Email
                  $subject = "Basketball Roster: Suspicious login prevented!";
                  $message =
                  "Hi,

                  Someone recently used wrong passwords to try to login to your account - ". $_SESSION["username"] ."
                  Please use this password to login instead. Remember to change the password immediately after you logged in.

                  ".$password."

                  Sincerely,
                  Webmaster
                  CPSC431 Basketball Roster
                  ";
                  $header = "From: <webmaster@CPSC431basketballroster.com>";

                  // send Email
                  $result = mail($email, $subject, $message, $header);

                  $_SESSION["timeout"] = $nextAttempt;

                  echo "<script type=\"text/javascript\"> alert(\"You have exceed the number of allowed login attempts.nPlease try again later.nnYour next login time will be ". $nextAttemptFormat . "\")</script>";
                }
            } else { // first time the username is found, record it in the database
              $lastAttempt = date("Y-m-d H:i:s");
              $attempt = 1;
              $query = "INSERT INTO LoginAttempts (Username, Attempt, LastAttempt)VALUES (?, ?, ?)";
              $stmt = $db->prepare($query);
              $stmt->bind_param("sis", $username, $attempt, $lastAttempt);
              $stmt->execute();
              echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
            }
          } else {
            // no username in database, but pretend that this username is in the database
            // check if that non-existed username has record in the loginAttempts or not
            $query = "SELECT Attempt, LastAttempt FROM LoginAttempts WHERE Username = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($attempt, $lastAttempt);
            $stmt->data_seek(0);
            while($stmt->fetch()){
              // fetch the latest one
            }
            if($stmt->num_rows > 0) {
              // there exists this non-existed username in the database
              // check if the time is in 2 mins interval
              $lastAttemptCheck = date("Y-m-d H:i:s");
              $diff = strtotime($lastAttemptCheck) - strtotime($lastAttempt);
              if ($diff > 120) { // if different is > 2 minutes (120 seconds)
                // not in 2 mins interval
                $attempt = 1;
                $disable = false;
              } else {
                // within 2 mins interval
                if ($attempt < 3) {
                  $attempt = $attempt + 1;
                  $disable = false;
                }
                else {
                  $disable = true;
                  // update the last attempt
                  $lastAttempt = $lastAttemptCheck;
                  $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                  // reformat for the user's ease of reading
                  $nextAttemptFormat = date("D F d, Y at g:i:s A", strtotime($nextAttempt));

                  $query = "UPDATE LoginAttempts SET LastAttempt = ?, NextAttempt = ? WHERE Username = ?";
                  $stmt = $db->prepare($query);
                  $stmt->bind_param("sss", $lastAttempt, $nextAttempt, $username);
                  $stmt->execute();
                }
              }
              // update in database if < 4 invalid login
              if (!$disable) {
                $lastAttempt = date("Y-m-d H:i:s");
                $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
                $stmt->execute();
                echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
              } else {
                // need to set duration 5 minutes until the user can login again
                // get the latest time?

                echo '<script type="text/javascript"> alert("You have exceed the number of allowed login attempts.nPlease try again later.")</script>';
              }
            } else { // first time the username is found, record it in the database
              $lastAttempt = date("Y-m-d H:i:s");
              $attempt = 1;
              $query = "INSERT INTO LoginAttempts VALUES (?, ?, ?)";
              $stmt = $db->prepare($query);
              $stmt->bind_param("sis", $username, $attempt, $lastAttempt);
              $stmt->execute();
              echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
            }
          }
        }
      }
    } else {
      /* if ($username == 'admin') {
        echo '<script type="text/javascript"> alert("You are tricky! Do not do this again.") </script>';
      } else {
        // check if the username is existed in the $database
        $query = "SELECT Username, Email FROM Account WHERE Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($username, $email);

        if($stmt->num_rows > 0) {
          $stmt->data_seek(0);
          while ($stmt->fetch()){
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;
          }
          // check if that username has record in the loginAttempts or not
          $query = "SELECT Attempt, LastAttempt, NextAttempt FROM LoginAttempts WHERE Username = ?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($attempt, $lastAttempt, $nextAttempt);
          $stmt->data_seek(0);
          while($stmt->fetch()){
            // fetch the latest one
          }

          if($stmt->num_rows > 0) {
              // there exists this username in the database
              // check if the time is in 2 mins interval
              $lastAttemptCheck = date("Y-m-d H:i:s");
              $diff = strtotime($lastAttemptCheck) - strtotime($lastAttempt);

              if ($diff > 120) { // if different is > 2 minutes (120 seconds)
                // not in 2 mins interval
                $attempt = 1;
                $disable = false;
              } else {
                  // within 2 mins interval
                  if ($attempt < 3) {
                    $attempt = $attempt + 1;
                    $disable = false;
                  }
                  else {
                    $disable = true;
                    // update the last attempt
                    $lastAttempt = $lastAttemptCheck;
                    $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                    // reformat for the user's ease of reading
                    $nextAttemptFormat = date("D F d, Y at g:i:s A", strtotime($nextAttempt));

                    $query = "UPDATE LoginAttempts SET LastAttempt = ?, NextAttempt = ? WHERE Username = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("sss", $lastAttempt, $nextAttempt, $username);
                    $stmt->execute();
                  }
              }
              // update in database if < 4 invalid login
              if (!$disable) {
                $lastAttempt = date("Y-m-d H:i:s");
                $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
                $stmt->execute();
                echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
              } else {
                // send email to notify user + send temporary password to user
                // prepare for generating new password
                $password = randPassword();

                // store new password to database;
                // dont forget to hash password!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

                $query = "UPDATE Account SET Password = ? WHERE Email = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("ss", $password, $_SESSION["email"]);
                $stmt->execute();

                // prepare for Email
                $subject = "Basketball Roster: Suspicious login prevented!";
                $message =
                "Hi,

                Someone recently used wrong passwords to try to login to your account - ". $username ."
                Please use this password to login instead. Remember to change the password immediately after you logged in.

                ".$password."

                Sincerely,
                Webmaster
                CPSC431 Basketball Roster
                ";
                $header = "From: <webmaster@CPSC431basketballroster.com>";

                // send Email
                $result = mail($email, $subject, $message, $header);

                $_SESSION["timeout"] = $nextAttempt;

                echo "<script type=\"text/javascript\"> alert(\"You have exceed the number of allowed login attempts.nPlease try again later.nnYour next login time will be ". $nextAttemptFormat . "\")</script>";
              }
          } else { // first time the username is found, record it in the database
            $lastAttempt = date("Y-m-d H:i:s");
            $attempt = 1;
            $query = "INSERT INTO LoginAttempts (Username, Attempt, LastAttempt) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sis", $username, $attempt, $lastAttempt);
            $stmt->execute();
            echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
          }
        } else {
          // no username in database, but pretend that this username is in the database
          // check if that non-existed username has record in the loginAttempts or not
          $query = "SELECT Attempt, LastAttempt FROM LoginAttempts WHERE Username = ?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($attempt, $lastAttempt);
          $stmt->data_seek(0);
          while($stmt->fetch()){
            // fetch the latest one
          }
          if($stmt->num_rows > 0) {
            // there exists this non-existed username in the database
            // check if the time is in 2 mins interval
            $lastAttemptCheck = date("Y-m-d H:i:s");
            $diff = strtotime($lastAttemptCheck) - strtotime($lastAttempt);
            if ($diff > 120) { // if different is > 2 minutes (120 seconds)
              // not in 2 mins interval
              $attempt = 1;
              $disable = false;
            } else {
              // within 2 mins interval
              if ($attempt < 3) {
                $attempt = $attempt + 1;
                $disable = false;
              }
              else {
                $disable = true;
                // update the last attempt
                $lastAttempt = $lastAttemptCheck;
                $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                // reformat for the user's ease of reading
                $nextAttemptFormat = date("D F d, Y at g:i:s A", strtotime($nextAttempt));

                $query = "UPDATE LoginAttempts SET LastAttempt = ?, NextAttempt = ? WHERE Username = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sss", $lastAttempt, $nextAttempt, $username);
                $stmt->execute();
              }
            }
            // update in database if < 4 invalid login
            if (!$disable) {
              $lastAttempt = date("Y-m-d H:i:s");
              $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
              $stmt = $db->prepare($query);
              $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
              $stmt->execute();
              echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
            } else {
              // need to set duration 5 minutes until the user can login again
              // get the latest time?

              echo '<script type="text/javascript"> alert("You have exceed the number of allowed login attempts.nPlease try again later.")</script>';
            }
          } else { // first time the username is found, record it in the database
            $lastAttempt = date("Y-m-d H:i:s");
            $attempt = 1;
            $query = "INSERT INTO LoginAttempts (Username, Attempt, LastAttempt) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sis", $username, $attempt, $lastAttempt);
            $stmt->execute();
            echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again.nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
          }
        }
      }
    }
  }*/
	   /* This is the end of Suchartee Kitisopakul's part */
	 }
  }
 }
    ?>


</body>
</html>
