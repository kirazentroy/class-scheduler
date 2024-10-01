var assignrow = -1,
    currentrow = [],
    counter = 1,
    bookmark = -1,
    subjectcount = {},
    timecap = 0,
    fixVacantRooms = [],
    occupiedRooms = [];

// selectsubject

function selectsubject() {
    let semester = $('#selsemester').val();
    let course = $('#studcourse').val();
    let subject = `<option value="0" disabled selected>No Value</option>`;
    if (semester.length > 0) {
        $.getJSON(`../queries/subjects.php?subject=${course}&semester=${semester}`, function (data) {
            if (data.length === 0) {
                $('#subject').html(subject);
            } else {
                subject = '';
                data.forEach(eleSub => {
                    subject += `<option value="${eleSub['subject_id']}">${eleSub['subject_code']}</option>`;
                })
                $('#subject').html(subject);
            }

            selecteddescription();
        });
    }
}


function selecteddescription() {
    $('#getdescription').addClass('fw-bold');
    $.getJSON(`../queries/getdescriptioninfo.php?description=${$('#subject').val()}`, function (data) {
        $('#getdescription').html(`${data}`);
    });
}

function selectroom() {
    let rooms = '';
    $.getJSON(`../queries/getroom.php?getrooms=${$('#roombuilding').val()}`, function (data) {
        data.forEach(room => {
            rooms += `<option value="${room.room_id}">${room.room_number} ${room.room_floor === '1' ? '1st ' : room.room_floor === '2' ? '2nd ' : room.room_floor === '3' ? '3rd ' : '4th '}Floor</option>`;
        });
        $('#room').html(rooms);
    });
};

function starttimecheck(id) {
    let starthour = `#timestarthour_${id}`;
    let ampm = `#timestart_${id}`;
    if (Number($(starthour).val()) <= 6 || Number($(starthour).val()) === 12) {
        $(ampm + " option[value='2']").prop('selected', true);
        $(ampm + " option[value='1']").addClass('d-none');
    } else {
        $(ampm + " option[value='1']").removeClass('d-none');
    }
}

function endtimecheck(id) {
    let endhour = `#timeendhour_${id}`;
    let ampm = `#timeend_${id}`;
    if (Number($(endhour).val()) <= 6 || Number($(endhour).val()) === 12) {
        $(ampm + " option[value='2']").prop('selected', true);
        $(ampm + " option[value='1']").addClass('d-none');
    } else {
        $(ampm + " option[value='1']").removeClass('d-none');
    }
}

const weekdays = {
    'mon': 'Monday',
    'tue': 'Tuesday',
    'wed': 'Wednesday',
    'thu': 'Thursday',
    'fri': 'Friday',
    'sat': 'Saturday'
};

// dagdag sa counter kung wala nagconflict
function counterincrement() {
    counter++;
    // console.log(counter);
    promptsched(currentrow);
}

// walay madagdag sa counter kung naay conflict
function remaincounter() {
    counter = counter;
    promptsched(currentrow);
    // console.log(counter, currentrow);
}

// pang check sa checkbox
function checktrue(id) {
    $(id).prop('checked', true);
    Swal.fire({
        icon: 'success',
        title: 'Processing',
        timer: 700,
        showConfirmButton: false
    });
    counterincrement();
    promptsched(currentrow);
}

// pang balik ug uncheck kay naay conflict
function checkfalse(id) {
    currentrow.pop();
    $(id).prop('checked', false);
    remaincounter();
}


function timetoseconds(time) {
    let timestring = time.toString();
    let seconds = 0;
    if (timestring.length >= 3) {
        seconds += Math.floor(time / 100) * 60 * 60;
        seconds += (time % 100) * 60;
    } else {
        seconds += (time % 100) * 60;
    }
    return seconds;
}

function timedifference(end, start) {
    return timetoseconds(end) - timetoseconds(start);
}

function timeconvertion(time) {
    if (typeof (time) === 'string') {
        time = Number(time);
    }
    if (time >= 10100 && time <= 11159) {
        time -= 10000;
    } else if (time >= 11200 && time <= 11259) {
        time -= 11200;
    } else if (time >= 20100 && time <= 21159) {
        time -= 20000;
        time += 1200;
    } else if (time >= 21200 && time <= 21259) {
        time -= 20000;
        // time += 100;
    }
    return time;
}

let hourconvertions = { 0: '12', 1: '01', 2: '02', 3: '03', 4: '04', 5: '05', 6: '06', 7: '07', 8: '08', 9: '09', 10: '10', 11: '11' };
let minuteconvertion = { 0: '00', 1: '01', 2: '02', 3: '03', 4: '04', 5: '05', 6: '06', 7: '07', 8: '08', 9: '09' };
function ampmConvertion(time) {
    time = timeconvertion(time);
    let convertion = '';
    if (time < 1200) {
        //  'am';
        convertion += hourconvertions[Math.floor(time / 100)];
        convertion += ':';
        convertion += ((time % 100) < 10 ? minuteconvertion[time % 100] : (time % 100));
        convertion += ' am';
    } else {
        time -= 1200;
        convertion += hourconvertions[Math.floor(time / 100)];
        convertion += ':';
        convertion += ((time % 100) < 10 ? minuteconvertion[time % 100] : (time % 100));
        convertion += ' pm';
        //  'pm';
    }

    return convertion;
}

let newArr = [];

function inputWeekdays(array, day) {
    array.sort((a, b) => timeconvertion(a.start_time) - timeconvertion(b.start_time));
    if (array.length === 0) {
        let noschedyet = `#input${day}0`;
        $(noschedyet).html(`<span style="color: green;">Not occupied</span>`);
    } else {
        for (let i = 0; i < array.length; i++) {
            let start = Number(array[i].start_time);
            let end = Number(array[i].end_time);
            if (i === 0) {
                if (start === 10700) {
                    newArr.push(end);
                } else {
                    newArr.push(10700);
                    newArr.push(start, end);
                }
            } else {
                if (start === newArr[newArr.length - 1]) {
                    newArr.pop();
                    newArr.push(end);
                } else {
                    newArr.push(start, end);
                }
            }
        }

        if (newArr[newArr.length - 1] !== 21155) {
            newArr.push(21155);
        } else {
            newArr.pop();
        }

        let inputHtml = '';
        if (newArr.length === 0) {
            // alert('hello world');
            let find = `#input${day}0`;
            inputHtml = `<span style="color: red;">No vacant</span>`;
            $(find).html(inputHtml);
        } else {
            for (let k = 0; k < newArr.length / 2; k++) {
                let find = `#input${day}${k}`;
                inputHtml = `${ampmConvertion(newArr[k * 2])} - ${ampmConvertion(newArr[(k * 2) + 1])}`;
                $(find).html(inputHtml);
                // console.log(newArr[k*2], newArr[(k*2) + 1], Math.floor(newArr[k*2] / 100), Math.floor(newArr[(k*2)+1] / 100));
            }
        }


        newArr = [];
    }
}

function checkroom() {
    // alert('hello world');
    let semcheck = $('#semester').val();
    let sycheck = $('#schoolyear').val();
    let roomcheck = $('#room').val();
    if (roomcheck < 1) {
        Swal.fire({
            icon: 'info',
            title: `Please select a room.`,
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        checkroomvacancy(semcheck, sycheck, roomcheck);
        $('#showTable').modal('show');
    }

}

function checkroomvacancy(sem, sy, room) {
    // alert(room);
    let mondayArr = [];
    let tuesdayArr = [];
    let wednesdayArr = [];
    let thursdayArr = [];
    let fridayArr = [];
    let saturdayArr = [];
    let weekdayCounter = [2, 2, 2, 2, 2, 2];

    if (currentrow.length > 0) {
        for (let i = 0; i < currentrow.length; i++) {
            let occupiedSched = {};
            let valuesRow = $(currentrow[i]).val().split('_/');
            let weekday = valuesRow[4];
            if (Number(valuesRow[2]) === Number(sem) && valuesRow[7] === sy && Number(valuesRow[8]) === Number(room)) {
                if (weekday === 'Monday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    mondayArr.push(occupiedSched);
                    weekdayCounter[0]++;
                } else if (weekday === 'Tuesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    tuesdayArr.push(occupiedSched);
                    weekdayCounter[1]++;
                } else if (weekday === 'Wednesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    wednesdayArr.push(occupiedSched);
                    weekdayCounter[2]++;
                } else if (weekday === 'Thursday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    thursdayArr.push(occupiedSched);
                    weekdayCounter[3]++;
                } else if (weekday === 'Friday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    fridayArr.push(occupiedSched);
                    weekdayCounter[4]++;
                } else if (weekday === 'Saturday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    saturdayArr.push(occupiedSched);
                    weekdayCounter[5]++;
                }
            }
        }
    }
    let roomname = `#room option[value="${room}"]`
    $('#timeVacancyTitle').html(`Time Vacancy for room <strong style="color: red;">${$(roomname).html()}</strong> on <strong style="color: red;">Trimester - ${sem}</strong> S.Y <strong style="color: red;">${sy}</strong>`);

    $.getJSON(`../queries/getvacancy.php?getroomvacancy=${room}&schoolyear=${sy}&semester=${sem}`, function (data) {
        data.sort((a, b) => timeconvertion(a.start_time) - timeconvertion(b.start_time));
        // console.log(data);
        for (let i = 0; i < data.length; i++) {
            // dataArr.push(data[i]);
            if (data[i].weekday === 'Monday') {
                weekdayCounter[0]++;
                mondayArr.push(data[i]);
            } else if (data[i].weekday === 'Tuesday') {
                weekdayCounter[1]++;
                tuesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Wednesday') {
                weekdayCounter[2]++;
                wednesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Thursday') {
                weekdayCounter[3]++;
                thursdayArr.push(data[i]);
            } else if (data[i].weekday === 'Friday') {
                weekdayCounter[4]++;
                fridayArr.push(data[i]);
            } else if (data[i].weekday === 'Saturday') {
                weekdayCounter[5]++;
                saturdayArr.push(data[i]);
            }
        }
        let maxCounter = Math.max(...weekdayCounter);
        let tbodyTimeCheck = '';
        if (maxCounter === 2) {
            tbodyTimeCheck +=
                `<tr id="cleartable_0" class="text-center">
                    <td class="d-none"></td>
                    <td id="inputMonday0">Not occupied</td>
                    <td id="inputTuesday0">Not occupied</td>
                    <td id="inputWednesday0">Not occupied</td>
                    <td id="inputThursday0">Not occupied</td>
                    <td id="inputFriday0">Not occupied</td>
                    <td id="inputSaturday0">Not occupied</td>
                </tr>`;
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
        } else {
            for (let k = 0; k < maxCounter; k++) {
                tbodyTimeCheck +=
                    `<tr id="cleartable_${k}" class="text-center">
                        <td class="d-none"></td>
                        <td id="inputMonday${k}"></td>
                        <td id="inputTuesday${k}"></td>
                        <td id="inputWednesday${k}"></td>
                        <td id="inputThursday${k}"></td>
                        <td id="inputFriday${k}"></td>
                        <td id="inputSaturday${k}"></td>
                    </tr>`;
            }
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
            inputWeekdays(mondayArr, 'Monday');
            inputWeekdays(tuesdayArr, 'Tuesday');
            inputWeekdays(wednesdayArr, 'Wednesday');
            inputWeekdays(thursdayArr, 'Thursday');
            inputWeekdays(fridayArr, 'Friday');
            inputWeekdays(saturdayArr, 'Saturday');

            for (let j = 0; j < maxCounter; j++) {
                let toclear = `#cleartable_${j}`;
                let [clearmon, cleartue, clearwed, clearthu, clearfri, clearsat] = [`#inputMonday${j}`, `#inputTuesday${j}`, `#inputWednesday${j}`, `#inputThursday${j}`, `#inputFriday${j}`, `#inputSaturday${j}`];
                if ($(clearmon).html() === '' && $(cleartue).html() === '' && $(clearwed).html() === '' && $(clearthu).html() === '' && $(clearfri).html() === '' && $(clearsat).html() === '') {
                    $(toclear).remove();
                }
            }
        }
    });
}

function checkteacher() {
    let semcheck = $('#semester').val();
    let sycheck = $('#schoolyear').val();
    let teachercheck = $('#teachers').val();
    if (teachercheck < 1) {
        Swal.fire({
            icon: 'info',
            title: `Please select a teacher.`,
            showConfirmButton: false,
            timer: 1500
        });
        return;
    } else {
        checkteachervacancy(semcheck, sycheck, teachercheck);
    }
    $('#showTable').modal('show');
}

function checkteachervacancy(sem, sy, teacher) {
    let mondayArr = [];
    let tuesdayArr = [];
    let wednesdayArr = [];
    let thursdayArr = [];
    let fridayArr = [];
    let saturdayArr = [];
    let weekdayCounter = [2, 2, 2, 2, 2, 2];

    if (currentrow.length > 0) {
        for (let i = 0; i < currentrow.length; i++) {
            let occupiedSched = {};
            let valuesRow = $(currentrow[i]).val().split('_/');
            console.log(valuesRow);
            let weekday = valuesRow[4];
            // alert(weekday);
            if (Number(valuesRow[2]) === Number(sem) && valuesRow[7] === sy && Number(valuesRow[0]) === Number(teacher)) {
                if (weekday === 'Monday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    mondayArr.push(occupiedSched);
                    weekdayCounter[0]++;
                } else if (weekday === 'Tuesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    tuesdayArr.push(occupiedSched);
                    weekdayCounter[1]++;
                } else if (weekday === 'Wednesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    wednesdayArr.push(occupiedSched);
                    weekdayCounter[2]++;
                } else if (weekday === 'Thursday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    thursdayArr.push(occupiedSched);
                    weekdayCounter[3]++;
                } else if (weekday === 'Friday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    fridayArr.push(occupiedSched);
                    weekdayCounter[4]++;
                } else if (weekday === 'Saturday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    saturdayArr.push(occupiedSched);
                    weekdayCounter[5]++;
                }
            }
        }
    }

    let teachername = `#teachers option[value="${teacher}"]`;
    $('#timeVacancyTitle').html(`Time Vacancy for <strong style="color: red;">${$(teachername).html()}</strong> on <strong style="color: red;">Trimester - ${sem}</strong> S.Y <strong style="color: red;">${sy}</strong>`);

    $.getJSON(`../queries/getvacancy.php?getteachervacancy=${teacher}&schoolyear=${sy}&semester=${sem}`, function (data) {
        data.sort((a, b) => timeconvertion(a.start_time) - timeconvertion(b.start_time));
        // console.log(data);
        for (let i = 0; i < data.length; i++) {
            // dataArr.push(data[i]);
            if (data[i].weekday === 'Monday') {
                weekdayCounter[0]++;
                mondayArr.push(data[i]);
            } else if (data[i].weekday === 'Tuesday') {
                weekdayCounter[1]++;
                tuesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Wednesday') {
                weekdayCounter[2]++;
                wednesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Thursday') {
                weekdayCounter[3]++;
                thursdayArr.push(data[i]);
            } else if (data[i].weekday === 'Friday') {
                weekdayCounter[4]++;
                fridayArr.push(data[i]);
            } else if (data[i].weekday === 'Saturday') {
                weekdayCounter[5]++;
                saturdayArr.push(data[i]);
            }
        }
        let maxCounter = Math.max(...weekdayCounter);
        let tbodyTimeCheck = '';
        if (maxCounter === 2) {
            tbodyTimeCheck +=
                `<tr id="cleartable_0" class="text-center">
                    <td class="d-none"></td>
                    <td id="inputMonday0">Not occupied</td>
                    <td id="inputTuesday0">Not occupied</td>
                    <td id="inputWednesday0">Not occupied</td>
                    <td id="inputThursday0">Not occupied</td>
                    <td id="inputFriday0">Not occupied</td>
                    <td id="inputSaturday0">Not occupied</td>
                </tr>`;
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
        } else {
            for (let k = 0; k < maxCounter; k++) {
                tbodyTimeCheck +=
                    `<tr id="cleartable_${k}" class="text-center">
                        <td class="d-none"></td>
                        <td id="inputMonday${k}"></td>
                        <td id="inputTuesday${k}"></td>
                        <td id="inputWednesday${k}"></td>
                        <td id="inputThursday${k}"></td>
                        <td id="inputFriday${k}"></td>
                        <td id="inputSaturday${k}"></td>
                    </tr>`;
            }
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
            inputWeekdays(mondayArr, 'Monday');
            inputWeekdays(tuesdayArr, 'Tuesday');
            inputWeekdays(wednesdayArr, 'Wednesday');
            inputWeekdays(thursdayArr, 'Thursday');
            inputWeekdays(fridayArr, 'Friday');
            inputWeekdays(saturdayArr, 'Saturday');

            for (let j = 0; j < maxCounter; j++) {
                let toclear = `#cleartable_${j}`;
                let [clearmon, cleartue, clearwed, clearthu, clearfri, clearsat] = [`#inputMonday${j}`, `#inputTuesday${j}`, `#inputWednesday${j}`, `#inputThursday${j}`, `#inputFriday${j}`, `#inputSaturday${j}`];
                if ($(clearmon).html() === '' && $(cleartue).html() === '' && $(clearwed).html() === '' && $(clearthu).html() === '' && $(clearfri).html() === '' && $(clearsat).html() === '') {
                    $(toclear).remove();
                }
            }
        }
    });
}

function checkcourse() {
    let semcheck = $('#semester').val();
    let sycheck = $('#schoolyear').val();
    let section = '';
    if ($('#section').val() === undefined || $('#section').val() === null || $('#section').val() === '') {
        section = '';
    } else {
        section = $('#section').val();
    }
    let coursecheck = $('#course').val() + section;
    if (coursecheck < 1) {
        Swal.fire({
            icon: 'info',
            title: `Please select a course/section.`,
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        checkcoursevacancy(semcheck, sycheck, coursecheck);
        $('#showTable').modal('show');
    }
}

function checkcoursevacancy(sem, sy, course) {
    let mondayArr = [];
    let tuesdayArr = [];
    let wednesdayArr = [];
    let thursdayArr = [];
    let fridayArr = [];
    let saturdayArr = [];
    let weekdayCounter = [2, 2, 2, 2, 2, 2];

    if (currentrow.length > 0) {
        for (let i = 0; i < currentrow.length; i++) {
            let occupiedSched = {};
            let valuesRow = $(currentrow[i]).val().split('_/');
            let weekday = valuesRow[4];
            if (Number(valuesRow[2]) === Number(sem) && valuesRow[7] === sy && valuesRow[1] === course) {
                if (weekday === 'Monday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    mondayArr.push(occupiedSched);
                    weekdayCounter[0]++;
                } else if (weekday === 'Tuesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    tuesdayArr.push(occupiedSched);
                    weekdayCounter[1]++;
                } else if (weekday === 'Wednesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    wednesdayArr.push(occupiedSched);
                    weekdayCounter[2]++;
                } else if (weekday === 'Thursday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    thursdayArr.push(occupiedSched);
                    weekdayCounter[3]++;
                } else if (weekday === 'Friday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    fridayArr.push(occupiedSched);
                    weekdayCounter[4]++;
                } else if (weekday === 'Saturday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    saturdayArr.push(occupiedSched);
                    weekdayCounter[5]++;
                }
            }
        }
    }

    $('#timeVacancyTitle').html(`Time Vacancy for section <strong style="color: red;">${course}</strong> on <strong style="color: red;">Trimester - ${sem}</strong> S.Y <strong style="color: red;">${sy}</strong>`);

    $.getJSON(`../queries/getvacancy.php?getcoursevacancy=${course}&schoolyear=${sy}&semester=${sem}`, function (data) {
        data.sort((a, b) => timeconvertion(a.start_time) - timeconvertion(b.start_time));
        // console.log(data);
        for (let i = 0; i < data.length; i++) {
            // dataArr.push(data[i]);
            if (data[i].weekday === 'Monday') {
                weekdayCounter[0]++;
                mondayArr.push(data[i]);
            } else if (data[i].weekday === 'Tuesday') {
                weekdayCounter[1]++;
                tuesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Wednesday') {
                weekdayCounter[2]++;
                wednesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Thursday') {
                weekdayCounter[3]++;
                thursdayArr.push(data[i]);
            } else if (data[i].weekday === 'Friday') {
                weekdayCounter[4]++;
                fridayArr.push(data[i]);
            } else if (data[i].weekday === 'Saturday') {
                weekdayCounter[5]++;
                saturdayArr.push(data[i]);
            }
        }
        let maxCounter = Math.max(...weekdayCounter);
        let tbodyTimeCheck = '';
        if (maxCounter === 2) {
            tbodyTimeCheck +=
                `<tr id="cleartable_0" class="text-center">
                    <td class="d-none"></td>
                    <td id="inputMonday0">Not occupied</td>
                    <td id="inputTuesday0">Not occupied</td>
                    <td id="inputWednesday0">Not occupied</td>
                    <td id="inputThursday0">Not occupied</td>
                    <td id="inputFriday0">Not occupied</td>
                    <td id="inputSaturday0">Not occupied</td>
                </tr>`;
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
        } else {
            for (let k = 0; k < maxCounter; k++) {
                tbodyTimeCheck +=
                    `<tr id="cleartable_${k}" class="text-center">
                        <td class="d-none"></td>
                        <td id="inputMonday${k}"></td>
                        <td id="inputTuesday${k}"></td>
                        <td id="inputWednesday${k}"></td>
                        <td id="inputThursday${k}"></td>
                        <td id="inputFriday${k}"></td>
                        <td id="inputSaturday${k}"></td>
                    </tr>`;
            }
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
            inputWeekdays(mondayArr, 'Monday');
            inputWeekdays(tuesdayArr, 'Tuesday');
            inputWeekdays(wednesdayArr, 'Wednesday');
            inputWeekdays(thursdayArr, 'Thursday');
            inputWeekdays(fridayArr, 'Friday');
            inputWeekdays(saturdayArr, 'Saturday');

            for (let j = 0; j < maxCounter; j++) {
                let toclear = `#cleartable_${j}`;
                let [clearmon, cleartue, clearwed, clearthu, clearfri, clearsat] = [`#inputMonday${j}`, `#inputTuesday${j}`, `#inputWednesday${j}`, `#inputThursday${j}`, `#inputFriday${j}`, `#inputSaturday${j}`];
                if ($(clearmon).html() === '' && $(cleartue).html() === '' && $(clearwed).html() === '' && $(clearthu).html() === '' && $(clearfri).html() === '' && $(clearsat).html() === '') {
                    $(toclear).remove();
                }
            }
        }
    });
}

function checkassigned() {
    // alert('hello world');
    let semcheck = $('#semester').val();
    let sycheck = $('#schoolyear').val();
    let roomcheck = $('#room').val();
    let teachercheck = $('#teachers').val();
    let section = '';
    if ($('#section').val() === undefined || $('#section').val() === null || $('#section').val() === '') {
        section = '';
    } else {
        section = $('#section').val();
    }
    let coursecheck = $('#course').val() + section;

    checkassignedvacancy(semcheck, sycheck, roomcheck, teachercheck, coursecheck);
    $('#showTable').modal('show');

}

function checkassignedvacancy(sem, sy, room, teacher, course) {
    // alert(room);
    let mondayArr = [];
    let tuesdayArr = [];
    let wednesdayArr = [];
    let thursdayArr = [];
    let fridayArr = [];
    let saturdayArr = [];
    let weekdayCounter = [2, 2, 2, 2, 2, 2];

    if (currentrow.length > 0) {
        for (let i = 0; i < currentrow.length; i++) {
            let occupiedSched = {};
            let valuesRow = $(currentrow[i]).val().split('_/');
            let weekday = valuesRow[4];
            if (Number(valuesRow[2]) === Number(sem) && valuesRow[7] === sy && (Number(valuesRow[8]) === Number(room) || Number(valuesRow[0]) === Number(teacher) || valuesRow[1] === course)) {
                if (weekday === 'Monday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    mondayArr.push(occupiedSched);
                    weekdayCounter[0]++;
                } else if (weekday === 'Tuesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    tuesdayArr.push(occupiedSched);
                    weekdayCounter[1]++;
                } else if (weekday === 'Wednesday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    wednesdayArr.push(occupiedSched);
                    weekdayCounter[2]++;
                } else if (weekday === 'Thursday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    thursdayArr.push(occupiedSched);
                    weekdayCounter[3]++;
                } else if (weekday === 'Friday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    fridayArr.push(occupiedSched);
                    weekdayCounter[4]++;
                } else if (weekday === 'Saturday') {
                    occupiedSched['start_time'] = Number(valuesRow[5]);
                    occupiedSched['end_time'] = Number(valuesRow[6]);
                    saturdayArr.push(occupiedSched);
                    weekdayCounter[5]++;
                }
            }
        }
    }
    let roomname = `#room option[value="${room}"]`;
    let teachername = `#teachers option[value="${teacher}"]`;
    $('#timeVacancyTitle').html(`Time Vacancy for room <strong style="color: red;">${$(roomname).html()}</strong>, section <strong style="color: green;">${course}</strong> and <strong style="color: blue;">${$(teachername).html()}</strong> on <strong style="color: orange;">Trimester - ${sem}</strong> S.Y <strong style="color: violet;">${sy}</strong>`);

    $.getJSON(`../queries/getvacancy.php?getassignedvacancy=${room}&teacher=${teacher}&course=${course}&schoolyear=${sy}&semester=${sem}`, function (data) {
        data.sort((a, b) => timeconvertion(a.start_time) - timeconvertion(b.start_time));
        for (let i = 0; i < data.length; i++) {
            if (data[i].weekday === 'Monday') {
                weekdayCounter[0]++;
                mondayArr.push(data[i]);
            } else if (data[i].weekday === 'Tuesday') {
                weekdayCounter[1]++;
                tuesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Wednesday') {
                weekdayCounter[2]++;
                wednesdayArr.push(data[i]);
            } else if (data[i].weekday === 'Thursday') {
                weekdayCounter[3]++;
                thursdayArr.push(data[i]);
            } else if (data[i].weekday === 'Friday') {
                weekdayCounter[4]++;
                fridayArr.push(data[i]);
            } else if (data[i].weekday === 'Saturday') {
                weekdayCounter[5]++;
                saturdayArr.push(data[i]);
            }
        }
        let maxCounter = Math.max(...weekdayCounter);
        let tbodyTimeCheck = '';
        if (maxCounter === 2) {
            tbodyTimeCheck +=
                `<tr id="cleartable_0" class="text-center">
                    <td class="d-none"></td>
                    <td id="inputMonday0">Not occupied</td>
                    <td id="inputTuesday0">Not occupied</td>
                    <td id="inputWednesday0">Not occupied</td>
                    <td id="inputThursday0">Not occupied</td>
                    <td id="inputFriday0">Not occupied</td>
                    <td id="inputSaturday0">Not occupied</td>
                </tr>`;
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
        } else {
            for (let k = 0; k < maxCounter; k++) {
                tbodyTimeCheck +=
                    `<tr id="cleartable_${k}" class="text-center">
                        <td class="d-none"></td>
                        <td id="inputMonday${k}"></td>
                        <td id="inputTuesday${k}"></td>
                        <td id="inputWednesday${k}"></td>
                        <td id="inputThursday${k}"></td>
                        <td id="inputFriday${k}"></td>
                        <td id="inputSaturday${k}"></td>
                    </tr>`;
            }
            $("#tbodyTimeCheck").html(tbodyTimeCheck);
            inputWeekdays(mondayArr, 'Monday');
            inputWeekdays(tuesdayArr, 'Tuesday');
            inputWeekdays(wednesdayArr, 'Wednesday');
            inputWeekdays(thursdayArr, 'Thursday');
            inputWeekdays(fridayArr, 'Friday');
            inputWeekdays(saturdayArr, 'Saturday');

            for (let j = 0; j < maxCounter; j++) {
                let toclear = `#cleartable_${j}`;
                let [clearmon, cleartue, clearwed, clearthu, clearfri, clearsat] = [`#inputMonday${j}`, `#inputTuesday${j}`, `#inputWednesday${j}`, `#inputThursday${j}`, `#inputFriday${j}`, `#inputSaturday${j}`];
                if ($(clearmon).html() === '' && $(cleartue).html() === '' && $(clearwed).html() === '' && $(clearthu).html() === '' && $(clearfri).html() === '' && $(clearsat).html() === '') {
                    $(toclear).remove();
                }
            }
        }
    });
}


// kung way nakacheck dili pud mo activate ang save na button
function promptsched(scheds) {
    if (scheds.length === 0) {
        $('button#tosave').prop('disabled', true);
    } else if (scheds.length >= 1) {
        $('button#tosave').prop('disabled', false);
    }
}



//school year
function schoolyear() {
    let now = Date().split(' ')[3];
    let schoolyear = [Number(Number(now) - 1) + '-' + now, now + '-' + Number(Number(now) + 1)];
    let sudlanan = '';
    schoolyear.forEach(eleSY => {
        sudlanan += `<option value="${eleSY}">${eleSY}</option>`;
    })
    $('#schoolyear').html(sudlanan);
}



$(document).ready(function () {
    promptsched(currentrow);
    schoolyear();

    selectsubject();
    selectroom();
    instructorSubjectCount();

    $('#checksection').change(function () {
        if ($(this).is(':checked')) {
            $('#section').prop('disabled', false);
            $("#section option[value='']").prop('selected', false);
        } else if (!$(this).is(':checked')) {
            $('#section').prop('disabled', true);
            $("#section option[value='']").prop('selected', true);
        }
    })


    //uncheck button
    $('#uncheckall').bind('click', function () {
        $("input[onclick^='tapsched']").prop('checked', false);
        $("input[onclick^='tapsched']").removeAttr('checked');
        currentrow = [];
        // console.log(currentrow);
        counter = 1;
        promptsched(currentrow);
        // $('#tosave').prop('disabled', true);
    })

    //add row sched
    $('#addsched').click(function () {
        assignrow++;
        let add =
            `<tr id="addrow_${assignrow}">
                <td>
                    <span class="text-nowrap">
                        <select name="starthour" id="timestarthour_${assignrow}" onchange="starttimecheck(${assignrow});">
                            <option value="01">
                                01
                            </option>
                            <option value="02">
                                02
                            </option>
                            <option value="03" selected>
                                03
                            </option>
                            <option value="04">
                                04
                            </option>
                            <option value="05">
                                05
                            </option>
                            <option value="06">
                                06
                            </option>
                            <option value="07">
                                07
                            </option>
                            <option value="08">
                                08
                            </option>
                            <option value="09">
                                09
                            </option>
                            <option value="10">
                                10
                            </option>
                            <option value="11">
                                11
                            </option>
                            <option value="12">
                                12
                            </option>
                        </select>
                        <span>:</span>
                        <select name="startminute" id="timestartminute_${assignrow}">
                            <option value="00">00</option>
                            <option value="05">05</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30" selected>30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                        </select>
                        &nbsp;
                        <select name="start" id="timestart_${assignrow}">
                            <option value="1" class="d-none">am</option>
                            <option value="2" selected>pm</option>
                        </select>
                    </span>
                    &nbsp;-&nbsp;
                    <span class="text-nowrap">
                        <select name="endhour" id="timeendhour_${assignrow}" onchange="endtimecheck(${assignrow});">
                            <option value="01">
                                01
                            </option>
                            <option value="02">
                                02
                            </option>
                            <option value="03">
                                03
                            </option>
                            <option value="04">
                                04
                            </option>
                            <option value="05" selected>
                                05
                            </option>
                            <option value="06">
                                06
                            </option>
                            <option value="07">
                                07
                            </option>
                            <option value="08">
                                08
                            </option>
                            <option value="09">
                                09
                            </option>
                            <option value="10">
                                10
                            </option>
                            <option value="11">
                                11
                            </option>
                            <option value="12">
                                12
                            </option>
                        </select>
                        <span>:</span>
                        <select name="endminute" id="timeendminute_${assignrow}">
                            <option value="00">00</option>
                            <option value="05">05</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30" selected>30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            
                        </select>
                        &nbsp;
                        <select name="end" id="timeend_${assignrow}">
                            <option value="1" class="d-none">am</option>
                            <option value="2" selected>pm</option>
                        </select>
                    </span>
                </td>
                <td id="mon_${assignrow}_row"><input type="checkbox" id="days_mon_${assignrow}" class="form-check-input" onclick="tapsched2('#days_mon_${assignrow}');"></td>
                <td id="tue_${assignrow}_row"><input type="checkbox" id="days_tue_${assignrow}" class="form-check-input" onclick="tapsched2('#days_tue_${assignrow}');"></td>
                <td id="wed_${assignrow}_row"><input type="checkbox" id="days_wed_${assignrow}" class="form-check-input" onclick="tapsched2('#days_wed_${assignrow}');"></td>
                <td id="thu_${assignrow}_row"><input type="checkbox" id="days_thu_${assignrow}" class="form-check-input" onclick="tapsched2('#days_thu_${assignrow}');"></td>
                <td id="fri_${assignrow}_row"><input type="checkbox" id="days_fri_${assignrow}" class="form-check-input" onclick="tapsched2('#days_fri_${assignrow}');"></td>
                <td id="sat_${assignrow}_row"><input type="checkbox" id="days_sat_${assignrow}" class="form-check-input" onclick="tapsched2('#days_sat_${assignrow}');"></td>
                <td class="text-center"><button class="btn btn-sm btn-danger" id="remove_${assignrow}" onclick="removerow('remove_${assignrow}');"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                <input type="hidden" name="mon" id="mon_${assignrow}">
                <input type="hidden" name="tue" id="tue_${assignrow}">
                <input type="hidden" name="wed" id="wed_${assignrow}">
                <input type="hidden" name="thu" id="thu_${assignrow}">
                <input type="hidden" name="fri" id="fri_${assignrow}">
                <input type="hidden" name="sat" id="sat_${assignrow}">
            </tr>`;
        appends(add);
        $(`#addrow_${assignrow}`).addClass('customopacity');
        setTimeout(() => {
            $(`#addrow_${assignrow}`).removeClass('customopacity');
        }, 100);
    });

    // addbookmark
    $('#addbookmark').click(function () {
        bookmark++;
        let contenteditable =
            `<tr id="cont_${bookmark}" style="background:lightgray;">
                <td><p contenteditable="true" class="h5 mb-3" style="color:black;">Bookmark! (Editable)</p></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-center"><span class="btn btn-sm btn-danger" onclick="removenote('${bookmark}');"><i class="fa fa-trash" aria-hidden="true"></i></span></td>
            </tr>`;
        appends(contenteditable);
        $(`#cont_${bookmark}`).addClass('customopacity');
        setTimeout(() => {
            $(`#cont_${bookmark}`).removeClass('customopacity');
        }, 1000);
    });

    // pangsave na sa tanan
    $('#tosave').click(function () {
        let allid = '';
        let allvalues = [];
        if (currentrow.length === 0) {
            Swal.fire({
                icon: 'question',
                title: 'Please assign a schedule',
                timer: '1500'
            });
        } else {
            for (let i = 0; i < currentrow.length; i++) {
                allid = currentrow[i];
                allvalues.push($(allid).val());
            }
            $('#allvaluescontainer').attr('value', allvalues);
        }
    })
})

// removebookmark
function removenote(string) {
    let id = `#cont_${string}`;
    setTimeout(() => {
        setInterval(() => {
            $(id).remove();
        }, 500);
        $(id).addClass('deletednote');
    }, 0);
}
// avoid duplicate
function appends(text) {
    $('#tbody').prepend(text);
}

// one-click sched
function tapsched2(stringid) {
    let checked = $(stringid).is(':checked');
    //kung way sulod
    let [, days, row] = stringid.split('_');

    let [startA, startB, startC] = ("#timestarthour_" + row + "/#timestartminute_" + row + "/#timestart_" + row).split('/');
    let start = $(startC).val().toString() + $(startA).val().toString() + $(startB).val().toString();
    let [endA, endB, endC] = ("#timeendhour_" + row + "/#timeendminute_" + row + "/#timeend_" + row).split('/');
    let end = $(endC).val().toString() + $(endA).val().toString() + $(endB).val().toString();
    let maestra = $('#teachers').val();
    let subject = $('#subject').val();
    let section = '';
    if ($('#section').val() === undefined || $('#section').val() === null || $('#section').val() === '') {
        section = '';
    } else {
        section = $('#section').val();
    }
    let klasehanan = $('#course').val() + section;
    let sem = $('#semester').val();
    let sy = $('#schoolyear').val();
    let certainday = weekdays[days];
    let room = $('#room').val();
    let ipakangNaSched = `${maestra}_/${klasehanan}_/${sem}_/${subject}_/${certainday}_/${start}_/${end}_/${sy}_/${room}`;
    end = Number(end);
    start = Number(start);
    // end = timeconvertion(end);
    // start = timeconvertion(start);
    if (maestra < 1 || $('#course').val() < 1 || subject < 1 || sem < 1 || sy < 1 || room < 1) {
        $(stringid).prop('checked', false);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please fill up all the list in the right side.',
            showConfirmButton: false,
            timer: 2000
        });
    } else if (maestra > 0 && klasehanan.length > 0 && subject.length > 0 && sem > 0 && sy.length > 0 && room.length > 0) {
        if (start === end || end < start) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Time cannot be the same and time end cannot be less than time start.',
                showConfirmButton: false,
                timer: 1000
            });
            $(stringid).prop('checked', false);
        } else if (start != end) {
            // get the unit of certain subject to put a time cap
            $.getJSON(`../queries/getunit.php?subject=${subject}`, function (data) {
                // alert();
                // unit = Number(data[0]);
                timecap = (Number(data[0]) * (4 * 60 * 60)) / 3;
            });
            // mga values
            let queue = `#${days}`;
            let pakangId = `${queue}_${row}`;
            if (checked) {
                $(pakangId).attr('value', `${ipakangNaSched}`);
                currentrow.push(pakangId);
                // console.log(currentrow);
                // console.log($(pakangId).val());
                if (conflictchecker(currentrow, counter) === false) { // conflict is false
                    // alert('conflictchecker = false, now entering loopdatabase()');
                    loopdatabase(stringid, sy, sem, certainday, room, maestra, klasehanan, start, end, subject);
                } else if (conflictchecker(currentrow, counter) === 'exceeding') {
                    // alert('nag 4 hours na siya kapin');
                    Swal.fire({
                        title: 'Time limit already reached!',
                        text: "The subject you are about to enter already reached its time limit in certain course or section. Do you wish to proceed?",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // alert('now entering kung gi confirm nimo');
                            loopdatabase(stringid, sy, sem, certainday, room, maestra, klasehanan, start, end, subject);
                        } else {
                            checkfalse(stringid);
                        }
                    })
                } else if (conflictchecker(currentrow, counter) === 'continue') {
                    // alert('padayon wala pa nilapas ug 4 hours');
                    loopdatabase(stringid, sy, sem, certainday, room, maestra, klasehanan, start, end, subject);
                } else if (conflictchecker(currentrow, counter) === 'exceeds') {
                    // alert('padulong na mulapas');
                    Swal.fire({
                        title: 'Time exceeding!',
                        text: "The subject you enter is about exceed the maximum time allowed in certain course or section. You may reduce the time or do you wish to proceed?",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // alert('imo gi confirm padulong na sa loopdatabase()');
                            loopdatabase(stringid, sy, sem, certainday, room, maestra, klasehanan, start, end, subject);
                        } else {
                            checkfalse(stringid);
                        }
                    })
                }
                else if (conflictchecker(currentrow, counter) === true) { // conflict is true
                    // alert('conflict is true = pakyas');
                    checkfalse(stringid);
                }
            } else if (!checked) {
                $(stringid).prop('checked', false);
                currentrow.splice(currentrow.indexOf(pakangId), 1);
                counter--;
                promptsched(currentrow);
                // console.log(currentrow);
                let [decmaestra, decklasehanan, decsem, decsubject, deccertainday, decstart, decend, decsy, decroom] = $(pakangId).val().split('_/');
                // subjectcount[sy+'_'+sem+'_'+klasehanan+'_'+subject];
                if (subjectcount[decsy + '_' + decsem + '_' + decklasehanan + '_' + decsubject] === undefined) {
                    subjectcount[decsy + '_' + decsem + '_' + decklasehanan + '_' + decsubject] = 0;
                } else {
                    subjectcount[decsy + '_' + decsem + '_' + decklasehanan + '_' + decsubject] -= timedifference(timeconvertion(decend), timeconvertion(decstart));
                }
            }
        }
    }
}

function removerow(stringrow) {
    let [, removeid] = stringrow.split('_');
    let id = `tr[id=addrow_${removeid}]`;
    setTimeout(() => {
        setInterval(() => {
            $(id).remove();
        }, 1000);
        $(id).addClass('deletedrow');
    }, 0);
    removesched(currentrow, removeid);
    currentrow = removesched(currentrow, removeid);
    // console.log(currentrow);
    counter = currentrow.length + 1;
    promptsched(currentrow);
}

// conflict checker
function conflictchecker(arr, sangkoanan) {
    if (arr.length <= 1) {
        return false;
    } else if (arr.length > 1) {
        if (arr.length === sangkoanan) {
            let pangid = arr;
            let limit = sangkoanan;
            loops(pangid, limit);
            let conflictid = '';
            if (loops(pangid, limit) === 'false') {
                let subjectchecking = subjecthourscheck(pangid, limit);
                if (subjectchecking === 'exceeding') {
                    return 'exceeding';
                } else if (subjectchecking === 'exceeds') {
                    return 'exceeds';
                } else if (subjectchecking === 'continue') {
                    return false;
                }
            } else {
                conflictid = loops(pangid, limit) + '_row';
                setTimeout(function () {
                    setInterval(function () {
                        $(conflictid).addClass('conflictdetected');
                    }, 1);
                    $(conflictid).removeClass('conflictdetected');
                }, 1);
                return true;
            }
        }
    }
}

// loops of currentrows and the specified value of each index
function loops(ids, asataman) {
    for (let i = 0; i < ids.length - 1; i++) {
        let latest = ids[asataman - 1];
        let [maestra, course, sem, subject, weekday, st, nd, sy, room] = $(latest).val().split('_/');
        let start = Number(st);
        let end = Number(nd);
        start = timeconvertion(start);
        end = timeconvertion(end);
        for (let j = 0; j < ids.length - 1; j++) {
            let comparevalid = ids[j];
            let splitted = $(comparevalid).val().split('_/');
            let [valmaestra, valcourse, valsem, valsubject, valweekday, valstart, valend, valsy, valroom] = [splitted[0], splitted[1], splitted[2], splitted[3], splitted[4], Number(splitted[5]), Number(splitted[6]), splitted[7], splitted[8]];
            valstart = timeconvertion(valstart);
            valend = timeconvertion(valend);

            // conditions of conflict
            if (sy === valsy) {
                if (sem === valsem) {
                    if (weekday === valweekday) {
                        if (maestra === valmaestra) {
                            if (course === valcourse) {
                                if (valstart === start && valend === end) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Same time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s vse ve or vs sve e
                                else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                    if (room === valroom) {
                                        if (valstart === start && valend === end) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and same time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vse ve or vs sve e
                                        else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vs ve e or vs s e ve
                                        else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s evs ve or vs ves e
                                        else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                            continue;
                                        }
                                    }
                                }
                            } else if (valcourse != course) {
                                if (valstart === start && valend === end) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Same time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s vse ve or vs sve e
                                else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });

                                    return comparevalid;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                    if (room === valroom) {
                                        if (valstart === start && valend === end) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and same time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vse ve or vs sve e
                                        else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vs ve e or vs s e ve
                                        else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s evs ve or vs ves e
                                        else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                            continue;
                                        }
                                    }
                                }
                            }
                        } else if (maestra != valmaestra) {
                            if (course === valcourse) {
                                if (valstart === start && valend === end) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Same time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s vse ve or vs sve e
                                else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Occupying time conflict!',
                                        showConfirmButton: false,
                                        timer: 1100
                                    });
                                    return comparevalid;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                    if (room === valroom) {
                                        if (valstart === start && valend === end) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and same time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vse ve or vs sve e
                                        else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s vs ve e or vs s e ve
                                        else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Same room and occupying time conflict!',
                                                showConfirmButton: false,
                                                timer: 1100
                                            });
                                            return comparevalid;
                                        }
                                        // s evs ve or vs ves e
                                        else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                            continue;
                                        }
                                    }
                                }

                            } else if (valcourse != course) {
                                if (room === valroom) {
                                    if (valstart === start && valend === end) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Same room and same time conflict!',
                                            showConfirmButton: false,
                                            timer: 1100
                                        });
                                        return comparevalid;
                                    }
                                    // s vse ve or vs sve e
                                    else if ((start < valstart && valstart < end && end < valend) || (valstart < start && start < valend && valend < end)) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Same room and occupying time conflict!',
                                            showConfirmButton: false,
                                            timer: 1100
                                        });
                                        return comparevalid;
                                    }
                                    // s vs ve e or vs s e ve
                                    else if ((start <= valstart && start < valend && end >= valend && end > valstart) || (valstart <= start && valstart < end && valend >= end && valend > start)) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Same room and occupying time conflict!',
                                            showConfirmButton: false,
                                            timer: 1100
                                        });
                                        return comparevalid;
                                    }
                                    // s evs ve or vs ves e
                                    else if ((end <= valstart && end < valend && valstart < valend) || (valend <= start && valend < end && start < end)) {
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

    }
    return 'false';
}

// kini na mga function kay pang detect sa subject kung mulapas ba siya ug time cap (timecap depends on the unit of the selected subject) then mag prompt na dayun kung ipadayun ba niyag check ang checkbox or dili
function subjecthourscheck(ids, asataman) {
    // kung makalapos sa loop mag ihap napud siya ug hours sa subject time difference dayun
    let counter = 0;
    for (let k = 0; k < ids.length - 1; k++) {
        let latest = ids[asataman - 1];
        let [maestra, course, sem, subject, weekday, st, nd, sy, room] = $(latest).val().split('_/');
        let start = Number(st);
        let end = Number(nd);
        start = timeconvertion(start);
        end = timeconvertion(end);
        let hoursofsubject = timedifference(end, start);
        for (let l = 0; l < ids.length - 1; l++) {
            let comparevalid = ids[l];
            let splitted = $(comparevalid).val().split('_/');
            let [valmaestra, valcourse, valsem, valsubject, valweekday, valstart, valend, valsy, valroom] = [splitted[0], splitted[1], splitted[2], splitted[3], splitted[4], Number(splitted[5]), Number(splitted[6]), splitted[7], splitted[8]];
            valstart = timeconvertion(valstart);
            valend = timeconvertion(valend);
            if (sy === valsy && sem === valsem && subject === valsubject && course === valcourse) {
                counter += timedifference(valend, valstart);
                if (counter > timecap) {
                    // alert('ninglapas na ug 4 hours ang schedule');
                    return 'exceeding';
                } else if (counter <= timecap) {
                    continue;
                }
            }
        }
        counter += hoursofsubject;
        if (counter > timecap) {
            return 'exceeds';
        } else {
            return 'continue';
        }
    }
}

// convert time string to analog
function clockconvert(analog) {
    let str = analog;
    str = str.split('');
    let x = str.shift();
    str = str.join('');
    if (Number(x) === 2) {
        return Number(str) + 1200;
    } else {
        return Number(str);
    }
}

// remove sched
function removesched(row, wew) {
    let deletesched = [`#mon_${wew}`, `#tue_${wew}`, `#wed_${wew}`, `#thu_${wew}`, `#fri_${wew}`, `#sat_${wew}`];
    let salaon = row;
    let result;
    for (let i = 0; i < deletesched.length; i++) {
        if (deletesched[i] === salaon[salaon.length - 1]) {
            salaon = salaon.join(' ');
            salaon = salaon.replace(`${deletesched[i]}`, '');
            salaon = salaon.split(' ');
            salaon.pop();
        } else {
            salaon = salaon.join(' ');
            salaon = salaon.replace(`${deletesched[i]} `, '');
            salaon = salaon.split(' ');
        }
        result = salaon;
    }
    if (result[0] === '') {
        result.shift();
    }
    return result;
}

function returntheconflict(array) {

    for (let i = 0; i < array.length; i++) {
        if (array[i] > 0) {
            return i;
        }
    }
    return 0;
}

function loopdatabase(stringid, sy, sem, certainday, room, maestra, klasehanan, start, end, subject) {
    $.getJSON(`../queries/getconflicts.php?getconflicts=${sy}&semester=${sem}&weekday=${certainday}&room=${room}&teacher=${maestra}&course=${klasehanan}&start=${start}&end=${end}`, function (data) {
        if (data[0] === 'error') {
            data.shift();
            checkfalse(stringid);
            if (returntheconflict(data) === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Room already occupied!',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function () {
                    $("#room").attr('style', "background-color:red; font-weight:bold;");
                    setInterval(function () {
                        $("#room").removeAttr('style');
                    }, 3000);
                }, 0);
            } else if (returntheconflict(data) === 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Teacher already assigned the stipulated schedule!',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function () {
                    $("#teachers").attr('style', "background-color:red; font-weight:bold;");
                    setInterval(function () {
                        $("#teachers").removeAttr('style');
                    }, 3000);
                }, 0);
            } else if (returntheconflict(data) === 2 || returntheconflict(data) === 3) {
                Swal.fire({
                    icon: 'error',
                    title: 'Course already assigned the stipulated schedule!',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function () {
                    $("#course").attr('style', "background-color:red; font-weight:bold;");
                    setInterval(function () {
                        $("#course").removeAttr('style');
                    }, 3000);
                }, 0);
            }
            return;
        } else {
            for (let i = 0; i < data.length; i++) {
                if (data[i].length === 0) {
                    continue;
                } else {
                    if (checkconflicts(i, data, start, end, subject) === 0) {
                        checkfalse(stringid);
                        Swal.fire({
                            icon: 'error',
                            title: 'Room already occupied!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function () {
                            $("#room").attr('style', "background-color:red; font-weight:bold;");
                            setInterval(function () {
                                $("#room").removeAttr('style');
                            }, 3000);
                        }, 0);
                        return;
                    } else if (checkconflicts(i, data, start, end, subject) === 1) {
                        checkfalse(stringid);
                        Swal.fire({
                            icon: 'error',
                            title: 'Teacher already assigned the stipulated schedule!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function () {
                            $("#teachers").attr('style', "background-color:red; font-weight:bold;");
                            setInterval(function () {
                                $("#teachers").removeAttr('style');
                            }, 3000);
                        }, 0);
                        return;
                    } else if (checkconflicts(i, data, start, end, subject) === 2 || checkconflicts(i, data, start, end, subject) === 3) {
                        checkfalse(stringid);
                        Swal.fire({
                            icon: 'error',
                            title: 'Course already assigned the stipulated schedule!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function () {
                            $("#course").attr('style', "background-color:red; font-weight:bold;");
                            setInterval(function () {
                                $("#course").removeAttr('style');
                            }, 3000);
                        }, 0);
                        return;
                    } else if (checkconflicts(i, data, start, end, subject) === 'noconflict') {
                        continue;
                    } else if (checkconflicts(i, data, start, end, subject) === 'samesubject') {
                        checkfalse(stringid);
                        Swal.fire({
                            icon: 'info',
                            title: 'Subject already assigned the stipulated schedule!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function () {
                            $("#subject").attr('style', "background-color:red; font-weight:bold;");
                            setInterval(function () {
                                $("#subject").removeAttr('style');
                            }, 3000);
                        }, 0);
                        return;
                    }
                }
            }
            databasesubjects(sy, sem, klasehanan, subject, stringid, start, end);
        }
    });
}

function checkconflicts(counts, data, start, end, subject) {
    for (let i = 0; i < data[counts].length; i++) {
        let starttimedb = Number(data[counts][i].start_time);
        let endtimedb = Number(data[counts][i].end_time);
        start = timeconvertion(start);
        end = timeconvertion(end);
        starttimedb = timeconvertion(starttimedb);
        endtimedb = timeconvertion(endtimedb);
        if ((starttimedb < start && start < end && end < endtimedb) || (start < starttimedb && starttimedb < endtimedb && endtimedb < end) || (starttimedb < start && start < endtimedb && endtimedb < end) || (start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb === start && endtimedb === end && start === starttimedb && end === endtimedb) || (starttimedb === start && start < endtimedb && endtimedb < end) || (start === starttimedb && starttimedb < end && end < endtimedb) || (start < starttimedb && starttimedb < end && end === endtimedb) || (starttimedb < start && start < endtimedb && endtimedb === end)) {
            return counts;
        }
    }
    return 'noconflict';
}

function databasesubjects(sy, sem, klasehanan, subject, stringid, start, end) {
    if (subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] === undefined) {
        subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] = timedifference(end, start);
    } else {
        subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] += timedifference(end, start);
    }
    let timetoadd = subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject];
    $.getJSON(`../queries/getconflicts.php?timesubjects=${sy}&semester=${sem}&course=${klasehanan}&subject=${subject}`, function (data) {
        // console.log(data);
        if (data.length === 0) {
            // alert('entering databasesubjects no data, now processing');
            checktrue(stringid);
        } else {
            let counter = 0;
            for (let i = 0; i < data.length; i++) {
                let dbstart = timeconvertion(Number(data[i].start_time));
                let dbend = timeconvertion(Number(data[i].end_time));
                counter += timedifference(dbend, dbstart);
                if (counter > timecap) {
                    Swal.fire({
                        title: 'Subject of this course/section already reached the time limit!',
                        text: "Do you want to proceed?",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            checktrue(stringid);
                        } else {
                            subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] -= timedifference(end, start);
                            checkfalse(stringid);
                        }
                    });
                    return;
                }
            }
            counter += timetoadd;
            if (counter > timecap) {
                Swal.fire({
                    title: 'Subject of this course/section will reach its time limit.',
                    text: "Do you want to proceed?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        checktrue(stringid);
                    } else {
                        subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] -= timedifference(end, start);
                        checkfalse(stringid);
                    }
                })
                return;
            } else {
                // alert('walay gasgas');
                checktrue(stringid);
            }
        }
    });
}

function insertcolon(mid) {
    let arraycolon = [];
    mid = mid.split('');
    for (let i = 0; i < mid.length; i++) {
        if (i === 1) {
            arraycolon.push(mid[i]);
            arraycolon.push(':');
        } else {
            arraycolon.push(mid[i]);
        }
    }
    arraycolon = arraycolon.join('');
    return arraycolon;
}

// instructor subject counter

function returnSemester(semester) {
    if (typeof semester === 'number') {
        semester = semester.toString();
    }
    if (semester === '1') {
        return '1st-Tri';
    } else if (semester === '2') {
        return '2nd-Tri';
    } else if (semester === '3') {
        return '3rd-Tri';
    }
}

function instructorSubjectCount() {
    let teacher = $('#teachers').val();
    let semester = $('#semester').val();
    let schoolyear = $('#schoolyear').val();
    let html = "";
    $.getJSON(`../queries/subjectcount.php?subjectcount=${teacher}&semester=${semester}&schoolyear=${schoolyear}`, function (data) {
        if (data >= 8) {
            html += `${$(`#teachers option[value="${teacher}"]`).html().split(',')[0]} got total of <span class="text-danger fw-bold">${data}</span> courses assigned on ${returnSemester(semester)} ${schoolyear}`;
        } else {
            html += `${$(`#teachers option[value="${teacher}"]`).html().split(',')[0]} has <span class="text-success fw-bold">${data === 0 ? 'no' : data}</span> ${data <= 1 ? 'course' : 'subjects'} assigned on ${returnSemester(semester)} ${schoolyear}`;
        }
        $('#instructorCounts').html(html);
    });
}