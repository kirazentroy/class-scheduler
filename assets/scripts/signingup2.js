let lastname = document.querySelector("#lastname");
let firstname = document.querySelector("#firstname");
let salut = document.querySelector("#salut");
let superiority = document.querySelector("#superiority");
let button = document.querySelector("#fullsign");
let others = document.querySelector("#others");
let specify = document.querySelector("#specify");
let specificstudent = document.querySelector("#specificstudent");
let specificstatus = document.querySelector("#specificstatus");

// code
// department
// course
// studentstatus
salut.addEventListener("change", function (e) {
  if (e.target.value === "others") {
    specify.classList.remove("d-none");
  } else {
    specify.classList.add("d-none");
  }
});

superiority.addEventListener("change", function () {
  if (superiority.value === "student") {
    document.querySelector("#course").required = true;
    document.querySelector("#studentstatus").required = true;
    specificstudent.classList.remove("d-none");
    specificstatus.classList.remove("d-none");
  } else {
    document.querySelector("#course").required = false;
    document.querySelector("#studentstatus").required = false;
    specificstudent.classList.add("d-none");
    specificstatus.classList.add("d-none");
  }
  if (superiority.value === "admin" || superiority.value === "faculty") {
    document.querySelector("#department").required = true;
    document.querySelector("#specificdepartment").classList.remove("d-none");
  } else {
    document.querySelector("#department").required = false;
    document.querySelector("#specificdepartment").classList.add("d-none");
  }
  if (superiority.value === "admin") {
    document.querySelector("#code").required = true;
    document.querySelector("#referral").classList.remove("d-none");
  } else {
    document.querySelector("#code").required = false;
    document.querySelector("#referral").classList.add("d-none");
  }
  if (superiority.value !== "student") {
    document.querySelector("#notforadmins").disabled = false;
  } else {
    document.querySelector("#notforadmins").disabled = true;
  }
});
