<?php


namespace Spencer;


class BootLoader
{
    const REGISTER_METHOD = 'register';

    const BOOTSTRAP_METHOD = 'bootstrap';

    protected $registerList = [
        Env::class,
        Config::class,
        Route::class,
        Request::class,
        Response::class,
        Validator::class,
        Pipeline::class
    ];

    protected $bootstrapperList = [
        Env::class,
        Config::class,
        Route::class
    ];

    protected $custom = [];

    public static function bootstrap()
    {
        $bootLoader = new static();

        $bootLoader->defineConstant()->registerComponents()->bootstrapComponents();
    }

    public function defineConstant()
    {
        define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../');
        define('APP_PATH', ROOT_PATH . 'app/');
        define('CONFIG_PATH', ROOT_PATH . 'config/');

        return $this;
    }

    /**
     * 向容器注册组件
     */
    public function registerComponents()
    {
        $this->checkCustomConfig();
        $this->execute($this->mergeList($this->registerList, self::REGISTER_METHOD), self::REGISTER_METHOD);
        return $this;
    }

    /**
     * 初始化组件
     */
    public function bootstrapComponents()
    {
        $this->checkCustomConfig();
        $this->execute($this->mergeList($this->bootstrapperList, self::BOOTSTRAP_METHOD), self::BOOTSTRAP_METHOD,false);
        return $this;
    }

    public function checkCustomConfig()
    {
        if(empty($this->custom)){
            $this->custom = require ROOT_PATH . 'config/boot.php';
        }
    }

    public function mergeList($internalList, $configKey)
    {
        return isset($this->custom[$configKey]) ? array_merge($internalList, $this->custom[$configKey]) : $internalList;
    }

    public function execute($list, $type, $static = true)
    {
        foreach ($list as $item) {
            if (is_callable($item)) { //传入闭包
                $item();
                continue;
            }
            if (is_array($item)) { //传入类名及方法名
                list($class, $method) = $item;
            } else { //仅传入类名
                $class = $item;
                $method = $type;
            }

            if (class_exists($class) && method_exists($class, $method)) {
                if(!$static){
                    $class = Application::instance()->make($class);
                }
                call_user_func_array([$class, $method], []);
            }
        }
    }
}