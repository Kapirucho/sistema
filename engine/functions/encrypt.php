<?php

if (!isset($config['fullpath'])) {
    //die;
}

class Encrypt extends Config {

    var $ref;
    var $llave;
    var $IV;
    var $ks;
    var $IV_size;
    var $cl;

    function __construct($data) {
        
        parent::start($data);
        
        $super_secret_key = md5($this->config['encrypt']['token']);

        $this->ref = mcrypt_module_open('rijndael-256', '', 'ofb', '');
        $this->IV_size = mcrypt_enc_get_iv_size($this->ref);
        $this->IV = mcrypt_create_iv($this->IV_size, MCRYPT_RAND);
        $this->ks = mcrypt_enc_get_key_size($this->ref);
        $this->llave = substr($super_secret_key, 0, $this->ks);
    }

    function __destruct() {
        mcrypt_module_close($this->ref);
    }

    function Encrypt($data) {
        mcrypt_generic_init($this->ref, $this->llave, $this->IV);
        $result = mcrypt_generic($this->ref, $data);
        $out = $this->IV . $result;
        mcrypt_generic_deinit($this->ref);
        return $out;
    }

    function Decrypt($data) {
        $iv = substr($data, 0, $this->IV_size);
        $string = substr($data, $this->IV_size);
        
        mcrypt_generic_init($this->ref, $this->llave, $iv);
        $result = mdecrypt_generic($this->ref, $string);
        mcrypt_generic_deinit($this->ref);
        return $result;
    }

}