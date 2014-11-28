<?php

namespace Kutny\Web;

use Kutny;

/**
 * Class Presenter
 *
 * @package Kutny\Web
 */
abstract class Presenter extends Kutny\Application\Presenter
{

	/**
	 * @var string
	 * @persistent
	 */
	public $web;

}
