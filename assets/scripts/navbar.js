function logout() {
    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log me out!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.location.href = `../functions/logout.php?logout=log`;
        }
    })
}

let proftoggle = 0
function profilemouseover() {
    // alert('hello');
    if (proftoggle % 2 === 0) {
        $('#showmouseover').slideDown();
    } else {
        $('#showmouseover').slideUp();
    }
    proftoggle++;
}

function profilemouseleave() {
    $('#showmouseover').slideUp();
}

function pendingusers() {
    let html = '';
    $.getJSON(`../queries/pendingfaculties.php?pendingfaculties=`, function(data){
        if (data.length === 0) {
            $('#nofacultypendings').removeClass('d-none');
        } else {
            $('#nofacultypendings').addClass('d-none');
            for (var i = 0; i < data.length; i++){
                html +=
                `<tr id="pendingusersrow${i}">
                    <td class="text-capitalize">${data[i]['member_salut']} ${data[i]['member_last']}, ${data[i]['member_first']} (${data[i]['dept_code']})</td>
                    <td class="text-capitalize">${data[i]['member_superiority']}</td>
                    <td><button class="btn btn-sm btn-success" onclick="approveuser('${i}','${data[i]['member_id']}');">Approve</button></td>
                    <td><button class="btn btn-sm btn-danger" onclick="deactivateuser('${i}','${data[i]['member_id']}');">Deactivate</button></td>
                </tr>`;
            }
            $('#pendingusertable tbody').html(html);
        }
    });
    $('#pendingusers-modal').modal('show');


    //         
    //     
    // <?php $tableincrementor++;
    // } ?>
}

function addanimation() {
    $('#pendingusers').addClass('fa-fade');
}

function deleteanimation() {
    $('#pendingusers').removeClass('fa-fade');
}

function approveuser(row, id) {
    // alert(row+' '+id);
    Swal.fire({
        title: 'Approve user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve user!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                showConfirmButton: false
            });
            $.ajax({
                url: "../functions/approveuser.php",
                method: "POST",
                data: {
                    approveuser: id
                },
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'User has been approved!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    let userrow = `#pendingusersrow${row}`;
                    setTimeout(() => {
                        setInterval(() => {
                            $(userrow).remove();
                        }, 500);
                        $(userrow).addClass('deleteduser');
                    }, 0);
                }
            });
        }
    });
}

function deactivateuser(row, id) {
    Swal.fire({
        title: 'Deactivate user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, deactivate user!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                showConfirmButton: false
            });
            $.ajax({
                url: "../functions/deactivate.php",
                method: "POST",
                data: {
                    deactivate: id
                },
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'User deactivated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    let userrow = `#pendingusersrow${row}`;
                    setTimeout(() => {
                        setInterval(() => {
                            $(userrow).remove();
                        }, 500);
                        $(userrow).addClass('deleteduser');
                    }, 0);
                }
            });
        }
    });
}

function unclicked1() {
    $.getJSON(`../functions/messages.php?loadunclicked=`, function (data) {
        // alert(data);
        if (data !== 'Wala'){
            if (data > 0) {
                $('#unclickedmsgs').html(data);
                $('#unclickedmsgs').addClass('unclickedmsgs');
            } else {
                $('#unclickedmsgs').html('');
                $('#unclickedmsgs').removeClass('unclickedmsgs');
            }
            setTimeout(function() {
                unclicked1();
            }, 3000);
        }
    });
}

unclicked1();

let unclickedmsgs = false;

function clicked1() {
    $.ajax({
        url: "../functions/messages.php",
        method: "POST",
        data: {
            click: 'clicked',
        },
        success: function(data) {
            document.location = '../pages/ascb-messenger.php';
        }
    });
}

function verifyEmail(id){
    $.ajax({
        url: "../functions/verify-email.php",
        method: "POST",
        data: {
            verify: id
        },
        success: function(data) {
            alert("Mail has been sent!");
        }
    });
}