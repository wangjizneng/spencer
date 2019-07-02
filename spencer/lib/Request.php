<?php


namespace Spencer;

use Spencer\Traits\SingletonBindable;

class Request
{
    use SingletonBindable;

    protected $headers = [];

    protected $input = [];

    const ALIAS = 'request';

    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * 获取HTTP请求类型
     * @return mixed
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取原始post请求数据体
     * @return false|string
     */
    public function body()
    {
        return file_get_contents('php://input');
    }

    /**
     * 域名
     * @return mixed
     */
    public function domain()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * http/https
     * @return mixed
     */
    public function scheme()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * 请求路由
     * @return bool|string
     */
    public function route()
    {
        $uri = $_SERVER['REQUEST_URI'];
        return substr($uri,0,strpos($uri,'?'));
    }

    public function ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 获取header数组
     * @return array|string
     */
    public function header($name=null)
    {
        if(empty($this->headers)){
            foreach ($_SERVER as $key => $val) {
                if (strpos($key,'HTTP_')===0){
                    $this->headers[str_replace('_','-', substr($key,5))] = $val;
                }
            }
        }
        if(empty($name)){
            return $this->headers;
        }
        $key = strtoupper($name);
        return isset($this->headers[$key]) ? $this->headers[$key] : '';
    }

    /**
     * 获取请求数据
     * @return array
     */
    public function input()
    {
        if(empty($this->input)){
            $this->input = $_REQUEST;
            if($this->header('Content-Type')===self::CONTENT_TYPE_JSON){
                $json = file_get_contents('php://input');
                if($decode = json_decode($json, true)){
                    $this->input = array_merge($this->input, $decode);
                }
            }
        }
        return $this->input;
    }
}