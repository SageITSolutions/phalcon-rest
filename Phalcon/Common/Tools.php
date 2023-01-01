<?php
namespace Phalcon\Common;

use \Phalcon\Text;

class Tools extends \Phalcon\Di\Injectable{
    /**
     * Obtain Controller portion of Route
     *
     * @param string $route
     * @return string
     */
    public function Controller(string $route){
        return $this->__splitRoute($route, 0) ?? 'index';
    }

    /**
     * Obtain Action portion of Route
     *
     * @param string $route
     * @return void
     */
    public function Action(string $route){
        return $this->__splitRoute($route, 1) ?? 'index';
    }

    /**
     * Obtain Params portion of Route
     *
     * @param string $route
     * @return void
     */
    public function Params(string $route){
        return $this->__splitRoute($route, 2) ?: null;
    }

    /**
     * Run Pattern against route
     *
     * @param string $route
     * @param int $index
     * @return string
     */
    protected function __splitRoute(string $route, int $index = 0){
        $split = preg_split('/([\/]+|[\?]+)/i',$route) ?: [];
        return @$split[$index] ?: null;
    }

    /**
     * Verifies Site generated CSRF token
     *
     * @param boolean $redirect
     * @return void
     */
    public function csrf($redirect = false){
        $security = $this->security ?: new \Phalcon\Security();
        if(!$security->checkToken()) {
            if ($this->flash) {
                $this->flash->error('Invalid CSRF Token');
            }
            if ($redirect){
                $this->response->redirect($redirect);
            }
            return;
        }
    }

    /**
     * Performs one-way encryption of a user's password using PHP's bcrypt
     *
     * @param string $rawPassword the password to be encrypted
     * @return bool|string
     */
    public function encryptPassword($rawPassword) {
        $security = $this->security ?: new \Phalcon\Security();
        return $security->hash($rawPassword);
    }


    /**
     * Verify that password entered will match the hashed password
     *
     * @param string $rawPassword the user's raw password
     * @param string $dbHash the hashed password that was saved
     * @return bool
     */
    public function verifyPassword($rawPassword, $dbHash) {
        $security = $this->security ?: new \Phalcon\Security();
        return $security->checkHash($rawPassword, $dbHash);
    }

    /**
     * Function to generate a random password
     * @param int $length
     * @param bool|true $strict
     * @return string
     */
    public function generatePassword($length = 8, $strict = true){
        $password = $this->generatePassword($length - 1, false);
        $shuffledSymbols = str_shuffle("@#$%^&*!+-_~");

        return substr($password, 0, strlen($password) - 1) . $shuffledSymbols[0] . Text::random(Text::RANDOM_NUMERIC, 1);
    }
    /**
     * @param array $keys
     * @param array $array
     * @return bool
     */
    public static function arrayHasAllKeys(array $keys, array $array) {
        foreach ($keys as $aKey) {
            if (!array_key_exists($aKey, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $properties
     * @param $object
     * @return bool
     */
    public function objectHasAllProperties(array $properties, $object) {
        foreach ($properties as $aProperty) {
            if (!property_exists($object, $aProperty)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get current date and time
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return bool|string
     */
    public static function getDateTime(){
        return date('Y-m-d H:i:s');
    }

    /**
     * Get current date
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return bool|string
     */
    public static function getDate(){
        return date('Y-m-d');
    }

    public static function appendArray(&$array, $add){
        if(@!in_array($add,$array)){
            $array[] = $add;
        }
    }

    public static function default(&$original, $default = null){
        if (!isset($original)){
            $original = $default;
            return true;
        }
        return false;
    }
}
?>