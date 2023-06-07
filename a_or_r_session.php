<?php
//Check if the user to logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get if $_GET is set
if(isset($_GET['action']) && isset($_GET['id']) && isset($_GET['page']))
{
    $page = $_GET['page'];
    $id = $_GET['id'];
    $action = $_GET['action'];
    //Accept session
    if($action == "1")
    {
        $sql = "UPDATE `6969_tutor_session` SET `is_active`='1' WHERE `id`=$id";
    }
    //Reject session
    else if($action == "2")
    {
        $sql = "DELETE FROM `6969_tutor_session` WHERE `id`=$id";
    }
    //Connect to db
    include("sys_page/db_connect.php");
    if ($conn->query($sql) === TRUE) {
        echo "Record altered successfully";
      } else {
        echo "Error altering record: " . $conn->error;
      }
    $conn->close();
    header("Location: " . $page);
}

else
{
    echo "error";
}

?>