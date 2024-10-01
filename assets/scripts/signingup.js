// let lastname = document.querySelector('#lastname');
// let firstname = document.querySelector('#firstname');
// let salut = document.querySelector('#salut');
// let superiority = document.querySelector('#superiority');
// let button = document.querySelector('#fullsign');
// let others = document.querySelector('#others');
// let specify = document.querySelector('#specify');
// let specificstudent = document.querySelector('#specificstudent');
// let specificstatus = document.querySelector('#specificstatus');

// window.onload = function() {
//     button.disabled=true;
// }

// superiority.addEventListener('change', function(){
//     if(lastname.value === '' || firstname.value === '' || salut.value === '' || superiority.value === ''){
//         button.disabled = true;        
//     }
//     else if (lastname.value.length > 0 && firstname.value.length > 0 && salut.value.length > 0 && superiority.value.length > 0) {
//         button.disabled = false;
//     }

//     if (superiority.value === 'student'){
//         specificstudent.classList.remove('d-none');
//         specificstatus.classList.remove('d-none');
//         document.querySelector('#specificdepartment').classList.add('d-none');
//     } else {
//         specificstudent.classList.add('d-none');
//         specificstatus.classList.add('d-none');
//     }
//     if ((superiority.value === 'admin') || (superiority.value === 'faculty')){
//         document.querySelector('#specificdepartment').classList.remove('d-none');
//     }
//     if (superiority.value === 'admin'){
//         document.querySelector('#referral').classList.remove('d-none');
//     } else {
//         document.querySelector('#referral').classList.add('d-none');
//     }
// });

// salut.addEventListener("change", function(e){
//     if(salut.value === 'others'){
//         specify.classList.remove('d-none');
//     } else {
//         specify.classList.add('d-none');
//         document.querySelector('#specifying').value = '';
//     }
//     if(lastname.value === '' || firstname.value === '' || salut.value === '' || superiority.value === ''){
//         button.disabled = true;        
//     }
//     else if (lastname.value.length > 0 && firstname.value.length > 0 && salut.value.length > 0 && superiority.value.length > 0) {
//         button.disabled = false;
//     }
// });

// lastname.addEventListener('keyup', function(){
//     if(lastname.value === '' || firstname.value === '' || salut.value === '' || superiority.value === ''){
//         button.disabled = true;        
//     }
//     else if (lastname.value.length > 0 && firstname.value.length > 0 && salut.value.length > 0 && superiority.value.length > 0) {
//         button.disabled = false;
//     }
// });

// firstname.addEventListener('keyup', function(){
//     if(lastname.value === '' || firstname.value === '' || salut.value === '' || superiority.value === ''){
//         button.disabled = true;        
//     }
//     else if (lastname.value.length > 0 && firstname.value.length > 0 && salut.value.length > 0 && superiority.value.length > 0) {
//         button.disabled = false;
//     }
// });

