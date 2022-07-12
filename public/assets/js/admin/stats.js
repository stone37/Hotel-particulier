$(document).ready(function() {
    $(document).ready(function() {
        // line order chart
        let cODaily = document.getElementById("orderDailyLineChart").getContext('2d'),
            cOMonthly = document.getElementById("orderMonthlyLineChart").getContext('2d'),
            oDaily = JSON.parse($oDaily), oMonthly = JSON.parse($oMonthly);

        let oDailyLineChart = new Chart(cODaily, {
            type: 'line',
            data: {
                labels: oDaily.map((point) => point["date"]),
                datasets: [{
                    label: "30 derniers jours",
                    data: oDaily.map((point) => point["amount"]),
                    backgroundColor: ['rgba(105, 0, 132, .2)',],
                    borderColor: ['rgba(200, 99, 132, .7)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                elements: {
                    line: {
                        tension: 0.3,
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [
                        {
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                            },
                        },
                    ],
                },
                animation: {
                    duration: 0,
                },
                hover: {
                    animationDuration: 0,
                },
                responsiveAnimationDuration: 0,
            }
        });

        let oMonthlyLineChart = new Chart(cOMonthly, {
            type: 'line',
            data: {
                labels: oMonthly.map((point) => point["date"]),
                datasets: [{
                    label: "24 derniers mois",
                    data: oMonthly.map((point) => point["amount"]),
                    backgroundColor: ['rgba(105, 0, 132, .2)'],
                    borderColor: ['rgba(200, 99, 132, .7)'],
                    borderWidth: 2
                }]
            },
            options: {
                elements: {
                    line: {
                        tension: 0.3,
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [
                        {
                            gridLines: {
                                drawOnChartArea: false,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                            },
                        },
                    ],
                },
                animation: {
                    duration: 0,
                },
                hover: {
                    animationDuration: 0,
                },
                responsiveAnimationDuration: 0,
            }
        });
    });


    // doughnut room chart
    let ctxP = document.getElementById("room-stats-chart").getContext('2d');
    let myPieChart = new Chart(ctxP, {
        type: 'doughnut',
        data: {
            labels: ["Disponible", "Occupé", "inactif"],
            datasets: [{
                data: [parseInt($roomEnabled), parseInt($roomBookingTotal), parseInt($roomInactif)],
                backgroundColor: ["#00C851", "#ffbb33", "#FF5252"],
                hoverBackgroundColor: ["rgba(0,200,81,0.7)", "#fec451", "#fa6e6e"]
            }]
        },
        options: {
            responsive: true
        }
    });
});



