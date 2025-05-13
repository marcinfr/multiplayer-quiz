<?php

namespace App\Controllers\Game;

class Form extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        $player->name = $this->getRequest()->getParam('name');
        $player->view_type = $this->getRequest()->getParam('view_type');
        app(\App\Models\Player::class)->save($player);

        $content = new \App\Block\Template('game/form.phtml');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}