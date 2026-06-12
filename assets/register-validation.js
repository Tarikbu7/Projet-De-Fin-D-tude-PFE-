// Get the register form.
const registerForm = document.querySelector(".auth-register-form");
const formMessage = document.querySelector(".auth-form-message");

// Form error messages.
const validationMessages = {
  name: "Please enter your name.",
  email: "Please enter a valid email address.",
  password: "Your password must contain at least 8 characters."
};

// Check one field.
function validateRegisterField(field) {
  const errorElement = document.getElementById(`${field.name}-error`);
  if (!errorElement) {
    return true;
  }

  let message = "";
  if (field.required && field.value.trim() === "") {
    message = validationMessages[field.name] || "This field is required.";
  } else if (field.name === "email" && !field.validity.valid) {
    message = validationMessages.email;
  } else if (field.name === "password" && field.value.length < 8) {
    message = validationMessages.password;
  }

  field.classList.toggle("is-invalid", message !== "");
  field.setAttribute("aria-invalid", String(message !== ""));
  errorElement.textContent = message;

  return message === "";
}

// Start form checks.
if (registerForm && formMessage) {
  const fields = [...registerForm.querySelectorAll("input")];

  // Check fields while the user types.
  fields.forEach((field) => {
    field.addEventListener("blur", () => validateRegisterField(field));
    field.addEventListener("input", () => {
      if (field.classList.contains("is-invalid")) {
        validateRegisterField(field);
      }
    });
  });

  // Stop the form when a field is wrong.
  registerForm.addEventListener("submit", (event) => {
    const firstInvalid = fields.find((field) => !validateRegisterField(field));
    formMessage.hidden = !firstInvalid;

    if (firstInvalid) {
      event.preventDefault();
      formMessage.textContent = "Please correct the highlighted fields.";
      firstInvalid.focus();
    }
  });
}
