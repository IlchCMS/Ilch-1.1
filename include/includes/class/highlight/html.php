<?php
#   Copyright by Thomas Bowe [Funjoy]
#	Modul nur f�r die bbcode.php Klasse. 
#   Support webmaster@phpline.de
#   link www.k4d-clan.com

//> Html farblich darstellen.
	function highlight_html($string,$CodeColor) {
		//> HTML Tags definieren und farben zuweisen.
		$parse_tags = array("script" => "#990000",
						    "a" => "#009900",
						    "*img" => "#990099",
						    "style" => "#990099",
						    "form" => "#ff9900",
						    "textarea" => "#ff9900",
						    "select" => "#ff9900",
						    "option" => "#ff9900",
						    "input" => "#ff9900",
						    "table" => "#009999",
						    "th" => "#009999",
						    "td" => "#009999",
						    "tr" => "#009999",
						    "tbody" => "#009999",
					        "tfoot" => "#009999");
		
		//> Restliche Htmlelemente f�rben				
		$parse_elements = array(//> F�r Format: &xxxx;
								"#000000",
								//> F�r Format: 'xxxx' und "xxxx"
								"#0000ff",
								//> Tags die keine definition haben.
								"#000099",
								//> Farbe f�r Kommentare.
								"#999999",
								//> Farbe in style tags f�r den Anf�hrungszeichen
								"#006600",
								//> Farbe in script Tags f�r die Anf�hrungszeichen
								"#000000",
								//> Default farbe f�r nicht definierten Code.
								$CodeColor);
		
		//> Tags farblich hervorheben!
		while(list($tag,$color) = each($parse_tags)) {
			if(substr($tag,0,1) == "*" && $tag != "#") {
				//> Offene Tags f�rben(f�r die Tags die keine end- Tags besitzen!)
				$pattern[] = "%&lt;".substr($tag,1)."(.+)&gt;%Uis";
				$replace[] = "<font color=\"".$color."\">&lt;".substr($tag,1)."$1&gt;</font>";
			} else {
				//> Offene Tags f�rben!
				$pattern[] = "%&lt;".$tag."(.+)&gt;%Uis";
				$replace[] = "<font color=\"".$color."\">&lt;".$tag."$1&gt;</font>";
					
				//> Geschlossene Tags f�rben!
				$pattern[] = "%&lt;/".$tag."&gt;%Uis";
				$replace[] = "<font color=\"".$color."\">&lt;/".$tag."&gt;</font>";
			}
		}
		
		//> Formatierung farblich hervorheben Beispiel: &xxxx;
		$pattern[] = "%&amp;([a-zA-Z0-9#]+);%Uis";
		$replace[] = "<font color=\"".$parse_elements[0]."\"><b><i>&amp;$1;</i></b></font>";
		
		//> Formatierung farblich hervorheben Beispiel: 'xxxx'
		$pattern[] = "%'(.*)'%esiU";
		$replace[] = "_html_quotes('\$1','\'','".$parse_elements[1]."')";
		
		//> Formatierung farblich hervorheben Beispiel: "xxxx"
		$pattern[] = "%&quot;(.*)&quot;%esiU";
		$replace[] = "_html_quotes('\$1','&quot;','".$parse_elements[1]."')";
		
		//> Kommentar farblich hervorheben Beispiel: <!-- xxxx -->;
		$pattern[] = "%&lt;!--(.*)--&gt;%esiU";
		$replace[] = "_html_comments('\$1','".$parse_elements[3]."')";
		
		//> Ausf�hrungszeichen in style- Tags farblich hervorheben.
		$pattern[] = "%&lt;style(.*)&gt;%esiU";
		$replace[] = "_html_styletag('\$1','".$parse_elements[4]."')";
		
		//> Ausf�hrungszeichen in script- Tags farblich hervorheben.
		$pattern[] = "%&lt;script(.*)&gt;%esiU";
		$replace[] = "_html_scripttag('\$1','".$parse_elements[5]."')";
		
		//> CSS Code inerhalb eines Tags entsprechend hervorheben.
		$pattern[] = "%style( =| = |= |=)(.+)</font>%esiU";
		$replace[] = "_html_style('\$2')";
		
		//> CSS Code farblich hervorheben. (ggf. an Modul weiterleiten.)
		if(function_exists("highlight_css")) {
			$pattern[] = "%(&lt;style)(.*)(&gt;</font>)(.*)(<font color=\"".$parse_tags['style']."\">&lt;\/style&gt;)%esiU";
			$replace[] = "highlight_css('\$4','\$1\$2\$3','\$5')";
		} else {
			$pattern[] = "%(&lt;style)(.*)(&gt;</font>)(.*)(<font color=\"".$parse_tags['style']."\">&lt;\/style&gt;)%esiU";
			$replace[] = "_html_defaultcode('\$4','\$1\$2\$3','\$5','".$parse_elements[6]."')";
		}
		
		//> JavaScript Code farblich hervorheben. (ggf. an Modul weiterleiten. Dazu bitte selber if bedingung basteln!!!)
		$pattern[] = "%(&lt;script)(.*)(&gt;</font>)(.*)(<font color=\"".$parse_tags['script']."\">&lt;\/script&gt;)%esiU";
		$replace[] = "_html_defaultcode('\$4','\$1\$2\$3','\$5','".$parse_elements[6]."')";
		
		$string = str_replace("&lt;","<font color=\"".$parse_elements[2]."\">&lt;",$string);
		$string = str_replace("&gt;","&gt;</font>",$string);
		$string = preg_replace($pattern,$replace,$string);
		return stripslashes($string);
	}
	
//> Sub funktion f�r die Funktion "xxxx" und 'xxxx'
	function _html_quotes($string,$type,$color) {
		//> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU",
						 "%<(i|b)>%siU",
						 "%</(i|b)>%siU");
		
		$string = preg_replace($pattern,"",$string);
		
		return "<font color=\"".$color."\">".$type.$string.$type."</font>";
	}
	
//> Sub Funktion um CSS Code inerhalb eines Tags entsprechend hervorheben.
	function _html_style($string) {
		//> Farb- Tags (<font>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU");
		
		$string = preg_replace($pattern,"",$string);
		unset($pattern);
		
		$pattern[] = "%(&quot;|')%siU";
		$replace[] = "<font color=\"#0000ff\">$1</font>";
		
		$pattern[] = "%:(.+);%siU";
		$replace[] = "<font color=\"#FF00FF\">:</font><font color=\"#0000ff\">$1</font><font color=\"#FF00FF\">;</font>";
		
		$string = preg_replace($pattern,$replace,$string);

		return "style=<font color=\"#000099\">".$string."</font>";
		
	}

//> Sub Funktion um in <style...> die Anf�hrungszeichen anders farblich hervorzuheben.
	function _html_styletag($string,$color) {
		//> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU");
						 
		$string = preg_replace($pattern,"",$string);
		unset($pattern);
		
		$pattern[] = "%&quot;(.+)&quot;%siU";
		$replace[] = "<font color=\"".$color."\">&quot;$1&quot;</font>";
		
		$pattern[] = "%'(.+)'%siU";
		$replace[] = "<font color=\"".$color."\">'$1'</font>";
		
		$string = preg_replace($pattern,$replace,$string);
		
		return "&lt;style".$string."&gt;";
	}
	
//> Sub Funktion um in <script...> die Anf�hrungszeichen anders farblich hervorzuheben.
	function _html_scripttag($string,$color) {
		//> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU");
						 
		$string = preg_replace($pattern,"",$string);
		unset($pattern);
		
		$pattern[] = "%&quot;(.+)&quot;%siU";
		$replace[] = "<font color=\"".$color."\">&quot;$1&quot;</font>";
		
		$pattern[] = "%'(.+)'%siU";
		$replace[] = "<font color=\"".$color."\">'$1'</font>";
		
		$string = preg_replace($pattern,$replace,$string);
		
		return "&lt;script".$string."&gt;";
	}
	
//> Sub Funktion um Kommentare Farblich hervorzuheben.
	function _html_comments($string,$color) {
		//> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU",
						 "%<(i|b)>%siU",
						 "%</(i|b)>%siU");
						 
		$string = preg_replace($pattern,"",$string);
		
		return "<font color=\"".$color."\">&lt;!--".$string."--&gt;</font>";
	}

//> Sub Funktion um Farben zu l�schen und text in anderer Farbe darzustellen.
	function _html_defaultcode($string,$stag,$etag,$color) {
		//> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) L�schen.
		$pattern = array("%<font(.*)>%siU",
						 "%</font>%siU",
						 "%<(i|b)>%siU",
						 "%</(i|b)>%siU");
						 
		$string = preg_replace($pattern,"",$string);
		
		return $stag."<font color=\"".$color."\">".$string."</font>".$etag;
	}
?>