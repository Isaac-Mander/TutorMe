<?php
//Get relevant info from session
$user_id = $_SESSION['user_id'];

$check = 0;

//Get the sessions this user is tutoring today
$session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=$user_id";
$session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);


//Get the sessions this user is being tutored today
$session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=$user_id";
$session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sessions</title>
    <!-- linking to bootstrap and the style sheet -->
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
  <?php
$tz = new DateTimeZone('NZ');
$dt = new DateTime('now',$tz);
$time_day = $dt->format('d'); // output: '1' - '31'
$time_month = $dt->format('m'); // output: '1' - '12'cc
$time_year = $dt->format('Y'); // output: '2023'
$time_hours = $dt->format('H'); // output: '2023'
$time_minutes = $dt->format('i'); // output: '2023'
$time = mktime($time_hours,$time_minutes,0,$time_month,$time_day,$time_year);
//getting the current time since january 1970 in seconds

if (is_array($session_today_tutor_data) && is_array($session_today_tutee_data)) {
    $session_combined_data = array_merge($session_today_tutor_data, $session_today_tutee_data);
  } else {
    //Create an empty array for all session data
    $session_combined_data = array();
    // Handle the case where one or both variables is not an array
    if(is_array($session_today_tutor_data)) {$session_combined_data = $session_today_tutor_data;}
    if(is_array($session_today_tutee_data)) {$session_combined_data = $session_today_tutee_data;}
  };
  if (is_array($session_combined_data)) { ?>
    <div class="upcoming_day_sessions container text-center border border-2 border-dark extra_rounded">
            <div class="row">
                <h3 class="col text-center py-3 m-0">Pending</h3>
                <div class="col red_box extra_rounded_tr"></div>
            </div>
            <div class="row row-cols-1 row-cols-md-3 gx-5 justify-content-center"><?php
            //Show the sessions today this user is tutoring
            for($i=0; $i<sizeof($session_combined_data); $i++)
            {
              if($session_combined_data[$i][9]==0){
                $day = substr($session_combined_data[$i][2],8,2);
                $month = substr($session_combined_data[$i][2],5,2);
                $year = substr($session_combined_data[$i][2],0,4);
                $hour = substr($session_combined_data[$i][2],11,2);
                $minutes = substr($session_combined_data[$i][2],14,2);
                $session_time = mktime($hour,$minutes,0,$month,$day,$year);
                //getting the time of the session as seconds from the first of january 1970
                //if session is in the future
                if ($time < $session_time) {
                //looping through all of the lines of the array
                $day = substr($session_combined_data[$i][1],0,10); //setting the day value
                $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
                $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
                $tutee =  $session_combined_data[$i][4]; //setting tutee name
                $tutor = $session_combined_data[$i][6]; //setting tutor name
                $subject = $session_combined_data[$i][8]; //setting subject name
                $session_id = $session_combined_data[$i][0];
                
                //prints out the information on a card
                ?><div class="col"> <div name="card" id="session_card" class='card' ><?php
                ?><h5 class="card-title" >Pending</h5>
                <p class="card-text"><?php echo $tutor." tutoring ".$tutee." in ".$subject  ?></p>
                <p class="card-text"><?php echo $day?></p>
                <p class="card-text"><?php echo "Earliest start time: ".$starttime ?></p>
                <p class="card-text"><?php echo "Latest end time: ".$endtime  ?></p>


                <?php
                //Find which user linked to the session is NOT the current user, so it can show the ratings of the other person
                if($session_combined_data[$i][3] != $user_id) //If user is tutor
                {
                  $other_user_id = $session_combined_data[$i][3];
                }
                else //If user is tutee
                {
                  $other_user_id = $session_combined_data[$i][5];
                }
                ?>
                <p class="card-text"><?php echo "Productivity Rating: " . average_ratings($conn,$other_user_id)[0];?></p>
                <p class="card-text"><?php echo "Experience Rating: " . average_ratings($conn,$other_user_id)[0];?></p>
                <div class="row"><?php
                //Check if the current user is a tutee, if so add an accept button
                if($session_combined_data[$i][3] == $user_id)
                {
                  //the buttons for the user to accept or reject the pending sessions
                  echo "
                    <div class='col'><a href='a_or_r_session.php?action=1&id=$session_id&page=action.php'><button id='accept' value='$session_id'>Accept</button></a></div>
                    <div class='col'><a href='a_or_r_session.php?action=2&id=$session_id&page=action.php'><button id='reject' value='$session_id'>Reject</button></a></div>
                  ";
                }
                else //If the session is not awaiting the users input, show that to the user
                {
                  echo "
                  <strong><div class='col'>This session is awaiting a response from the tutee</div></strong>";
                }?>
                </div>
                </div></div><?php

                $check=1;

              }
              }
        }if ($check == 0){
          //a message so that the user knows what is happening
          ?><div class="col"><div class="card"><h3>There are no pending sessions</h3></div></div><?php
        }     ?> </div></div></div>

    <!-- the element for the confirmed sessions -->
    <div class="upcoming_week_sessions container text-center border border-2 border-dark extra_rounded mt-4">
        <div class="row">
            <h3 class="col text-center py-1 m-0">Confirmed sessions (click to contact other user)</h3>
            <div class="col red_box extra_rounded_tr"></div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 gx-5 justify-content-center"><?php
    for($i=0; $i<sizeof($session_combined_data); $i++){
      if($session_combined_data[$i][9]==1){
        $day = substr($session_combined_data[$i][2],8,2);
        $month = substr($session_combined_data[$i][2],5,2);
        $year = substr($session_combined_data[$i][2],0,4);
        $hour = substr($session_combined_data[$i][2],11,2);
        $minutes = substr($session_combined_data[$i][2],14,2);
        $session_time = mktime($hour,$minutes,0,$month,$day,$year);
        //setting the times from the confirmed sessions
        //getting the time of the session as seconds from the first of january 1970

        //if session is in the future
        if ($time < $session_time){
          //looping through all of the lines of the array
            $day = substr($session_combined_data[$i][1],0,10); //setting the day value
            $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
            $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
            $tutee =  $session_combined_data[$i][4]; //setting tutee name
            $tutor = $session_combined_data[$i][6]; //setting tutor name
            $subject = $session_combined_data[$i][8]; //setting subject name

        //printing out a card with all of the information needed for the confirmed sessions
        ?>   <div class="col" > <div name="card" id="session_card" class='card'><?php
        echo "<div class=row>" . "<p class=col>Tutor: </p><p class=col id=tutor>".$tutor."</p>" . "</div>";
        echo "<div class=row>" . "<p class=col>Tutee: </p><p class=col id=tutee>".$tutee."</p>" . "</div>";
        echo "<div class=row>" . "<p class=col>Subject: </p><p class=col id=subject>".$subject."</p>" . "</div>";
        echo "<div class=row>" . "<p class=col>Date : </p><p class=col id=day>".$day."</p>" . "</div>";
        echo "<div class=row>" . "<p class=col>Earliest start time: </p><p class=col id=starttime>".$starttime."</p>" . "</div>";
        echo "<div class=row>" . "<p class=col>Latest end time: </p><p class=col id=endtime>".$endtime."</p>" . "</div>";
        echo "<div class=hide_on_start>".$session_combined_data[$i][11][0]."</div>";
        echo "<div class=hide_on_start>".$session_combined_data[$i][11][1]."</div>";
        echo "<div class=hide_on_start>".$session_combined_data[$i][10][0]."</div>";
        echo "<div class=hide_on_start>".$session_combined_data[$i][10][1]."</div>";
        ?> </div> </div>  <?php
        $check=2;
        }
      }
    }if ($check == 0){
      //message for the user so that they know what is happening
      ?><div class="col"><div class="card"><h3>There are no confirmed sessions</h3></div></div><?php
    } elseif ($check ==1){
      //message for the user so that they know what is happening
      ?><div class="col"><div class="card"><h3>There are no confirmed sessions</h3></div></div><?php
    }
    ?> </div></div><?php
}

?>
<!-- this is the modal for the user to get the contact details for the confirmed sessions -->
<div class="modal fade" id="contact_detail_popup_test" tabindex="-1" role="dialog" aria-labelledby="contact_detail_popup_test" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- modal header where you can close + title -->
        <h5 class="modal-title" id="exampleModalLabel">General info</h5>
        <a type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </a>
      </div>
      <!-- the main content of the modal which will be changed via JavaScript to have the right data -->
      <div id="contact_detail_content_test" class="modal-body">
                <p>Tutor name</p>
                <p>Tutee name</p>
                <p>Subject Name</p>
                <p>Date</p>
                <p>Start time</p>
                <p>End time</p>

                <h3>Contact info</h3>
                <p>Email/Phone number</p>
                <p>Email/Phone number</p>
                <p>Email/Phone number</p>
                <p>Email/Phone number</p>
      </div>
      <div class="modal-footer">
      <!-- the button that copys an emailing guide to the clip board, the hidden emailing guide and the link to the visible version -->
      <p><button onclick="copyToClip(document.getElementById('foo').innerHTML)">Click to copy email template</button></p>
        <p><a href="../TutorMe/contact.php">Emailing Guide</a></p>
          <div id=foo style="display:none">
          Dear (Insert Name),

          I am contacting you to talk about organising a place for the tutoring program that has been agreed upon. Would you be able to go to (Place 1) or (Place 2)? And what times are you able to be there between (start time) and (End time)? If not do you have any suggestions?

          Sincerely (Your Name)
          </div>
        <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
        <!-- button to close the modal -->
      </div>
    </div>
  </div>
</div>



  <?php
$check = 0;
//Get the sessions this user is tutoring today
$session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=$user_id";
$session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);


//Get the sessions this user is being tutored today
$session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=$user_id";
$session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);

//when they are both arrays they are merged
if (is_array($session_today_tutor_data) && is_array($session_today_tutee_data)) {
    $session_combined_data = array_merge($session_today_tutor_data, $session_today_tutee_data);
  } else {
    // Handle the case where one or both variables is not an array
    // For example:
    $session_combined_data = array();
    if(is_array($session_today_tutor_data)) {$session_combined_data = $session_today_tutor_data;}
    if(is_array($session_today_tutee_data)) {$session_combined_data = $session_today_tutee_data;}
  };
  //when there is a merged array
  if (is_array($session_combined_data)) { ?>
    <div class="upcoming_week_sessions container text-center border border-2 border-dark extra_rounded mt-4">
        <div class="row">
            <h3 class="col text-center py-1 m-0">Past Sessions (click to place feedback)</h3>
            <div class="col red_box extra_rounded_tr"></div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 gx-5 justify-content-center"><?php
    for($i=0; $i<sizeof($session_combined_data); $i++){
      if($session_combined_data[$i][9]==1){
        $day = substr($session_combined_data[$i][2],8,2);
        $month = substr($session_combined_data[$i][2],5,2);
        $year = substr($session_combined_data[$i][2],0,4);
        $hour = substr($session_combined_data[$i][2],11,2);
        $minutes = substr($session_combined_data[$i][2],14,2);
        $session_time = mktime($hour,$minutes,0,$month,$day,$year);
        //setting the times from the confirmed sessions
        //getting the time of the session as seconds from the first of january 1970
        //if session is in the past
        if ($time > $session_time){
              $check = 1;
              //looping through all of the lines of the array
              $day = substr($session_combined_data[$i][1],0,10); //setting the day value
              $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
              $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
              $tutee =  $session_combined_data[$i][4]; //setting tutee name
              $tutor = $session_combined_data[$i][6]; //setting tutor name
              $subject = $session_combined_data[$i][8]; //setting subject name

              $session_id = $session_combined_data[$i][0];

        //printing out all of the past sessions with all of the information needed to identify them.
        ?>   <a href="<?php echo 'feedback.php?session_id=' . $session_id; ?>"><div class="col" > <div id="session_card" class='card'><?php
        echo ($tutor." tutoring ".$tutee."<br>"."Subject: ".$subject."<br>".$day."<br>"."Start time: ".$starttime . "<br>"."End time: ".$endtime);?> </div> </div>  <?php
        $check=3;
        }
      }
    }if ($check == 0){
      //a message for the users so they know what's happening
      ?><div class="col"><div class="card"><h3>There have been no confirmed sessions</h3></div></div><?php
    } elseif ($check ==1){
      //a message for the users so they know what's happening
      ?><div class="col"><div class="card"><h3>There have been no confirmed sessions</h3></div></div><?php
    } elseif ($check ==2){
      //a message for the users so they know what's happening
      ?><div class="col"><div class="card"><h3>There have been no confirmed sessions</h3></div></div><?php
    }
    ?> </div></div></a><?php
}
?>
    <!-- a div tag marker and linking to the javascript library -->
    <div id="session_page_marker"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>
