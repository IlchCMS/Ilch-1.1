<?php 
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

function unescape ( $var ) {
  $var = stripslashes($var);
  return ($var);
}

# moegliche typ vars
# - integer
# - string
# - textarea
function escape($var, $type) {
    switch ($type) {
        case 'integer' :
            $var = intval($var);
            break;
        case 'string' :
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = strip_tags($var);
            $var = db_escape_string($var);
            break;
        case 'textarea' :
            $var = (get_magic_quotes_gpc() ? stripslashes($var) : $var);
            $var = db_escape_string($var);

            break;
    }
    return ($var);
}

function escape_nickname ($t) {
  $t = preg_replace("/[^a-zA-Z0-9-\[\]\*\ \+=\._\|]/","",$t);
  $t = substr($t, 0, 15);
  $t = escape($t, 'string');
  return ($t);
}

function escape_for_email ($t, $leerzeichen = false) {
  if ($leerzeichen === true) {
    $t = preg_replace ("/\015\012|\015|\012|\072|\074|\076/", "", $t);
  } else {
    $t = preg_replace ("/\015\012|\015|\012|\072|\074|\076|\040/", "", $t);
  }
  return ($t);
}

function escape_for_fields ($t) {
#  $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
#  $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
#  $t = str_replace ('<', '&lt;', str_replace('>', '&gt;', $t));
  $t = htmlentities($t, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);
  
  return ($t);
}

function escape_email_to_show ($str) {
  $ret = "";
  $arr = unpack("C*", $str);
  foreach ($arr as $char) {
    $ret .= sprintf("%%%X", $char);
  }
  return $ret;
}
