document.addEventListener("DOMContentLoaded", function() {
    // Get the modals
    var modal = document.getElementById('myModal');
    var modal1 = document.getElementById('myModal1');

    // Get the buttons that open the modals
    var btn = document.getElementById("editButton");
    var btn2 = document.getElementById("editButton2");

    // Get the <span> elements that close the modals
    var span = document.getElementsByClassName("close")[0];
    var span1 = document.getElementsByClassName("close")[1];

    // When the user clicks the button, open the corresponding modal
    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        };
    }
    if (btn2) {
        btn2.onclick = function() {
            modal1.style.display = "block";
        };
    }

    // When the user clicks on <span> (x), close the modal
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        };
    }
    if (span1) {
        span1.onclick = function() {
            modal1.style.display = "none";
        };
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        } else if (event.target == modal1) {
            modal1.style.display = "none";
        }
    };
});
