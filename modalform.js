
var modal = document.getElementById('myModal');
var editButtons = document.querySelectorAll('.edit-button');
var span = document.getElementsByClassName("close")[0];

// When the user clicks on an edit button, open the modal and populate the form//
editButtons.forEach(function(button) {
    button.onclick = function() {
        var accountId = this.getAttribute('data-id');
        //(php/get-account-details.php)  the is to locate subfolder code)//
        fetch('php/get-account-details.php?id=' + accountId)
        
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                } else {
                    document.getElementById('accountId').value = accountId;
                    document.getElementById('name').value = data.FullName;
                    document.getElementById('birthday').value = data.Birthday;
                    document.getElementById('address').value = data.Address;
                    document.getElementById('contact').value = data.ContactNumber;
                    document.getElementById('email').value = data.Email;
                    document.getElementById('role').value = data.Role;
                }
            })
            .catch(error => console.error('Error:', error));

        modal.style.display = "block";
    }
});

// Handle form submission for update
var form = document.getElementById("modalForm");
form.addEventListener("submit", function(event) {
    event.preventDefault();

    var formData = new FormData(form);

    fetch('php/update-account.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert(result);
        location.reload(); 
    })
    .catch(error => console.error('Error:', error));
});

// Handle delete button click//
var deleteBtn = document.querySelector(".delete-btn");
deleteBtn.addEventListener("click", function() {
    var formData = new FormData(form);
    var accountId = formData.get('id'); // Get the account ID from the form

    if (confirm("Are you sure you want to delete this account?")) {
        fetch('php/delete-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'id': accountId
            })
        })
        .then(response => response.text())
        .then(result => {
            alert(result); // Show result or handle it accordingly
            location.reload(); // Reload the page or update the table dynamically
        })
        .catch(error => console.error('Error:', error));
    }
});

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}



// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}



