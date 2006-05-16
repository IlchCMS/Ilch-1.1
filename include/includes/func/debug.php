<?php 
#   Copyright by Manuel Staechele
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

$debug_output = '';

function debug ($d,$x = 0) {
  global $debug_output;
  
  $o = 0;
  if ( $o == 1 AND $x == 0) {
    $debug_output .= '<span style="background-color: #FFFFFF; border:1px solid grey; color: #000000">';
	  $debug_output .= '&nbsp;'.$d.'&nbsp;</span><br />';
  }
  if ($x == 1 AND $o == 1) {
    ?>
    <script language="JavaScript" type="text/javascript"><!--
    function closeDebugDivID () {
      if (document.getElementById('debugDivID').style.display == 'none') {
        document.getElementById('debugDivID').style.display = 'inline';
      } else {
        document.getElementById('debugDivID').style.display = 'none';
      }
    }
    //--></script>
    <div id="debugDiv" style="position:absolute; top:0px; left:0px; display:inline; width:500px;">
    <a href="javascript:closeDebugDivID();"><img src="include/images/icons/del.gif" alt=""></a>
    <div id="debugDivID">
    <?php echo $debug_output; ?>
    </div></div><?php
  }
}
?>