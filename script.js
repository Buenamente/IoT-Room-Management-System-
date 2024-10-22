    // Toggle sidebar functionality
    const menuItems = document.querySelectorAll('#sidebar .side-menu li a');

    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all menu items
            menuItems.forEach(i => i.parentElement.classList.remove('active'));
            // Add active class to the clicked menu item
            this.parentElement.classList.add('active');
        });
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

    if(window.innerWidth < 768) {
        sidebar.classList.add('hide');
    } else if(window.innerWidth > 576) {
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
        searchForm.classList.remove('show');
    }

    window.addEventListener('resize', function () {
        if(this.innerWidth > 576) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
        }
    });

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



// scroll into about us//

    document.querySelector('a[href="about-us"]').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('#about-us').scrollIntoView({
            behavior: 'smooth'
        });
    });

//end//

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