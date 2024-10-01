let signup = document.querySelector('#signup');
let confirming = document.querySelector('#modalPasswordC');
let pass = document.querySelector('#modalPassword');
let confirmed = document.querySelector('#confirmed');
let username = document.querySelector('#modalUsername');
let sbutton = document.querySelector('#signupbutton');

const acceptedCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789'.split('');

function passkeyup() {
    if (pass.value.length > 0 && confirming.value.length > 0) {
        if (pass.value === confirming.value) {
            confirmed.innerHTML = ``;
        } else {
            confirmed.innerHTML = `<p id="passwordwrong">Password not matched!.</p>`;
        }
    } else if (pass.value.length === 0 || confirming.value.length === 0) {
        confirmed.innerHTML = '';
    }

    keyupall();
}

function keyupall() {
    // alert('hello world');
    if (username.value.length > 0) {
        if (pass.value.length > 0 && confirming.value.length > 0 && pass.value === confirming.value) {
            sbutton.disabled = false;
        } else {
            sbutton.disabled = true;
        }
    } else {
        sbutton.disabled = true;
    }
}

username.addEventListener('keyup', function (e) {
    if ((e.which > 90 && e.which < 96) || e.which < 48 || e.which > 105) {
        if (e.which === 32 || e.key === '(blank space)' || e.keyCode === 32 || e.target.value[e.target.value.length - 1] === ' ') {
            username.value = e.target.value.split(' ').join('');
        } else {
            username.value = e.target.value.split(e.key).join('');
        }
    }
});


