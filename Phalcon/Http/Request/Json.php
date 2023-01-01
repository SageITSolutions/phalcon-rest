<?php
namespace Phalcon\Http\Request;

class Json extends \Phalcon\Http\Request
{

    public function getBearerToken()
    {
        /**
         * @todo: update once corrected by Phalcon
         */
        $headers = getallheaders();
        if (!$headers['Authorization']) {
            throw new \Phalcon\Exception\AuthenticationException();
        }
        ;
        $authorization = $headers['Authorization'];
        if (!empty($authorization)) {
            $matches = null;
            if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
                return $matches[1];
            }
        }
        return $authorization;
    }

    /**
     * Override Phalcon\Http\Request to translate JSON to $_REQUEST
     *
     * @param string|null $name
     * @param mixed $filters
     * @param mixed $defaultValue
     * @param boolean $notAllowEmpty
     * @param boolean $noRecursive
     * @return void
     */
    public function get(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
    {
        $request = $this->getJsonRawBody(true);
        if (!$request)
            return false;
        return $this->getHelper(
            $request,
            $name,
            $filters,
            $defaultValue,
            $notAllowEmpty,
            $noRecursive
        );
    }

    /**
     * Override Phalcon\Http\Request to translate JSON to $_POST
     *
     * @param string|null $name
     * @param mixed $filters
     * @param mixed $defaultValue
     * @param boolean $notAllowEmpty
     * @param boolean $noRecursive
     * @return void
     */
    public function getPost(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
    {
        $request = $this->getJsonRawBody(true);
        if (!$request)
            return false;
        return $this->getHelper(
            $request,
            $name,
            $filters,
            $defaultValue,
            $notAllowEmpty,
            $noRecursive
        );
    }

    /**
     * Override Phalcon\Http\Request to check JSON object for attribute
     *
     * @param string $name
     * @return boolean
     */
    public function hasPost(string $name): bool
    {
        $request = $this->getJsonRawBody(true);
        return isset($request[$name]);
    }
}
?>