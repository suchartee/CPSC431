<?php
  session_start();
  require_once "config.php";
  require_once "functions.php";

  if (isset($_SESSION["authenticated"])) {
      header("Location: dashboard.php");
      exit;
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php include_once "default_navbar.php" ?>

  <form id="loginform" method="post" action="index.php">
    <div class="box">
      <h1>Login</h1>
      <input type="text" name="username" placeholder="Username" class="textbox" required/><br/>
      <input type="password" name="password" placeholder="Password" class="textbox" required/><br/>
      <input type="submit" class="btn_login" value="Login" name="login"/>
      <a href="register.php"><input type="button" class="btn_reg" value="Register" name="register"/></a>
    </div>
  </form>

  <p>Forgot your password? <a href="forgotpassword.php"><u style="color:#f1c40f;">Click Here!</u></a></p>

  <?php
  /*--------------------------------------This is the beginning of Suchartee Kitisopakul's part-----------------------------------------------------*/
  if (isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"])) {
      // sql injection
      $username = strtolower(trim(strip_tags(htmlspecialchars(htmlentities($_POST["username"])))));
      $password = strip_tags(trim(htmlspecialchars(htmlentities($_POST['password']))));

      // getting the hashed password from DB
      $db = configDB(5);
      $query = "SELECT Username, Password, Email, RoleID FROM Account WHERE Username = ?";
      if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($username, $hashedpasswordDB, $email, $role);
          if($stmt->num_rows > 0) {
            // has this username
            $stmt->data_seek(0);
            while ($stmt->fetch()) {
              // verfiy hashed password
              if (password_verify($password, $hashedpasswordDB)) {
                // successfully login
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;
                $_SESSION["role"] = $role;
                $_SESSION["authenticated"] = 'yes';
                $_SESSION["lastlogin"] = date("Y-m-d H:i:s");

                // update last login time
                $query = "UPDATE Account SET LastLogin = ? WHERE Username = ?";
                if ($stmt = $db->prepare($query)) {
                    $stmt->bind_param("ss", $_SESSION["lastlogin"], $username);
                    $stmt->execute();
                } else {
                    // prepare statement error
                    echo '<script type="text/javascript"> alert("Error!")</script>';
                    echo "<script>window.location = 'index.php';</script>";
                }
                // Reset record from the LoginAttempts table
                $nextAttempt = null;
                $attempt = 0;
                $query = "UPDATE LoginAttempts SET Attempt = ?, NextAttempt = ? WHERE Username = ?";

                if ($stmt = $db->prepare($query)) {
                  $stmt->bind_param("iss", $attempt, $nextAttempt ,$username);
                  $stmt->execute();
                  // redirect to dashboard.php = successfully logged in
                  echo "<script>window.location = 'dashboard.php';</script>";
                } else {
                  // prepare statement error
                  echo '<script type="text/javascript"> alert("Error!")</script>';
                  echo "<script>window.location = 'index.php';</script>";
                }
              } else {
                // wrong password
                // for sending email
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;
              }
            }
          } else {
              // this username is not in account table
              $_SESSION["username"] = $username;
          }

          // invalid login
          if ($username == 'admin') { // admin is not supposed to forget password
              echo '<script type="text/javascript"> alert("You are tricky! Do not do this again.") </script>';
              echo "<script>window.location = 'index.php';</script>";
          } else {
              // check if timeout isset
              if (isset($_SESSION["timeout"]) && !empty($_SESSION["timeout"])) {
                  if ($_SESSION["timeout"] > date("Y-m-d H:i:s")) {
                      // still cannot login
                      echo '<script type="text/javascript"> alert("You still have to wait. Be patient!")</script>';
                  } else {
                        unset($_SESSION["timeout"]);
                        echo '<script type="text/javascript"> alert("Thank you for being patient! Refresh this page and try again!") </script>';
                  }
                  echo "<script>window.location = 'index.php';</script>";
              } else {
                      // timeout is not set (Not exceed 3 times yet)
                      // check if that username has record in the loginAttempts table
                      $query = "SELECT Attempt, LastAttempt, NextAttempt FROM LoginAttempts WHERE Username = ?";
                      if ($stmt = $db->prepare($query)) {
                          $stmt->bind_param("s", $username);
                          $stmt->execute();
                          $stmt->store_result();
                          $stmt->bind_result($attempt, $lastAttempt, $nextAttempt);
                          $stmt->data_seek(0);
                          while ($stmt->fetch()){
                          }

                          if ($stmt->num_rows > 0) {
                              // there exists this username in the loginAttempts table
                              // check if the time is in 2 mins interval
                              $lastAttemptCheck = date("Y-m-d H:i:s");
                              $diff = strtotime($lastAttemptCheck) - strtotime($lastAttempt);

                              $disable = false;
                              if ($diff > 120) { // if different is > 2 minutes (120 seconds)
                                  // not in 2 mins interval
                                  $attempt = 1;
                              } else {
                                  // within 2 mins interval
                                  if ($attempt < 3) {
                                      $attempt = $attempt + 1;
                                  } else {
                                      $disable = true;
                                      // update the last attempt
                                      $lastAttempt = $lastAttemptCheck;
                                      $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                                      // reformat for the user's ease of reading
                                      $nextAttemptFormat = date("l F d, Y. g:i:s A", strtotime($nextAttempt));

                                      $query = "UPDATE LoginAttempts SET LastAttempt = ?, NextAttempt = ? WHERE Username = ?";
                                      if ($stmt = $db->prepare($query)) {
                                          $stmt->bind_param("sss", $lastAttempt, $nextAttempt, $username);
                                          $stmt->execute();
                                      } else {
                                          // prepare statement error
                                          echo '<script type="text/javascript"> alert("Error!")</script>';
                                          echo "<script>window.location = 'index.php';</script>";
                                      }
                                  }
                              }
                              // update in database if < 3 invalid login
                              if (!$disable) {
                                  // still has chance for invalid login
                                  $lastAttempt = date("Y-m-d H:i:s");
                                  $query = "UPDATE LoginAttempts SET Attempt = ?, LastAttempt = ? WHERE Username = ?";
                                  if ($stmt = $db->prepare($query)) {
                                      $stmt->bind_param("iss", $attempt, $lastAttempt, $username);
                                      $stmt->execute();
                                      echo "<script type=\"text/javascript\"> alert(\"Wrong username or password! Please try again\\nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
                                  } else {
                                      // prepare statement error
                                      echo '<script type="text/javascript"> alert("Error!")</script>';
                                      echo "<script>window.location = 'index.php';</script>";
                                  }
                              } else {
                                  // exceed the invalid attempt
                                  // check if email or username is in $_SESSION
                                  if (isset($_SESSION["username"]) && !empty($_SESSION["username"]) &&
                                  isset($_SESSION["email"]) && !empty($_SESSION["email"])) {
                                      // prepare for generating new password
                                      $password = randPassword();

                                      // store new password to database;
                                      // Hash Password
                                      $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

                                      $query = "UPDATE Account SET Password = ? WHERE Email = ?";
                                      if ($stmt = $db->prepare($query)) {
                                          $stmt->bind_param("ss", $hashedpassword, $_SESSION["email"]);
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
                                          $header = "From: <webmaster@basketballroster.com>";
                                          // send email to notify user + send temporary password to user
                                          mail($email, $subject, $message, $header);
                                          // set timeout for checking
                                          $_SESSION["timeout"] = $nextAttempt;
                                          echo "<script type=\"text/javascript\"> alert(\"You have exceed the number of allowed login attempts\\nPlease try again later\\nYour next login time will be ". $nextAttemptFormat . "\")</script>";
                                      } else {
                                          // prepare statement error
                                          echo '<script type="text/javascript"> alert("Error!")</script>';
                                          echo "<script>window.location = 'index.php';</script>";
                                      }
                                  } else {
                                      // for those whose account is not in account table = no email for sending
                                      $_SESSION["timeout"] = $nextAttempt;
                                      echo "<script type=\"text/javascript\"> alert(\"You have exceed the number of allowed login attempts\\nPlease try again later\\nYour next login time will be: ". $nextAttemptFormat ."\")</script>";
                                  }
                              }
                          } else {
                              // first time the username is found, record it in the database
                              $lastAttempt = date("Y-m-d H:i:s");
                              $attempt = 1;
                              $query = "INSERT INTO LoginAttempts (Username, Attempt, LastAttempt)VALUES (?, ?, ?)";
                              if ($stmt = $db->prepare($query)) {
                                  $stmt->bind_param("sis", $username, $attempt, $lastAttempt);
                                  $stmt->execute();
                                  echo "<script type=\"text/javascript\">alert(\"Wrong username or password! Please try again\\nCount: ". $attempt ." (Limit 3 Counts) \")</script>";
                              } else {
                                  // prepare statement error
                                  echo '<script type="text/javascript"> alert("Error!")</script>';
                                  echo "<script>window.location = 'index.php';</script>";
                              }
                          }
                      } else {
                          // prepare statement error
                          echo '<script type="text/javascript"> alert("Error!")</script>';
                          echo "<script>window.location = 'index.php';</script>";
                      }
                  }
              }
          } else {
              // prepare statement error
              echo '<script type="text/javascript"> alert("Error!")</script>';
              echo "<script>window.location = 'index.php';</script>";
      }
    }
/*--------------------------------------This is the end of Suchartee Kitisopakul's part-----------------------------------------------------*/
  ?>
</body>
</html>
