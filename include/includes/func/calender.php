<?php
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );


function getCalendar($mon, $jahr, $url, $urlod, $data, $cellpadding=2) {

  # lege einige variablen fest
  $ak_tag    = date('j');
  $ak_mon    = date('n');
  $ak_jahr   = date('Y');
  $days      = date('t',mktime(0,0,0,$mon,1,$jahr));
  $fw        = str_replace(0,7,date('w',mktime(0,0,0,$mon,1,$jahr))) -1;
  $next_mon  = $mon +1;
  $last_mon  = $mon -1;
  $next_jahr = $jahr;
  $last_jahr = $jahr;
  if ($next_mon > 12) {
    $next_jahr = $jahr +1;
    $next_mon  = 1;
  }
  if ($last_mon == 0) {
    $last_jahr = $jahr -1;
    $last_mon  = 12;
  }

  # erstelle return string
  $return_str  = '';
  $return_str .= '<table class="border ilch_kalender_box" cellpadding="'.$cellpadding.'" cellspacing="1" border="0">';
  $return_str .= '<tr class="Chead">';
  $return_str .= '<th><a href="'.str_replace('{mon}',$last_mon, str_replace('{jahr}',$last_jahr, $urlod)).'"><b>&lt;</b></a></th>';
  $return_str .= '<th colspan="5" class="text-center">'.$mon.'. '.$jahr.'</th>';
  $return_str .= '<th><a href="'.str_replace('{mon}',$next_mon, str_replace('{jahr}',$next_jahr, $urlod)).'"><b>&gt;</b></a></th>';
  $return_str .= '</tr><tr class="Cdark">';
  $return_str .= '<td>Mo</td><td>Di</td><td>Mi</td><td>Do</td><td>Fr</td><td>Sa</td><td>So</td>';
  $return_str .= '</tr><tr class="Cnorm">';
  $return_str .= str_repeat ('<td>&nbsp;</td>', $fw);

  for($i=1;$i<=$days;$i++) {
    if (($i+$fw-1) % 7 == 0 AND $i > 1) { $return_str .= '</tr><tr>'; }
    if ($i == $ak_tag AND $mon == $ak_mon AND $jahr == $ak_jahr) { $class = 'Cmite'; } else { $class = 'Cnorm'; }
    $surl = str_replace('{mon}', $mon, str_replace('{tag}',$i, str_replace('{jahr}',$jahr, $url)));
    if (isset($data[mktime (0,0,0,$mon,$i,$jahr)])) { $out_i = '<b>'.$i.'</b>'; } else { $out_i = $i; }
    $return_str .= '<td class="'.$class.' text-center"><a href="'.$surl.'">'.$out_i.'</a></td>';
  }

  $return_str .= str_repeat ('<td class="Cnorm">&nbsp;</td>', (7-(($i+$fw-1) % 7)) % 7 );
  $return_str .= '</tr></table>';
  return ($return_str);
}

?>