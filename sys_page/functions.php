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
            //tutor_session_data is formatted like:
            //[index of session in array][(0)Session id,  (1)session start time, (2)session end time,   (3)session tutee id,   (4)session tutee name,  (5)session tutor id,   (6)session tutor name,   (7)subject_id,   (8)subject_name]
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
    } else {
        echo "0 results";
        $no_today_sessions = true; //This variable tells the page to show the no sessions today msg
    }

    //Return the result of the query 
    if($no_today_sessions) return 1;
    else return $tutor_session_data;
}
?>