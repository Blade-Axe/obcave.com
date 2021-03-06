var icon = document.getElementById("theme-switch-icon");

if(localStorage.getItem("theme") == null){
    localStorage.setItem("theme", "light");
}

let theme = localStorage.getItem("theme");

if (theme == "dark") {
    document.body.classList.toggle("dark-theme");
    icon.src = "images/icons/sun.png";
} else {
    icon.src = "images/icons/moon.png";
}

icon.onclick = function() {

    document.body.classList.toggle("dark-theme");

    if(document.body.classList.contains("dark-theme")){

        localStorage.setItem("theme", "dark");
        icon.src = "images/icons/sun.png";

    } else {
        
        localStorage.setItem("theme", "light");
        icon.src = "images/icons/moon.png";
    }
}
