$(function () {

    var fuelChart = null;

    Highcharts.setOptions({
        lang: {
            decimalPoint: ',',
            thousandsSep: ' '
        }
    });

    var climateChartOptions = {
        chart: {
            type: 'spline',
            marginTop: 50
        },
        title: {
            text: 'Średnie dobowe temperatury',
            x: -20 //center
        },
        subtitle: {
            text: 'w twojej okolicy',
            x: -20
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e.%m',
                year: '%b'
            },
            title: {
                text: 'Data'
            }
        },
        yAxis: {
            title: {
                text: 'Temperatura (°C)'
            },
            plotBands : [{
                from: -40,
                to : 8,
                color : 'rgba(68, 170, 213, 0.2)',
                label : {
                    text : 'Sezon grzewczy'
                }
            }]
  
        },
        tooltip: {
            formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    Highcharts.dateFormat('%e.%m', this.x) +': '+ this.y +'°C';
            }
        },
        plotOptions: {
          column: {
              pointWidth: 10,
              pointPadding: 0,
              groupPadding: 0
          },
          series: {
              groupPadding: 0
          },
          spline: {
              lineWidth: 3,
              states: {
                  hover: {
                      lineWidth: 3
                  }
              },
              marker: {
                  enabled: false
              }
          }
        },
        series: []
    };

    $.getJSON(Routing.generate('details_climate'), function(data) {
        climateChartOptions.series = data.series;
        createClimateChart(climateChartOptions);
    });

    breakdownOptions = {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b>: '+ Math.round(this.percentage) +' %';
            },
            percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                    }
                }
            }
        },
        series: []
    };

    $.getJSON(Routing.generate('details_breakdown'), function(data) {
            breakdownOptions.series.push(data);
            createBreakdownChart(breakdownOptions);
    });

    var fuelChartOptions = {
            chart: {
                type: 'bar',
                renderTo: 'fuel_chart'
            },
            title: {
                text: 'Koszty ogrzewania twojego domu różnymi paliwami'
            },
            xAxis: {
                categories: [],
                labels: {
                  align: 'right',
                  style: {
                      fontSize: '12px',
                      fontFamily: 'Verdana, sans-serif'
                  }
                }
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Koszt (zł)'
                },
                stackLabels: {
                    enabled: true,
                    formatter: function() {
                        return Highcharts.numberFormat(100 * Math.ceil(this.total / 100), 0) + 'zł';
                    },
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }

            },
            tooltip: {
                headerFormat: '<span><b>{point.key}</b></span><table>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                },
                column: {
                    dataLabels: {
                        enabled: false,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'ccc',
                        formatter: function() {
                            return Highcharts.numberFormat(this.y, 0) + 'zł';
                        }
                    }
                }
            },
            series: []
        };

    $.getJSON(Routing.generate('details_fuels'), function(data) {
            fuelChartOptions.series = calculateFuelCosts(data.series);
            fuelChartOptions.series[0].tooltip = {};
            fuelChartOptions.series[1].tooltip = {};
            fuelChartOptions.series[0].tooltip.pointFormat = '<tr><td>{point.version}</td>' +
                              '<td style="padding:0">&nbsp;</td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Sprawność spalania:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.efficiency}%</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Cena:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.trade_unit_price}zł/{point.trade_unit}</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Zużycie:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.consumption}{point.trade_unit}</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Koszt paliwa:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.y}zł</b></td></tr>';
            fuelChartOptions.series[1].tooltip.pointFormat = '<tr><td style="color:{series.color};padding:0">Czas obsługi:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.hours}h</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Koszt obsługi:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.y}zł</b></td></tr>';
            fuelChartOptions.xAxis.categories = data.categories;
            createFuelChart(fuelChartOptions);
    });

    $('#update_fuels').bind('click', function() {
        if (!fuelChart) {
            return;  
        }
        
        var fuelIndex = 1;
        var costsIndex = 0;
        
        var newData = fuelChart.series[fuelIndex].options.data;

        var dataChanged = false;

        for (var i = 0; i < newData.length; i++) {
            var fuelType = newData[i].fuel_type;

            var fuelField = fuelType == 'coal_cleaner'
                ? 'coal_dirty'
                : fuelType;

            var newPrice = parseFloat($('#fuel_' + fuelField).val().replace(',','.').replace(' ',''));
            if (newPrice < 0) {
                continue;
            }

            var newUnitPrice = newPrice / newData[i].trade_amount;
            if (newUnitPrice == newData[i].price) {
                continue;
            }

            dataChanged = true;
            newData[i].price = newUnitPrice;
            newData[i].y = Math.round(newData[i].price * newData[i].amount);
            newData[i].trade_unit_price = newData[i].price * newData[i].trade_amount;
        }

        if (dataChanged) {
            fuelChart.series[fuelIndex].setVisible(false);
            fuelChart.series[fuelIndex].setData(newData);
            fuelChart.series[fuelIndex].setVisible(true, true);
        }

        var newData = fuelChart.series[costsIndex].options.data;
        var dataChanged = false;

        for (var i = 0; i < newData.length; i++) {
            var fuelType = newData[i].fuel_type;

            var newWorkPrice = parseFloat($('#work_hour_cost').val().replace(',','.').replace(' ',''));
            if (newWorkPrice < 0) {
                continue;
            }

            var newWorkCost = newWorkPrice * newData[i].hours;
            if (newWorkCost == newData[i].y) {
                continue;
            }

            dataChanged = true;
            newData[i].y = Math.round(newWorkPrice * newData[i].hours);
        }

        if (dataChanged) {
            fuelChart.series[costsIndex].setVisible(false);
            fuelChart.series[costsIndex].setData(newData);
            fuelChart.series[costsIndex].setVisible(true, true);
        }

        $('#custom_fuel_prices').modal('hide');

        return false;
    });

    function calculateFuelCosts(series)
    {
        /*
         'price' => cena jednostkowa
         'amount' => ilosc jednostek paliwa
         'trade_amount' => mnoznik handlowy jednostek paliwa,
         'trade_unit' => nazwa jednostki handlowej,*/
        for (var i = 0; i < series[0].data.length; i++) {
            series[0].data[i].y = Math.round(series[0].data[i].price * series[0].data[i].amount);
            series[0].data[i].trade_unit_price = series[0].data[i].price * series[0].data[i].trade_amount;
        }

        return series;
    }

    function createClimateChart(options) {
        $('#climate_chart').highcharts(options);
    }

    function createBreakdownChart(options) {
        $('#heat_loss_breakdown').highcharts(options);
    }

    function createFuelChart(options) {
        fuelChart = new Highcharts.Chart(options);
    }
});
