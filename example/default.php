<?php

class MyAction
{
    use \Extasy\Usecase\Usecase;

    protected function action()
    {
        return 'some complex response';
    }
}

$action = new MyAction();
$action->before(function ($usecase) {
    printf("Before action we log something\r\n");
});
$action->instead(function ($usecase) {
    printf("Executing instead of default action\r\n");

    return 'some cached response';
});
$action->around(function ($usecase, $response) {
    return json_encode($response);
});
$action->after(function ($usecase) {
    printf('After action we can log transactions, cleanup resources and etc...' . "\r\n");
});

var_dump($action->execute());