<?php
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED

//Check if user is logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get the user info from the session cookie
$user_id = $_SESSION['user_id'];
$school_code = $_SESSION['school_code'];

//Import functions
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");
?>
<!-- links to the bootstrap and JavaScript tables -->
<script src="content.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <?php
    $tz = new DateTimeZone('NZ');
    $dt = new DateTime('now',$tz);
    $time_day = $dt->format('d'); // output: '1' - '31'
    $time_month = $dt->format('m'); // output: '1' - '12'cc
    $time_year = $dt->format('Y'); // output: '2023'
    $time_hour = $dt ->format('h');// output: '09'
    $time_minute = $dt ->format('i');// out: ':46'
    $date =  $time_year . "-" . $time_month . "-" . $time_day; //setting the day for the calendar to use.


    $events = grab_events($conn,$user_id);

    $available_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=$user_id";
    $available_session_times_data = get_available_session_data($available_session_times_sql, $conn);
    //pulls all the potential times from the database, runs through function
    //$events = grab_events($conn);
    //calling the events function, and setting the events?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>
//loading in the calender
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    <?php 

    $JsonEvents = json_encode($events); //putting the array into a format the calendar can read ?>
    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      headerToolbar: {
        left:'',
        center: 'title',
        right:''
      },
      //setting the tool bar
      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        timeGridWeek: { buttonText: 'grid week' }
      },

      //only having the initial view
      initialView: 'timeGridWeek',
      initialDate:  '<?php echo $date?>',
      allDaySlot: false,
      navLinks: false, // can click day/week names to navigate views
      dayMaxEvents: true, // allow "more" link when too many events
      events: <?php echo $JsonEvents ?> //uploading all of the events
    });
    calendar.render();
    //rendering calendar
  });
</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 0 auto;
  }

</style>
  </head>
  <body>
    <!-- this is used to set up the different potential times for the user, and will be posted to calendar_2 page which will process it-->
    <div class="card mx-auto w-75">
    <form action='calendar_2.php' method='post'>
    <div class="card-body">
    <div class="form-group row">
        <!-- input type time field to enter the start time for a potential time -->
        <label for="start_time" class="col-sm-2 col-form-label"><b>Start time</b></label>
        <div class="col-sm-10">
        <input type="time" class="form-control" id="start_time" placeholder="Start time" name="start_time" required><br>
        </div>
        </div>

        <!-- input type time field to enter the end time for a potential time -->
        <div class="form-group row">
        <label for="end_time" class="col-sm-2 col-form-label"><b>End time</b></label>
        <div class="col-sm-10">
        <input type="time" class="form-control" id="end_time" placeholder="End time" name="end_time" required><br>
        </div>
        </div>

        <!-- the input field for the user to input the day of the week this free time is on -->
        <fieldset class="form-group">
          <div class="row">
            <legend class="col-form-label col-sm-2 pt-0">Day of week</legend>
            <div class="col-sm-10">
              <div class="form-check">
                <!-- input for monday, setting the day of week value to 1 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week1" value=1>
                <label class="form-check-label" for="gridRadios1">
                  Monday
                </label>
              </div>
              <div class="form-check">
                <!-- input for tuesday, setting the day of week value to 2 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week2" value=2>
                <label class="form-check-label" for="gridRadios2">
                  Tuesday
                </label>
              </div>
              <div class="form-check">
                <!-- input for wednesday, setting the day of week value to 3 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week3" value=3>
                <label class="form-check-label" for="gridRadios3">
                  Wednesday
                </label>
              </div>
              <div class="form-check">
                <!-- input for thursday, setting the day of week value to 4 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week4" value=4>
                <label class="form-check-label" for="gridRadios4">
                  Thursday
                </label>
              </div>
              <div class="form-check">
                <!-- input for friday, setting the day of week value to 5 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week5" value=5>
                <label class="form-check-label" for="gridRadios5">
                  Friday
                </label>
              </div>
              <div class="form-check">
                <!-- input for saturday, setting the day of week value to 6 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week6" value=6>
                <label class="form-check-label" for="gridRadios6">
                  Saturday
                </label>
              </div>
              <div class="form-check">
                <!-- input for sunday, setting the day of week value to 7 -->
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week7" value=7>
                <label class="form-check-label" for="gridRadios7">
                  Sunday
                </label>
              </div>
            </div>
          </div>
  </fieldset>
        <!-- hidden input field because the form needs to have the student id to process the data -->
        <!-- this allows the form to send through the id without allowing the user to tamper with it and potentially cause an error -->
        <input type="hidden" id="student_id" name="student_id" value="<?php echo $user_id ?>">
        <input type="submit">
    </div>
    </form> 
    </div>
    </div>

    <?php
    //if there are potential sessions set
    if (is_array($available_session_times_data)) {
      //if is array
      for($i=0; $i<sizeof($available_session_times_data); $i++){
        //running through all of the array
        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $day =  $time_year . "-" . $time_month . "-" . $time_day;
        $name = $available_session_times_data[$i][0]; //setting name
        $day_of_week = $available_session_times_data[$i][3]; //setting day of the week

        $potential_start_time_session = $available_session_times_data[$i][1]; //setting potential start time
        $potential_end_time_session = $available_session_times_data[$i][2]; //setting potential end time
        $potential_starttime_rough = strtotime($day.$potential_start_time_session); //getting a time value
        $potential_endtime_rough = strtotime($day.$potential_end_time_session); //getting a time value
        if (date('N') == $day_of_week){ //checking if the date is the same
          $potential_starttime = $potential_starttime_rough; //if so the times need no change
          $potential_endtime = $potential_endtime_rough;

        } elseif (date('N') > $day_of_week){ //if it is different 
          $time_diff = date('N') - $day_of_week; //find the time difference
          $potential_starttime = $potential_starttime_rough - ($time_diff * 86400);//accounts for the diffence
          $potential_endtime = $potential_endtime_rough - ($time_diff * 86400);

        } elseif(date('N') < $day_of_week){//if it is different
          $time_diff = $day_of_week - date('N'); //finds the time difference 
          $potential_starttime = $potential_starttime_rough + ($time_diff * 86400); //acounts for the difference
          $potential_endtime = $potential_endtime_rough + ($time_diff * 86400);
        }

        //setting the card id
        $card_id = $available_session_times_data[$i][4];
        //prints out the cards with the details of the potential times
        ?>    <div id=<?php echo $card_id; ?> class='card mx-auto' style="width: 35rem;"><?php
        echo ($name."<br>".date("l h:i:s A", $potential_starttime) . "<br>");
        echo date("l h:i:s A", $potential_endtime); //prints out the cards of the time sessions.
        ?> <a href="delete_calendar_time.php?id=<?php echo $card_id; ?>">Remove</a>     </div>   <?php //the removal link to remove the potential session times     }
    }?>

    <!-- the div tag to call the calendar-->
    <div id='calendar'></div>
  </body>
</html>
