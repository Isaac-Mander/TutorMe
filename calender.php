<?php
function get_session_data($sql,$conn)
{
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_today_sessions = false; //Tell other elements to expect session data
        $tutor_session_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            //tutor_session_data is formatted like [index of session in array][Session id,session time,session tutee id,session tutee name (string),subject_id,subject_name]
            $tutor_session_data[$session_index][0] = $row['id']; //Tutor Session Id
            $tutor_session_data[$session_index][1] = $row['time']; //Session time
            $tutor_session_data[$session_index][2] = $row['tutee_id']; //Session tutee id
            $tutee_id = $tutor_session_data[$session_index][2]; //Set variable to session tutee id for sql query

            //Query the student list to get the tutee's name
            $sql_tutee_name = "SELECT name FROM 6969_students WHERE id=$tutee_id";
            $result_tutee = $conn->query($sql_tutee_name);
            $data = $result_tutee->fetch_assoc();
            $tutor_session_data[$session_index][3] = $data['name'];//Tutee name

            //Each tutor session can only have a single subject. This program will filter out any id that is zero
            if($row['global_subject_id'] == 0) $tutor_session_data[$session_index][4] = $row['local_subject_id']; //Session subject
            else $tutor_session_data[$session_index][4] = $row['global_subject_id']; //Session subject id

            //Query the subject list to get the subjects's name
            $subject_id = $tutor_session_data[$session_index][4];
            $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
            $result_subject = $conn->query($sql_subject_name);
            $data2 = $result_subject->fetch_assoc();
            $tutor_session_data[$session_index][5] = $data2['name'];//Tutee name
            
            //Increment the session index the data is stored under
            $session_index += 1;
        }
    } else {
        echo "0 results";
        $no_today_sessions = true; //This variable tells the page to show the no sessions today msg
    }

    //Return the result of the query 
    if($no_today_sessions) return 1;
    else return $tutor_session_data;
}?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <?php
    include("sys_page/header.html");
    include("sys_page/db_connect.php");?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

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
        listWeek: { buttonText: 'list week' }
      },

      initialView: 'listWeek',
      initialDate: '2023-01-12',
      navLinks: true, // can click day/week names to navigate views
      editable: true,
      dayMaxEvents: true, // allow "more" link when too many events
      events: [
        {
          title: 'All Day Event',
          start: '2023-01-10'
        },
        {
          title: 'Conference',
          start: '2023-01-11',
          end: '2023-01-13'
        },
        {
          title: 'Meeting',
          start: '2023-01-12T10:30:00',
          end: '2023-01-12T12:30:00'
        },
        {
          title: 'Lunch',
          start: '2023-01-12T12:00:00'
        },
        {
          title: 'Meeting',
          start: '2023-01-12T14:30:00'
        },
      ]
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