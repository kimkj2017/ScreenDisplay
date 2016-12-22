// kwangju kim
// cse383 f16
// display
// Necessary functions

var uid = 'kimk3';
var password = 'kim';

var parsedNow = function() {
  var time = new Date();
  var hour = time.getHours();
  var minute = time.getMinutes();
  var ampm = 'AM';
  if (hour >= 12 && hour < 24) {
    ampm = 'PM';
    if (hour > 12) {
      hour -= 12;
    }
  }
  if (hour == 0) {
    hour = 12;
  }
  if (hour < 10) {
    hour = '0' + hour.toString();
  }
  if (minute < 10) {
    minute = '0' + minute.toString();
  }
  return hour.toString() + ":" + minute + " " + ampm;
}

$(document).ready(function() {
    setInterval(function() {
        $.ajax({
          'url': 'cse383-f16-screen/rest/displayItems.php/random',
          'method': 'GET',
          'dataType': 'json'
        }).done(function(data, textStatus, jqXHR) {
            $('#rot-msg').html('Sign System by: <span style=\'font-family: "Courier New";\'>' + uid + '</span><br>');
            try {
              if (data['content'] == '' || data['content'] == null) { // When content does not exist
                if (data['imgValue'] == '' || data['imgValue'] == null) {
                  $('#display-object').html('<span style=\'font-family: "Courier New"; color: red;\'>ERROR, content not found.</span>');
                } else {
                  $('#display-object').html('<img src=\'' + data['imgValue'] + '\' alt=\'' + data['fileName'] + '\'>');
                }
              } else {
                $('#display-object').html(data['content']);
              }
              $('#rot-msg').append('<span style=\'font-family: "Courier New";\'>' + data['textValue'] + '</span>');
            } catch(err) {
              $('#display-object').html('<span style=\'font-family: "Courier New"; color: red;\'>Failed to pull the content</span>');
              $('#rot-msg').append('<span style=\'color:red;\'>Error on image ' + data['pk'] + '<br>' + err.message + '</span>');
            } finally {
              $('#rot-msg').append('<br>Project for CSE383 Fall 2016');
            }
        });
    }, 15000);
});

$(document).ready(function() {
  $('#current-time').html('<br>' + parsedNow() + '<br><br>');
  setInterval(function() {
    $('#current-time').html('<br>' + parsedNow() + '<br><br>');
  }, 2000);
});
