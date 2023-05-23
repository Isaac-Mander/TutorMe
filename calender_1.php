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
        $potential_start_time = $avaliable_session_times_data[$i][1];
        $potential_end_time = $avaliable_session_times_data[$i][2];
        $week_day =  $avaliable_session_times_data[$i][3];
        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $date =  $time_year . "-" . $time_month . "-" . $time_day;
        $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $day_of_week = $days_of_week_array[$week_day-1];

        $blob = date('N');
        if ($blob == 7){
          $value_day_actual = Strtotime("Next week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;

        } else {
          $value_day_actual = Strtotime("This week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;

        }
        $potential_events[]=[
          "title" => "potential session",
          "start" => $potential_start_datetime,
          "end"   => $potential_end_datetime,
          "color" => "yellow"
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
    <?php $JsonEvents = json_encode($events);  ?>
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
        
        <label for="day_of_week">Day of the week (between 1 and 7) 1 = Monday, 7 = Sunday:</label>
        <input type="number" id="day_of_week" name="day_of_week" min="1" max="7">
        <input type="submit">
    </div>
    </form> 
    <?php
    $card_id_increment = 0;
    if (is_array($avaliable_session_times_data)) {
      for($i=0; $i<sizeof($avaliable_session_times_data); $i++){
        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $day =  $time_year . "-" . $time_month . "-" . $time_day;
        $name = $avaliable_session_times_data[$i][0];
        $day_of_week = $avaliable_session_times_data[$i][3];

        $potential_start_time_session = $avaliable_session_times_data[$i][1];
        $potential_end_time_session = $avaliable_session_times_data[$i][2]; 
        $potential_starttime_rough = strtotime($day.$potential_start_time_session);
        $potential_endtime_rough = strtotime($day.$potential_end_time_session);
        if (date('N') == $day_of_week){
          $potential_starttime = $potential_starttime_rough;
          $potential_endtime = $potential_endtime_rough;

        } elseif (date('N') > $day_of_week){
          $time_diff = date('N') - $day_of_week;
          $potential_starttime = $potential_starttime_rough - ($time_diff * 86400);
          $potential_endtime = $potential_endtime_rough - ($time_diff * 86400);

        } elseif(date('N') < $day_of_week){
          $time_diff = $day_of_week - date('N');
          $potential_starttime = $potential_starttime_rough + ($time_diff * 86400);
          $potential_endtime = $potential_endtime_rough + ($time_diff * 86400);
        }
        $card_id = "card_" . $card_id_increment;
        $card_id_increment += 1;
        ?>    <div id=<?php echo $card_id; ?> class='card' style="width: 18rem;"><?php
        echo ($name."<br>".date("l jS \of F Y h:i:s A", $potential_starttime) . "<br>");
        echo date("l jS \of F Y h:i:s A", $potential_endtime);
        ?>    </div><?php
      }
    }?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <div id='calendar'></div>
  </body>
</html>
