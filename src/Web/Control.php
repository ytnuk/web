<?php

namespace Ytnuk\Web;

use Ytnuk;

/**
 * Class Control
 *
 * @package Ytnuk\Web
 */
final class Control extends Ytnuk\Application\Control
{

	/**
	 * @var Entity
	 */
	private $web;

	/**
	 * @var Repository
	 */
	private $repository;

	/**
	 * @var Ytnuk\Menu\Control\Factory
	 */
	private $menuControl;

	/**
	 * @param Entity $web
	 * @param Repository $repository
	 * @param Ytnuk\Menu\Control\Factory $menuControl
	 */
	public function __construct(Entity $web, Repository $repository, Ytnuk\Menu\Control\Factory $menuControl)
	{
		$this->web = $web;
		$this->repository = $repository;
		$this->menuControl = $menuControl;
	}

	public function redrawControl($snippet = NULL, $redraw = TRUE)
	{
		parent::redrawControl($snippet, $redraw);
		$this[Ytnuk\Menu\Control::class]->redrawControl($snippet, $redraw);
	}

	/**
	 * @return Ytnuk\Menu\Control
	 */
	protected function createComponentYtnukMenuControl()
	{
		return $this->menuControl->create($this->web->menu);
	}
}