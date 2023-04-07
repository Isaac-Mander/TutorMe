#A short python program to make generating new sessions easier
import random

output = ""
weekdays = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"]
months = ["Mar","Apr","May","Jun","Jul","Aug"]
days = [31,30,31,30,31,31]

day = 1
weekday = 2
current_month = 0


tutee_id = 1
tutor_id = 3
global_subject_id = 3
for i in range(80):
    #Add a bit of variablility in the times
    hour = random.randrange(9,20)
    minute = random.randrange(0,60)
    if(day<10): day_string = f"0{day}"
    else: day_string = f"{day}"
    if(hour<10): hour_string = f"0{hour}"
    else: hour_string = f"{hour}"
    if(minute<10): min_string = f"0{minute}"
    else: min_string = f"{minute}"
    date = f"{2023}-0{current_month+4}-{day_string}"
    time = f"{hour_string}:{min_string}:00.000000"
    
    if(days[current_month]-1<day):
        day = 1
        current_month += 1
    else:
        day += 1

    if(weekday >= 6): weekday += -6
    else: weekday += 1

    if(random.randrange(0,2)):
        tutee_id = 1
        tutor_id = 3
    else:
        tutee_id = 3
        tutor_id = 1
    if(random.randrange(0,2)):
        tutee_id2 = 1
        tutor_id2 = 3
    else:
        tutee_id2 = 3
        tutor_id2 = 1
    if(random.randrange(0,2)):
        tutee_id3 = 1
        tutor_id3 = 3
    else:
        tutee_id3 = 3
        tutor_id3 = 1

    sql = f"INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `session_start`, `session_end`, `global_subject_id`, `local_subject_id`) VALUES ('{tutee_id}', '{tutor_id}', '0', '0', '{date} {time}', '{date} {time}', '{global_subject_id}', '0');"    
   
    print(sql)