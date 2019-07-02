<?php

namespace Spencer;


class Application
{
    protected $instances = [];

    protected $bindings = [];

    protected $alias = [];

    /** @var self| */
    protected static $instance;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static function instance()
    {
        if (!static::$instance instanceof Application) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 注册绑定
     * @param string $name
     * @param null|\Closure|string $creation
     * @param boolean $single
     */
    public function bind($name, $creation = null, $single = false)
    {
        if (!$creation) {
            $creation = $name;
        }

        if($name != $creation && is_string($creation)){
            $this->alias[$creation] = $name;
        }

        if (!$creation instanceof \Closure) {
            $creation = function ($parameters) use ($creation) {
                return $this->build($creation, $parameters);
            };
        }

        $this->bindings[$name] = [
            'creation' => $creation,
            'single' => $single
        ];
    }

    /**
     * 单例绑定快捷方式
     * @param $name
     * @param null $creation
     */
    public function singleton($name, $creation = null)
    {
        $this->bind($name, $creation, true);
    }

    /**
     * 解析对象
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    public function make($name, $parameters = [])
    {
        // 已创建的单例，直接返回
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        // 未绑定过，直接实例化
        if (!isset($this->bindings[$name]) && !isset($this->alias[$name])) {
            return $this->build($name, $parameters);
        }

        if(isset($this->alias[$name])){
            $name = $this->alias[$name];
        }

        $bind = $this->bindings[$name];

        // 创建对象
        $object = $this->build($bind['creation'], $parameters);

        // 保存单例
        if ($bind['single']) {
            $this->instances[$name] = $object;
        }

        return $object;
    }

    public function build($creation, $parameters)
    {
        // 已绑定闭包方法，执行返回
        if ($creation instanceof \Closure) {
            return $creation($parameters);
        }

        // 使用的类名，使用反射方法
        $reflection = new \ReflectionClass($creation);
        // 获取构造函数
        $constructor = $reflection->getConstructor();
        // 没构造函数直接new
        if (is_null($constructor)) {
            return new $creation;
        }

        // 构造函数非公有，无法创建实例
        if (!$constructor->isPublic()) {
            throw new \Exception('cannot create object by non-public constructor');
        }

        $dependencies = $constructor->getParameters();

        // 将依赖与传入的参数对应
        foreach ($parameters as $name => $parameter) {
            if (is_numeric($name)) {
                unset($parameters[$name]);
                $parameters[$dependencies[$name]->name] = $parameter;
            }
        }

        // 解决依赖
        $args = [];
        foreach ($dependencies as $dependency) {
            $class = $dependency->getClass();
            if (isset($parameters[$dependency->name])) {
                $args[] = $parameters[$dependency->name];
            } elseif (is_null($class)) { //没有传入参数值，需要获取默认值
                try {
                    $args[] = $dependency->getDefaultValue();
                } catch (\Exception $e) {
                }
            } else {
                $args[] = $this->make($class->name);
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}