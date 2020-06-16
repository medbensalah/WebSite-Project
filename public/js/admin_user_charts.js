var current = document.getElementById('current');
current.classList.add("week");

let weekActivity = JSON.parse(document.querySelector('#data').dataset.weekId);
let monthActivity = JSON.parse(document.querySelector('#data').dataset.monthId);
let yearActivity = JSON.parse(document.querySelector('#data').dataset.yearId);

chart(7, weekActivity)
charts(7, weekActivity)
charts(30, monthActivity)
charts(365, yearActivity)

function charts(x, activity) {
    switch (x) {
        case 7:
            classe = 'week';
            break;
        case 30:
            classe = 'month';
            break;
        case 365:
            classe = 'year';
            break;
    }
    const ctx = document.getElementById(classe).getContext('2d');
    const xlabel = getData(x);
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: xlabel,
            datasets: [{
                label: 'Users/Day',
                data: activity,
                borderColor:  "rgba(30,158,5, 1)",
                borderWidth: 1,
                backgroundColor: "rgba(30,158,5,0.3)",
            }]
        },
        options: {
            legend: {
                labels: {
                    fontColor: 'black',
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function(value) {if (value % 1 === 0) {return value;}},
                        fontColor: 'black',
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, 0)",
                    }
                }]
            }

        }
    });
}

function chart(x, activity) {
    console.log(activity);
    const ctx = document.getElementById('current').getContext('2d');
    const xlabel = getData(x);
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: xlabel,
            datasets: [{
                label: 'Users/Day',
                data: activity,
                borderColor:  "rgba(30,158,5, 1)",
                borderWidth: 1,
                backgroundColor: "rgba(30,158,5,0.3)",
            }]
        },
        options: {
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 28
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function(value) {if (value % 1 === 0) {return value;}},
                        fontColor: 'black',
                        fontSize: 20
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20
                    }
                }]
            }

        }
    });
}

function getData(x) {

    const curr = new Date;
    let date;
    const days = [];
    switch (x) {
        case 7:
            for (let i = 0; i < x; i++) {
                date = new Date(
                    curr.getFullYear(),
                    curr.getMonth(),
                    i + curr.getDate() - curr.getDay()
                )
                days.push(
                    date.getFullYear() + "/" +
                    (1+date.getMonth()) + "/" +
                    date.getDate()
                );
            }
            break;
        case 30:
            for (let i = 0; i < x; i++) {
                date = new Date(
                    curr.getFullYear(),
                    curr.getMonth(),
                    i + 1
                );
                days.push(
                    date.getFullYear() + "/" +
                    (1+date.getMonth()) + "/" +
                    date.getDate()
                );
            }
            break;
        case 365:
            for (let i = 0; i < x; i++) {
                date = new Date(curr.getFullYear(), 0, i + 1);
                days.push(
                    date.getFullYear() + "/" +
                    (1+date.getMonth()) + "/" +
                    date.getDate()
                );
            }
            break;
    }


    return days;
}