<?php
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}


//Check if the post variables are set
if(isset($_POST['experience']) && isset($_POST['productivity']))
{
    //Insert the feedback into the db
    include("sys_page/db_connect.php");
    //Set variables used in query
    $session_id = $_SESSION['session_id'];
    $user_id = $_SESSION['user_id'];
    $experience = $_POST['experience'];
    $productivity = $_POST['productivity'];
    $sql = "INSERT INTO `6969_feedback`(`session_id`, `user_id`, `experience`, `productivity`) VALUES ('$session_id','$user_id','$experience','$productivity')";

    if ($conn->query($sql) === TRUE) 
    {
        //If the query was a succsess check if a session should be set to recuring
        echo "New record created successfully in feedback table";
        header("Location: sessions.php");
    } else 
    {
        //If something went wrong show error msg and ask user to make a bug report
        echo "Error: " . $sql . "<br>" . $conn->error;
        echo "<p>Something went wrong, please fill out a bug report at <a href='https://forms.office.com/Pages/DesignPageV2.aspx?subpage=design&FormId=cykeNF9KHku4jCjioH-b7TtxZ5zZX8tNluxRgC8GdjVUMDZVWjJQQUJCMlhGMTVJOThLQVVBOEoxQi4u&Token=003f6a68591e489190bcbad4f2cdcaff'>this link</a></p>";
    }
}
else
{
    echo"form error";
}

?>