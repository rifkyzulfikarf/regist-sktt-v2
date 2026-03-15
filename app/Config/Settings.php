<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Settings extends BaseConfig
{
    /*
    * Recaptha Google
    *
    * See link below for setup and get key
    * http://www.google.com/recaptcha/admin
    */
    public $recaptcha_site_key = '6LcGuZ4pAAAAABRutSzxXk5rdvNQ0OLLwTWcO7cF';
    public $recaptcha_secret_key = '6LcGuZ4pAAAAAAOeNmdy7BbUP12Pib2sE6r69h4c';
    public $recaptcha_lang = 'id';
}
