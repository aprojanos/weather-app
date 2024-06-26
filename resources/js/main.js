import WeatherUpdater from './weather-updater';
import WeatherCharts from './weather-charts';
import { enablePushNotifications } from './notification';
import { disablePushNotifications } from './notification';

const weatherUpdater = new WeatherUpdater();
const weatherCharts = new WeatherCharts();

let map;
let lastSearchedKeyword;
let initalLocationSet = false;
let defaultPosition = {lat: 47.234, lon: 19.429}; // centre coordinates of Hungary 

// initialize dashboard elements when document was loaded
document.addEventListener('DOMContentLoaded', function() {

  // initialize map
  map = L.map('map');

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 18,
      id: 'osm'
  }).addTo(map);

  map.on('locationfound', function(ev) { // use current location for forecast if available
      setForecastLocation({lat: ev.latlng.lat, lon: ev.latlng.lng});
      initalLocationSet = true;
      Toastify({text: 'Get weather data for the current location!'}).showToast();
  });
  map.on('locationerror', function(ev) { // use default location for forecast
    setTimeout(function() {
      if (!initalLocationSet) {
        setForecastLocation();
        initalLocationSet = true
        Toastify({text: 'Get weather data for the default location!'}).showToast();
      }
    }, 1000);
  });

  map.locate();

  // initialize autocomplete
  let input = document.querySelector("#autocompleteInput");

  input.onkeyup = function(event) {

    let keyword = event.target.value;
    lastSearchedKeyword = keyword;

    setTimeout(function() {
        if (lastSearchedKeyword == keyword) {
            weatherUpdater.searchForLocations(keyword, function(response) {
              renderLocations(response);
            });
        }
    }, 500);
  }

  // initialize constrols for push notification subscription
  document.getElementById('set-alert').addEventListener('click', function () {
    const params = {
      threshold: document.getElementById('threshold').value,
      alert_type: document.getElementById('alert_type').value,
      coordinates: weatherUpdater.currentLocation
        ? weatherUpdater.currentLocation.lat + ' ' + weatherUpdater.currentLocation.lon
        : weatherUpdater.defaultCity,
      location:
        weatherUpdater.currentLocation?.location
        ?? weatherUpdater.defaultCity
    }
    if (params.threshold && params.coordinates) {
      enablePushNotifications(params);
    } else {
      Toastify({text: 'Please select location and set threshold for creating an alert subscription!'}).showToast();
    }
  })

  document.getElementById('remove-alert').addEventListener('click', function () {
      disablePushNotifications();
  })

  input.onclick = function(event) {
      input.select();
  }

  // initialize gauge for air quality data
  weatherCharts.createAqiGauge('aqiGauge');

  // listen to websocket events
  window.Echo.channel('weather-channel')
 .listen('WeatherUpdateEvent', (e) => {
    Toastify({text: 'Weather data are updated.'}).showToast();
    weatherUpdater.getForecast(updateView);
 });

});

// update map and get weather data from the server after location change or when default location is requested
function setForecastLocation(l) {

    let zoomLevel = l ? 13 : 6;

    let mapLocation = l ? {lat: l.lat, lon: l.lon} : defaultPosition;

    if (l != undefined) {
      weatherUpdater.currentLocation = l
    }

    weatherUpdater.getForecast(updateView);

    map.setView(
      new L.LatLng(mapLocation.lat, mapLocation.lon),
      zoomLevel,
      {animate: true, duration: 1}
    );


}

// populate location dropdown
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

// location selected: hide dropdown, set location input and pass location to forecast
function selectLocation(l) {

  showAutocompleteDropdown(false);

  setForecastLocation(l);

  let input = document.querySelector("#autocompleteInput");
  input.value = l.location;

}

// hide autocomplete dropdown on document click
document.addEventListener("click", () => {
  showAutocompleteDropdown(false);
});

// show / hide autocomplete dropdown
function showAutocompleteDropdown(show) {

  let dropdownEl = document.querySelector("#autocompleteDropdown");

  if (show == undefined || show) {
      dropdownEl.classList.remove("hidden");
  } else {
      dropdownEl.classList.add("hidden");
  }

}

// update the weather dashboard after retrieving the data from the server
function updateView(data) {

  weatherCharts.updateAqiGauge(data.forecast.current?.air_quality?.pm2_5);

  updateHourlyForecast(data.forecast.location, data.forecast.forecast.forecastday);

  updateCurrent(data.forecast.location, data.forecast.current);

}

// Update weather data to dashboard elements
function updateCurrent(location, data) {

  document.getElementById('currentTemp').innerHTML = data.temp_c;
  document.getElementById('currentCondition').innerHTML = data.condition.text;
  document.getElementById('currentIcon').setAttribute('src', `https:${data.condition.icon}`);
  let dt = new Date(location.localtime_epoch * 1000).toLocaleTimeString('hu-HU', {hour: '2-digit', minute:'2-digit', timeZone: location.tz_id});
  document.getElementById('currentTime').innerHTML = dt;//dt.getHours() + ':' + dt.getMinutes();
  document.getElementById('locationName').innerHTML = location.name;
  document.getElementById('current').style.display = 'flex';

}

// Create an array of forecast data and pass it to the bar chart
function updateHourlyForecast(location, days) {

  let bgColorDay = 'lightyellow';
  let bgColorNight = 'lightcyan';
  let borderColorDay = 'rgba(150, 150, 150, 0.2)';
  let borderColorNight = 'rgba(20, 20, 20, 0.2)';

  let today = true;
  let ldt = new Date(location.localtime_epoch * 1000) // current hour in location's time zone
  .toLocaleTimeString('hu-HU', {hour: '2-digit', timeZone: location.tz_id});

  let data = [];

  for (let daily of days) {

    for (let hourData of daily.hour) {

      let dt = new Date(hourData.time_epoch * 1000) // forecast hour in location's time zone
        .toLocaleTimeString('hu-HU', {hour: '2-digit', timeZone: location.tz_id});

      if (today && dt <= ldt) continue; // it is not necessary to forecast in the past

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

