<?php
namespace Ytnuk\Web\Application;

use Ytnuk;

abstract class Presenter
	extends Ytnuk\Application\Presenter
{

	/**
	 * @var Ytnuk\Translation\Locale\Entity
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

	public function inject(Ytnuk\Web\Control\Factory $control)
	{
		$this->control = $control;
	}

	//TODO: should not be used for accesing menu, create directly menu control using multiplier and access directly using an identifier
	protected function createComponentYtnukWebControl() : Ytnuk\Web\Control
	{
		return $this->control->create($this->web);
	}
}
