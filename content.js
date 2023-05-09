

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
    //These loops add on extra id that is invalid, so this function will remove them
    tutor_subject_divs.pop();
    tutee_subject_divs.pop();

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
            var crosses_to_hide = document.getElementsByClassName("edit_cross");
            for(var i = 0; i < crosses_to_hide.length; i++){
                crosses_to_hide[i].style.display = "flex";
            }

            //Remove crosses if clicked
            for(i=0;i<tutee_subject_divs.length;i++)
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
            }
        }
        else //Edit mode is off ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        {   
            //Set button to save mode  
            profile_edit_button.innerHTML = "Edit";
            //Get the value of the input field and set the desc text to be =
            profile_desc_text.innerHTML = profile_desc_edit.value;
            profile_desc_edit.replaceWith(profile_desc_text);
            
            //Update the database with the new description
            const xhttp = new XMLHttpRequest();
            xhttp.open("GET", "secure_query.php?description=" + profile_desc_text.innerHTML);
            xhttp.send();

            //Update subject preferences
            profile_studying_parent = document.getElementById("studying");
            //If there are any preferences to update
            if(profile_studying_parent.children.length > 0)
            {
                
            }
            //Hide any crosses still visible using display = none
            var crosses_to_hide = document.getElementsByClassName("edit_cross");
            for(var i = 0; i < crosses_to_hide.length; i++){
                crosses_to_hide[i].style.display = "none";
            }
        }
        //End of section ===========================================================================================================================================

        
     };
     
}