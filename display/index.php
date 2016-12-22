<!DOCTYPE html>
<!--
KWANGJU KIM
CSE 383 Fall 2016
SCREEN PROJECT
-->
<html lang="en">
<head>
<title>KIMK3 Screen Display</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
.row.content {height: 550px}

/* Set gray background color and 100% height */
.sidenav {
	background-color: #f1f1f1;
	height: 100%;
}

/* On small screens, set height to 'auto' for the grid */
@media screen and (max-width: 767px) {
	.row.content {height: auto;}
}

.well-white {
	background-color: #FFFFFF;
}
</style>
</head>
<body>
<div class='container'>
  <div id='display-object' class='well well-white well-lg' style='min-height: 600px;'>
    Loading...<!--Current Display Object. This message should not be shown if loaded successfully-->
  </div>
</div>
<div class='container'>
  <div class='well well-sm col-md-3' id='rot-msg'>
    Loading...<!--Rotating Message This message should not be shown if loaded successfully-->
  </div>
  <div class='well well-sm col-md-6' id='rest-msg' style='height: 82px; text-align: center;'>
    Loading...<!--Message displayed by calling REST interface1 This message should not be shown if loaded successfully-->
  </div>
  <div class='well well-sm col-md-3' id='current-time' style='height: 82px; text-align:center;'>
    Loading...<!--00:00 AM This message should be reflecting correct time if loaded successfully-->
  </div>
</div>
<script>
// It has a necessary functions
var uid = 'kimk3';
var password = 'kim';

var parsedNow = function() { // parse current time
  var time = new Date();
  var hour = time.getHours();
  var minute = time.getMinutes();
  var ampm = 'AM';
  if (hour >= 12 && hour < 24) {
    ampm = 'PM'; // 12 ~ 23 is PM
    if (hour > 12) {
      hour -= 12; // Change 13 ~ 23 to 1 ~ 11 PM (12-hour system)
    }
  }
  if (hour == 0) {
    hour = 12;
  }
  if (hour < 10) {
    hour = '0' + hour.toString(); // Add 0 if one digit
  }
  if (minute < 10) {
    minute = '0' + minute.toString(); // Add 0 if one digit
  }
  return hour.toString() + ":" + minute + " " + ampm; // should print HH:MM AA
}

$(document).ready(function() {
  var rotateMessage = function() { // DEFINES how the rotate the message and contents
    $.ajax({
      'url': 'cse383-f16-screen/rest/displayItems.php/random',
      'method': 'GET',
      'dataType': 'json',
      'timeout': 5000,
    }).done(function(data, textStatus, jqXHR) {
      $('#rot-msg').html('Sign System by: <span style=\'font-family: "Courier New";\'>' + uid + '</span><br>');
      try {
        if (data['content'] == '' || data['content'] == null) { // When content does not exist
          if (data['imgValue'] == '' || data['imgValue'] == null) {
            $('#display-object').html('<span style=\'font-family: "Courier New"; color: red;\'>Content not found.</span>');
          } else {
            $('#display-object').html('<img src=\'uploads/' + data['imgValue'] + '\' alt=\'' + data['fileName'] + '\' style=\'max-width: 100%;\'>');
          }
        } else {
          $('#display-object').html(data['content']);
        }
        $('#rot-msg').append('<span style=\'font-family: "Courier New";\'>' + data['textValue'] + '</span>');
      } catch(err) { // REST thrown error
        $('#display-object').html('<span style=\'font-family: "Courier New"; color: red;\'>Failed to pull the content</span>');
        $('#rot-msg').append('<span style=\'color:red;\'>Error on image ' + data['pk'] + '<br>' + err.message + '</span>');
      } finally {
        $('#rot-msg').append('<br>Project for CSE383 Fall 2016');
        setTimeout(function() {
          rotateMessage(); // Call same function again after 15 secs
        }, 15000);
      }
    }).fail(function(jqXHR, textStatus, errorThrown) { // HTTP thrown error
      var errmsg = '<span style=\'color: red; font-family: "Courier New";\'>Unexpected error detected: ' + textStatus + ' - ' + errorThrown + '. Please reload.</span>';
      $('#rot-msg').html(errmsg);
      $('#display-object').html(errmsg);
    });
  };
  rotateMessage();
});

$(document).ready(function() {
  $('#current-time').html(parsedNow());
  setInterval(function() {
    $('#current-time').html(parsedNow()); // Update the time within 2 secs
  }, 2000);
});

$('#rest-msg').ready(function() {
  $.ajax({ // get authenticated
    'url': 'http://ceclnx01.cec.miamioh.edu/~campbest/cse383-f16/sign3/rest/message.php',
    'method': 'POST',
    'dataType': 'json',
    'data': JSON.stringify({'uid':uid,'password':password}),
    'timeout': 5000, // if more than 5 sec not responding, throw error
  }).done(function(data, textStatus, jqXHR) { // if authenticated
    var getAuth = function(auth, session) {
      $.ajax({
        'url': 'http://ceclnx01.cec.miamioh.edu/~campbest/cse383-f16/sign3/rest/message.php/' + auth,
        'method': 'GET',
        'headers': {'X-SESSION':session},
        'timeout': 5000,
      }).done(function(data, textStatus, jqXHR) {
        $('#rest-msg').html('');
        try {
          if (data['status'] == 'OK') {
            $('#rest-msg').append(data['message']);
          } else if (data['status'] == 'AUTHEXPIRED') {
            location.reload(true); // session expired -> refresh the page to reauthenticate: to prevent stack overflow error due to recursive call
          } else {
            $('#rest-msg').html('<span style=\'color: red; font-family: "Courier New";\'>Message loading failed. REASON: '+ data['status'] +' </span>');
          }
        } catch(err) { // rEST thrown error
          $('#rest-msg').html('<span style=\'color: red; font-family: "Courier New";\'>Unexpected error thrown. REASON: ' + err.message + ' </span>');
        } finally {
          $('#rest-msg').append('');
          setTimeout(function() {
            getAuth(auth, data['session']); // update the message after 1 min
          }, 60000);
        }
      }).fail(function(jqXHR, textStatus, errorThrown) { // HTTP thrown error
        $('#rest-msg').html('<span style=\'color: red; font-family: "Courier New";\'>Unexpected error detected: ' + textStatus + ' - ' + errorThrown + '<br>Please reload.</span>');
      });
    };

    getAuth(data['auth'], data['session']);
  }).fail(function(jqXHR, textStatus, errorThrown) {
    $('#rest-msg').html('<span style=\'color: red; font-family: "Courier New";\'>Unexpected error detected: ' + textStatus + ' - ' + errorThrown + '<br>Please reload.</span>');
  });
});
</script>
</body>
</html>
