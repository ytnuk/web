<?php
namespace Ytnuk\Web;

use Ytnuk;

final class Control
	extends Ytnuk\Orm\Control
{

	/**
	 * @var Entity
	 */
	private $web;

	/**
	 * @var Ytnuk\Web\Repository
	 */
	private $repository;

	/**
	 * @var Form\Control\Factory
	 */
	private $formControl;

	/**
	 * @var Ytnuk\Orm\Grid\Control\Factory
	 */
	private $gridControl;

	/**
	 * @var Ytnuk\Menu\Control\Factory
	 */
	private $menuControl;

	public function __construct(
		Entity $web,
		Repository $repository,
		Form\Control\Factory $formControl,
		Ytnuk\Orm\Grid\Control\Factory $gridControl,
		Ytnuk\Menu\Control\Factory $menuControl
	) {
		parent::__construct($web);
		$this->web = $web;
		$this->repository = $repository;
		$this->formControl = $formControl;
		$this->gridControl = $gridControl;
		$this->menuControl = $menuControl; //TODO: should not be here
	}

	public function redrawControl(
		string $snippet = NULL,
		bool $redraw = TRUE
	) {
		parent::redrawControl(
			$snippet,
			$redraw
		);
		$this[Ytnuk\Menu\Control::class]->redrawControl(); //TODO: should not be here
	}

	protected function createComponentYtnukMenuControl() : Ytnuk\Menu\Control //TODO: should not be here
	{
		return $this->menuControl->create($this->web->menu);
	}

	protected function createComponentYtnukOrmFormControl() : Form\Control
	{
		return $this->formControl->create($this->web);
	}

	protected function createComponentYtnukGridControl() : Ytnuk\Orm\Grid\Control
	{
		return $this->gridControl->create($this->repository);
	}
}
