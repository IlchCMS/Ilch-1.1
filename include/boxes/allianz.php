<?php 
#   Copyright by Manuel Staechele
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

  $allyAnzahl = $allgAr['Aanz'];
  if ( $allgAr['Aart'] == 1 ) {
	  $sqlORDER = 'pos';
	} else {
	  $sqlORDER = 'RAND()';
	}
	
	
	$allyNameAr = array();
	$allyLinkAr = array();
	$allyBanaAr = array();
  $allyAktAnz = 0;
	
	$allyAbf = 'SELECT * FROM `prefix_partners` ORDER BY '.$sqlORDER.' LIMIT  0,'.$allyAnzahl;
	$allyErg = db_query($allyAbf);
	if ( db_num_rows($allyErg) > 0) {
	  echo '<div align="center">';
		while($allyRow = db_fetch_object($allyErg)) {
		    echo '<a class="box" href="'.$allyRow->link.'" target="_blank">';
		    if ( empty ($allyRow->banner) OR $allyRow->banner == 'http://' ) {
		      echo $allyRow->name;
		    } else {
		      echo '<img src="'.$allyRow->banner.'" alt="'.$allyRow->name.'" border="0">';
		    }
		    echo '</a><br />';   
	  }
	  echo '</div>';
  }

?>
