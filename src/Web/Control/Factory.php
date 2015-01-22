<?php

namespace Ytnuk\Web\Control;

use Ytnuk;

/**
 * Interface Factory
 *
 * @package Ytnuk\Web
 */
interface Factory
{

	/**
	 * @param Ytnuk\Web\Entity $web
	 *
	 * @return Ytnuk\Web\Control
	 */
	public function create($web);
}
