
<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get relevant info from session
$user_id = $_SESSION['user_id'];

//Import functions
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
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
  
<div id=foo style="display:none">
Dear (Insert Name),

I am contacting you to talk about organising a place for the tutoring program that has been agreed upon. Would you be able to go to (Place 1) or (Place 2)? If not do you have any suggestions?

Sincerely (Your Name)
</div>
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
  <body>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="sys_page/styles.css">
  <section class="h-100 h-custom" style="background-color: #8fc4b7;">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-8 col-xl-6">
          <div class="card rounded-3">
            <div class="card-body p-4 p-md-5">
            <p>  <h3 class="border border-3 rounded border-success mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">Dear (Insert Name),</h3> </p>
<p>  <h5 class="border border-3 rounded border-success mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">I am contacting you to talk about organising a place for the tutoring program that has been agreed upon. Would you be able to go to (Place 1) or (Place 2)? If not do you have any suggestions?</h5> </p>
<p>  <h3 class="border border-3 rounded border-success mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">Sincerely (Your Name)</h3> </p>
<button onclick="copyToClip(document.getElementById('foo').innerHTML)">
  Copy the stuff
  </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
<style>
@media (min-width: 1025px) {
  .h-custom {
  height: 100vh !important;
  }
}
</style>
</html>