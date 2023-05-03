<?php
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Connect to database
include("sys_page/db_connect.php");

//Get info about user
$user_id = $_SESSION['user_id'];
$school_code = $_SESSION['school_code'];

$sql = "SELECT * FROM " . $school_code . "_students WHERE id=" . $user_id;
$result = $conn->query($sql); //Query database
if ($result->num_rows > 0) { //If the number of rows are not zero
  $data = $result->fetch_assoc();
  $display_name = $_SESSION['user'];
  $desc = $data['description'];
  $hours_tutored = $data['hours_tutored'];
  $sessions_tutored = $data['sessions_tutored'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    
    <button id=profile_edit_button>Edit</button>
    <p><?php echo $display_name;?></p>
    <h3>Studying</h3>
    <h3>Tutoring</h3>
    <h3>Description</h3>
    <p id="profile_desc_text"><?php echo $desc; ?></p>
    <div class="profile_statistics">
      <h3>Statistics</h3>
      <p>Hours spent tutoring: <?php echo $hours_tutored; ?></p>
      <p>Sessions tutored: <?php echo $sessions_tutored; ?></p>
    </div>

    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>