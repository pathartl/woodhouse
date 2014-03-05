 // Weather
$(document).ready(function() {
  setInterval((function () {

      $.simpleWeather({
      location: 'Salem, WI',
      woeid: '',
      unit: 'f',
      success: function(weather) {
        // html = '<h2><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</h2>';
        // html += '<ul><li>'+weather.city+', '+weather.region+'</li>';
        // html += '<li class="currently">'+weather.currently+'</li>';
        // html += '<li>'+weather.wind.direction+' '+weather.wind.speed+' '+weather.units.speed+'</li></ul>';
        // html += weather.tomorrow.high;

        html = '<div class="forecast"><i class="icon-'+weather.code+'"></i>';
        html += '<h2>'+weather.temp+'&deg;</h2></div>';
        html += '<div class="forecast future"><i class="icon-'+weather.tomorrow.code+'"></i>';
        html += '<h3>'+weather.tomorrow.high+'&deg;</h3>';
        html += '<h3>'+weather.tomorrow.low+'&deg;</h3></div>';
        html += '<div class="forecast future"><i class="icon-'+weather.tomorrow.code+'"></i>';
        html += '<h3>'+weather.forecasts.two.high+'&deg;</h3>';
        html += '<h3>'+weather.forecasts.two.low+'&deg;</h3></div>';
        html += '<div class="forecast future"><i class="icon-'+weather.tomorrow.code+'"></i>';
        html += '<h3>'+weather.forecasts.three.high+'&deg;</h3>';
        html += '<h3>'+weather.forecasts.three.low+'&deg;</h3></div>';
        html += '<div class="forecast future"><i class="icon-'+weather.tomorrow.code+'"></i>';
        html += '<h3>'+weather.forecasts.four.high+'&deg;</h3>';
        html += '<h3>'+weather.forecasts.four.low+'&deg;</h3></div>';

    
        $("#weather").html(html);
      },
      error: function(error) {
        $("#weather").html('<p>'+error+'</p>');
      }
    });

  })(), 3600);
});