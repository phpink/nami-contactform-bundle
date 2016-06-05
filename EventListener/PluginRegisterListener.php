<?php

namespace PhpInk\Nami\ContactFormBundle\EventListener;

use PhpInk\Nami\CoreBundle\Event\PluginRegisterEvent;

class PluginRegisterListener
{
    public function onPluginRegister(PluginRegisterEvent $event)
    {
        $event->registerPlugin('ContactForm');
    }

}
