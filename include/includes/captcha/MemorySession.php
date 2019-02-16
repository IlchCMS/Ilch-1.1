<?php
/**
 * Captcha f�r www.ilch.de
 * @author T0P0LIN0
 * thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts
 */

/**
 * MemorySession
 * Speichern der Zahlen f�r das Captcha in der Session
 *
 * @author Mairu
 */
class MemorySession
{
    /**
     * Initialisiert Session
     * 
     * @param boolean $startSession session_start wird ausgef�hrt
     * @param string $sessionId SessionId die dabei verwendet wird
     */
    public function __construct($startSession = true, $sessionId = 'sid')
    {
        if ($startSession) {
            session_name($sessionId);
            session_start();
        }
        if (!isset($_SESSION['antispam_numbers'])) {
            $_SESSION['antispam_numbers'] = array();
        } else {
            $this->updateMemory();
        }
    }

    /**
     * Pr�ft, ob �bergebener CaptchaCode zur angegeben Id gefunden wird
     * 
     * @param string $captchaCode
     * @param string $captchaId
     * @return boolean
     */
    public function checkCode($captchaCode, $captchaId)
    {
        if (isset($_SESSION['antispam_numbers'][$captchaId])
            && $_SESSION['antispam_numbers'][$captchaId]['captcha_code'] == $captchaCode
        ) {
            unset($_SESSION['antispam_numbers'][$captchaId]);
            return true;
        }
        return false;
    }

    /**
     * L�scht alte Eintr�ge aus dem Speicher
     */
    public function updateMemory()
    {
        $tooOld = time() - 60 * 30; //30 Minuten
        foreach ($_SESSION['antispam_numbers'] as $captchaId => $values) {
            if ($tooOld > $values['time']) {
                unset($_SESSION['antispam_numbers'][$captchaId]);
            }
        }
    }

    /**
     * Legt neuen Eintrag f�r einen Captchacode mit Id an im Speicher an
     * 
     * @param string $captchaCode
     * @param string $captchaId
     */
    public function saveCode($captchaCode, $captchaId)
    {
        $_SESSION['antispam_numbers'][$captchaId] = array(
                'captcha_code' => $captchaCode,
                'time' => time()
        );
    }
}
