<?php
#   Copyright by ilch.de
#   Support www.ilch.de

function db_count_query($query) {
    return db_result(db_query($query), 0);
}

function db_check_erg ($erg) {
    if (!$erg || db_num_rows($erg) == 0) {
        exit ('Es ist ein Fehler aufgetreten');
    }
}

function db_make_sites($page, $where, $limit, $link, $table, $anzahl = NULL)
{
    $hvmax = 4; // hinten und vorne links nach page
    if (empty ($MPL)) {
        $MPL = '';
    }
    if (is_null($anzahl)) {
        $resultID = db_query("SELECT COUNT(*) FROM prefix_" . $table . " " . $where);
        $total = db_result($resultID, 0);
    } else {
        $total = $anzahl;
    }
    if ($limit < $total) {
        $maxpage = $total / $limit;
        if (is_double($maxpage)) {
            $maxpage = ceil($maxpage);
        }
        $ibegin = $page - $hvmax;
        $iende = $page + $hvmax;

        $vgl1 = $iende + $ibegin;
        $vgl2 = ($hvmax * 2) + 1;
        if ($vgl1 <= $vgl2) {
            $iende = $vgl2;
        }
        $vgl3 = $maxpage - ($vgl2 - 1);
        if ($vgl3 < $ibegin) {
            $ibegin = $vgl3;
        }

        if ($ibegin < 1) {
            $ibegin = 1;
        }
        if ($iende > $maxpage) {
            $iende = $maxpage;
        }
        $vMPL = '';
        if ($ibegin > 1) {
            $vMPL = '<a href="' . $link . '-p1">&laquo;</a> ';
        }
        $MPL = $vMPL . '[ ';
        for ($i = $ibegin; $i <= $iende; $i++) {
            if ($i == $page) {
                $MPL .= $i;
            } else {
                $MPL .= '<a href="' . $link . '-p' . $i . '">' . $i . '</a>';
            }
            if ($i != $iende) {
                $MPL .= ' | ';
            }
        }
        $MPL .= ' ]';
        if ($iende < $maxpage) {
            $MPL .= ' <a href="' . $link . '-p' . $maxpage . '">&raquo;</a>';
        }
    }
    return $MPL;
}