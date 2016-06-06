<?php
namespace Extasy\Usecase\tests;

use Extasy\Usecase\Usecase;

class SampleUsecase
{
    use Usecase;
    protected $action = null;

    public function setAction( $action ) {
        $this->action = $action;
    }
    protected function action() {
        if ( !empty( $this->action )) {
            return call_user_func( $this->action, $this );
        }

    }
}