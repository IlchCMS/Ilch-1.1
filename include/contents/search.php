<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );



function  serach_mark($text,$such) { 
  #$text = BBcode($text);
	$serar = explode(' ', $such);
  $text  = strip_tags($text);
  $text  = stripslashes($text);
  $rte   = '';
  $tleng = 30;
  foreach($serar as $v) {
    $firs = strpos(strtolower($text),strtolower($v));
    $begi = (($firs - $tleng) < 0 ? 0 : $firs - $tleng );
    $leng = strlen($text);
    $ende = (($firs + strlen($v) + $tleng) > $leng ? $leng : $firs + strlen($v) + $tleng );
    $ttxt = substr($text,$begi,($ende - $begi));
    $rte .= ' ... '.preg_replace("/".$v."/si",'<b>'.$v.'</b>',$ttxt);
  }
  return ($rte); 
}

function search_finduser() {
  $design = new design ( 'Finduser' , '', 0 );
  $design->header();
  
  $tpl = new tpl ( 'search_finduser' );
  $tpl->out(0);
  if ( isset ( $_POST['sub'] ) AND !empty($_POST['name']) ) {
	  $name = str_replace('*',"%",$_POST['name']);
	  $q = "SELECT name,name FROM prefix_user WHERE name like '".$name."'";
	  $tpl->set ('username',dbliste('',$tpl,'username',$q));
	  $tpl->out(1);
  }
  $tpl->out(2);  
  $design->footer();
}

if ($menu->get(1) == 'finduser') {
  search_finduser();
  exit();
}


$such = '';
if ($menu->get(1) != '') {
  $such = $menu->get(1);
} elseif (isset($_REQUEST['search'])) {
  $such = $_REQUEST['search'];
}

$such = stripslashes(escape($such, 'string'));

$snac = 'Suche';
if ($such == 'augt' OR $such == 'aeit' OR $such == 'aubt') {
  $ar_s = array('aubt'=>'unbeantworteten Themen','aeit'=>'eigenen Beitr&auml;gen','augt'=>'neue Themen seit dem letzten Besuch');
  $snac = $ar_s[$such];
} elseif ( isset($_REQUEST['search']) ) {
  $snac = 'nach: '.$such;
}


$title = $allgAr['title'].' :: Suchen :: '.htmlentities($snac);
$hmenu  = '<a class="smalfont" href="index.php?search">Suchen</a><b> &raquo; </b>'.htmlentities($snac);
$design = new design ( $title , $hmenu );
$design->header();

$tpl = new tpl ('search');
$tpl->set ('size', 30);
if ($such != 'augt' AND $such != 'aeit' AND $such != 'aubt') {
  $tpl->set_out('search',escape_for_fields($such),0);
}

if (!empty($such)) {
  $page = 1;
  if (isset($_GET['page'])) {
    $page = str_replace('-p','',$_GET['page']);
  }
  
  
  $limit = 25;  // Limit 
  $anfang = ($page - 1) * $limit;	
  
  if ($such == 'aubt' OR $such == 'augt' OR $such == 'aeit') {
    $s = "DISTINCT b.id as fid, a.name as titel, 'foru' as typ, a.id as id";
    $q = "select {SELECT}
      FROM prefix_topics a
        LEFT JOIN prefix_forums b ON b.id = a.fid
        LEFT JOIN prefix_posts c ON c.tid = a.id
      WHERE (b.view >= ".$_SESSION['authright']." OR b.reply >= ".$_SESSION['authright']." OR b.start >= ".$_SESSION['authright'].") 
         AND {WHERE}
      ORDER BY c.time DESC";
  }
  $x = time() - (3600 * 24 * 360);
  if ($such == 'aubt') {
    $where = "c.time >= ". $x ." AND a.rep = 0";
    $c = str_replace('{WHERE}',$where,str_replace('{SELECT}','COUNT(DISTINCT a.id)',$q));
    $gAnz  = db_result(db_query($c),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q));
  } elseif ($such == 'augt') {
    $where = "c.time >= ". $x ." AND c.time >= ".$_SESSION['lastlogin'];
    $gAnz  = db_result(db_query(str_replace('{WHERE}',$where,str_replace('{SELECT}','COUNT(DISTINCT a.id)',$q))),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q));
  } elseif ($such == 'aeit') {
    $where = "c.time >= ". $x ." AND c.erstid = ".$_SESSION['authid'];
    $gAnz  = db_result(db_query(str_replace('{WHERE}',$where,str_replace('{SELECT}',' COUNT(DISTINCT a.id)',$q))),0);
    $q     = str_replace('{WHERE}',$where,str_replace('{SELECT}',$s,$q));
   } else {
    $such = str_replace('-','',$such);
    $such = str_replace('=','',$such);
    $such = str_replace('&','',$such);
  
	  $serar = explode(' ', $such);
    $str_forum = '';
    $str_news  = '';
    $str_downs  = '';
	  foreach($serar as $v) {
	    $str = str_replace('\'','',$v);
		  $str = str_replace('"','',$str);
      $str = addslashes($str);
		  if ( !empty($str) ) {
		    $str_forum .= "txt LIKE '%".$str."%' AND ";
        $str_news  .= "news_text LIKE '%".$str."%' AND ";
        $str_downs  .= "`descl` LIKE '%".$str."%' AND ";
		  }
	  }
    
    $q = "(
      SELECT DISTINCT
        0 as fid,
        news_title as titel,
        'news' as typ,
        news_id as id,
        news_time as time
      FROM prefix_news
      WHERE (".$str_news." 1 = 1)
        AND (news_time >= ". $x .")
      
    ) UNION (
    
      SELECT DISTINCT
        prefix_topics.fid as fid,
        prefix_topics.name as titel,
        'foru' as typ,
        prefix_topics.id as id,
        time as time
      FROM prefix_posts
        LEFT JOIN prefix_topics ON prefix_topics.id = prefix_posts.tid
        LEFT JOIN prefix_forums ON prefix_forums.id = prefix_topics.fid
      WHERE (prefix_forums.view >= ".$_SESSION['authright']." OR prefix_forums.reply >= ".$_SESSION['authright']." OR prefix_forums.start >= ".$_SESSION['authright'].")
        AND (".$str_forum." 1 = 1)
        AND (time >= ". $x .")
      GROUP BY prefix_topics.id

    ) UNION (
    
      SELECT DISTINCT
        0 as fid,
        CONCAT( name, ' ', version ) AS titel,
        'down' as typ,
        id,
        UNIX_TIMESTAMP(time) as time
      FROM prefix_downloads
      WHERE (".$str_downs." 1 = 1)
        AND (time >= ". $x .")
    )
    
    ORDER BY time DESC";
          
    $gAnz = db_num_rows(db_query($q));
  }

  $q .= " LIMIT ".$anfang.",".$limit;
  
  $MPL = db_make_sites ($page , "" , $limit , "index.php?search=".urlencode($such)."&amp;page=", "", $gAnz );
  $tpl->set_ar_out(array('MPL'=>$MPL,'gAnz'=>$gAnz),1);
  
  $q = db_query($q);
  $class = '';
  while($r = db_fetch_assoc($q) ) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
    $r['class'] = $class;
    if ($r['typ'] == 'foru') {
      $r['ctime'] = db_result(db_query("SELECT MAX(time) FROM prefix_posts WHERE tid = ".$r['id']),0,0);
      $r['ord'] = forum_get_ordner($r['ctime'],$r['id'],$r['fid']);
      $r['link'] = 'forum-showposts-'.$r['id'];
    } elseif ($r['typ'] == 'news') {
      $r['ord']  = 'ord';
      $r['link'] = 'news-'.$r['id'];
    } elseif ($r['typ'] == 'down') {
      $r['ord']  = 'ord';
      $r['link'] = 'downloads-show-'.$r['id'];
    }
    $tpl->set_ar_out($r,2);
  }
  $tpl->out(3);
}    
				
$design->footer();

?>