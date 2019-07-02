<?php


namespace Spencer;


use Spencer\Traits\SingletonBindable;

class Env
{
    use SingletonBindable;

    protected $env = [];

    const ALIAS = 'env';

    public function bootstrap()
    {
        if (is_file(ROOT_PATH . '.env')) {
            $this->env = parse_ini_file(ROOT_PATH . '.env', true);
        }
    }

    public function get($key)
    {
        return isset($this->env[$key]) ? $this->env[$key] : null;
    }
}