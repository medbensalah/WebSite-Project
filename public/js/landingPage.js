const buttonRight = document.getElementById('slideRight');
const buttonLeft = document.getElementById('slideLeft');

let slide=document.getElementsByClassName('card');
let x=slide[0].clientWidth;
x+=2.5;
buttonRight.onclick = function () {
    document.getElementById('liste').scrollLeft += x;
};
buttonLeft.onclick = function () {
    document.getElementById('liste').scrollLeft -= x;
};