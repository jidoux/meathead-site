/*
    This module generates the plots. The traitsAndTotals and traitPoints arrays are arrays which store trait names, their total drop rates, and their trait point values, generated from mysql server.
    these 2 variables were initialized in index.php as a method to transfer a php associative array into javascript
*/

var x_row = []; // needs to be var because if its let, the updates in the for loop wont be applied past it (reminder for me so I never forget)
var y_row = [];

var barColors = [];

for (const key in traitsAndTotals) {         
    x_row.push(`${key}`);
    y_row.push(`${traitsAndTotals[key]}`) // TODO remove this + and whatevers after, ensure its 0 before updating database
}
// this determines the colors of each trait based on their trait points value
for (const key in traitPoints) {
    switch (`${traitPoints[key]}`) {
        case "1":
            barColors.push("#fed866");
            break;
        case "2":
            barColors.push("#b7d7a9");
            break;
        case "3":
            barColors.push("#6a9ffa");
            break;
        case "4":
            barColors.push("#f9ba9d");
            break;
        case "5":
            barColors.push("#dd9e4b");
            break;
        case "6":
            barColors.push("#c37ba1");
            break;
        case "7":
            barColors.push("#4d134f");
            break;
        case "8":
            barColors.push("#674ea9");
            break;
        case "9":
            barColors.push("#43b5d9");
            break;
        case "NULL":
            barColors.push("#5a0f01");
            break;
        default:
            barColors.push("grey");
            break;
    }
}

var dataBar = [
    {
        x: x_row,
        y: y_row,
        type: 'bar',
        marker: {color: barColors},
    }
];

var dataPie = [
    {
        values: y_row,
        labels: x_row,
        type: 'pie',
        domain: {
            x: [0, 1], // this should cause the pie chart to take up it the full width & height
            y: [0, 1]
        },
        marker: {colors: barColors},
        textposition: 'inside',

    }
];

var layoutBar = {
    title: 'Bar Graph of All-Time Trait Drops',
    yaxis: {fixedrange: true},
    xaxis: {
        fixedrange: true,
        tickangle: 270,
        automargin: true,
        tickfont: {
            family: 'Verdana',
            color: '#000',
        }
    },
    margin: {
        l: 35,
        r: 35,
        t: 35,
        b: 35,
    },
};

var layoutPie = {
    title: 'Pie Chart of All-Time Trait Drops',
    showlegend: false,
    margin: {
        l: 10,
        r: 10,
        t: 75,
        b: 5,
    },
};

Plotly.newPlot('bar-graph-area-all-time-js', dataBar, layoutBar, {displayModeBar: false});
Plotly.newPlot('pie-chart-area-all-time-js', dataPie, layoutPie, {displayModeBar: false});
