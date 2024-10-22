document.addEventListener('DOMContentLoaded', function () {
    // Line chart data
    const lineData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June','july', 'aug', 'sep',],
        datasets: [{
            label: 'Monthly Users',
            data: [30, 45, 20, 60, 90,28,105,10,70],
            fill: false,
            borderColor: '#FF6384',
            tension: 0.1
        }]
    };

    // Pie chart data
    const pieData = {
        labels: ['Room1', 'Room2', 'Room3','Room4'],
        datasets: [{
            data: [10, 5, 15,50],
            backgroundColor: ['#FF6384', '#36A2EB', '#00ff00','#FFF2C6'],
        }]
    };

    // Options for the charts
    const options = {
        responsive: true,
        maintainAspectRatio: false
    };

    // Initialize the line chart
    const ctxLine = document.getElementById('chartContainerLine').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: lineData,
        options: options
    });

    // Initialize the pie chart
    const ctxPie = document.getElementById('chartContainerPie').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: pieData,
        options: options
    });
});