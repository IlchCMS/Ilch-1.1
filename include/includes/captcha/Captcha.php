<?php
/**
 * Captcha f�r www.ilch.de
 * @author T0P0LIN0
 * thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts
 */
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MemorySession.php';

class Captcha
{

    var $memory;
    var $passphrase = null;
    var $width = 170;
    var $height = 60;
    var $fontsPath = "fonts";
    var $fontColor = array();
    var $font_type = 5;
    var $useRandomColors = true;
    var $bgColor = array();
    var $passphraselenght = 5;
    var $fontSize = 20;
    var $image = null;
    var $angle = 45;
    var $scratches = true;
    var $background_intensity = 50;
    var $addhorizontallines = true;
    var $image_font_width;
    var $image_font_height;
    var $scraches_amount = 25;
    var $minsize = 20;
    var $maxsize = 30;
    var $addagrid = true;
    var $character = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'Q', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'U', 'V', 'Y', 'W', '2', '3', '4', '5', '6', '7');

    public function __construct()
    {
        $this->memory = new MemorySession();
    }

    public function isValid($number, $captchaId)
    {
        return $this->memory->checkCode($number, $captchaId);
    }

    protected function checkTypeSupport()
    {
        $get_info = gd_info();
        $Version = preg_replace('%[^\d\.]+%', '', $get_info['GD Version']);
        $get_info['GD VERSION'] = $Version;
        if (!is_array($get_info)) {
            return false;
        } else {
            return $get_info;
        }
    }

    protected function openCaptchaImage()
    {
        // Grafik anlegen
        $gd_lib_version = $this->checkTypeSupport();
        if (( is_array($gd_lib_version) ) && ( $gd_lib_version['GD VERSION'] > "2.0.0" ))
            return ImageCreateTrueColor($this->width, $this->height);
        else
            return ImageCreate($this->width, $this->height);
    }

    public function setUseRandomColors($useRandomColors = false)
    {
        $this->fontColor['r'] = 0;
        $this->fontColor['g'] = 0;
        $this->fontColor['b'] = 0;

        $this->bgColor['r'] = 225;
        $this->bgColor['g'] = 225;
        $this->bgColor['b'] = 225;

        $this->useRandomColors = $useRandomColors;
    }

    function setFontColor($r, $g, $b)
    {
        $this->fontColor['r'] = $r;
        $this->fontColor['g'] = $g;
        $this->fontColor['b'] = $b;
    }

    function setBgColor($r, $g, $b)
    {
        $this->bgColor['r'] = $r;
        $this->bgColor['g'] = $g;
        $this->bgColor['b'] = $b;
    }

    function setCharacter($character)
    {
        $this->character = $character;
    }

    function setBackgroundIntensity($background_intensity = 50)
    {
        $this->background_intensity = $background_intensity;
    }

    function setAngle($angle = 45)
    {
        $this->angle = $angle;
    }

    function setImageWidth($width)
    {
        $this->width = $width;
    }

    function setImageHeight($height)
    {
        $this->height = $height;
    }

    function setFontsPath($fontsPath)
    {
        $this->fontsPath = $fontsPath;
    }

    function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    function setMinMaxSize($minsize = 20, $maxsize = 30)
    {
        $this->minsize = $minsize;
        $this->maxsize = $maxsize;
    }

    function setFontType($font_type = 5)
    {
        $this->font_type = $font_type;
    }

    function enableScratches($scratches = true)
    {
        $this->scratches = $scratches;
    }

    function setScratchesAmount($amount = 25)
    {
        $this->scraches_amount = $amount;
    }

    function setShowgrid($what = true)
    {
        $this->addagrid = $what;
    }

    function setShowColoredLines($what = true)
    {
        $this->addhorizontallines = $what;
    }

    function setPassPhraselenght($passphraselenght)
    {
        $this->passphraselenght = $passphraselenght;
    }

    function getRandomFont()
    {
        static $fonts = array();
        if (count($fonts) == 0) {
            $dh = opendir($this->fontsPath);
            while ($font = readdir($dh)) {
                if (( $font != "." ) && ( $font != ".." )) {
                    if (substr(strtolower($font), -3) == "ttf") {
                        $fonts[] = sprintf("%s/%s", $this->fontsPath, $font);
                    }
                }
            }
            closedir($dh);
        }
        return $fonts[rand(0, count($fonts) - 1)];
    }

    function inRgbTolerance($originalColors, $newColors)
    {
        foreach ($originalColors as $rgbIdx => $value) {
            if (abs($newColors[$rgbIdx] - $value) < 60) {
                return false;
            }
        }
        return true;
    }

    function createCaptchaBackground()
    {
        // Breite eines Zeichens
        $this->image_font_width = ImageFontWidth($this->font_type) + 2;
        // Hoehe eines Zeichens
        $this->image_font_height = ImageFontHeight($this->font_type) + 2;
        // Zufallswerte f�r hintergrundfarbe
        if ($this->useRandomColors) {
            $this->setBgColor(intval(rand(225, 255)), intval(rand(225, 255)), intval(rand(225, 255)));
        } else {
            $this->setBgColor(225, 225, 225);
        }
        // Hintergrund-Farbe stzen
        $captcha_background_color = ImageColorAllocate($this->image, $this->bgColor['r'], $this->bgColor['g'], $this->bgColor['b']);
        // Flaeche fuellen
        ImageFilledRectangle($this->image, 0, 0, $this->width, $this->height, $captcha_background_color);
        // Zufallsstrings durchloopen
        for ($x = 0; $x < $this->background_intensity; $x++) {
            // Zufallsstring-Farbe
            $random_string_color = ImageColorAllocate($this->image, intval(rand(164, 254)), intval(rand(164, 254)), intval(rand(164, 254)));
            // Zufalls-String generieren
            $random_string = chr(intval(rand(65, 122)));
            // X-Position
            $x_position = intval(rand(0, $this->width - $this->image_font_width * strlen($random_string)));
            // Y-Position
            $y_position = intval(rand(0, $this->height - $this->image_font_height));
            // Zufalls-String
            ImageString($this->image, $this->font_type, $x_position, $y_position, $random_string, $random_string_color);
        }
        if ($this->addagrid) {
            $this->addGrid();
        }
        if ($this->addhorizontallines) {
            $this->addHorizontalLines();
        }
    }

    function createScratches()
    {
        for ($i = 1; $i < $this->scraches_amount; $i++) {
            $randPixSpaceLeft = mt_rand(0, $this->width);
            $randPixSpaceTop = mt_rand(0, $this->height);
            $style = mt_rand(0, 2);
            if (0 == $style) {
                $txtColor = $this->getRandomColor();
                ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft + 10, $randPixSpaceTop + 7, $txtColor);
            } elseif (1 == $style) {
                $noiseColor = $this->getRandomColor();
                ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft - 3, $randPixSpaceTop + 7, $noiseColor);
            } else {
                $bgColor = $this->getRandomColor();
                ImageLine($this->image, $randPixSpaceLeft, $randPixSpaceTop, $randPixSpaceLeft - 5, $randPixSpaceTop - 5, $bgColor);
            }
        }
    }

    function displayImage($captchaId)
    {
        $this->image = $this->openCaptchaImage();
        $this->createCaptchaBackground();
        $numbers = '';
        $phraseLength = $this->passphraselenght;
        $widthPerChar = $this->width / $phraseLength;
        $heightPerChar = $this->height - 2; //2pix spacing...
        $color = imagecolorallocate($this->image, $this->fontColor['r'], $this->fontColor['g'], $this->fontColor['b']);
        for ($idx = 0; $idx < $phraseLength; $idx++) {
            $number = $this->character[rand(0, count($this->character) - 1)];
            $currentFont = $this->getRandomFont();
            $disangle = rand(-$this->angle, $this->angle);
            $charInfo = imageftbbox($this->fontSize, $disangle, $currentFont, $number);
            $charWidth = $charInfo[4] - $charInfo[6];
            if ($charWidth > $widthPerChar) {
                echo "Please increase image width or use a smaller fon/font size";
                exit(1);
            }
            $xMargin = ( $widthPerChar - $charWidth ) / 2;
            $x = ( $idx * $widthPerChar ) + $xMargin;
            $charHeight = $charInfo[1] - $charInfo[7];
            if ($charHeight > $heightPerChar) {
                echo "Please increase image height or use a smaller fon/font size";
                exit(1);
            }
            $baseline = ( $heightPerChar - $charHeight ) / 2;
            $y = $baseline + $charHeight;

            if ($this->useRandomColors) {
                do {
                    $r = rand(0, 255);
                    $g = rand(0, 255);
                    $b = rand(0, 255);
                } while (!$this->inRgbTolerance($this->bgColor, array(
                        "r" => $r,
                        "g" => $g,
                        "b" => $b
                )));
                $color = imagecolorallocate($this->image, $r, $g, $b);
            }


            $numbers .= $number;
            imagettftext($this->image, $this->fontSize, $disangle, $x, $y, $color, $currentFont, $number);
        }
        if ($this->scratches) {
            $this->createScratches($color);
        }
        header("Content-type: image/jpeg");
        imagejpeg($this->image);
        $this->memory->saveCode($numbers, $captchaId);
    }

    function addHorizontalLines()
    {
        $red = imagecolorallocatealpha($this->image, 255, 0, 0, 75);
        $green = imagecolorallocatealpha($this->image, 0, 255, 0, 75);
        $blue = imagecolorallocatealpha($this->image, 0, 0, 255, 75);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $red);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $green);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $blue);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $red);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $green);
        imageline($this->image, rand(1, $this->width), rand(1, $this->height), rand(101, $this->width), rand(26, $this->height), $blue);
    }

    function getRandomColor($min = 0, $max = 255)
    {
        return imagecolorallocate($this->image, mt_rand($min, $max), mt_rand($min, $max), mt_rand($min, $max));
    }

    function addGrid()
    {
        for ($i = 0; $i < $this->width; $i += (int) ( $this->minsize / 1.5 )) {
            $color = $this->getRandomColor(160, 224);
            @imageline($this->image, $i, 0, $i, $this->height, $color);
        }
        for ($i = 0; $i < $this->height; $i += (int) ( $this->minsize / 1.8 )) {
            $color = $this->getRandomColor(160, 224);
            @imageline($this->image, 0, $i, $this->width, $i, $color);
        }
        @imageline($this->image, $this->width, 0, $this->width, $this->height, $color);
        @imageline($this->image, 0, $this->height, $this->width, $this->height, $color);
    }

    function pickRandomBackground()
    {
        $bg_color = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $bg_color);
        for ($i = 0; $i < $this->height; $i++) {
            $c = rand(140, 170);
            $d = rand(0, 10);
            $e = rand(0, 10);
            $f = rand(0, 10);
            $line_color = imagecolorallocate($this->image, $c + $d, $c + $e, $c + $f);
            imagesetthickness($this->image, rand(1, 5));
            imageline($this->image, 0, $i + rand(-15, 15), $this->width, $i + rand(-15, 15), $line_color);
        }
    }
}
