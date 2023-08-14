//THIS PAGE CONTAINS ALL THE JS USED FOR THIS SITE
//EACH PAGE WILL CONTAIN A "MARKER" DIV SO ONLY THE CODE THAT IS NEEDED WILL BE EXECUTED
//TO ADD ANOTHER PAGE CREATE AN EMPTY DIV TAG WITH A UNIQUE ID AND CHECK IF THAT EXISTS

function copyToClip(str) {
    function listener(e) {
      e.clipboardData.setData("text/html", str);
      e.clipboardData.setData("text/plain", str);
      e.preventDefault();
    }
    document.addEventListener("copy", listener);
    document.execCommand("copy");
    document.removeEventListener("copy", listener);
    alert("Copied the text");
};




//Store days of week and months for future use
const weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
const months = ["January","February","March","April","May","June","July","August","September","October","November","December"]
const date_superscript = ["st","nd","th"];

//Get current date and time
const currentDate = new Date();
let day_of_week = weekday[currentDate.getDay()];
let time = currentDate.getHours() + ":" + currentDate.getMinutes();
let date_of_month = currentDate.getDate();
let current_month = months[currentDate.getMonth()];


//Show the date and time on login home and calendar 3
if(document.getElementById("index_date_time"))
{
    //Get Date time div tag
    let index_date_time = document.getElementById("index_date_time");

    //Figure out what suffix to use (credit to https://stackoverflow.com/questions/13627308/add-st-nd-rd-and-th-ordinal-suffix-to-a-number)
    function ordinal_suffix_of(i) {
        var j = i % 10,
            k = i % 100;
        if (j == 1 && k != 11) {
            return "st";
        }
        if (j == 2 && k != 12) {
            return "nd";
        }
        if (j == 3 && k != 13) {
            return "rd";
        }
        return "th";
    }
    //Check if time uses am or pm
    time_suffix = "am";
    if(currentDate.getHours() > 12) time_suffix = "pm";
    //Set div content to the current date and time with a line between each datapoint
    index_date_time.innerHTML = time + time_suffix + "<br>" + day_of_week + "<br>" + date_of_month + ordinal_suffix_of(date_of_month) + " " + current_month;
}

//If the profile page is the current page ===========================================================================================================
profile_edit_mode = false;
if(document.getElementById("profile_edit_button"))
{
    //Get the edit button
    let profile_edit_button = document.getElementById("profile_edit_button");

    //Find the subjects that php generated
    tutor_subject_divs = [];
    element_id = "tutor_0";
    i = 0;
    while(document.getElementById(element_id))
    {
        element_id = "tutor_" + i;
        tutor_subject_divs[i] = element_id;
        i += 1;
    }
    tutee_subject_divs = [];
    element_id = "tutee_0";
    i = 0;
    while(document.getElementById(element_id))
    {
        element_id = "tutee_" + i;
        tutee_subject_divs[i] = element_id;
        i += 1;
    }

    //Find the checkboxes that php generated
    checkbox_subject_divs = [];
    element_id = "checkbox_0";
    i = 0;
    while(document.getElementById(element_id))
    {
        element_id = "checkbox_" + i;
        checkbox_subject_divs[i] = element_id;
        i += 1;
    }
    //These loops add on extra id that is invalid, so this function will remove them
    tutor_subject_divs.pop();
    tutee_subject_divs.pop();

    //Get the checkbox elements
    checkbox_study_subjects = document.getElementById("tutoring_subjects_checkbox_studying");
    checkbox_tutor_subjects = document.getElementById("tutoring_subjects_checkbox_tutoring");

    //Get the subject card container div
    studying_subject_card_div = document.getElementById("studying_subject_cards");
    tutoring_subject_card_div = document.getElementById("tutoring_subject_cards");


    //If it is clicked toggle edit mode
    profile_edit_button.onclick = function() {
        profile_edit_mode = !profile_edit_mode;

        //If edit mode is on ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        if(profile_edit_mode)
        {
            //Set button to edit mode
            profile_edit_button.innerHTML = "Save";

            //Make the subject edit checkboxes visible
            checkbox_study_subjects.style.display = "flex";
            checkbox_tutor_subjects.style.display = "flex";
            
            //Hide subject cards when edit mode is on
            studying_subject_card_div.style.display = "none";
            tutoring_subject_card_div.style.display = "none";
        }
        else //Edit mode is off ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        {   
            //Set button to save mode  
            profile_edit_button.innerHTML = "Edit";

            //Make the subject edit checkboxes hidden
            checkbox_study_subjects.style.display = "none";
            checkbox_tutor_subjects.style.display = "none";

            //Make subject cards visible when edit mode is turned off
            studying_subject_card_div.style.display = "flex";
            tutoring_subject_card_div.style.display = "flex";

            //Check what subjects are checked, so we know what to save
            let subjects_status = "";
            for(i=0;i<checkbox_subject_divs.length-1;i++)
            {
                if(document.getElementById(checkbox_subject_divs[i]).checked) {subjects_status += "1";}
                else {subjects_status += "0";}
            }
            

            //Update the database with the new info
            const xhttp = new XMLHttpRequest();
            xhttp.open("GET", "secure_query.php?subjects=" + subjects_status);
            xhttp.send();
            //If the request failed, send user to error page
            xhttp.onreadystatechange = function() {
                if (this.readyState != 4 && this.status != 200) {
                    alert("Something went wrong, you are being redirected back to safety");
                    window.location = "http://localhost/dashboard/TutorMe";
                }
                if (this.readyState == 4 && this.status == 200) {
                    //Create the new subjects if needed
                    window.location.reload();
                }};
            
        }
        //End of section ===========================================================================================================================================
     };
     
}
//Calendar page
if(document.getElementById("calendar"))
{
    //Store the data from the ?xxx=xxx url
    var parts = window.location.search.substr(1).split("&");
    var $_GET = {};
    for (var i = 0; i < parts.length; i++) {
        var temp = parts[i].split("=");
        $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
    }

    //If error due to start time after end time
    if($_GET.invalid_time == "1")
    {
        alert("The session start time must be before the end time.");
    }
    //If error due to session overlap
    if($_GET.invalid_time == "2")
    {
        alert("You can not have multiple potential session slots overlapping on the same day, edit or delete the conflicting slot and try again.");
    }
}


//If the navbar is present ==================================================================================================================================================
if(document.getElementById("notification_bell"))
{
    var notif_content = document.getElementById("notif_content");

    notif_button = document.getElementById("notification_bell");
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = notif_button;

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    notif_function = function() {
        const xhttp = new XMLHttpRequest();
            xhttp.open("GET", "sys_page/notification_check.php");
            xhttp.send();
            //Use data sent back to create a new notification card
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //Check if any data was returned
                    if(this.responseText != "nodata")
                    {
                        // Converting JSON-encoded string to JS object
                        var notif_data = JSON.parse(this.responseText);
                        //Set the number of notifications to the length of the parsed array
                        document.getElementById("number_notif").innerHTML = notif_data.length;
                        //Delete all children of the notification menu
                        while (notif_content.hasChildNodes()) {
                            notif_content.removeChild(notif_content.firstChild);
                            }
                        for(var i = 0; i < notif_data.length; i++){
                            //Create the relevant session requests
                            var session_card = document.createElement("div");
                            session_card.className = "session_card";
                            notif_content.appendChild(session_card);
                            var tutor_name = document.createElement("p");
                            session_card.appendChild(tutor_name);
                            var subject_name = document.createElement("p");
                            session_card.appendChild(subject_name);
                            var time_start = document.createElement("p");
                            session_card.appendChild(time_start);
                            var time_end = document.createElement("p");
                            session_card.appendChild(time_end);
                            var av_prod = document.createElement("p");
                            session_card.appendChild(av_prod);
                            var av_expe = document.createElement("p");
                            session_card.appendChild(av_expe);
                        
                            //Set up links for buttons
                            var accept_link = document.createElement("a");
                            session_card.appendChild(accept_link);
                            var reject_link = document.createElement("a");
                            session_card.appendChild(reject_link);
                            
                            accept_link.href = "a_or_r_session.php?page=" + window.location.href + "&action=1&id=" + notif_data[i]['id'];
                            reject_link.href = "a_or_r_session.php?page=" + window.location.href + "&action=2&id=" + notif_data[i]['id'];
                        
                            //Create ui buttons
                            var accept_button = document.createElement("button");
                            accept_link.appendChild(accept_button);
                            accept_button.innerHTML = "Accept session";
                            var reject_button = document.createElement("button");
                            reject_link.appendChild(reject_button);
                            reject_button.innerHTML = "Reject session";
                        
                        
                            tutor_name.innerHTML = notif_data[i]['tutor_name'];
                            subject_name.innerHTML = notif_data[i]['subject_name'];
                            time_start.innerHTML = notif_data[i]['session_start'];
                            time_end.innerHTML = notif_data[i]['session_end'];
                            av_prod.innerHTML = "Productivity Rating: " + notif_data[i]['av_prod'];
                            av_expe.innerHTML = "Experience Rating: " + notif_data[i]['av_expe'];
                            
                            
                            
                        }
                    }

                }};
    }

    //Set the notifications to update every few seconds
    const notification_check_loop = setInterval(notif_function(),5000)
}

//If the session matching page is present
if(document.getElementById("session_matching"))
{
    
    //Check if any alerts are present
    //Store the data from the ?xxx=xxx url
    var parts = window.location.search.substr(1).split("&");
    var $_GET = {};
    for (var i = 0; i < parts.length; i++) {
        var temp = parts[i].split("=");
        $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
    }
    
    if($_GET.alert == '1') {alert("The request was sent to the tutee");}
    if($_GET.alert == '2') {alert("Something went wrong, try again later");}

    // Get the modal
    var modal_session_match = document.getElementById("session_accept_popup");

    // Get the button that opens the modal

    // Get the <span> element that closes the modal
    var span_session = document.getElementsByClassName("close_session_match")[0];

    var session_match_close_button = document.getElementById("session_match_close");

    // When the user clicks on <span> (x), close the modal
    span_session.onclick = function() {
        modal_session_match.style.display = "none";
    }

    session_match_close_button.onclick = function() {
        modal_session_match.style.display = "none";
    }
    
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal_session_match) {
            modal_session_match.style.display = "none";
        }
    }
    //Get the cards on the page
    pot_sesssion_cards = [];
    //A card has an id formatted as
    // x-x-x-x-x-x
    // table_id - subject_id  - tutee_id - tutor_id - start_time - end_time

    //Find all the elements with card as name
    
    pot_sesssion_cards = document.getElementsByName("card");
    
    for(var i = 0; i < pot_sesssion_cards.length; i++){
        pot_sesssion_cards[i].onclick = function() {
            modal_session_match.style.display = "block";
            modal_session_match.children[0].children[0].innerHTML = this.children[0].children[0].innerHTML;
            modal_session_match.children[0].children[1].innerHTML = this.children[0].children[3].innerHTML;
            modal_session_match.children[0].children[2].innerHTML = this.children[0].children[4].innerHTML;
            

            id_part_1 = this.id;

            //Add the next 10 dates to the dropdown menu
            //Get current date and time
            dropdown_date = document.getElementById("date");
            while (dropdown_date.hasChildNodes()) {
                dropdown_date.removeChild(dropdown_date.firstChild);
              }
            start_day = weekday.indexOf(this.children[0].children[4].textContent.substr(1, this.children[0].children[4].textContent.length-1));

            for (let i = 0; i < 11; i++)
            {
                var current_date = new Date();
                potential_date = new Date(current_date.setDate(current_date.getDate() + 7*i + start_day - current_date.getDay()));
                var current_date = new Date();
                if(current_date < potential_date) //If time is not in the past create element for option
                {
                    //Create new option based on current date
                    let option = document.createElement("option");
                    option.innerHTML = potential_date;
                    option.id = potential_date.getFullYear() + "-" + (potential_date.getMonth()+1) + "-" + potential_date.getDate() + " " + (potential_date.getHours()+1) + ":" + (potential_date.getMinutes()+1) + ":" + (potential_date.getSeconds()+1) + ":" + "000000";
                    dropdown_date.appendChild(option);
                    option.onclick = function() {
                        modal_session_match.children[0].children[6].href="tutor_accept.php?id=" + id_part_1 + "-" + this.id;         
                    }
                }
            }
            var options = dropdown_date.options;
            var id      = options[options.selectedIndex].id;
            var value   = options[options.selectedIndex].value;
            //Calculate the session hours
            start_time_array = this.children[0].children[1].innerHTML.match(/^\d+|\d+\b|\d+(?=\w)/g).map(function (v) {return +v;});
            end_time_array = this.children[0].children[2].innerHTML.match(/^\d+|\d+\b|\d+(?=\w)/g).map(function (v) {return +v;});
            hours = end_time_array[0] - start_time_array[0]
            min = (end_time_array[1] - start_time_array[1])
            min_in_hours = min/60
            hours_combined = hours + min_in_hours;
            hours_combined_rounded = Number((hours_combined).toFixed(1));

            //Check if the number should be displayed in mins or hours
            if(hours_combined_rounded < 1) final_time = min + " min";
            else final_time = hours_combined_rounded + " h";
            
            modal_session_match.children[0].children[3].innerHTML = "Max Session Length: " + final_time;


            //Set the id of the session card as a value to send if submit is pressed
            modal_session_match.children[0].children[6].href="tutor_accept.php?id=" + id_part_1 + "-" + id;
        }
    }
}

//If the sessions page is present
if(document.getElementById("session_page_marker"))
{
    var contact_detail_content = document.getElementById("contact_detail_content");

    // Get the modal
    var contact_detail_modal = document.getElementById("contact_detail_popup");

    // Get the <span> element that closes the modal
    var contact_detail_span = document.getElementsByClassName("contact_detail_close")[0];



    // When the user clicks on <span> (x), close the modal
    contact_detail_span.onclick = function() {
        contact_detail_modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == contact_detail_modal) {
            contact_detail_modal.style.display = "none";
        }
    }

    sesssion_cards = [];
    //Find all the elements with card as name
    sesssion_cards = document.getElementsByName("card");
    console.log(sesssion_cards);
    for(var i = 0; i < sesssion_cards.length; i++){
        sesssion_cards[i].onclick = function() {
            console.log("Children");
            console.log(this.children[0].children[4].children[0].innerHTML);
            //Get data from card p tags
            var tutee = this.children[0].children[0].children[0].innerHTML;
            var subject = this.children[0].children[1].children[0].innerHTML;
            var date = this.children[0].children[2].children[0].innerHTML;
            var starttime = this.children[0].children[3].children[0].innerHTML;
            var endtime = this.children[0].children[4].children[0].innerHTML;


            var tutor_email = this.children[6].innerHTML;
            var tutor_phone = this.children[7].innerHTML;
            var tutee_email = this.children[8].innerHTML;
            var tutee_phone = this.children[9].innerHTML;

            
        



            //Make the popup visible
            contact_detail_modal.style.display = "block";

            //Set tutor name
            contact_detail_content.children[2].innerHTML = "<b>Tutor:</b> "+tutor;
            //Set tutee name
            contact_detail_content.children[3].innerHTML = "<b>Tutee:</b> "+tutee;
            //Set Subject name
            contact_detail_content.children[4].innerHTML = "<b>Subject:</b> "+subject;
            //Set date
            contact_detail_content.children[5].innerHTML = "<b>Date:</b> "+date;
            //Set Start time
            contact_detail_content.children[6].innerHTML = "<b>Earliest start time:</b> "+starttime;
            //Set End time
            contact_detail_content.children[7].innerHTML = "<b>Latest end time:</b> "+endtime;

            //Set Contact Details

            //Tutor Contact
            contact_detail_content.children[9].innerHTML = "<b>Tutor Email:</b> "+tutor_email;
            contact_detail_content.children[10].innerHTML = "<b>Tutor Phone Num:</b> "+tutor_phone;
            
            //Tutor Contact
            contact_detail_content.children[11].innerHTML = "<b>Tutee Email:</b> "+tutee_email;
            contact_detail_content.children[12].innerHTML = "<b>Tutee Phone Num:</b> "+tutee_phone;
        }
    }
}