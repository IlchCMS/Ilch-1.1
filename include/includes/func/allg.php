<?php
#   Copyright by Manuel
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );

##
###
####
#####  W E I T E R L E I T U N G S   F U N K T I O N
function wd ($wdLINK,$wdTEXT,$wdZEIT=3) {
	global $lang;

  if (!is_array($wdLINK)) {
	  $urls  = '<a href="'.$wdLINK.'">'.$lang['forward2'].'</a>';
	  $wdURL = $wdLINK;
	} else {
	  $urls  = '';
    $i = 0;
		foreach($wdLINK as $k => $v) {
		  if ( $i == 0 ) {
			  $wdURL = $v;
			}
			$urls .= '<a href="'.$v.'">'.$k.'</a><br />';
		  $i++;
		}
	}
	$tpl = new tpl ( 'weiterleitung.htm' );
	$ar = array
	(
    'LINK' => $urls,
	  'URL'  => $wdURL,
		'ZEIT' => $wdZEIT,
		'TEXT' => $wdTEXT
	);
	$tpl->set_ar_out ( $ar, 0 );
	unset($tpl);
}

##
###
####
##### g e t   R e c h t
function getrecht ($RECHT, $USERRECHT) {
	if ( empty ( $USERRECHT ) ) {
	  return (false);
	} else {
	  if($USERRECHT <= $RECHT) {
	    return (true);
	  } else {
	    return (false);
	  }
  }
}

##
###
####
##### g e t   U s e r   N a m e
function get_n($uid) {
	$row = db_fetch_object(db_query("SELECT name FROM prefix_user WHERE id = '".$uid."'"));
	return $row->name;
}

##
###
####
##### wochentage sonntag 0 samstag 6
function wtage ($tag) {
  $wtage = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
  return ($wtage[$tag]);
}

##
###
####
##### monate in deutsch
function getDmon ($mon) {
  $monate = array('Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
  return($monate[$mon-1]);
}


##
###
####
##### a l l g e m e i n e s   A r r a y
function getAllgAr () {

	# v1 = schluessel
	# v2 = wert
	# v3 = feldtyp
  # v4 = kurze beschreibung wenn n�tig

	$ar = array();
	$abf = "SELECT schl, wert FROM `prefix_config`";
	$erg = db_query($abf);
	while($row = db_fetch_assoc($erg) ) {
	  $ar[$row['schl']] = $row['wert'];
	}
	return $ar;
}

##
###
####
##### UserRang ermitteln
function userrang ($post,$uid) {
  global $global_user_rang_array;

  if (!isset($global_user_rang_array[$uid])) {
    if (!isset($global_user_rang_array)) {
      $global_user_rang_array = array();
    }
    if ( empty($uid) ) {
      $rRang = 'Gast';
    } else {
      $rRang = @db_result(db_query("SELECT bez FROM prefix_user LEFT JOIN prefix_ranks ON prefix_ranks.id = prefix_user.spezrank WHERE prefix_user.id = ".$uid),0);
    }
    if ( empty($rRang) ) {
      $post = ( $post == 0 ? 1 : $post );
      $rRang = @db_result(db_query("SELECT bez FROM `prefix_ranks` WHERE spez = 0 AND min <= ".$post." ORDER BY min DESC LIMIT 1"),0);
    } elseif ( $rRang != 'Gast' ) {
      $rRang = '<i><b>'.$rRang.'</b></i>';
    }
    $global_user_rang_array[$uid] = $rRang;
  }

  return ($global_user_rang_array[$uid]);
}


##
###
####
##### makiert suchwoerter
function  markword($text,$such) {
  $erg  = '<span style="background-color: #EBF09B;">';
  $erg .= $such."</span>";
	$text = str_replace($such,$erg,$text);
  return $text;
}


##
###
####
##### gibt die smiley lilste zurueck
function getsmilies () {
  global $lang;
  $zeilen = 3; $i = 0;
	$b = '<script language="JavaScript" type="text/javascript">function moreSmilies () { var x = window.open("about:blank", "moreSmilies", "width=250,height=200,status=no,scrollbars=yes,resizable=yes"); ';
  $a = '';
  $erg = db_query('SELECT emo, ent, url FROM `prefix_smilies`');
	while ($row = db_fetch_object($erg) ) {

    $b .= 'x.document.write ("<a href=\"javascript:opener.put(\''.addslashes(addslashes($row->ent)).'\')\">");';
    $b .= 'x.document.write ("<img style=\"border: 0px; padding: 5px;\" src=\"include/images/smiles/'.$row->url.'\" title=\"'.$row->emo.'\"></a>");';

    if ($i<12) {
      # float einbauen
      if($i%$zeilen == 0 AND $i <> 0) { $a .= '<br /><br />'; }
      $a .= '<a href="javascript:put(\''.addslashes($row->ent).'\')">';
      $a .= '<img style="margin: 2px;" src="include/images/smiles/'.$row->url.'" border="0" title="'.$row->emo.'"></a>';
    }
    $i++;
	}
  $b .= ' x.document.write("<br /><br /><center><a href=\"javascript:window.close();\">'.$lang['close'].'</a></center>"); x.document.close(); }</script>';
  if ($i>12) { $a .= '<br /><br /><center><a href="javascript:moreSmilies();">'.$lang['more'].'</a></center>'; }
  $a = $b.$a;
  return ($a);
}



##
###
####
##### generey key with x length
function genkey ( $anz ) {
	$letterArray = array ('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
  $key = '';
	for ($i=0;$i < $anz ; $i ++)
	{
	    mt_srand((double)microtime()*1000000);
	    $zufallZahl = mt_rand(0,62);
      $key .= $letterArray[$zufallZahl];
  }
	return ( $key );
}

function icmail ($mail, $bet, $txt, $from = '', $html = false) {
	global $allgAr;
	include_once('include/includes/class/phpmailer/class.phpmailer.php');
	$mailer = new PHPMailer();
	if (empty($from)) {
		$mailer->From = $allgAr['adminMail'];
		$mailer->FromName = $allgAr['allg_default_subject'];
	} elseif ( preg_match('%(.*) <([\w\.-]*@[\w\.-]*)>%i', $from, $tmp) ) {
		$mailer->From = trim($tmp[2]);
		$mailer->FromName = trim($tmp[1]);
	} elseif (preg_match('%([\w\.-]*@[\w\.-]*)%i', $from, $tmp)) {
		$mailer->From = trim($tmp[1]);
		$mailer->FromName = '';
	}
	if ($allgAr['mail_smtp']) { //SMTP Versand

		$smtpser = @db_result(db_query('SELECT `t1` FROM `prefix_allg` WHERE `k` = "smtpconf"'));
		if (empty($smtpser)) {
			echo '<span style="font-size: 2em; color: red;">Mailversand muss konfiguriert werden!</span><br />';
		} else {
			$smtp = unserialize($smtpser);

			$mailer->IsSMTP();
			$mailer->Host = $smtp['smtp_host'];
			$mailer->SMTPAuth = ($smtp['smtp_auth'] == 'no' ? false : true);
			if ($smtp['smtp_auth'] == 'ssl' or $smtp['smtp_auth'] == 'tls') {
				$mailer->SMTPSecure = $smtp['smtp_auth'];
			}
			if (!empty($smtp['smtp_port'])) {
				$mailer->Port = $smtp['smtp_port'];
			}
			$mailer->AddReplyTo($mailer->From, $mailer->FromName);

			if ($smtp['smtp_changesubject'] and $mailer->From != $smtp['smtp_email']) {
				$bet = '(For ' .$mailer->FromName . ' - '. $mailer->From .') '. $bet;
				$mailer->From = $smtp['smtp_email'];
			}

			$mailer->Username = $smtp['smtp_login'];

			require_once('include/includes/class/AzDGCrypt.class.inc.php');
			$cr64 = new AzDGCrypt(DBDATE.DBUSER.DBPREF);
			$mailer->Password = $cr64->decrypt($smtp['smtp_pass']);

			if ($smtp['smtp_pop3beforesmtp'] == 1) {
				include_once('include/includes/class/phpmailer/class.pop3.php');
				$pop = new POP3();
				$pop3port = !empty($smpt['smtp_pop3port']) ? $smpt['smtp_pop3port'] : 110;
				$pop->Authorise($smpt['smtp_pop3host'], $pop3port, 5, $mailer->Username, $mailer->Password, 1);
			}
		}
		//$mailer->SMTPDebug = true;
	}
	if (is_array($mail)) {
		if ($mail[0] != 'bcc') {
			array_shift($mail);
			foreach ($mail as $m){
				$mailer->AddBCC(escape_for_email($m));
			}
			$mailer->AddAddress($mailer->From);
		} else {
			foreach ($mail as $m){
				$mailer->AddAddress(escape_for_email($m));
			}
		}
	} else {
		$mailer->AddAddress(escape_for_email($mail));
	}
	$mailer->Subject = escape_for_email($bet, true);
	$txt = str_replace("\r", "\n", str_replace("\r\n", "\n", $txt));
	if ($html) {
		$mailer->IsHTML(true);
		$mailer->AltBody = strip_tags($txt);
	}
	$mailer->Body = $txt;

	if ($mailer->Send()) {
		return true;
	} else {
		if (is_coadmin()) {
			echo "<h2 style=\"color:red;\">Mailer Error: " . $mailer->ErrorInfo . '</h2>';
		}
		return false;
	}
}

function html_enc_substr($text, $start, $length) {
    if (version_compare(PHP_VERSION, '5.3.4') !== -1) {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);
    } else {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES, ILCH_ENTITIES_FLAGS);
    }
    $trans_tbl = array_flip($trans_tbl);
    return(htmlentities(substr(strtr($text, $trans_tbl), $start, $length), ILCH_ENTITIES_FLAGS, ILCH_CHARSET));
}

function get_datum ($d) {
  if (strpos($d,'.') !== FALSE) { $d = str_replace('.','-',$d); }
  if (strpos($d,'/') !== FALSE) { $d = str_replace('/','-',$d); }
  if (is_numeric(substr($d,-4))) {
    list($t,$m,$j) = explode('-', $d);
  } elseif (is_numeric(substr($d,0,4))) {
    list($j,$m,$t) = explode('-', $d);
  }
  $d = $j.'-'.$m.'-'.$t;
  return ($d);
}

/**
 * Add "http" to url if no "http/https" given.
 *
 * @param string $homepage
 * @return string
 */
function get_homepage($homepage) {
    $homepage = trim($homepage);

    if (!empty($homepage)
        && substr($homepage, 0, 7) !== 'http://'
        && substr($homepage, 0, 8) !== 'https://'
    ) {
        $homepage = 'http://'.$homepage;
    }

    return $homepage;
}

function get_wargameimg ($img) {
  if (file_exists('include/images/wargames/'.$img.'.gif')) {
    return ('<img src="include/images/wargames/'.$img.'.gif" alt="'.$img.'" border="0">');
  } elseif (file_exists('include/images/wargames/'.$img.'.jpg')) {
    return ('<img src="include/images/wargames/'.$img.'.jpg" alt="'.$img.'" border="0">');
  } elseif (file_exists('include/images/wargames/'.$img.'.jpeg')) {
    return ('<img src="include/images/wargames/'.$img.'.jpeg" alt="'.$img.'" border="0">');
  } elseif (file_exists('include/images/wargames/'.$img.'.png')) {
    return ('<img src="include/images/wargames/'.$img.'.png" alt="'.$img.'" border="0">');
  }
  return ('');
}

function iurlencode_help ($a) {
  if (preg_match("/(http:|https:|ftp:)/", $a[0])) {
    return ($a[0]);
  }

  return (rawurlencode($a[1]).substr($a[0], -1));
}

function iurlencode ($s) {
  return (preg_replace_callback("/([^\/]+|\/[^\.])[\.\/]/", 'iurlencode_help', $s));
  /*
  $x = 'false';
  if (preg_match ('/(http:|https:|ftp:)[^:]+:[^@]+@./', $s)) {
    $x = preg_replace('/([^:]+:[^@]+@)./',"\\1",$s);
  	$s = str_replace($x,'',$s);
	} elseif (substr($s, 0, 7) == 'http://') {
	  $s = substr ($s, 7);
		$x = 'http://';
	} elseif (substr($s, 0, 8) == 'https://') {
	  $s = substr ($s, 8);
	  $x = 'https://';
	} elseif (substr($s, 0, 6) == 'ftp://') {
	  $s = substr ($s, 6);
	  $x = 'ftp://';
	}


	$a = explode('/', $s);
  $r = '';
  for ($i=0;$i<count($a);$i++) {
    $r .= rawurlencode($a[$i]).'/';
  }

	if ($x !== 'false') {
	  $r = $x.$r;
	}

  $r = substr($r, 0, -1);
  return ($r);
  */
}

/**
 * Pr�ft, ob der Antispamcode richtig eingegeben wurde
 * Der NoPictureMode f�gt ein Hidden Feld ein, um Cross Site Request Forgery Attacken zu unterbinden, der NoPictureMode
 * wird automatisch genutzt, wenn kein Bildabfrage statt findet, kann aber auch erzwungen werden
 *
 * @global array $allgAr
 * @param string $m Modulname, um unterschiedliche Antispamfelder auf einer Seite zu erm�glichen
 * @param boolean $nopictures NoPictureMode erzwingen
 * @return boolean
 */
function chk_antispam($m, $nopictures = false)
{
    global $allgAr;

    if (!$nopictures && is_numeric($allgAr['antispam']) && has_right($allgAr['antispam'])) {
        $nopictures = true;
    }

    $valid = false;

    if ($nopictures && isset($_POST['antispam_id'])) {
        $antispamId = $_POST['antispam_id'];
        if (isset($_SESSION['antispam'][$antispamId]) && $_SESSION['antispam'][$antispamId]) {
            $valid = true;
            unset($_SESSION['antispam'][$antispamId]);
        }
    } elseif (isset($_POST['captcha_code']) && isset($_POST['captcha_id'])) {
        require_once 'include/includes/captcha/Captcha.php';
        $controller = new Captcha();
        $captchaCode = strtoupper($_POST['captcha_code']);
        $valid = $controller->isValid($captchaCode, $_POST['captcha_id']);
    }
    return $valid;
}

/**
 * Erzeugt HTML Code f�r ein Formularfeld, welches f�r einen Antibot-Schutz dienen oder vor CSFR Attacken sch�tzen soll
 * Beschreibung zum NoPictureMode bitte der chk_antispam Funktion entnehmen
 *
 * @global array $allgAr
 * @param string $m Modulname
 * @param integer $t Type, der angibt wie das Formularfeld formatiert wird (0, 1 oder > 10 als Breite f�r das label) siehe Code :P
 * @param boolean $nopictures Erzwing NoPictureMode
 * @return string
 */
function get_antispam($m, $t, $nopictures = false)
{
    global $allgAr, $ILCH_BODYEND_ADDITIONS;
    static $addedJavascript = false;

    if ($addedJavascript === false) {
        $ILCH_BODYEND_ADDITIONS .= '<script type="text/javascript" src="include/includes/js/captcha.js"></script>' . "\n";
        $addedJavascript = true;
    }

    if (!$nopictures && $t < 0 || (is_numeric($allgAr['antispam']) && has_right($allgAr['antispam']))) {
        $nopictures = true;
    }

    $id = uniqid($m . '_', true);

    if ($nopictures) {
        if (!isset($_SESSION['antispam']) || !is_array($_SESSION['antispam'])) {
            $_SESSION['antispam'] = array();
        }

        $_SESSION['antispam'][$id] = true;
        return '<input type="hidden" name="antispam_id" value="' . $id . '" />';
    }

    include 'include/includes/captcha/settings.php';

    $helpText = 'Geben Sie diese Zeichen in das direkt daneben stehende Feld ein.';
    $seperator = ' ';

    if ($t == 0) {
        $seperator = '<br />';
        $helpText = 'Geben Sie diese Zeichen in das direkt darunter stehende Feld ein.';
    }
    $img = '<img width="' . $imagewidth . '" height="' . $imageheight . '" src="include/includes/captcha/captchaimg.php?id='
        . $id . '&nocache=' . time() . '" alt="captchaimg" title="' . $helpText . '" class="captchaImage">'
        . $seperator . '<input class="captcha_code" name="captcha_code" type="text" maxlength="5" size="8" title="Geben Sie die Zeichen aus dem Bild ein">'
        . '<input type="hidden" name="captcha_id" value="' . $id .  '" />';
        ;

    if ($t == 1) {
        $img = '<tr><td class="Cmite"><b>Antispam</b></td><td class="Cnorm">' . $img . '</td></tr>';
    } elseif ($t > 10) {
        $img = '<label style="float:left; width: ' . $t . 'px; ">Antispam</label>' . $img . '<br/>';
    }
    return $img;
}

// Funktion scandir f�r PHP 4
if (version_compare(phpversion(), '5.0.0') == -1) {
    function scandir($dir)
    {
        $dh = opendir($dir);
        while (false !== ($filename = readdir($dh))) $files[] = $filename;
        sort($files);
        return $files;
    }
}
// Funktion array_fill_keys < PHP 5.2
if (version_compare(phpversion(), '5.2.0') == -1) {
	function array_fill_keys($target, $value = '') {
		if(is_array($target)) {
			foreach($target as $key => $val) {
				$filledArray[$val] = is_array($value) ? $value[$key] : $value;
			}
		}
		return $filledArray;
	}
}

// Funktion, die die Größe aller Dateien im Ordner zusammenrechnet
function dirsize($dir)
{
    if (!is_dir($dir)) {
        return -1;
    }
    $size = 0;
    $files = array_slice(scandir($dir), 2);
    foreach ($files as $filenr => $file) {
        if (is_dir($dir . $file)) {
            $size += dirsize($dir . $file . '/');
        } else {
            $size += @filesize($dir . $file);
        }
    }
    return $size;
}

//Rechnet bytes in KB oder MB um
function nicebytes($bytes){
    if ($bytes<1000000) {
        return round($bytes/1024,2).' KB';
    } else {
        return round($bytes/(1024*1024),2).' MB';
    }
}

?>
