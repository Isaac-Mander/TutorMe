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
function get_tutee_session_select_data($sql,$conn)
{   //(0)id, (1)potential session start times, (2)potential session end time, (3)name of the person, (4)subject_id, (5) Subject name.
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_sessions = false; //Tell other elements to expect session data
        $session_select_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            
            $session_select_data[$session_index][0] = $row['student_id']; //Tutor Session Id
            $id = $row['student_id'];
            $session_select_data[$session_index][1] = $row['session_start'];
            $session_select_data[$session_index][2] = $row['session_end'];
            $session_select_data[$session_index][3] = $row['day_of_week'];
            $session_select_data[$session_index][4] = $row['name'];
            
            $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutee WHERE tutee_id=$id";
            $result_subject = $conn->query($sql_subject);
            $data = $result_subject->fetch_assoc();

            //Each tutor session can only have a single subject. This program will filter out any id that is zero
            if($data['global_subject_id'] == 0) $session_select_data[$session_index][5] = $data['local_subject_id']; //Session subject
            else $session_select_data[$session_index][5] = $data['global_subject_id']; //Session subject id
            //Query the subject list to get the subjects's name
            $subject_id = $session_select_data[$session_index][5];
            $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
            $result_subject = $conn->query($sql_subject_name);
            $data2 = $result_subject->fetch_assoc();
            $session_select_data[$session_index][6] = $data2['name'];//Subject english name
            //Increment the session index the data is stored under
            $session_index += 1;
        }
        return $session_select_data;
    } 
    else 
    {
        return 1;
    }
}
function get_tutor_session_select_data($sql,$conn)
{   //(0)id, (1)potential session start times, (2)potential session end time, (3)name of the person, (4)subject_id, (5) Subject name.
    //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero
        $no_sessions = false; //Tell other elements to expect session data
        $session_select_data = []; //Output data of each row into an array of session ids
        $session_index = 0;
        while($row = $result->fetch_assoc()) {
            
            $session_select_data[$session_index][0] = $row['student_id']; //Tutor Session Id
            $id = $row['student_id'];
            $session_select_data[$session_index][1] = $row['session_start'];
            $session_select_data[$session_index][2] = $row['session_end'];
            $session_select_data[$session_index][3] = $row['day_of_week'];
            $session_select_data[$session_index][4] = $row['name'];

            $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutor WHERE tutor_id=$id";
            $result_start_time = $conn->query($sql_subject);
            $data = $result_start_time->fetch_assoc();
            
            //Each tutor session can only have a single subject. This program will filter out any id that is zero
            if($data['global_subject_id'] == 0) $session_select_data[$session_index][5] = $data['local_subject_id']; //Session subject
            else $session_select_data[$session_index][5] = $data['global_subject_id']; //Session subject id
            //Query the subject list to get the subjects's name
            $subject_id = $session_select_data[$session_index][5];
            $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
            $result_subject = $conn->query($sql_subject_name);
            $data2 = $result_subject->fetch_assoc();
            $session_select_data[$session_index][6] = $data2['name'];//Subject english name
            //Increment the session index the data is stored under
            $session_index += 1;
        }
        return $session_select_data;
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
            
            $session_select_data[$session_index][0] = $row['student_id']; //Tutor Session Id
            $id = $row['student_id'];
            $session_select_data[$session_index][1] = $row['session_start'];//Session start time
            $session_select_data[$session_index][2] = $row['session_end'];//Session end time
            $session_select_data[$session_index][3] = $row['day_of_week'];//The day of the week Monday-Sunday in a 1-7 format
            $session_select_data[$session_index][4] = $row['name'];//Gets the person's name

            if ($status == TRUE) {
                $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutor WHERE tutor_id=$id";
                $result_subject = $conn->query($sql_subject);
                $data = $result_subject->fetch_assoc();
                //querys the database to obtain the global_subject_id and local_subject_id from the tutor table
            }else{
                $sql_subject = "SELECT global_subject_id,local_subject_id FROM 6969_subjects_tutee WHERE tutee_id=$id";
                $result_subject = $conn->query($sql_subject);
                $data = $result_subject->fetch_assoc();
                //querys the database to obtain the global_subject_id and local_subject_id from the tutee table
            }

            //Each tutor session can only have a single subject. This program will filter out any id that is zero
            if($data['global_subject_id'] == 0) $session_select_data[$session_index][5] = $data['local_subject_id']; //Session subject
            else $session_select_data[$session_index][5] = $data['global_subject_id']; //Session subject id
            //Query the subject list to get the subjects's name
            $subject_id = $session_select_data[$session_index][5];
            $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
            $result_subject = $conn->query($sql_subject_name);
            $data2 = $result_subject->fetch_assoc();
            $session_select_data[$session_index][6] = $data2['name'];//Subject english name
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
?>