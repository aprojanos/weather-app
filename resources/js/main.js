import WeatherUpdater from './weather-updater';
import WeatherCharts from './weather-charts';


const weatherUpdater = new WeatherUpdater();
const weatherCharts = new WeatherCharts();


let map;
let lastSearchedKeyword;


function setForecastLocation(l) {
    let zoomLevel = l ? 13 : 6;
    if (l != undefined) {
      weatherUpdater.currentLocation = l
    }

    
    weatherUpdater.getForecast(updateView);

    map.setView(new L.LatLng(weatherUpdater.currentLocation.lat, weatherUpdater.currentLocation.lon), zoomLevel, {animate: true, duration: 1} ); 
    
}

function onautocompleteKeyUp(e) {

    let keyword = e.target.value;
    lastSearchedKeyword = keyword;

    setTimeout(function() {
        if (lastSearchedKeyword == keyword) {
            weatherUpdater.searchForLocations(keyword, function(response) {
              renderLocations(response);
            });
        }
    }, 500); 

  }

  function renderLocations(options) {

    let dropdownEl = document.querySelector("#autocompleteDropdown");
    dropdownEl.innerHTML = '';

    options.forEach((location) => {
        let div = document.createElement('div');
        div.classList.add('px-5', 'py-3', 'border-b', 'border-gray-200', 'text-stone-600', 'cursor-pointer', 'hover:bg-slate-100', 'transition-colors');
        const name = `${location.name}, ${location.country}`
        div.innerHTML = name;
        div.onclick = function() {
            selectLocation({location: name, lat:location.lat, lon: location.lon});
        }
        dropdownEl.append(div);
    });


    showAutocompleteDropdown();

  }

  function selectLocation(l) {
    showAutocompleteDropdown(false);
    let input = document.querySelector("#autocompleteInput");
    input.value = l.location;
    setForecastLocation(l)    
  }

  document.addEventListener("click", () => {
    showAutocompleteDropdown(false);
  });

  function showAutocompleteDropdown(show) {

    let dropdownEl = document.querySelector("#autocompleteDropdown");

    if (show == undefined || show) {
        dropdownEl.classList.remove("hidden");
    } else {
        dropdownEl.classList.add("hidden");                
    }

  }

  document.addEventListener('DOMContentLoaded', function() {
    
    // initilize charts
    

    // initialize map
    map = L.map('map');
   
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
        id: 'osm'
    }).addTo(map);    

    map.on('locationfound', function(ev) {        
        setForecastLocation({lat: ev.latlng.lat, lon: ev.latlng.lng});
    });
    map.on('locationerror', function(ev) {
        setForecastLocation();        
    });

    map.locate();    

    // initialize autocomplete
    let input = document.querySelector("#autocompleteInput");
    input.onkeyup = function(event) {        
        onautocompleteKeyUp(event);
    }
    input.onclick = function(event) {
        input.select();
    }    
    weatherCharts.createAqiGauge('aqiGauge');
});

function updateView(data) {
  
  weatherCharts.updateAqiGauge(data.forecast.current?.air_quality?.pm2_5, data.forecast.current?.air_quality['us-epa-index']);
  updateHourlyForecast(data.forecast.location, data.forecast.forecast.forecastday);
  updateCurrent(data.forecast.location, data.forecast.current);
  weatherUpdater.waitForWeatherUpdate(updateView);  
}
function updateCurrent(location, data) {
  document.getElementById('currentTemp').innerHTML = data.temp_c;
  document.getElementById('currentCondition').innerHTML = data.condition.text;
  document.getElementById('currentIcon').setAttribute('src', `https:${data.condition.icon}`);
  let dt = new Date(location.localtime_epoch * 1000).toLocaleTimeString('hu-HU', {hour: '2-digit', minute:'2-digit', timeZone: location.tz_id});
  document.getElementById('currentTime').innerHTML = dt;//dt.getHours() + ':' + dt.getMinutes();
  document.getElementById('locationName').innerHTML = location.name;
  document.getElementById('current').style.display = 'flex';
}
function updateHourlyForecast(location, days) {
  let today = true;
  let bgColorDay = 'lightyellow';
  let bgColorNight = 'lightcyan';
  let borderColorDay = 'rgba(150, 150, 150, 0.2)';
  let borderColorNight = 'rgba(20, 20, 20, 0.2)';
  let data = [];
  for (let daily of days) {    
    for (let hourData of daily.hour) {      
      
      let dt = new Date(hourData.time_epoch * 1000).toLocaleTimeString('hu-HU', {hour: '2-digit', timeZone: location.tz_id});
      let ldt = new Date(location.localtime_epoch * 1000).toLocaleTimeString('hu-HU', {hour: '2-digit', timeZone: location.tz_id});

      if (today && dt <= ldt) continue;
      let temp = hourData.temp_c;
      let icon = hourData.condition.icon;
      
      data.push({
        temp_c: hourData.temp_c,
        hour: `${dt}:00`,
        icon: hourData.condition.icon,
        backgroundColor: hourData.is_day == 1 ? bgColorDay : bgColorNight,
        borderColor: hourData.is_day == 1 ? borderColorDay : borderColorNight
      });
    }
    today = false;
  }
  
  weatherCharts.hourlyForecastBars('hourlyForecastBars', data);

}

