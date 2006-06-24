<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

function rteSafe($strText) {
	//returns safe code for preloading in the RTE
	$tmpString = $strText;
	
	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);
	
	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
  $tmpString = str_replace("\\\"", "\"", $tmpString);
	
	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);
	
	return $tmpString;
}

function get_akl ($ak) {
  $ar_l = array();

  if ( is_writeable ( 'include/contents/selfbp/selfp' ) ) {
    $ar_l['pneu'] = 'Neue Seite';
    $o = opendir ( 'include/contents/selfbp/selfp' );
    while ($v = readdir($o) ) {
      if ( $v == '.' OR $v == '..' ) { continue; }
      $ar_l['p'.$v] = $v;
    }
    closedir($o);
  }
  if ( is_writeable ( 'include/contents/selfbp/selfb' ) ) {
    $ar_l['bneu'] = 'Neue Box';
    $o = opendir ( 'include/contents/selfbp/selfb' );
    while ($v = readdir($o) ) {
      if ( $v == '.' OR $v == '..' ) { continue; }
      $ar_l['b'.$v] = $v;
    }
    closedir($o);
  }
  
  $l = '';
  foreach ($ar_l as $k => $v ) {
    if ( $k == $ak ) { $sel = ' selected'; } else { $sel = ''; }
    $l .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
  }
  return ($l);
}
function get_name ($akl) {
  $n = substr ($akl,1);
  $n = str_replace('.php','',$n);
  return ($n);
}

function get_nametosave ($n) {
  $n = preg_replace("/[^a-zA-Z0-9]/","",$n);
  return ($n);
}

function get_text ($akl) {
  $f = substr ( $akl, 0, 1);
  if (($f == 'b' OR $f == 'p') AND file_exists ( 'include/contents/selfbp/self'.$f.'/'.substr($akl,1))) {
    $t = implode("", file ('include/contents/selfbp/self'.$f.'/'.substr($akl,1)));
		return (edit_text ($t, false));
  }
 
  return ('');
}

function edit_text ($t, $add) {
  $erg = preg_match ("/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>/s", $t);
  if (!$erg AND $add) {
    $t = trim($t);
    $t = '<?php defined (\'main\') or die (\'no direct access\'); ?>'. $t;
    $t = preg_replace("/\/([^>]*)>/","/\\1>\n",$t);
  } elseif ($erg AND !$add) {
    $t = preg_replace("/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>(.*)$/s","\\1",$t);
    $t = preg_replace ("/(\015\012|\015|\012)/", "", $t);
  }
  return ($t);
}

# speichert die datei
function save_file_to ($filename, $data, $flags = 0, $f = FALSE) {
  if(($f===FALSE) && (($flags%2)==1)) $f=fopen($filename, 'a'); else if($f===FALSE) $f=fopen($filename, 'w');
  if(round($flags/2)==1) while(!flock($f, LOCK_EX)) { /* lock */ }
  if(is_array($data)) $data=implode('', $data);
  fwrite($f, $data);
  if(round($flags/2)==1) flock($f, LOCK_UN);
  fclose($f);
}

$f = false;
if ( !is_writable('./include/contents/selfbp/selfp') ) {
  $f = true;
  echo 'Das include/contents/selfbp/selfp Verzeichnis braucht chmod 777 Rechte damit du eine eigene Datei erstellen kannst!<br /><br />';
}
if ( !is_writable('./include/contents/selfbp/selfb') ) {
  echo 'Das include/contents/selfbp/selfb Verzeichnis braucht chmod 777 Rechte damit du eine eigene Box erstellen kannst!<br /><br />';
  if ( $f == true ) {
    exit ( 'Entweder das include/contents/selfbp/selfb oder das include/contents/selfbp/selfp Verzeichnis brauchen Schreibrechte sonst kann hier nicht gearbeitet werden' );
  }
}


if ( isset ($_POST['text']) AND isset($_POST['name']) AND isset($_POST['akl']) ) {
  # speichern
  $akl = $_POST['akl'];
  $text = rteSafe($_POST['text']);
  $text = edit_text($text, true);
  $name = get_nametosave($_POST['name']);
  
  $a = substr ( $akl, 0, 1);
  $e = substr ( $akl, 1 );
  
  if ( $e != 'neu' ) {
    unlink ( 'include/contents/selfbp/self'.$a.'/'.$e );
  }
  
  $fname = 'include/contents/selfbp/self'.$a.'/'.$name.'.php';
  save_file_to ( $fname, $text );
  
  wd ('admin.php?selfbp', 'Ihre Aenderungen wurden gespeichert...', 3);
} else {
  $tpl = new tpl ( 'selfbp', 1 );
  $akl  = '';
  if ( isset ( $_REQUEST['akl'] ) ) { $akl = $_REQUEST['akl']; }
  if (isset($_REQUEST['del'] )){
    $del=$_REQUEST['del'];
    $a = substr ( $del, 0, 1);
    $e = substr ( $del, 1 );
  
    if ( $e != 'neu' ) {
      unlink ( 'include/contents/selfbp/self'.$a.'/'.$e );
    }
  }
  $text = get_text($akl);
  $text = rteSafe($text);
  $name = get_name($akl);
  $akl  = get_akl ( $akl );
  $tpl->set_ar_out(array('akl'=>$akl,'text'=>$text,'name'=>$name),0);
}

$design->footer();
?>
