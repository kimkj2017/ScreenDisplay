<?php
/*
Kwangju kim


Individual VIEW code for displayItems
*/
//security check to ensure it is being called from index
if (!isset($index_check))
	die("invalid invocation");



include "cse383-f16-screen/models/displayItems.php";

function tailoredColumnName($name) {
  switch ($name) {
    case 'textValue': return "Text Value";
    case 'startDate': return "Start Date";
    case 'endDate': return "End Date";
    case 'creator': return "Created by";
    case 'fileName': return "File Name";
    case 'imgValue': return "Image/Content";
    case 'content': return "Image/Content";
    default: return "Unknown column";
  }
}

if (!isset($_REQUEST['pk'])) {
  print "<h4>Invalid request</h4>";
  exit;
}

$data = getDisplayItem($_REQUEST['pk']);
$pk = $data['pk'];
$offset = getVar('offset');
if ($offset=="") {
  $offset = 0;
}
$textValue = $data['textValue'];
print "<h4>$textValue</h4>";
print "<a href='?cmd=editItem&pk=$pk' style='margin: 10px;' class='btn btn-warning'>Edit Item</a>";
print "<a href='?cmd=delItem&pk=$pk' style='margin: 10px;' class='btn btn-danger'>Delete Item</a>";
print "<a href='?cmd=displayItems&offset=$offset' style='margin: 10px;' class='btn btn-default'>Go Back</a>";
print '<table class="table table-bordered"><col width="20%"><col width="80%">';
foreach ($data as $k=>$v) {
  if ($k != 'pk' && $k != 'version' && $k != 'content') {
    print "<tr>";
    if ($k == 'imgValue') {
      if (!isset($data['content']) || $data['content'] == '') {
        print "<th>".tailoredColumnName($k)."</th><td><img src='uploads/$v' alt='$v' width='100%'></td>";
      }
      else {
        print '<th>Image/Content</th>';
        print '<td>'.htmlspecialchars_decode($data['content']).'</td>';
      }
    } else {
        print "<th>".tailoredColumnName($k)."</th>";
        print "<td>$v</td>";
    }
    print "</tr>";
  }
}
print '</table>';

?>
