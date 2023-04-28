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
    $day = '╰(⇀︿⇀)つ-]═───';
    $starttime = '╰(⇀︿⇀)つ-]═───';
    $endtime = '╰(⇀︿⇀)つ-]═───';
    $tutee =  '╰(⇀︿⇀)つ-]═───';
    $tutor = '╰(⇀︿⇀)つ-]═───';
    $subject = '╰(⇀︿⇀)つ-]═───';

    $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
    $date =  $time_year . "-" . $time_month . "-" . $time_day;
    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);

    //Get the sessions this user is being tutored today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);?>

    <?php
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
    } else {
      // Assign an array value to $session_today_tutor_data
      echo '(」゜ロ゜)」';
    }
      ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    <?php $JsonEvents = json_encode($events); ?>;
    console.log('<?php echo $JsonEvents?>');
    var calendar = new FullCalendar.Calendar(calendarEl, {

      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'listDay,listWeek'
      },

      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        listDay: { buttonText: 'list day' },
        listWeek: { buttonText: 'list week' },
        listMonth: { buttonText: 'list month'}
      },

      initialView: 'listWeek',
      initialDate:  '<?php echo $date?>',
      navLinks: true, // can click day/week names to navigate views
      editable: true,
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
    <h1>Hello, world!</h1>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    
    <div id='calendar'></div>
  </body>
</html>
