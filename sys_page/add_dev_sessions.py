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
    hour_variable = random.randrange(-5,5)
    min_variable = random.randrange(-15,15)
    time = f"{weekdays[weekday]} {months[current_month]} {day} 2023 {12+hour_variable}:{30+min_variable}:00 GMT+1300 (New Zealand Daylight Time)"
    #Add a bit of variablility in the times
    hour_variable2 = random.randrange(-5,5)
    min_variable2 = random.randrange(-15,15)
    time2 = f"{weekdays[weekday]} {months[current_month]} {day} 2023 {12+hour_variable2}:{30+min_variable2}:00 GMT+1300 (New Zealand Daylight Time)"
    #Add a bit of variablility in the times
    hour_variable3 = random.randrange(-5,5)
    min_variable3 = random.randrange(-15,15)
    time3 = f"{weekdays[weekday]} {months[current_month]} {day} 2023 {12+hour_variable3}:{30+min_variable3}:00 GMT+1300 (New Zealand Daylight Time)"
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

    sql = f"INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `time`, `global_subject_id`, `local_subject_id`) VALUES ('{tutee_id}', '{tutor_id}', '0', '0', '{time}', '{global_subject_id}', '0');"    
    sql2 = f"INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `time`, `global_subject_id`, `local_subject_id`) VALUES ('{tutee_id2}', '{tutor_id2}', '0', '0', '{time2}', '{global_subject_id}', '0');"    
    sql3 = f"INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `time`, `global_subject_id`, `local_subject_id`) VALUES ('{tutee_id3}', '{tutor_id3}', '0', '0', '{time3}', '{global_subject_id}', '0');"    
    if(sql[174] == " "):
        sql_to_edit = list(sql)
        sql_to_edit[174] = sql_to_edit[173]
        sql_to_edit[173] = "0"
        sql = ''.join(sql_to_edit)
        sql = sql[:175] + " " + sql[175:]
    print(sql)
    if(sql2[174] == " "):
        sql_to_edit = list(sql2)
        sql_to_edit[174] = sql_to_edit[173]
        sql_to_edit[173] = "0"
        sql2 = ''.join(sql_to_edit)
        sql2 = sql2[:175] + " " + sql2[175:]
    print(sql2)
    if(sql3[174] == " "):
        sql_to_edit = list(sql3)
        sql_to_edit[174] = sql_to_edit[173]
        sql_to_edit[173] = "0"
        sql3 = ''.join(sql_to_edit)
        sql3 = sql3[:175] + " " + sql3[175:]
    print(sql3)