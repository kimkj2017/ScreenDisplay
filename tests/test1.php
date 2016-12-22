<?php
header("content-type: text/plain");

print "************* TEST display items        *****\n";
include "models/displayItems.php";

//$l = getDisplayItemList();
//var_dump($l);

addDisplayItem("scotttest","2016-01-01","2017-01-01","campbest","null","null");

//get last item
$l=getDisplayItemList();
$pk = $l[count($l)-1]['pk'];
print "\nget item\n";
$l=getDisplayItem($pk);
var_dump($l);

print "\nUpdateItem\n";
$d['textValue'] = 'update123';
print updateItem($pk,$d);

print "\nitem\n";
$l=getDisplayItem($pk);
var_dump($l);

$d['startDate'] = '2016-01-01';
$d['endDate'] = '2017-01-01';
$d['fileName'] = 'update123';
print updateItem($pk,$d);
$l=getDisplayItem($pk);
var_dump($l);

print "\nTest delete - should return null\n";
deleteDisplayItem($pk);
var_dump(getDisplayItem($pk));
?>


