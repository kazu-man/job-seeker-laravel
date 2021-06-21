<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class InterviewServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'InterviewService';
    }
}