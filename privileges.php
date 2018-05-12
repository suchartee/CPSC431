<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";
  require_once "functions.php";

  if (!checkPermission($_SESSION["role"], basename($_SERVER['PHP_SELF']))) {
    echo "<script type=\"text/javascript\"> alert(\"You cannot see this page\")</script>";
    echo "<script>window.location = 'dashboard.php';</script>"; // redirect to index.php (login page)
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Account Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript">
      $(function showfield() {
          $("#search").change(function () {
            if ($(this).val() == "searchByUsername" || $(this).val() == "searchByEmail") {
                $("#searchrole").hide();
                $("#criteria").show();
            } else if ($(this).val() == "searchByRole") {
                $("#searchrole").show();
                $("#criteria").hide();
            } else {
                $("#criteria").hide();
                $("#searchrole").hide();
            }
          });
      });

  </script>
</head>

<body>
  <?php
    include 'logged_navbar.php';
    // prepare for getting role name from $db
    $db = configDB($_SESSION["role"]);
    $query = "SELECT * FROM Role";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($roleid, $rolename);
    }
    ?>
    <div class="header">
      <form action="privileges.php" method="post">
      <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
        <option value="viewAllPlayer">View All Account</option>
        <option value="searchByUsername">Search By Username</option>
        <option value="searchByEmail">Search By Email</option>
        <option value="searchByRole">Search By Role</option>
        <option value="searchByLastLogin">Search By Login Time in The Past 48 Hours</option>
      </select>
      <input type="text" name="textbox" id="criteria" style="display: none;" />

      <select name="role" id="searchrole" style="display: none;"/>
        <option value="0" disabled selected value>Select Role</option>
        <?php
        $stmt->data_seek(0);
        while ($stmt->fetch()) {
            echo "<option value=".$roleid.">".ucwords($rolename)."</option>";
        }
        ?>
      </select>
      <input type="submit" id="searchButton" name="searchbutton" value="Search"/>
    </form>
    </div>


    <?php
    // by default, all player is displayed
    if (empty($_POST["searchbutton"])) {
      $count = 1;
      $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID ORDER BY RoleName, Username";
      $db = configDB($_SESSION["role"]);
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($accountid, $username, $email, $rolename, $question, $answer, $lastlogin);

        $stmt->data_seek(0);
        echo "<div class=\"header\" style=\"display:table;\">All Account</div>
              <div class=\"container\">
              <table>
                <tr>
                  <th>No.</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role Name</th>
                  <th>Security Question</th>
                  <th>Answer</th>
                  <th>Last Login</th>
                </tr>";
            while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'accountid'=>$accountid, 'username'=>$username, 'email'=>$email,'rolename'=>$rolename, 'question'=>$question, 'answer'=>$answer, 'lastlogin'=>$lastlogin);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['username'] ."</td>
                    <td>". $row['email'] ."</td>
                    <td>". $row['rolename'] ."</td>
                    <td>". $row['question'] ."</td>
                    <td>". $row['answer'] ."</td>
                    <td>". $row['lastlogin'] ."</td>
                  </tr>";
            }
        echo "</table>
        </div>";
      }
    } else {
        // when clicking the button
        if (isset($_POST["searchbox"]) && !empty($_POST["searchbox"])) {
          // check what kind of selection is
          switch($_POST["searchbox"]) {
            case "searchByUsername":
              $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID WHERE Username LIKE ? ORDER BY Username";
            break;
            case "searchByEmail":
              $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID WHERE Email LIKE ? ORDER BY Email";
            break;
            case "searchByRole":
              $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID WHERE Role.ID = ? ORDER BY RoleName, Username";
            break;
            case "searchByLastLogin":
              $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 48 HOUR) ORDER BY Username";
            break;
            default:
              $query = "SELECT Account.ID, Username, Email, Role.RoleName, Question.Question, Answer, LastLogin FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID ORDER BY RoleName, Username";
            break;
          }
          $count = 1;
          $db = configDB($_SESSION["role"]);
          if ($stmt = $db->prepare($query)) {
            // check SQL injection for textbox, if any
            if (isset($_POST["textbox"]) && !empty($_POST["textbox"])) {
              $searchtextbox = lcfirst(strip_tags(htmlspecialchars($_POST["textbox"]))) . "%"; // First letter uppercase and search anything that starts with the value in textbox
              $stmt->bind_param("s", $searchtextbox);
            } else if (isset($_POST["role"]) && !empty($_POST["role"])) {
              $searchroleid = $_POST["role"];
              $stmt->bind_param("i", $searchroleid);
            }
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($accountid, $username, $email, $rolename, $question, $answer, $lastlogin);

            $stmt->data_seek(0);
            echo "<div class=\"header\" style=\"display:table;\">All Account</div>
                  <div class=\"container\">
                  <table>
                    <tr>
                      <th>No.</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Role Name</th>
                      <th>Security Question</th>
                      <th>Answer</th>
                      <th>Last Login</th>
                    </tr>";
                while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'accountid'=>$accountid, 'username'=>$username, 'email'=>$email, 'rolename'=>$rolename, 'question'=>$question, 'answer'=>$answer, 'lastlogin'=>$lastlogin);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['username'] ."</td>
                    <td>". $row['email'] ."</td>
                    <td>". $row['rolename'] ."</td>
                    <td>". $row['question'] ."</td>
                    <td>". $row['answer'] ."</td>
                    <td>". $row['lastlogin']."</td>
                  </tr>";
                }
            echo "</table>
            </div>";
          }
        }
      }
    ?>

</body>
</html>
