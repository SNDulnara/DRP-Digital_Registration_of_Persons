document.addEventListener("DOMContentLoaded", () => {
  const nextBtn = document.getElementById("nbutton");
  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      window.location.href = "../HTML/Card_Payment.html";
    });
  }
});
