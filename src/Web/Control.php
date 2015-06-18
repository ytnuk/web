<?php

namespace Ytnuk\Web;

use Ytnuk;

/**
 * Class Control
 *
 * @package Ytnuk\Web
 */
final class Control extends Ytnuk\Orm\Control
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
		parent::__construct($web);
		$this->web = $web;
		$this->repository = $repository;
		$this->menuControl = $menuControl;
	}

	/**
	 * @inheritdoc
	 */
	public function redrawControl($snippet = NULL, $redraw = TRUE)
	{
		parent::redrawControl($snippet, $redraw);
		$this[Ytnuk\Menu\Control::class]->redrawControl($snippet, $redraw);
	}

	/**
	 * @return array
	 */
	protected function startup()
	{
		return [
			'web' => $this->web
		];
	}

	/**
	 * @return array
	 */
	protected function renderTitle()
	{
		return [
			'breadcrumb' => $this[Ytnuk\Menu\Control::class]->getBreadcrumb(),
		];
	}

	/**
	 * @inheritdoc
	 */
	protected function getViews()
	{
		return [
			'title' => NULL,
		] + parent::getViews();
	}

	/**
	 * @return Ytnuk\Menu\Control
	 */
	protected function createComponentYtnukMenuControl()
	{
		return $this->menuControl->create($this->web->menu);
	}
}
