<?php
/**
 * Corezoid Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        Corezoid
 * @package         Corezoid/Corezoid
 * @version         1.0
 * @author          corezoid.com
 * @copyright       Copyright (c) 2013 corezoid.com
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * EXTENSION INFORMATION
 *
 * Corezoid API       http://www.corezoid.com/how_to_use/api/en/
 *
 */

/**
 * Corezoid Class
 *
 * @author      Corezoid <support@mcorezoid.com>
 */
class Corezoid
{
    /**
     * host Corezoid
     */
    private $_host = 'https://www.corezoid.com';

    /**
     * Version API
     */
    private $_version = '1';

    /**
     * Format API
     */
    private $_format = 'json';

    /**
     * User API
     */
    private $_api_login;

    /**
     * API secret key
     */
    private $_api_secret;

    /**
     * Array tasks
     */
    private $_tasks = array();


    /**
     * Constructor.
     *
     * @param string $api_login
     * @param string $api_secret
     *
     * @throws InvalidArgumentException
     */
    public function __construct($api_login, $api_secret)
    {
        if (empty($api_login)) {
            throw new InvalidArgumentException('api_login is empty');
        }
        if (empty($api_secret)) {
            throw new InvalidArgumentException('api_secret is empty');
        }

        $this->_api_login = $api_login;
        $this->_api_secret = $api_secret;
    }


    /**
     * Add new task
     *
     * @param string $ref External id for the task
     * @param string $conv_id Corezoid process id
     * @param array $data Sending data
     *
     * @throws InvalidArgumentException
     */
    public function add_task($ref, $conv_id, $data = array())
    {
        if (empty($ref)) {
            throw new InvalidArgumentException('ref is empty');
        }
        if (empty($conv_id)) {
            throw new InvalidArgumentException('conv_id is empty');
        }

        $this->_tasks[] = array(
            'ref' => $ref,
            'type' => 'create',
            'obj' => 'task',
            'conv_id' => $conv_id,
            'data' => $data
        );
    }


    /**
     * Send tasks to Corezoid
     *
     * @param bool $clear_tasks Clears current tasks
     * @return string
     */
    public function send_tasks($clear_tasks = true)
    {

        $content = json_encode(array('ops' => $this->_tasks));
        if ($clear_tasks) {
            $this->clear_tasks();
        }
        $time = time();

        $url = $this->make_url($time, $content);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }

    public function clear_tasks()
    {
        $this->_tasks = array();
    }


    /**
     * Check Signature
     *
     * @param string $sign
     * @param string $time
     * @param string $content
     *
     * @return string
     */
    public function check_sign($sign, $time, $content)
    {
        $make_sign = $this->make_sign($time, $content);
        return ($sign == $make_sign) ? true : false;
    }


    /**
     * Create URL to Corezoid
     *
     * @param string $time
     * @param string $content
     *
     * @return string
     */
    private function make_url($time, $content)
    {
        $sign = $this->make_sign($time, $content);

        return $this->_host . '/api/'
        . $this->_version . '/'
        . $this->_format . '/'
        . $this->_api_login . '/'
        . $time . '/'
        . $sign;
    }


    /**
     * Create Signature
     *
     * @param string $time
     * @param string $content
     *
     * @return string
     */
    private function make_sign($time, $content)
    {
        return $this->str2hex(sha1($time . $this->_api_secret . $content . $this->_api_secret, 1));
    }


    /**
     * String to HEX
     *
     * @param string $str
     *
     * @return string
     */
    private function str2hex($str)
    {
        $r = unpack('H*', $str);
        return array_shift($r);
    }
}
