<?php
namespace Ytnuk\Web;

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
	 * @var Entity
	 * @persistent
	 */
	public $web;

	/**
	 * @var Control\Factory
	 */
	private $control;

	public function inject(Control\Factory $control)
	{
		$this->control = $control;
	}

	protected function createComponentYtnukWebControl() : Control
	{
		return $this->control->create($this->web);
	}
}
