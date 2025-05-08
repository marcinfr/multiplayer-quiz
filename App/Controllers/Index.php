<?php

namespace App\Controllers;

class Index extends AbstractController
{
	public function execute()
	{
		ob_start();
        include __DIR__ . '/../../view/templates/menu.phtml';
        echo ob_get_clean();
	}
}