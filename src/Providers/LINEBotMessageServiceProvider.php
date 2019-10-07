<?php

namespace JollyGene\Providers;

use Illuminate\Support\ServiceProvider;

class LINEBotMessageServiceProvider extends ServiceProvider
{
    public function register()
    {
         $this->app->bind('linebot_message_creator', \JollyGene\Creators\LINEBotMessageCreator::class);
    }

    public function boot()
    {
        //
    }
}