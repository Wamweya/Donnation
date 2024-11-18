document.addEventListener("DOMContentLoaded", function () {
  const toggleButtons = document.querySelectorAll(".toggle-btn");

  toggleButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const article = this.parentNode;
      const excerpt = article.querySelector(".excerpt");
      const fullText = article.querySelector(".full-text");

      if (excerpt.style.display === "none") {
        excerpt.style.display = "block";
        fullText.style.display = "none";
        button.textContent = "Read more";
      } else {
        excerpt.style.display = "none";
        fullText.style.display = "block";
        button.textContent = "Read less";
      }
    });
  });
});
