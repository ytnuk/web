<?php

namespace Ytnuk\Web;

use Ytnuk;

/**
 * Class Presenter
 *
 * @package Ytnuk\Web
 */
abstract class Presenter extends Ytnuk\Application\Presenter
{

	/**
	 * @var string
	 * @persistent
	 */
	public $web;

	protected function beforeRender()
	{
		parent::beforeRender();
		if ($this->isAjax()) {
			$this['menu']->redrawControl();
			$this->redrawControl();
		}
	}
}
