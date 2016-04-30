<?php

namespace PhpInk\Nami\ContactFormBundle\EventListener;

use PhpInk\Nami\CoreBundle\Event\BlockRenderEvent;

class BlockRenderListener
{
    public function onBlockRender(BlockRenderEvent $event)
    {
        var_dump('foo');exit;
    }
}
