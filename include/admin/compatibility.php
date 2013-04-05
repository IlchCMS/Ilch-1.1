<?php
#   Copyright by: Mairu
#   Support: www.ilch.de
defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

/**
 * Alle PHP Dateien in dem Verzeichnis (und allen Unterverzeichnissen) suchen
 * 
 * @param string $dir
 * @return array
 */
function getPhpFiles($dir = '.')
{
    $array = array();
    $dirContent = scandir($dir);
    foreach ($dirContent as $entry) {
        $completePath = getCompletePath($dir, $entry);
        if (is_dir($completePath) && strpos($entry, '.') !== 0) {
            $array = array_merge($array, getPhpFiles($completePath));
        } elseif (substr($completePath, -4) === '.php') {
            $array[] = $completePath;
        }
    }
    return $array;
}

/**
 * Gibt kompletten Pfad zurück und entfernt ggf. ./ am Anfang
 * 
 * @param string $dir
 * @param string $entry
 * @return string
 */
function getCompletePath($dir, $entry)
{
    $completePath = $dir . DIRECTORY_SEPARATOR . $entry;
    if (strpos($completePath, './') === 0) {
        $completePath = substr($completePath, 2);
    }
    return $completePath;
}

$ignoredFiles = array(
    'include/backup/bigdump.php',
    'include/includes/class/xajax.php4.inc.php',
    'include/includes/class/xajax.php5.inc.php'
);

$phpFiles = array_diff(getPhpFiles(), $ignoredFiles);

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

$tpl = new tpl('compatibility', 1);
$tpl->out(0);
$i = 1;

foreach ($phpFiles as $phpFile) {
    $fileContents = file_get_contents($phpFile);
    $fileContents = htmlentities($fileContents, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);
    $matches = array();
    $changes = 0;
    if (preg_match_all('~(htmlentities|htmlspecialchars)\s*\(.*\)~', $fileContents, $matches) > 0) {
        $toHighlightArray = array();
        foreach ($matches[0] as $match) {
            if (preg_match('~ILCH_ENTITIES_FLAGS\s*,\s*ILCH_CHARSET~', $match) === 0) {
                $toHighlightArray[] = $match;
            }
        }
        $toHighlightArray = array_unique($toHighlightArray);
        if (count($toHighlightArray)) {
            foreach ($toHighlightArray as $toHighlight) {
                $fileContents = str_replace($toHighlight, '<span style="background: red; font-weight:bold;">'
                    . $toHighlight . '</span>', $fileContents, $replaces);
                $changes += $replaces;
            }
            $class = $class === 'Cmite' ? 'Cmite' : 'Cnorm';
            $tpl->set_ar(array(
                'class' => $class,
                'phpFile' => $phpFile,
                'changes' => $changes,
                'code' => $fileContents,
                'id' => $i++
            ));
            $tpl->out(1);
        }
        
    }
}
$tpl->out(2);

$design->footer();