<?php

namespace JollyGene\Facades;

use Illuminate\Support\Facades\Facade;

class LINEBotMessage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'linebot_message_creator';
    }
}