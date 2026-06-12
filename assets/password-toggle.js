// Add show and hide buttons to passwords.
document.querySelectorAll("[data-password-toggle]").forEach((button) => {
  button.addEventListener("click", () => {
    // Find the password field.
    const field = button.closest(".password-input");
    const input = field?.querySelector("[data-password-input]");

    if (!input) {
      return;
    }

    // Show or hide the password.
    const passwordIsVisible = input.type === "text";
    input.type = passwordIsVisible ? "password" : "text";
    button.textContent = passwordIsVisible ? "Show" : "Hide";
    button.setAttribute("aria-label", passwordIsVisible ? "Show password" : "Hide password");
    button.setAttribute("aria-pressed", String(!passwordIsVisible));
  });
});
