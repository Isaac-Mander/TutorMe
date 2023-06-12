

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

    //Get the desc text dom element
    let profile_desc_text = document.getElementById('profile_desc_text');

    //Create a input text box to replace the desc text element when in edit mode
    let profile_desc_edit = document.createElement("input");
    profile_desc_edit.type = "text";
    profile_desc_edit.id = "profile_desc_edit";

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
            //Get the value of the desc text and set the input field to be =
            profile_desc_edit.value = profile_desc_text.innerHTML;
            profile_desc_text.replaceWith(profile_desc_edit);

            //Set remove crosses to display: flex
            //CROSSES ARE NOT CURRENTLY USED
            /*var crosses_to_hide = document.getElementsByClassName("edit_cross");
            for(var i = 0; i < crosses_to_hide.length; i++){
                crosses_to_hide[i].style.display = "flex";
            }*/

            //Remove crosses if clicked
            //THIS CODE IS NOT CURRENTLY USED BY ANYTHING
            /*for(i=0;i<tutee_subject_divs.length;i++)
            {
                let div_element = document.getElementById(tutee_subject_divs[i])
                div_element.onclick = function() {
                    div_element.remove();
                }
            }
            for(i=0;i<tutor_subject_divs.length;i++)
            {
                let div_element = document.getElementById(tutor_subject_divs[i])
                div_element.onclick = function() {
                    div_element.remove();
                }
            }*/

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
            //Get the value of the input field and set the desc text to be =
            profile_desc_text.innerHTML = profile_desc_edit.value;
            profile_desc_edit.replaceWith(profile_desc_text);
            
            
            //Hide any crosses still visible using display = none
            //THIS CODE IS NOT CURRENTLY USED BY ANYTHING
            /*var crosses_to_hide = document.getElementsByClassName("edit_cross");
            for(var i = 0; i < crosses_to_hide.length; i++){
                crosses_to_hide[i].style.display = "none";
            }*/

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
            xhttp.open("GET", "secure_query.php?description=" + profile_desc_text.innerHTML + "&subjects=" + subjects_status);
            xhttp.send();
            //If the request failed, send user to error page
            xhttp.onreadystatechange = function() {
                if (this.readyState != 4 && this.status != 200) {
                    alert("Something went wrong, you are being redirected back to safety");
                    window.location = "http://localhost/dashboard/TutorMe";
                }
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
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

    //Set the notifications to update every few seconds
    const notification_check_loop = setInterval(function() {
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
                            console.log(notif_data[i]);
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
                            subject_name.innerHTML = notif_data[i]['subject'];
                            time_start.innerHTML = notif_data[i]['session_start'];
                            time_end.innerHTML = notif_data[i]['session_end'];
                            
                        }
                    }

                }};
    },5000)
}

//If the session matching page is present
if(document.getElementById("session_matching"))
{
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
            
            //Add the next 10 dates to the dropdown menu
            //Get current date and time
            dropdown_date = document.getElementById("date");
            while (dropdown_date.hasChildNodes()) {
                dropdown_date.removeChild(dropdown_date.firstChild);
              }
            start_day = weekday.findIndex((element) => element > this.children[0].children[4].innerHTML) + 1;
            for (let i = 1; i < 11; i++)
            {
                var d2 = new Date();
                d2 = new Date(d2.setDate(d2.getDate() + 7*i + start_day - d2.getDay()));
                //Create new option based on current date
                let option = document.createElement("option");
                option.innerHTML = d2;
                option.id = d2.toISOString().slice(0, 19).replace('T', ' ');
                dropdown_date.appendChild(option);
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
            modal_session_match.children[0].children[6].href="tutor_accept.php?id=" + this.id + "-" + id;
        }
    }
}