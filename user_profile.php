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


//Get the info about the users subjects
$subject_tutor_sql = "SELECT * FROM `6969_subjects_tutor` WHERE tutor_id=$user_id";
$subject_tutor_result = $conn->query($subject_tutor_sql); //Query database
if ($subject_tutor_result->num_rows > 0) { //If the number of rows are not zero
  $subject_array = [];
  $i = 0;
  while($row = $subject_tutor_result->fetch_assoc()) {
    //Check which subject ID to use (if = 0 ignore ID)
    $is_global = false;
    if($row['global_subject_id'] == "0") {$is_global = false;}
    else {$is_global = true;}

    if($is_global) {$subject_tutor_id = $row['global_subject_id'];}
    else {$subject_tutor_id = $row['local_subject_id'];}
    //Add the id to an array
    $subject_array[$i][0] = $subject_tutor_id;
    //Add the current state of is_global
    $subject_array[$i][1] = $is_global;

    $i += 1;
  }
  $no_subjects = false;

  //Get the subject names as an array
  for($i=0;$i<sizeof($subject_array);$i++)
  {
    //Change the query type depending if the subject is global or local

    //Global
    if($subject_array[$i][1] == True) {$subject_name_sql = "SELECT * FROM `6969_subjects_tutee` INNER JOIN `subjects` ON `6969_subjects_tutee`.`global_subject_id`=`subjects`.`id` WHERE `6969_subjects_tutee`.`tutee_id`=$user_id;";}
    
    //Local
    else {$subject_name_sql = "SELECT * FROM `6969_subjects_tutee` INNER JOIN `6969_subjects` ON `6969_subjects_tutee`.`local_subject_id`=`6969_subjects`.`id` WHERE `6969_subjects_tutee`.`tutee_id`=$user_id;";}
    

    //Query the database to find the subject name
    $subject_name_result = $conn->query($subject_name_sql); //Query database
    if ($subject_name_result->num_rows > 0) { //If the number of rows are not zero
      $subject_name_array = [];
      $i = 0;
      while($row = $subject_name_result->fetch_assoc()) {
        $subject_array[$i][2] = $row['name'];
        $i += 1;
      }
    }
  }
  
}
else
{
  //If no subjects are found
  $no_subjects = true;
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

    <div class="container text-center border border-3 border-dark extra_rounded">
              <div class="row">
                  <h3 class="col text-center py-3 m-0">Studying</h3>
                  <div class="col purple_box extra_rounded_r"></div>
              </div>
      </div>
      
      <div>
        <div class="row" id="studying">
          <?php
            //Get number of items in subject array
            if($no_subjects == false)
            {
              for($x=0;$x<sizeof($subject_array);$x++)
              {
                echo "<div class='col'><p class='nowrap'>";
                echo $subject_array[$x][2];
                echo "</p><img class='hide_on_start edit_cross' src='sys_img/icons8-x-100.png' alt=''></div>";
              }
            }
          ?>
          
          

        </div>
    </div>
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