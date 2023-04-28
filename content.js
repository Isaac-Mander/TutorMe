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