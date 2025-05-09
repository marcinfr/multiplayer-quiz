<?php

namespace App\Controllers;

class Index extends AbstractController
{
	public function execute()
	{
        $template = new \App\Block\Template('menu.phtml');
        $template->render();
	}
}
