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
 * Gibt kompletten Pfad (mit / als Directory Separator) zurück und entfernt ggf. ./ am Anfang
 * 
 * @param string $dir
 * @param string $entry
 * @return string
 */
function getCompletePath($dir, $entry)
{
    $completePath = $dir . DIRECTORY_SEPARATOR . $entry;
    if (strpos($completePath, '.' . DIRECTORY_SEPARATOR) === 0) {
        $completePath = substr($completePath, 2);
    }
    if (DIRECTORY_SEPARATOR !== '/') {
        $completePath = str_replace(DIRECTORY_SEPARATOR, '/', $completePath);
    }
    return $completePath;
}

$ignoredFiles = array(
    'include/backup/bigdump.php',
    'include/includes/class/xajax.php4.inc.php',
    'include/includes/class/xajax.php5.inc.php',
    'include/admin/compatibility.php',
    'update.php'
);

$ignoredMatches = array(
    'include/includes/func/allg.php' => array('get_html_translation_table(HTML_ENTITIES, ILCH_ENTITIES_FLAGS)')
);

$phpFiles = array_diff(getPhpFiles(), $ignoredFiles);

$tpl = new tpl('compatibility', 1);

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->addheader($tpl->get(0));
$design->header();

$tpl->set('notneeded', version_compare(PHP_VERSION, '5.4', '<') ? 1 : 0);
$tpl->out(1);
$i = 1;

$filesWithChanges = 0;

foreach ($phpFiles as $phpFile) {
    $fileContents = file_get_contents($phpFile);
    $fileContents = htmlentities($fileContents, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);
    $matches = array();
    $changes = 0;
    if (preg_match_all('~(htmlentities|htmlspecialchars|html_entity_decode|get_html_translation_table)\s*\(.*\)~', $fileContents, $matches) > 0) {
        $toHighlightArray = array();
        foreach ($matches[0] as $match) {
            if (preg_match('~ILCH_ENTITIES_FLAGS\s*,\s*ILCH_CHARSET~', $match) === 0) {
                $toHighlightArray[] = $match;
            }
        }
        $toHighlightArray = array_unique($toHighlightArray);
        if (isset($ignoredMatches[$phpFile])) {
            foreach ($ignoredMatches[$phpFile] as $ignoreMatch) {
                $found = array_search($ignoreMatch, $toHighlightArray);
                if ($found !== false) {
                    unset($toHighlightArray[$found]);
                }
            }
        }
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
            $tpl->out(2);
            $filesWithChanges++;
        }
    }
}
if ($filesWithChanges === 0) {
    $tpl->out(3);
}
$tpl->out(4);

$design->footer();