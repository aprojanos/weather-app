class WeatherUpdater {
    eventSource;
    defaultCity = 'Budapest';
    currentLocation = {lat: 47.234, lon: 19.429};
    waitForWeatherUpdate(callback) {

        this.closeSSL();
        this.eventSource = new EventSource('/sse');
        var self = this;
        this.eventSource.onmessage = function(event) {
            console.log('Message received: ' + event.data);
            self.closeSSL();
            self.getForecast(callback);
        };  
    }
    closeSSL() {
        if (this.eventSource != null) {
            this.eventSource.close();
            this.eventSource = null;
            console.log('eventsource closed');
        }
    }
    getForecast(callback) {        
        this.closeSSL();
        const locationQ = this.currentLocation.location ? `${this.currentLocation.lat} ${this.currentLocation.lon}` : this.defaultCity;
        console.log({getForecast: locationQ});
        this.lastForecastLocation = locationQ;
        var self = this;
        $.ajax({
            url: `weather/forecast/${locationQ}`,
            method: 'GET',
            success: function(response) {
                if (self.lastForecastLocation == locationQ && callback) {
                    callback(response);                    
                }
                
            }, 
            error: function(xhr, status, error) {
                // Handle the error
                console.log(error);
              }
            }        
        );
    }
    searchForLocations(keyword, callback) {
        this.closeSSL();
        console.log({searchForLocation: keyword});
        this.lastSearchedKeyword = keyword;
        var self = this;
        $.ajax({
            url: `weather/search/${keyword}`,
            method: 'GET',
            success: function(response) {
                if (keyword == self.lastSearchedKeyword && callback) {
                    console.log({locationsFound: response});
                    callback(response);                    
                }
            }, 
            error: function(xhr, status, error) {
                // Handle the error
                console.log(error);
              }
            }        
        );
    
    }
    
}

export default WeatherUpdater;