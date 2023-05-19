<?php
//Import functions\
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

?>
  
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
    $time_hour = $dt ->format('h');
    $time_minute = $dt ->format('i');
    $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
    $date =  $time_year . "-" . $time_month . "-" . $time_day;
    $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
    $date =  $time_year . "-" . $time_month . "-" . $time_day;
    $avaliable_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=3";
    $avaliable_session_times_data = get_avaliable_session_data($avaliable_session_times_sql, $conn);

    function grab_events($conn){
    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);

    //Get the sessions this user is being tutored today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);
    $avaliable_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=3";
    $avaliable_session_times_data = get_avaliable_session_data($avaliable_session_times_sql, $conn);

    if (is_array($avaliable_session_times_data)) {
      for($i=0; $i<sizeof($avaliable_session_times_data); $i++){
        $name = $avaliable_session_times_data[$i][0];
        $potential_starttime = $avaliable_session_times_data[$i][1];
        $potential_endtime = $avaliable_session_times_data[$i][2];
        $day = (int)substr($avaliable_session_times_data[$i][1],8,2);
        $month = (int)substr($avaliable_session_times_data[$i][1],5,2);
        $year = (int)substr($avaliable_session_times_data[$i][1],0,4);
        $week_day =  idate('w', mktime(0,0,0,$month,$day,$year));

        $potential_events[]=[
          "title" => "potential session",
          "start" => $potential_starttime,
          "end"   => $potential_endtime,
          "color" => "purple",
          "daysOfWeek" => [$week_day]
          
        ];
      }
    }
    if (is_array($session_today_tutor_data) && is_array($session_today_tutee_data)) {
      $session_combined_data = array_merge($session_today_tutor_data, $session_today_tutee_data);
    } else {
      // Handle the case where one or both variables is not an array
      // For example:
      $session_combined_data = array();};
    if (is_array($session_combined_data)) {
      for($i=0; $i<sizeof($session_combined_data); $i++){
        $day = substr($session_combined_data[$i][1],0,10);
        $starttime = substr($session_combined_data[$i][1],11,8);
        $endtime = substr($session_combined_data[$i][2],11,8);
        $tutee =  $session_combined_data[$i][4];
        $tutor = $session_combined_data[$i][6];
        $subject = $session_combined_data[$i][8];
        $events[] = [
          "title" => $tutor.' tutoring '.$tutee.' in '.$subject,
          "start" => $day . "T" . $starttime,
          "end"   => $day."T".$endtime,
      ];
      }
      if (is_array($events) && is_array($potential_events)) {
        $all_events = array_merge($events, $potential_events);
        return $all_events;
      }else {
        return '(」゜ロ゜)」';
      }
    } else {
      // Assign an array value to $session_today_tutor_data
      return '(」゜ロ゜)」';
    }
  };
    $events = grab_events($conn);  ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    <?php         grab_events($conn); $JsonEvents = json_encode($events);  ?>
    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      headerToolbar: {
        left:'',
        center: 'title',
        right:''
      },
      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        timeGridWeek: { buttonText: 'grid week' }
      },

      initialView: 'timeGridWeek',
      initialDate:  '<?php echo $date?>',
      navLinks: true, // can click day/week names to navigate views
      businessHours: true,
      dayMaxEvents: true, // allow "more" link when too many events
      events: <?php echo $JsonEvents ?>
    });
    calendar.render();
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
  <form action='calender_2.php' method='post'>
    <div class="container">
        <label for="start_time"><b>Start time</b></label>
        <input type="time" id="start_time" placeholder="Start time" name="start_time" required><br>


        <label for="end_time"><b>Start time</b></label>
        <input type="time" id="end_time" placeholder="End time" name="end_time" required><br>
        
        <label for="day_of_week">Day of the week (between 0 and 6) 0 = Sunday, 6 = Saturday:</label>
        <input type="number" id="day_of_week" name="day_of_week" min="0" max="6">
        <input type="submit">
    </div>
    </form> 
    <?php    
    if (is_array($avaliable_session_times_data)) {
      for($i=0; $i<sizeof($avaliable_session_times_data); $i++){
        $name = $avaliable_session_times_data[$i][0];
        $potential_starttime_year = date();
        $potential_starttime_month = date():

        $potential_endtime = $avaliable_session_times_data[$i][2];
        ?>    <div class='card' style="width: 18rem;"><?php
        echo $name."     ".$potential_starttime."       ".$potential_endtime;
        ?>     </div><?php
      }
    }?>
    <?php
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <div id='calendar'></div>
  </body>
</html>
