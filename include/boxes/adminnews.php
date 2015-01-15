<?php 
defined ('main') or die ( 'no direct access' );

$abf = 'SELECT
          a.news_kat as kate,
          DATE_FORMAT(a.news_time,"%d.%m.%Y") as datum,      
          a.news_title as title,
          a.news_kat as kate,
          a.news_id as id,      
          b.name as username,
          b.id as userid         
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= '.$_SESSION['authright'].'
          ORDER BY a.news_time DESC
          LIMIT 0,3';
echo '<ul class="list-group list-group-boxen text-left">';        
$erg = db_query($abf);
if ( @db_num_rows($erg) == 0 ) {
	echo '<ul class="list-group list-group-boxen text-center"><div class="alert" role="alert">kein Newseintrag vorhanden<br><a class="text-info" href="admin.php?news"><strong>neue News schreiben</strong></a></div></ul>';
} 
while ($row = db_fetch_object($erg)) {

	echo '<a href="admin.php?news-edit-'.$row->id.'" class="list-group-item"><small>Kategorie: '.$row->kate.'</small><h5><strong><i class="fa fa-angle-double-right"></i> '.$row->title.'</strong></h5><small>Autor : '.$row->username.' | '.$row->datum.'</small></a>';

}
echo '</ul>';
?>