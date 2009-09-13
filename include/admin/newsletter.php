<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de
defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

function XAJAX_changeList($select)
{
    $objResponse = new xajaxResponse();
    
    if ($select == 'Normal')
    {
        $auswahl = array (
	       'u0'   => 'an alle User',
        );
    
        $erg = db_query("SELECT `name`,`id` FROM `prefix_groups` ORDER BY `id`");
        while ($RRrow = db_fetch_object($erg))
        {
            $auswahl['g'.$RRrow->id]= $RRrow->name;
        }

        $listeB = '';
        $listeT = '';

        foreach ( $auswahl as $k => $v )
        {
            if (strpos($k,'u') !== false)
            {
                $listeB .= '<option value="P'.$k.'">'.$v.' PrivMsg</option>'."\n";
                $listeB .= '<option value="E'.$k.'">'.$v.' eMail</option>'."\n";
            }
            elseif ( strpos($k,'g') !== false)
            {
                $listeT .= '<option value="P'.$k.'">'.$v.' PrivMsg</option>'."\n";
                $listeT .= '<option value="E'.$k.'">'.$v.' eMail</option>'."\n";
            }
        }
    
        $content =
<<<END
            <select name="auswahl">
                <option value="Enews" selected="selected">eMail Newsletter</option>
                <optgroup label="Benutzer">
                    {$listeB}
                </optgroup>
                <optgroup label="Gruppen">
                    {$listeT}
                </optgroup>
    		</select>
END;
    }
    else
    {
        $erg = db_query("SELECT * FROM `prefix_grundrechte` ORDER BY `id` ASC");
        $listeG = '';

        while ($row = db_fetch_assoc($erg))
        {
            $listeG .= '<optgroup label="'.$row['name'].'">';
            $listeG .= '<option value="Pr'.$row['id'].'"> PrivMsg</option>';
            $listeG .= '<option value="Er'.$row['id'].'"> eMail</option>';
            $listeG .= '</optgroup>';
        }
        
        $content =
<<<END
            <select name="auswahl">
                <option selected="selected" disabled="disabled">Bitte treffen Sie eine Auswahl</option>
                    {$listeG}
            </select>
END;
    }
    
    $objResponse->assign('list', 'innerHTML', $content);
    
    return $objResponse;
}

$xajax = new xajax('http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?newsletter=0');
$xajax->registerFunction('XAJAX_changeList');
$xajax->processRequest();

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

if (empty($_POST['SEND']))
{    
    echo $xajax->printJavascript();
    $tpl = new tpl ('newsletter', 1);
    $tpl->out(0);
}
else
{
    $mailopm = substr($_POST['auswahl'],0,1);
    $usrogrp = substr($_POST['auswahl'],1,1);
  
	if ( $_POST['auswahl'] == 'Enews' ) 
	{
		$q = "SELECT `email` FROM `prefix_newsletter`";
	}
	elseif ( $usrogrp == 'u' ) 
	{
		$q = "SELECT `email`,`name` as `uname`,`id` as `uid` FROM `prefix_user` WHERE `recht` <= '-1'";
	}
	elseif ( $usrogrp == 'g' )
	{
        $gid = substr ( $_POST['auswahl'], 2 , strlen ( $_POST['auswahl'] ) -1 );
        $q = "SELECT `b`.`email`, `b`.`name` as `uname`, `b`.`id` as `uid` FROM `prefix_groupusers` `a` LEFT JOIN `prefix_user` `b` ON `a`.`uid` = `b`.`id` WHERE `a`.`gid` = '$gid'";
	}
	elseif ( $usrogrp == 'r' )
    {
        $q = "SELECT `email`,`id` as `uid` FROM `prefix_user` WHERE `recht` = '".substr($_POST['auswahl'], 2, strlen($_POST['auswahl'])-1)."'";
 	}	
  
	$erg = db_query ( $q );
	
	$zahler = 0;
    
    if ( db_num_rows($erg) > 0 ) 
	{
        while ($row = db_fetch_object($erg) )
        {
            if ( $mailopm == 'E' ) 
            {
                icmail ( $row->email ,$_POST['bet'],$_POST['txt'] );
            } 
            elseif ($mailopm == 'P' ) 
            {	
                sendpm($_SESSION['authid'], $row->uid, escape($_POST['bet'], 'string'), escape($_POST['txt'], 'string'));
            }
        
            $zahler++;
        }

        if ( $mailopm == 'E' )
        {
            $eMailorPmsg = 'eMail(s)';		
        }
        elseif ($mailopm == 'P' )
        {	
            $eMailorPmsg = 'Private Nachrichte(n)';			
        }

        wd('admin.php?newsletter', 'Es wurde(n) '.$zahler.' '.$eMailorPmsg.' verschickt.',5);	
    }
	else
	{
        wd('admin.php?newsletter', 'F&uuml;r diese Auswahl konnte nichts gefunden werden.', 5);
	}
}

$design->footer();
?>