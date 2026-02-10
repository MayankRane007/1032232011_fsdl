console.log("JavaScript connected successfully!");
const formProject = document.getElementById("formProject");
const modal = document.getElementById("formModal");
const closeBtn = document.querySelector(".close");
const form = document.getElementById("validationForm");

// Open form
formProject.addEventListener("click", () => {
  modal.style.display = "flex";
});

// Close form
closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
});

// Validation
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = document.getElementById("name");
  const email = document.getElementById("email");
  const phone = document.getElementById("phone");
  const password = document.getElementById("password");

  let isValid = true;

  // Regex patterns
  const nameRegex = /^[A-Za-z ]{3,}$/;
  const emailRegex = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
  const phoneRegex = /^[0-9]{10}$/;
  const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

  isValid &= validateField(name, nameRegex, "Enter valid name");
  isValid &= validateField(email, emailRegex, "Enter valid email");
  isValid &= validateField(phone, phoneRegex, "Enter 10-digit number");
  isValid &= validateField(password, passwordRegex, "Min 8 chars, 1 capital & number");

  if (isValid) {
    alert("Form submitted successfully ✅");
    form.reset();
    modal.style.display = "none";
  }
});

function validateField(input, regex, message) {
  const small = input.nextElementSibling;
  if (!regex.test(input.value)) {
    small.innerText = message;
    return false;
  } else {
    small.innerText = "";
    return true;
  }
}
