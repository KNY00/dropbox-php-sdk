<?php
namespace Kunnu\Dropbox\Security;

if (! \function_exists( __NAMESPACE__ . 'function_exists')) {
    /**
     * function that replaces default one for the test
     *
     * @param string $function function's name
     *
     * @param boolean $deploy defines whether mock should be applied
     *
     * @return bool
     */
    function function_exists($function, $deploy = true)
    {
        global $testClass;

        $class = new $testClass();

        if($deploy === false){
            return \function_exists($function);
        }

        if ($function === 'openssl_random_pseudo_bytes') {
            return $class::$opensslExists;
        }

        if ($function === 'mcrypt_create_iv') {
            return $class::$mcryptExists;
        }

        return \function_exists($function);
    }
}
