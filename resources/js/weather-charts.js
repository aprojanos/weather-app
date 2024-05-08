class WeatherCharts {
    hourlyForecastBars(divId, data) {
        Highcharts.chart(divId, {
            chart: {
              type: 'column',
              backgroundColor: 'transparent',
              scrollablePlotArea: {                
                minWidth: data.length * 270,
              }
            },
            title: {
              text: '',
            },
            xAxis: {
              categories: data.map(item => 
                `<div><img src="https:${item.icon}" style="width:64px; height:64px;" /></div>`
                +`<div class="text-lg text-orange-500 font-semibold">${item.temp_c}&deg;</div>`
                +`<div class="text-base">${item.hour}</div>`
              ),
              lineWidth: 0,
              labels: {
                useHTML: true,
                rotation: 0,
                align: 'center',
                x: 0,
                y: -110,
                style: {
                  width: '100px'
                }
              }
            },
            yAxis: {
              visible: false,
            },
            plotOptions: {
              column: {
                pointWidth: 80,         
                borderWidth: 5,
                heigth: 300,
              },
              series: {
                groupPadding: 0,
                pointPadding: 0.1,
        
              }
            },    
            series: [{
              name: '',
              data: data.map(item => ({
                y: item.temp_c,
                color: item.backgroundColor,
                borderColor: item.borderColor,
              })),
              colorByPoint: true,              
            }]
        });
        
    }

    updateAqiGauge(pm2_5) {
        if (this.aqiGauge && !this.aqiGauge.renderer.forExport) {// aqiGauge.series[0].points[0].update(pm2_5);
            const point = this.aqiGauge.series[0].points[0];
            point.update(pm2_5);
            this.aqiGauge.setTitle({ text: 'Air Quality: ' + this.getAqiIndex(pm2_5) });
        }
      
    }

    // calculate air quality index 
    // https://atmotube.com/blog/standards-for-air-quality-indices-in-different-countries-aqi
    getAqiIndex(pm2_5) {
          if (pm2_5 <= 10) return 'Good';
          if (pm2_5 <= 20) return 'Fair';
          if (pm2_5 <= 25) return 'Moderate';
          if (pm2_5 <= 50) return 'Poor';
          if (pm2_5 <= 75) return 'Very poor';
          return 'Extremly poor';
    }

    createAqiGauge(divId)  {
        this.aqiGauge = Highcharts.chart(divId, {
        
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: '60%',
                backgroundColor: 'transparent'
            },
        
            title: {
                text: 'Air Quality',
                verticalAlign: 'bottom'
            },
        
            pane: {
                startAngle: -90,
                endAngle: 89.9,
                background: null,
                center: ['50%', '75%'],
                size: '110%'
            },
        
            // the value axis
            yAxis: {
                min: 0,
                max: 100,
                tickPixelInterval: 72,
                tickPosition: 'inside',
                tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                tickLength: 20,
                tickWidth: 2,
                minorTickInterval: null,
                labels: {
                    distance: 20,
                    style: {
                        fontSize: '14px'              
                    },
                    
        
                },
                lineWidth: 0,
                
                // colors and values by aiq index
                plotBands: [{   
                    from: 0,
                    to: 10,
                    color: '#7cdded',
                    thickness: 20
                }, {
                    from: 10,
                    to: 20,
                    color: '#7bda72',
                    thickness: 20
                }, {
                    from: 20,
                    to: 25,
                    color: '#f0c42d',
                    thickness: 20
                }, {
                    from: 25,
                    to: 50,
                    color: '#ec2c45',
                    thickness: 20
                }, {
                    from: 50,
                    to: 75,
                    color: '#960232',
                    thickness: 20
                }, {
                    from: 75,
                    to: 100,
                    color: '#512771',
                    thickness: 20
                }]
            },
        
            series: [{
                name: 'PM 2.5',
                data: [0],                
                tooltip: {
                    valueSuffix: ''
                },
                dataLabels: {                
                    style: {
                        display: 'none',
        
                    }
                },
                dial: {
                    radius: '80%',
                    backgroundColor: 'gray',
                    baseWidth: 12,
                    baseLength: '0%',
                    rearLength: '0%'
                },
                pivot: {
                    backgroundColor: 'gray',
                    radius: 6
                }
        
            }]
        
        });
    }
}

export default WeatherCharts;