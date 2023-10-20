
<!DOCTYPE html>
<!-- this is the guide page so that users if they don't know what to do will be able to easily navigate the site. -->
<?php include("sys_page/header.html"); ?>
<html lang="en">
  <!-- the styling/css linking for the page -->
<link rel="stylesheet" href="sys_page/styles.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<!-- this is the top of the page just informing them what's the page is -->
<h1 class="text-center">Welcome to the TutorMe guide!</h1>    
<p class="text-center pb-5">This is a tool to help clear up questions and kick start your TutorMe journey</p>

<!-- Step one, this is the page where we want to get the users to add sujects, and just get them on the info setting page -->
<h1 class="text-center pb-2 border-bottom border-2">Step 1:</h1>
<div class='flex-row d-flex justify-content-centre pb-5'><img class="img-fluid mx-auto" style="max-width:900px" src="sys_img\guide_image_profile.jpg" alt=""></div>
  <h1 class="text-center">User setup</h1>
  <h5 class="text-center">This is the setup page, and can be found by clicking the hamburger.</h5>
  <h5 class="text-center">Here you set the subjects that you need help with by pressing the addsubject button under the "need help with" section.</h5>
  <h5 class="text-center pb-2 border-bottom border-2">You set the subjects that you want to help tutor others with by pressing the addsubject button under the "Tutoring subjects" section.</h5>

<!-- After setting the subjects, the users only need to set the potential times to be able to start getting sessions set. -->
  <h1 class="text-center pb-2 border-bottom border-2">Step 2:</h1>
<div class='flex-row d-flex justify-content-centre pb-5'><img class="img-fluid mx-auto" style="max-width:900px" src="sys_img\guide_image_setting_times.jpg" alt=""></div>
<h1 class="text-center">Setting times</h1>
  <h5 class="text-center">This on the profile page, underneath the user setup</h5>
  <h5 class="text-center pb-2 border-bottom border-2">Here you set the times that you are free to be tutored/tutor others, type in the start and end times, and select the days you are free (only one at a time atm).</h5>
  
<!-- this step is more to explain the different components that are a part of the website -->
  <h1 class="text-center pb-2 border-bottom border-2">Step 3:</h1>
<div class='flex-row d-flex justify-content-centre pb-5'><img class="img-fluid mx-auto" style="max-width:900px" src="sys_img\guide_image_calendar.jpg" alt=""></div>
  <h1 class="text-center">Seeing times</h1>
  <h5 class="text-center">This on the profile page, underneath the user setup</h5>
  <h5 class="text-center pb-2 border-bottom border-2">Here you see the times that you are free in a calendar format.</h5>
  
<!-- Here just getting the user onto the session setting page so they can start matching sessions. -->
  <h1 class="text-center pb-2 border-bottom border-2">Step 4:</h1>
<div class='flex-row d-flex justify-content-centre pb-5'><img class="img-fluid mx-auto" style="max-width:900px" src="sys_img\guide_image_tutoring_others.jpg" alt=""></div>
<h1 class="text-center">Setting sessions</h1>
  <h5 class="text-center">This on the session & tutoring page, underneath the different times</h5>
  <h5 class="text-center pb-2 border-bottom border-2">Here you can match you times with when other people are free, here you can see the people that need tutoring and select one person to tutor.</h5>
  
<!-- This step is for after the user has set up the session and just helping them manage the different sessions -->
  <h1 class="text-center pb-2 border-bottom border-2">Step 5:</h1>
<div class='flex-row d-flex justify-content-centre pb-5'><img class="img-fluid mx-auto" style="max-width:900px" src="sys_img\guide_image_sessions.jpg" alt=""></div>
<h1 class="text-center">Seeing Sessions</h1>
  <h5 class="text-center">This on the session & tutoring page, above setting sessions</h5>
  <h5 class="text-center pb-2 border-bottom border-2">Here you can see what times you have tutoring sessions, and you can review pending requests and review past sessions.</h5>
  
<!-- this is just a nice step to make the user feel more welcomed to the website. -->
  <h5 class="text-center">You are now finished with the TutorMe guide</h5>
  <h5 class="text-center pb-2 border-bottom border-2">You are now ready to start your TutorMe experience and improve your academic experience.</h5>
<!-- the linking to the JavaScript code and library -->
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> 
</body>
</html>