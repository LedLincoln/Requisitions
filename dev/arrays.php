<?php
//approval levels are specified in functions.php
//$approvallevels = ['S'=>100000000, 'A'=>10000, "B"=>5000, "C"=>2500, "D"=>1000, "E"=>500, "F"=>0];
$maxselfapproval = 3000;
//$overseeremail = "mark.kurtenbach@licor.com";
$ubean = R::findOne('requser', 'username = ?', [$dn]);
$applevel = $ubean->applevel;
$currentusermax = $approvallevels[$applevel];
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$users = R::findAll('requser', 'ORDER BY username');
$units = R::findAll('unit', 'ORDER BY unitname');
$buyerrole = R::findOne('role', "rolename = 'Buyer'");
//$buyers = $buyerrole->sharedRequserList;
$buyers = $buyerrole->with("ORDER BY username")->sharedRequserList;
$projects = R::findAll('project', 'ORDER BY projnum');
$buyercodes = R::findAll('buyercode');
$specials = [['Normal', 'Normal req'], ['Quoted', 'Already quoted to user'], ['Ordered', 'Already ordered by user']];
//$priorities = [['Standard', 'Standard Delivery'], ['Expedited', 'Expedited Delivery'], ['Nextday', 'Next Day Delivery'], ['specify', 'Specify Date:']];
$priorities = [['Standard', 'Standard Delivery'], ['specify', 'Specify Date:']];
$vias = [['Burlington', 'Burlington Northern'], ['DHL', 'DHL'], ['FedEx', 'Federal Express'], ['FedExSO', 'FedExSO'], ['Overnite', 'Overnite Truck'], ['ParcelPost', 'Parcel Post'], ['UPSBlue', 'UPS Blue'], ['UPSRed', 'UPS Red'], ['UPSGround', 'UPS Ground']];
$dests = [['1000', 'Warehouse 01 (West)'], ['1001', 'Warehouse 02 (East)'], ['9999', 'Drop ship']];
$types = [['PO', 'PO'], ['PP', 'PP'], ['PD', 'PD'], ['PS', 'PS']];
$litypes = ['I'=>'Inventory', 'E'=>'Expense', 'F'=>'Capital', 'S'=>'Services', 'C'=>'Comment'];
$methods = [['Phone', 'Phone'], ['Fax', 'Fax'], ['Postal', 'Postal'], ['Email', 'Email'], ['Web', 'Web'], ['InPerson', 'In person'], ['Other', 'Other']];
$userroles = getUserRoles();
?>
