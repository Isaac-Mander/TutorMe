<?php
//Check if sent via correct form
if(!isset($_POST['username']) && !isset($_POST['password']))
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}
else
{
//Store data to check
$input_username = $_POST['username'];
$input_password = $_POST['password'];
//Check if the user exists in the database
//Connect to database
include("sys_page/db_connect.php");
$login_check_sql = "SELECT * FROM `6969_students` WHERE username = '$input_username'";
$result = $conn->query($login_check_sql); //Query database
if ($result->num_rows > 0) { //If the number of rows are not zero
    //Check if the password is the same as in the database
    $data = $result->fetch_assoc();
    //If credentials right setup user session tokens
    if(password_verify($input_password,$data['hashed_password']))
    {
        echo "Login successful";
        // Start the session
        session_start();
        $_SESSION['user_id'] = $data['id'];
        $_SESSION["school_code"] = "6969";
        $_SESSION["user"] = $input_username;
        //Redirect to login homepage
        header("Location: user_home.php");
    }
    else
    {
        echo "password is wrong lel";
    }
}
else //If user does not exist give error msg
{
    echo "Username or password is incorrect";
}

}
?>