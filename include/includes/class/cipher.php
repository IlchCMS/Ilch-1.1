<?php
//Cipher Class from http://www.php.net/manual/en/function.mcrypt-encrypt.php
if (function_exists('mcrypt_encrypt')) {
    class cipher {
        var $securekey, $iv;
        function cipher($textkey) {
            $this->securekey = hash('sha256',$textkey,TRUE);
            $this->iv = mcrypt_create_iv(32);
        }
        function encrypt($input) {
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
        }
        function decrypt($input) {
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
        }
    }
} else {
    class cipher {
        var $securekey, $iv;
        function cipher($textkey) {
        }
        function encrypt($input) {
            return $input;
        }
        function decrypt($input) {
            return $input;
        }
    }
}
?>