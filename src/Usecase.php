<?php


namespace Extasy\Usecase;


trait Usecase
{
    protected $beforeAdvices = [];
    protected $afterAdvices = [];
    protected $insteadAdvices = [];
    protected $aroundAdvices = [];
    protected $isAllowedToRun = false;
    protected $callAction = true;

    public function before($callback)
    {
        $this->beforeAdvices[] = $callback;
    }

    public function after($callback)
    {
        $this->afterAdvices[] = $callback;
    }

    public function instead($callback)
    {
        $this->insteadAdvices[] = $callback;
    }

    public function around($callback)
    {
        $this->aroundAdvices[] = $callback;
    }

    public function stopAdvices()
    {
        $this->isAllowedToRun = false;
    }

    public function enableDefaultAction($flag)
    {
        $this->callAction = (bool)$flag;
    }

    protected function callAdvices($queue, $response = null)
    {
        $this->isAllowedToRun = true;
        foreach ($queue as $callable) {
            if (!$this->isAllowedToRun) {
                break;
            }
            $response = call_user_func($callable, $this, $response);
        }
        $this->isAllowedToRun = true;

        return $response;
    }

    public function execute()
    {
        $response = null;
        $this->callAction = empty($this->insteadAdvices);
        $this->callAdvices($this->beforeAdvices);

        $response = $this->callAdvices($this->insteadAdvices);
        if ($this->callAction) {
            $response = $this->action();
        }
        $response = $this->callAdvices($this->aroundAdvices, $response);

        $this->callAdvices($this->afterAdvices);

        return $response;
    }

    abstract protected function action();
}