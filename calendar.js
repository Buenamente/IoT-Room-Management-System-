// Initialize Calendar

function updateCalendar() {
	const calendarElement = document.getElementById('calendar');
	const now = new Date();
	const date = now.toLocaleDateString();
	const hours = now.getHours();
	const minutes = now.getMinutes().toString().padStart(2, '0');
	const seconds = now.getSeconds().toString().padStart(2, '0');
	const ampm = hours >= 12 ? 'PM' : 'AM';
	const hours12 = hours % 12 || 12;
	const time = `${hours12}:${minutes}:${seconds} ${ampm}`;
	calendarElement.innerHTML = `<strong>${date}</strong> ${time}`;
}

document.addEventListener('DOMContentLoaded', function() {
	updateCalendar();
	setInterval(updateCalendar, 1000);

	const calendarElement = document.getElementById('calendar');
	if (localStorage.getItem('dark-mode') === 'true') {
		document.body.classList.add('dark');
		switchMode.checked = true;
		calendarElement.classList.add('dark-mode');
		calendarElement.classList.remove('light-mode');
	} else {
		calendarElement.classList.add('light-mode');
		calendarElement.classList.remove('dark-mode');
	}
});

switchMode.addEventListener('change', function () {
	const calendarElement = document.getElementById('calendar');
	if(this.checked) {
		document.body.classList.add('dark');
		calendarElement.classList.add('dark-mode');
		calendarElement.classList.remove('light-mode');
		localStorage.setItem('dark-mode', 'true');
	} else {
		document.body.classList.remove('dark');
		calendarElement.classList.add('light-mode');
		calendarElement.classList.remove('dark-mode');
		localStorage.setItem('dark-mode', 'false');
	}
});

// Profile dropdown toggle
const profilePicture = document.getElementById('profile-picture');
const profileDropdown = document.getElementById('profile-dropdown');

profilePicture.addEventListener('click', function () {
    profileDropdown.classList.toggle('active');
});

document.addEventListener('click', function(event) {
    if (!profilePicture.contains(event.target) && !profileDropdown.contains(event.target)) {
        profileDropdown.classList.remove('active');
    }
});


