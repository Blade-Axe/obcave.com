var mobileMenuBtn = document.querySelector("#mobile-menu-btn");
var mobileMenu = document.querySelector(".mobile-menu");

mobileMenuBtn.addEventListener("click", () => {
  if (mobileMenu.style.display === "none") {
    mobileMenu.style.display = "flex";
    mobileMenuBtn.innerHTML = "&#10799"; //cross
  } else {
    mobileMenu.style.display = "none";
    mobileMenuBtn.innerHTML = "&#9776"; //hamburger menu
  }
});