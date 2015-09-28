<?php
namespace Ytnuk\Web\Application;

use Nette;
use Ytnuk;

abstract class Presenter
	extends Ytnuk\Application\Presenter
{

	/**
	 * @var string
	 * @persistent
	 */
	public $locale;

	/**
	 * @var Ytnuk\Web\Entity
	 * @persistent
	 */
	public $web;

	/**
	 * @var Ytnuk\Web\Control\Factory
	 */
	private $control;

	public function injectWeb(Ytnuk\Web\Control\Factory $control)
	{
		$this->control = $control;
	}

	//TODO: should not be used for accesing menu, create directly menu control using multiplier and access directly using an identifier
	protected function createComponentWeb() : Ytnuk\Web\Control
	{
		return $this->control->create($this->web);
	}
}
