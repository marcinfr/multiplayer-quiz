<?php

namespace App\Controllers;

class Index extends AbstractController
{
	public function execute()
	{
        $content = new \App\Block\Template('menu.phtml');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
	}
}
