function myFunction() {
    var el = document.getElementById("user-menu");
    el.classList.toggle("show");
    console.log(3);
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
var dropOpen = document.getElementsByClassName('show');
console.log(dropOpen[0].classList)
            if (!event.target.classList.contains('badge')) {
                dropOpen[0].classList.remove('show');
                console.log('here');
            }else{
                console.log('here2');
    }
}