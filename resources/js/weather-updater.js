class WeatherUpdater {
    
    defaultCity = 'Budapest';
    currentLocation = {lat: 47.234, lon: 19.429}; // centre coordinates of Hungary 

    // fetch forecast data from the server and pass it to the callback function
    getForecast(callback) {        
        const locationQ = this.currentLocation.location ? `${this.currentLocation.lat} ${this.currentLocation.lon}` : this.defaultCity;
        //console.log({getForecast: locationQ});
        this.lastForecastLocation = locationQ;
        var self = this;
        axios.get(`weather/forecast/${locationQ}`)
            .then(function(response) {

                if (self.lastForecastLocation == locationQ && callback) {
                    callback(response.data);                    
                }                
            }) 
            .catch(function(error) {
                console.log(error);
            });      
    }

    // search for location and pass it to the callback
    searchForLocations(keyword, callback) {
        
        this.lastSearchedKeyword = keyword;
        var self = this;

        axios.get(`weather/search/${keyword}`)
            .then(function(response) {
        
                if (keyword == self.lastSearchedKeyword && callback) {
                    callback(response.data);                    
                }
            })
            .catch(function(error) {
                console.log(error);
            });    
    }
    
}

export default WeatherUpdater;