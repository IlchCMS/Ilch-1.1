<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

if (!is_admin()) {
  echo '<div class="alert alert-danger" role="alert">Dieser Bereich ist nicht fuer dich...</div>';
  $design->footer();
  exit();
}

# hilfsfunktionen
function get_links_array () {
  $ar = array ();
  $handle=opendir('include/contents');
  while ($ver = readdir ($handle)) {
    if ($ver != "." AND $ver != ".." AND !is_dir('include/contents/'.$ver) ) {
	    $n = explode('.',$ver);
      $ar[$n[0]] = $ver;
    }
  }
  closedir($handle);
  $handle=opendir('include/contents/selfbp/selfp');
  while ($ver = readdir ($handle)) {
    if ($ver == "." OR $ver == ".." OR is_dir('include/contents/selfbp/selfp/'.$ver) ) { continue; }
	  $n = explode('.',$ver);
    if ( file_exists ( 'include/contents/'.$ver) OR file_exists ( 'include/contents/'.$n[0].'.php') ) {
      $n[0] = 'self-'.$n[0];
    }
    $ar[$n[0]] = 'self_'.$ver;
  }
  closedir($handle);
  asort ($ar);
  return ($ar);
}

# funktionen fuer listen
function admin_allg_gfx ( $ak ) {
	$gfx = '';
	$o = opendir('include/designs');
  while ($ver = readdir ($o)) {
    if ($ver != "." AND $ver != ".." AND is_dir('include/designs/'.$ver) ) {

			if ($ver == $ak) {
			  $sel = ' selected';
			} else {
			  $sel = '';
			}
			$gfx .= '<option'.$sel.'>'.$ver.'</option>';
		}
	}
	closedir($o);
  return ( $gfx );
}
function admin_allg_smodul ( $ak ) {
	$ordner = array();
  $handle=opendir('include/contents');
  while ($ver = readdir ($handle)) {
    if ($ver == '.' OR $ver == '..' OR is_dir ('include/contents/'.$ver)) { continue; }
    $lver = explode('.',$ver);
    $ordner[] = $lver[0];
  }
	$smodul = '';
	$ordner = get_links_array ();
  foreach ($ordner as $a => $x) {
	  if ( $a == $ak ) {
		  $sel = ' selected';
		} else {
		  $sel = '';
		}
		$smodul .= '<option'.$sel.' value="'.$a.'">'.ucfirst($a).'</option>';
	}
  return ( $smodul );
}
function admin_allg_wars_last_komms ( $ak ) {
  $ar = array ( 0 => 'nein', -1 => 'ab User', -3 => 'ab Trial', -4 => 'ab Member' );
  $l = '';
  foreach ( $ar as $k => $v ) {
    if ( $k == $ak ) { $sel = ' selected'; } else { $sel = ''; }
    $l .= '<option'.$sel.' value="'.$k.'">'.$v.'</option>';
  }
  return ($l);
}

$csrfCheck = chk_antispam('admin_allg', true);

if ( empty ($_POST['submit']) || !$csrfCheck ) {
  $gfx             = admin_allg_gfx( $allgAr['gfx'] );
  $smodul          = admin_allg_smodul ( $allgAr['smodul'] );
  $wars_last_komms = admin_allg_wars_last_komms ( $allgAr['wars_last_komms'] );

  echo '<legend><h2><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>  Konfiguration</h2></legend>';

  echo '<form action="admin.php?allg" method="POST" class="form-horizontal" role="form">';
	echo '<div class="panel panel-default"><div class="panel-body bg-warning">';

	$ch = '';

  $abf = 'SELECT * FROM `prefix_config` ORDER BY kat,pos,typ ASC';
	$erg = db_query($abf);
	while($row = db_fetch_assoc($erg) ) {
	  if ( $ch != $row['kat'] ) {
echo '<legend>'.$row['kat'].'</legend><br>';
		}
		echo '<div class="form-group"><label class="col-sm-5 control-label text-warning">'.$row['frage'].'</label>';
		echo '<div class="col-sm-6">';
		if ( $row['typ'] == 'input' ) {
		  echo '<input class="form-control" type="text" name="'.$row['schl'].'" value="'.$row['wert'].'">';
		} elseif ($row['typ'] == 'r2') {
		  $checkedj = '';
			$checkedn = '';
			if ($allgAr[$row['schl']] == 1) {
			  $checkedj = 'checked';
				$checkedn = '';
			} else {
			  $checkedn = 'checked';
				$checkedj = '';
			}
		  echo '<label class="radio-inline"><input type="radio" name="'.$row['schl'].'" value="1" '.$checkedj.' > ja</label>';
			echo '<label class="radio-inline"><input type="radio" name="'.$row['schl'].'" value="0" '.$checkedn.' > nein</label>';
		} elseif ( $row['typ'] == 's' ) {
		  $vname = $row['schl'];
		  echo '<div class="col-xs-6"><select class="form-control" name="'.$row['schl'].'">'.$$vname.'</select></div>';
		} elseif ($row['typ'] == 'textarea') {
          echo '<textarea class="form-control" rows="3" name="'.$row['schl'].'">'.$row['wert'].'</textarea>';
        } elseif ($row['typ'] == 'grecht') {
          $grl = dblistee($allgAr[$row['schl']],"SELECT id,name FROM prefix_grundrechte ORDER BY id ASC");
          echo '<div class="col-xs-6"><select class="form-control" name="'.$row['schl'].'">'.$grl.'</select></div>';
        } elseif ($row['typ'] == 'grecht2') {
          $grl = dblistee($allgAr[$row['schl']],"SELECT id,name FROM prefix_grundrechte WHERE id >= -2 ORDER BY id ASC");
          echo '<div class="col-xs-6"><select class="form-control" name="'.$row['schl'].'">'.$grl.'</select></div>';
        } elseif ($row['typ'] == 'password' ) {
		  echo '<input class="form-control" type="password" name="'.$row['schl'].'" value="***">';
		}
		echo '</div></div>';
		$ch = $row['kat'];
	}


	echo '</div></div>';
    echo get_antispam('admin_allg', 0, true);
	echo '<div class="text-center"><input class="btn btn-primary" type="submit" value="&Auml;nderungen speichern" name="submit"></div>';
	echo '</form>';


} elseif ($csrfCheck) {
	$abf = 'SELECT * FROM `prefix_config` ORDER BY kat';
	$erg = db_query($abf);
	while($row = db_fetch_assoc($erg) ) {
	  if ($row['typ'] == 'password' AND $_POST[$row['schl']] == '***') {
	      continue;
	  } elseif ($row['typ'] == 'password') {
	      require_once('include/includes/class/AzDGCrypt.class.inc.php');
          $cr64 = new AzDGCrypt(DBDATE.DBUSER.DBPREF);
          $_POST[$row['schl']] = $cr64->crypt($_POST[$row['schl']]);
      }
	  db_query('UPDATE `prefix_config` SET wert = "'.escape($_POST[$row['schl']], 'textarea').'" WHERE schl = "'.$row['schl'].'"');
	}
    wd ('admin.php?allg', 'Erfolgreich ge&auml;ndert' , 2);

}

//-----------------------------------------------------------|
$design->footer();
?>