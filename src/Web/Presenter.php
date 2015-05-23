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

	/**
	 * @var string
	 * @persistent
	 */
	public $locale;

	/**
	 * @var Entity
	 */
	private $entity;

	/**
	 * @var Control\Factory
	 */
	private $control;

	/**
	 * @var Repository
	 */
	private $repository;

	/**
	 * @param Control\Factory $control
	 * @param Repository $repository
	 */
	public function inject(Control\Factory $control, Repository $repository)
	{
		$this->control = $control;
		$this->repository = $repository;
	}

	/**
	 * @inheritdoc
	 */
	public function redrawControl($snippet = NULL, $redraw = TRUE)
	{
		parent::redrawControl($snippet, $redraw);
		$this[Control::class]->redrawControl($snippet, $redraw);
	}

	protected function startup()
	{
		parent::startup();
		$this->entity = $this->repository->get($this->web);
		if ( ! $this->entity) {
			$this->error();
		}
	}

	/**
	 * @return Control
	 */
	protected function createComponentYtnukWebControl()
	{
		return $this->control->create($this->entity);
	}
}
