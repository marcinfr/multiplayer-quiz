<?php

namespace App\Controllers;

class Game extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $template = new \App\Block\Template('game.phtml');
        $template->render();
    }
}