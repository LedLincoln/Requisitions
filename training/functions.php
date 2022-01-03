<?php
$approvallevels = ['S'=>100000000, 'A'=>10000, "B"=>5000, "C"=>2500, "D"=>1000, "E"=>500, "F"=>0];
function isValidDate($d) {
  $dateparts = explode('-', $d);
  return sizeof($dateparts) == 3 && checkdate($dateparts[1], $dateparts[2], $dateparts[0]);
}
function getUserRoles() {
  global $ubean;
  //$ubean = R::findOne('requser', 'username = ?', [$uname]);
  if($ubean == null) {
    return null;
  } else {
    $rbeans = $ubean->sharedRoleList;
    $rarray = [];
    foreach($rbeans as $r) {
      array_push($rarray, $r->rolename);
    }
    return $rarray;
  }
}
function getRoleMembers($rolename) {
  $rbean = R::findOne('role', 'rolename = ?', [$rolename]);
//var_dump($rbean);
  if($rbean == null) {
    return [];
  } else {
    $membeans = $rbean->with('ORDER BY username')->sharedRequserList;
//var_dump($membeans);
    $marray = [];
    foreach($membeans as $m) {
      $mn = $m->username;
      $me = $m->email;
      $marray[$mn] = $me;
    }
    return $marray;
  }
}
function in_array_any($needles, $haystack) {
        return !empty(array_intersect($needles, $haystack));
}
function getApprovers($obo, $amt) {
  global $approvallevels;
  $abean = R::findOne('requser', 'username = ?', [$obo]);
  $uid = $abean->id;
  if($uid == 0) {
    //return null;
    return [];
  }
  $uname = $abean->username;
  $mgr = $abean->manager;
  $lletter = $abean->applevel;
  $level = $approvallevels[$lletter];
  $approvers = [];
  while($uname != $mgr and $mgr != '') {
    if($amt <= $level) {
      array_push($approvers, $uname);
    }
    $uname = $mgr;
    $abean = R::findOne('requser', 'username = ?', [$uname]);
    $mgr = $abean->manager;
    $lletter = $abean->applevel;
    $level = $approvallevels[$lletter];
  }
  //add the highest level, if Bill is to be included
  array_push($approvers, $uname);
  return $approvers;
}
?>
