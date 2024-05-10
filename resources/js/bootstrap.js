import axios from 'axios';
import * as L from 'leaflet';
import Highcharts from 'highcharts';
import highchartsMore from 'highcharts/highcharts-more';
highchartsMore(Highcharts);
import './weather-charts';
import Toastify from 'toastify-js'
window.axios = axios;
window.Highcharts = Highcharts;
window.L = L;
window.Toastify = Toastify;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
