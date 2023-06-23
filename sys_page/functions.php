<?php
//Returns the session data from a query into a single array formated as below
//[index of session in array][(0)Session id,  (1)session start time, (2)session end time,   (3)session tutee id,   (4)session tutee name,  (5)session tutor id,   (6)session tutor name,   (7)subject_id,   (8)subject_name]
//THE SQL ARGUMENT MUST RETURN ALL FROM SESSION, IF NOT ERROR WILL OCCUR
function get_session_data($sql,$conn)
{
    include("sys_page/db_connect.php");
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_sessions = false; //Tell other elements to expect session data
        $tutor_session_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            
            $tutor_session_data[$session_index][0] = $row['id']; //Tutor Session Id
            $tutor_session_data[$session_index][1] = $row['session_start']; //Session start time
            $tutor_session_data[$session_index][2] = $row['session_end']; //Session end time
            $tutor_session_data[$session_index][3] = $row['tutee_id']; //Session tutee id
            $tutee_id = $tutor_session_data[$session_index][3]; //Set variable to session tutee id for sql query
            $tutor_session_data[$session_index][5] = $row['tutor_id']; //Session tutor id
            $tutor_id = $tutor_session_data[$session_index][5]; //Set variable to session tutor id for sql query
            //Query the student list to get the tutee's name
            $sql_tutee_name = "SELECT name FROM 6969_students WHERE id=$tutee_id";
            $result_tutee = $conn->query($sql_tutee_name);
            $data = $result_tutee->fetch_assoc();
            $tutor_session_data[$session_index][4] = $data['name'];//Tutee name

            //Query the student list to get the tutor's name
            $sql_tutor_name = "SELECT name FROM 6969_students WHERE id=$tutor_id";
            $result_tutor = $conn->query($sql_tutor_name);
            $data = $result_tutor->fetch_assoc();
            $tutor_session_data[$session_index][6] = $data['name'];//Tutor name

            //Each tutor session can only have a single subject. This program will filter out any id that is zero
            if($row['global_subject_id'] == 0) $tutor_session_data[$session_index][7] = $row['local_subject_id']; //Session subject
            else $tutor_session_data[$session_index][7] = $row['global_subject_id']; //Session subject id
            //Query the subject list to get the subjects's name
            $subject_id = $tutor_session_data[$session_index][7];
            $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
            $result_subject = $conn->query($sql_subject_name);
            $data2 = $result_subject->fetch_assoc();
            $tutor_session_data[$session_index][8] = $data2['name'];//Subject english name
            
            //Increment the session index the data is stored under
            $tutor_session_data[$session_index][9] = $row['is_active'];
            $session_index += 1;
        }
        return $tutor_session_data;
    } 
    else 
    {
        return 1;
    }
}
function get_available_session_data($sql,$conn)
{
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_sessions = false; //Tell other elements to expect session data
        $tutor_session_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            
            $tutor_session_data[$session_index][0] = $row['name']; //Gets the person's name
            $tutor_session_data[$session_index][1] = $row['session_start']; //Session start time
            $tutor_session_data[$session_index][2] = $row['session_end']; //Session end time
            $tutor_session_data[$session_index][3] = $row['day_of_week'];//The day of the week Monday-Sunday in a 1-7 format
            $tutor_session_data[$session_index][4] = $row['id']; //Id in table
            
            //Increment the session index the data is stored under
            $session_index += 1;
        }
        return $tutor_session_data; //Returns the data
    } 
    else 
    {
        return 1;
    }
}
//This function gets the subjects available to a specific user =========================================================================================================================================
function get_available_subjects($school_code)
{
    //Connect to db
    include("sys_page/db_connect.php");

    //Get the subjects of the school 
    $all_local_subject_sql = "SELECT * FROM `" . $school_code . "_subjects` WHERE 1;";
    $all_global_subject_sql = "SELECT * FROM `subjects` WHERE 1;";

    //Get local data
    $all_local_subject_result = $conn->query($all_local_subject_sql); //Query database
    if ($all_local_subject_result->num_rows > 0) { //If the number of rows are not zero
    $all_available_subject_array = []; //This array is formatted as [index][isglobalvariable?, name of subject, id of subject]
    $i = 0;
    while($row = $all_local_subject_result->fetch_assoc()) {
        $all_available_subject_array[$i][0] = false; //This variable is set to false as this is not a global table info
        $all_available_subject_array[$i][1] = $row['name'];
        $all_available_subject_array[$i][2] = $row['id'];
        $i += 1;
    }
    }

    //Get global data
    $all_global_subject_result = $conn->query($all_global_subject_sql); //Query database
    if ($all_global_subject_result->num_rows > 0) { //If the number of rows are not zero
    while($row = $all_global_subject_result->fetch_assoc()) {
        $all_available_subject_array[$i][0] = true; //This variable is set to true as this is the global table info
        $all_available_subject_array[$i][1] = $row['name'];
        $all_available_subject_array[$i][2] = $row['id'];
        $i += 1;
    }
    }

    //Return the available subjects as an array
    return $all_available_subject_array;
    
}
function get_session_select_data($sql,$conn,$status)
{   //(0)id, (1)potential session start times, (2)potential session end time, (3)name of the person, (4)subject_id, (5) Subject name.
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_sessions = false; //Tell other elements to expect session data
        $session_select_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            
            $session_select_data[$session_index]['student_id'] = $row['student_id']; //Tutor Session Id
            $student_id = $row['student_id'];
            $session_select_data[$session_index]['start_time'] = $row['session_start'];//Session start time
            $session_select_data[$session_index]['end_time'] = $row['session_end'];//Session end time
            $session_select_data[$session_index]['dayofweek'] = $row['day_of_week'];//The day of the week Monday-Sunday in a 1-7 format
            $session_select_data[$session_index]['user_name'] = $row['name'];//Gets the person's name
            $session_select_data[$session_index]['table_id'] = $row['id']; //Gets the id in the table

            if ($status == TRUE) {
                $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutor WHERE tutor_id=$student_id";
                $result_subject = $conn->query($sql_subject); 
                //querys the database to obtain the global_subject_id and local_subject_id from the tutor table//Query database
                if ($result_subject->num_rows > 0) { //If the number of rows are not zero
                $subject_index = 0;
                while($row_2 = $result_subject->fetch_assoc()) {
                    if($row_2['global_subject_id'] == 0) {
                      $session_select_data[$session_index]['subject_id'][$subject_index] = $row_2['local_subject_id']; //Session subject
                      //Query the subject list to get the subjects's name
                      $subject_id = $row_2['local_subject_id'];
                      $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
                      $result_subject_name = $conn->query($sql_subject_name);
                      $data2 = $result_subject_name->fetch_assoc();
                      $session_select_data[$session_index]['subject_name'][$subject_index] = $data2['name'];//Subject english name
                    }
                    else{
                      $session_select_data[$session_index]['subject_id'][$subject_index] = "G".$row_2['global_subject_id']; //Session subject id
                      //Query the subject list to get the subjects's name
                      $subject_id = $row_2['global_subject_id'];
                      $sql_subject_name = "SELECT name FROM  subjects WHERE id=$subject_id";
                      $result_subject_name = $conn->query($sql_subject_name);
                      $data2 = $result_subject_name->fetch_assoc();
                      $session_select_data[$session_index]['subject_name'][$subject_index] = $data2['name'];//Subject english name
                    } 
                    //Increment the session index the data is stored under
                    $subject_index += 1;
                }}
            }elseif ($status == False){
                $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutee WHERE tutee_id=$student_id";
                $result_subject = $conn->query($sql_subject);
                //querys the database to obtain the global_subject_id and local_subject_id from the tutor table//Query database
                if ($result_subject->num_rows > 0) { //If the number of rows are not zero
                $subject_index =0;
                while($row_2 = $result_subject->fetch_assoc()) {
                    if($row_2['global_subject_id'] == 0) {
                      $session_select_data[$session_index]['subject_id'][$subject_index] = $row_2['local_subject_id']; //Session subject
                      //Query the subject list to get the subjects's name
                      $subject_id = $row_2['local_subject_id'];
                      $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
                      $result_subject_name = $conn->query($sql_subject_name);
                      $data2 = $result_subject_name->fetch_assoc();
                      $session_select_data[$session_index]['subject_name'][$subject_index] = $data2['name'];//Subject english name
                    }
                    else {
                      $session_select_data[$session_index]['subject_id'][$subject_index] = "G".$row_2['global_subject_id']; //Session subject id
                      //Query the subject list to get the subjects's name
                      $subject_id = $row_2['global_subject_id'];
                      $sql_subject_name = "SELECT name FROM subjects WHERE id=$subject_id";
                      $result_subject_name = $conn->query($sql_subject_name);
                      $data2 = $result_subject_name->fetch_assoc();
                      $session_select_data[$session_index]['subject_name'][$subject_index] = $data2['name'];//Subject english name
                      //Increment the session index the data is stored under
                    }
                    $subject_index += 1;
                }}
            }
            //Increment the session index the data is stored under
            $session_index += 1;
            }
        
        return $session_select_data;//Returns the data
    }
    else 
    {
        return 1;//if there is no data then it returns a 1
    }
}

function grab_events($conn,$id)
{
    //function so that when the database is updated the calender's events can be as well

    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=$id";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);


    //Get the sessions this user is being tutored today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=$id";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);

    $available_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=$id";
    $available_session_times_data = get_available_session_data($available_session_times_sql, $conn);
    //pulls all the potential times from the database, runs through function
    $potential_events = array();
    $events = array();
    if (is_array($available_session_times_data)) {


      for($i=0; $i<sizeof($available_session_times_data); $i++){
        //looping through all of the lines of the array

        $name = $available_session_times_data[$i][0]; //sets the name
        $potential_start_time = $available_session_times_data[$i][1]; //sets the start time
        $potential_end_time = $available_session_times_data[$i][2]; //sets the end time
        $week_day =  $available_session_times_data[$i][3]; //sets the day of the week 1-7

        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $date =  $time_year . "-" . $time_month . "-" . $time_day;

        $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $day_of_week = $days_of_week_array[$week_day-1]; //converts integer value to word, as $weekday is 1-7 and array is 0-6, 1 is minused

        $day_current_week = date('N');
        if ($day_current_week == 7){
          //checking if the day is Sunday, because the week on the calendar starts on Sunday, this if it is the events need to be for the next week
          $value_day_actual = Strtotime("Next week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;
          //converting values for the upcomming week
        } else {
          //if it is not Sunday

          $value_day_actual = Strtotime("This week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;
          //converting values for the current values
        }

        $potential_events[] =[
          "title" => "potential session",
          "start" => $potential_start_datetime,
          "end"   => $potential_end_datetime,
          "color" => "purple"
        ];
        //putting all of the potential sessions into an array
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
        //looping through all of the lines of the array
        $day = substr($session_combined_data[$i][1],0,10); //setting the day value
        $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
        $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
        $tutee =  $session_combined_data[$i][4]; //setting tutee name
        $tutor = $session_combined_data[$i][6]; //setting tutor name
        $subject = $session_combined_data[$i][8]; //setting subject name

        $events[] = [
          "title" => $tutor.' tutoring '.$tutee.' in '.$subject,
          //setting a title
          "start" => $day . "T" . $starttime,
          //setting the start time
          "end"   => $day."T".$endtime,
          //setting the end time
      ];
      //creating an array of all of the sessions that have been booked in 
      }
      if (is_array($events) && is_array($potential_events)) {
        //making sure that both arrays are arrays
        $all_events = array_merge($events, $potential_events);

        return $all_events;
        //merging the arrays to be input into the calendar api
      }else {
        return '(」゜ロ゜)」';
      }
    } else {
      // Assign an array value to $session_today_tutor_data
      return '(」゜ロ゜)」';
    }
}
?>