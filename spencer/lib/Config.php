<?php


namespace Spencer;


use Spencer\Traits\SingletonBindable;

class Config
{
    use SingletonBindable;

    protected $config = [];

    const ALIAS = 'config';

    const DEFAULT_CONFIG_PREFIX = 'app'; //默认获取config/app.php内定义的配置

    public function bootstrap()
    {
        $dir = opendir(CONFIG_PATH);
        while($file = readdir($dir)){
            if($file != '.' && $file != '..'){
                $fullName = CONFIG_PATH . $file;
                $prefix = explode('.',$file)[0];
                $this->config[$prefix] = include $fullName;
            }
        }
    }

    /**
     * 获取配置值
     * @param $key
     * @return array|mixed
     */
    public function get($key)
    {
        $construct = $this->getKeyConstruct($key);
        $temp = &$this->config;
        foreach ($construct as $item) {
            $temp = &$temp[$item];
        }
        return $val = $temp;
    }

    public function set($key, $val)
    {
        $construct = $this->getKeyConstruct($key);
        $temp = &$this->config;
        foreach ($construct as $item) {
            $temp = &$temp[$item];
        }
        $temp = $val;
    }

    public function getKeyConstruct($key)
    {
        $construct = explode('.', $key);
        if(count($construct) == 1){
            array_unshift($construct, self::DEFAULT_CONFIG_PREFIX);
        }
        return $construct;
    }
}