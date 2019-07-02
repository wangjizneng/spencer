<?php


namespace Spencer;


use Spencer\Traits\Bindable;

class Pipeline
{
    use Bindable;

    protected $filters = [];

    const ALIAS = 'pipeline';

    protected $default_method = 'handle';

    public function addFilter($filters)
    {
        $this->filters = is_array($filters) ? $filters : func_get_args();
        return $this;
    }

    public function then(\Closure $then)
    {
        $this->filters[] = $then;
        return $this;
    }

    public function send($payload)
    {
        $init = array_pop($this->filters);
        $pipeline = function ($payload) use ($init){
            return $init($payload);
        };
        $filters = array_reverse($this->filters);
        foreach ($filters as $filter) {
            $pipeline = function ($payload) use ($filter, $pipeline){
                if(is_callable($filter)){
                    return $filter($payload, $pipeline);
                }
                if(!is_object($filter)){
                    $filter = Application::instance()->make($filter);
                }
                return $filter->{$this->default_method}($payload, $pipeline);
            };
        }
        $pipeline($payload);
    }
}