<?php
/*
Scott Campbell
VIEW code for displayItems
*/
//security check to ensure it is being called from index
if (!isset($index_check))
	die("invalid invocation");


include "cse383-f16-screen/models/displayItems.php";

if (isset($_REQUEST['result'])) {
  if ($_REQUEST['result'] == 1) {
    echo("<div class='alert alert-success'>Successfully uploaded your image/content.</div>");
  } else {
    echo("<div class='alert alert-success'>Edit/Update was successful.</div>");
  }
}

$totcount = getDisplayItemCount();

$limit = getVar('limit');
if ($limit == "") {
  $limit = 25;
}
$offset = getVar('offset');
if ($offset == "") {
  $offset = 0;
}
$prev = $offset - $limit;
$next = $offset + $limit;
if ($prev < 0) {
  $prev = 0;
}
if ($offset == 0) {
  $prevmsg = 'disabled';
} else {
  $prevmsg = "href='?cmd=displayItems&offset=$prev'";
}
$nextmsg = "href='?cmd=displayItems&offset=$next'";

$l=getDisplayItemList($limit, $offset, 'endDate desc');
$count = sizeof($l);

if ($count < $limit) {
  $nextmsg = "disabled";
}

echo("<h4>Items</h4>");
echo("<p>Showing $count of $totcount items, from $offset to ".($offset + $count)."<a class='btn btn-link' $prevmsg>Previous</a><a class='btn btn-link' $nextmsg>Next</a></p>");
echo("<a href='?cmd=addItem' style='margin-bottom: 10px;' class='btn btn-default'>Add Item</a>");
echo("  <table class='table table-bordered'>");
echo("  <tr>");
echo("    <th>Text Value</th>");
echo("    <th>Start Date</th>");
echo("    <th>End Date</th>");
echo("    <th>Created by</th>");
echo("    <th>File Name</th>");
echo("    <th>Image/Content</th>");
echo("    <th>Edit/Delete</th>");
echo("  </tr>");


//$l=getDisplayItemList();
//$l2=array();
//$count = 25;
//foreach($l as $i) {
//  if ($count == 0) {
//    break;
//  }
//  array_push($l2, $i);
//  $count--;
//}

foreach ($l as $i) {
	//if ($count == 0) {
	//	break;
	//}
	$a=getDisplayItem($i['pk']);
	print "<tr>";
	foreach ($a as $k=>$v) {
		if ($k!="pk" && $k!="version" && $k!=="content") {
			if ($v == 'null') {
				$v = '';
			}
			print "<td>";
			if ($k=="textValue") {
				if ($v == '') {
					$v = '<span style=\'color: red;\'>Text value not set</span>';
				}
				print "<a href='?cmd=displayItems&pk=".$i['pk']."&offset=$offset'>".$v."</a>";
			}
			//elseif ($k=="startDate" || $k=="endDate") {
			//	$dat = date("Y-m-d", strtotime($v));
			//	print $dat;
			//}
			elseif ($k=="imgValue") {
				if (!isset($a["content"]) || $a['content']=='') {
					print "<img src='uploads/$v' alt='$v' width='500px'>";
				} else {
					print '<span style=\'font-family: "Courier New"\'>'.substr(htmlspecialchars($a["content"]), 0, 75).'</span>';
				}

			}
			else {
				print $v;
			}
			print "</td>";
		}
	}
	print "<td>";
	print "<a href='?cmd=editItem&pk=" . $i['pk'] . "'><span class=\"glyphicon glyphicon-pencil\"></span></a>";
	print "&nbsp";
	print "<a href='?cmd=delItem&pk=" . $i['pk'] . "'><span class=\"glyphicon glyphicon-remove itemDelete\"></span></a>";
	print "</td>";
	print "</tr>";
	//$count--;
}
?>
</table>
