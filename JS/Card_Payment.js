document.addEventListener("DOMContentLoaded", () => {
  const cancelBtn = document.getElementById("canbtn");
  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      window.location.href = "../HTML/Bank_Payment.html";
    });
  }
});
