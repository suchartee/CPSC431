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
  <title>Modify User Account Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Account.ID, Username, Email, RoleID, Role.RoleName, QuestionNum, Question.Question, Answer FROM Account JOIN Role on RoleID = Role.ID JOIN Question ON QuestionNum = Question.ID ORDER BY RoleName, Username";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($accountid, $username, $email, $roleid, $rolename, $questionid, $question, $answer);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Modify User Account</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role Name</th>
              <th>Security Question</th>
              <th>Answer</th>
              <th>Action</th>
            </tr>";

        while( $stmt->fetch() ) {
          $row = array('id'=>$count++, 'accountid'=>$accountid, 'username'=>$username, 'email'=>$email, 'roleid'=>$roleid, 'rolename'=>$rolename, 'questionid'=>$questionid, 'question'=>$question, 'answer'=>$answer);
          echo "<tr>
            <td>". $row['id'] ."</td>
            <td>". $row['username'] ."</td>
            <td>". $row['email'] ."</td>
            <td>". $row['rolename'] ."</td>
            <td>". $row['question'] ."</td>
            <td>". $row['answer'] ."</td>
            <td><a href=\"changeuseraccountinfo.php?accountid=".$row['accountid']."&username=".$row['username']."&email=".$row['email']."&roleid=".$row['roleid']."&rolename=".$row['rolename']."&questionid=".$row['questionid']."&question=".$row['question']."&answer=".$row['answer']."\">Change</a>
          </tr>";
        }
    echo "</table>
    </div>";
    ?>


</body>
</html>
