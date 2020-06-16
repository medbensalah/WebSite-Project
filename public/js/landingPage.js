function drop() {
    var el = document.getElementById("user-menu");
    el.classList.toggle("show");
    console.log(3);
}

window.onclick = function(event) {
    var dropOpen = document.getElementsByClassName('show');
    var t = event.target;
    if(typeof t !== "undefined") {
        if (!t.classList.contains('badge'))
            dropOpen[0].classList.remove('show');
    }
    console.log(t.classList);
}