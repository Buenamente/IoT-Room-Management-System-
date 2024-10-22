const switchMode = document.getElementById('switch-mode');

        // Load dark mode setting from local storage
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark');
            switchMode.checked = true;
        }

        switchMode.addEventListener('change', function () {
            if(this.checked) {
                document.body.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            } else {
                document.body.classList.remove('dark');
                localStorage.setItem('dark-mode', 'false');
            }
        });

        // TOGGLE SIDEBAR
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');

        menuBar.addEventListener('click', function () {
            sidebar.classList.toggle('hide');
        });

        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', function (e) {
            if(window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                if(searchForm.classList.contains('show')) {
                    searchButtonIcon.classList.replace('bx-search', 'bx-x');
                } else {
                    searchButtonIcon.classList.replace('bx-x', 'bx-search');
                }
            }
        });

        const profile = document.querySelector('#content nav .profile');
        const imgProfile = profile.querySelector('img');
        const dropdownProfile = profile.querySelector('.profile-dropdown');

        imgProfile.addEventListener('click', function () {
            dropdownProfile.classList.toggle('show');
        });

        // Modal functionality
const editModal = document.getElementById('editModal');
const editForm = document.getElementById('editForm');
const closeBtn = document.querySelector('.modal .close');
const cancelBtn = document.getElementById('cancelButton');
const editButtons = document.querySelectorAll('.edit-btn');

// Function to open the modal and populate the form
editButtons.forEach(function (editBtn) {
    editBtn.addEventListener('click', function () {
        var accountId = this.getAttribute('data-id');
        // Fetch user data via AJAX or any other method
        fetch('../php/get-use-info.php?id=' + accountId)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                } else {
                    document.getElementById('accountId').value = accountId;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editAddress').value = data.address;
                    document.getElementById('editBirthday').value = data.birthday;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editContactNumber').value = data.contact;
                    document.getElementById('editRole').value = data.role;
                    document.getElementById('editRFID').value = data.rfid;
                    document.getElementById('editPin').value = data.pin;
                }
                // Open modal
                editModal.style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    });
});
// Handle form submission for update
var form = document.getElementById("editForm");
form.addEventListener("submit", function(event) {
    event.preventDefault();

    var formData = new FormData(form);

    fetch('../php/update-user.php', {
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
    var accountId = formData.get('accountId'); // Get the account ID from the form

    if (confirm("Are you sure you want to delete this account?")) {
        fetch('../php/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'accountId': accountId // Use 'accountId' to match the PHP script
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





// Close the modal when clicking on the close button or cancel button
closeBtn.addEventListener('click', function () {
    editModal.style.display = 'none';
});

cancelBtn.addEventListener('click', function () {
    editModal.style.display = 'none';
});

// Close the modal when clicking outside of it
window.addEventListener('click', function (event) {
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
});