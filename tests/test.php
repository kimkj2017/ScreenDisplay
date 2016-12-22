<?php
header("content-type: text/plain");

include "models/screen.php";


print "test adding screen element\n";
$r = addScreen("Test","test location","test destinatin","127.0.0.1","100","200");
print 'Add ' . $r . "\n";

print "get list\n";
$l = getScreenList();
var_dump($l);

print "\n";

$pk=$l[0]['pk'];
print "get screen info $pk\n";
$r = getScreen($pk);
var_dump($r);

print "\nTest Delete\n";
$r=deleteScreen($pk);
print $r;


print "************  USER tests ******************\n";
include "models/userModel.php";
$l = getUserList();
var_dump($l);

print "\n**** Test Add\n";
$r = createUser("testtest","testdisplay","testpwd",0);
print $r. "\n";

print "get added user\n";
$r=getUser("testtest");
var_dump($r);

print "delete added user\n";
$r=deleteUser("testtest");
print "$r";

$ll=getUser("testtest");
print "should be empty\n";
var_dump($ll);


print "************* TEST display items        *****\n";
include "models/displayItems.php";

$l = getDisplayItemList();
var_dump($l);

print "\nget item\n";
$l=getDisplayItem($l[0]['pk']);
var_dump($l);

print "\nUpdateItem\n";
$d['textValue'] = 'update123';
print updateItem(10,$d);
$l=getDisplayItem(10);
var_dump($l);

print "*************** TEST mapping ***********\n";
include("models/mapping.php");
$map = new Mapping();
$list = $map->getAll();
var_dump($list);

print "\nCreate map\n";
print $map->createMap(1,2,'2016-11-01','2016-11-02',2);

$list = $map->getAll();
var_dump($list);

print "\nTest Delete\n";
print $map->deleteMap(1,2);
$list=$map->getAll();
var_dump($list);

?>


