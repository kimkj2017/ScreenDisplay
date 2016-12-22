<?

include 'cse383-f16-screen/models/displayItems.php';

if (!isset($_REQUEST['pk'])) {
  print 'Invalid approach';
  exit;
}
?>


<table id='data' class='table table-condensed'>

</table>

<script>
$(document).ready(function() {
  $.ajax({
    url: 'cse383-f16-screen/rest/displayItems.php/<?php print $_REQUEST['pk'];?>',
    type: 'GET',
    success: function(result) {
      var tableStr = '';
      for (var k in result) {
        tableStr += '<tr><th>' + k + '</th><td>' + result[k] + '</td></tr>';
      }
      $('#data').html(tableStr);
    }
  });
});
</script>
