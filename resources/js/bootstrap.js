import axios from 'axios';
import jQuery from 'jquery';
import * as L from 'leaflet';
import Highcharts from 'highcharts';
import highchartsMore from 'highcharts/highcharts-more';
highchartsMore(Highcharts);
import './weather-charts';
window.$ = jQuery;
window.axios = axios;
window.Highcharts = Highcharts;
window.L = L;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
