<?php

namespace WebEdit\Web;

use WebEdit;

/**
 * Class Presenter
 *
 * @package WebEdit\Web
 */
abstract class Presenter extends WebEdit\Application\Presenter
{

	/**
	 * @var string
	 * @persistent
	 */
	public $web;

}
