const prev = document.querySelector(".prev");
const next = document.querySelector(".next");
const next2 = document.getElementById("deuxiemeNext")
const carousel = document.querySelector(".carousel-container");
const track = document.querySelector(".track");
const track2 = document.getElementById("track");
let width = carousel.offsetWidth;
let index = 0;
window.addEventListener("resize", function () {
    width = carousel.offsetWidth;
});

function Next(id) {
    let next = document.getElementById("next" + id);
    let track = document.getElementById("track" + id);
    let prev = document.getElementById("prev" + id);
    index = parseInt(next.dataset.index) + 1;
    next.setAttribute("data-index", parseInt(index));
    prev.setAttribute("data-index", parseInt(index));
    console.log(index);
    prev.classList.add("show");
    track.style.transform = "translateX(" + index * -width + "px)";
    if (track.offsetWidth - index * width < index * width) {
        next.classList.add("hide");
    }
}

function Prev(id) {
    let next = document.getElementById("next" + id);
    let track = document.getElementById("track" + id);
    let prev = document.getElementById("prev" + id);
    index = parseInt(next.dataset.index) - 1;
    prev.setAttribute("data-index", parseInt(index));
    next.setAttribute("data-index", parseInt(index));

    console.log(index);
    next.classList.remove("hide");
    if (index <= 0) {
        prev.classList.remove("show");
    }
    track.style.transform = "translateX(" + index * -width + "px)";

    console.log(index * -width + "px");
}