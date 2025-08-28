document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("btn1");
  if (btn) {
    btn.addEventListener("click", () => {
      window.location.href = "Form_NIC_for_first_time.html";
    });
  }
});

// Disable submit button after first click
function disableSubmitButton() {
  const submitBtn = document.getElementById("submit");
  if (submitBtn) {
    submitBtn.disabled = true;
  }
  return true; // Allow form submission
}
