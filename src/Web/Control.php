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
	 * @var Entity
	 */
	private $entity;

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
		$this->setEntity($this->web = $web);
		$this->repository = $repository;
		$this->formControl = $formControl;
		$this->gridControl = $gridControl;
		$this->menuControl = $menuControl;
	}

	public function setEntity(Entity $entity)
	{
		$this->entity = $entity;
	}

	protected function startup()
	{
		return [
			'web' => $this->web,
		];
	}

	protected function getViews() : array
	{
		return [
			'title' => TRUE,
			'navbar' => TRUE,
		] + parent::getViews();
	}

	protected function createComponentMenu() : Ytnuk\Menu\Control
	{
		return $this->menuControl->create($this->web->menu);
	}

	protected function createComponentForm() : Form\Control
	{
		return $this->formControl->create($this->entity);
	}

	protected function createComponentGrid() : Ytnuk\Orm\Grid\Control
	{
		return $this->gridControl->create($this->repository);
	}
}
