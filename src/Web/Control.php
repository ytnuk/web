<?php
namespace Ytnuk\Web;

use Ytnuk;

final class Control
	extends Ytnuk\Orm\Control
{

	const NAME = 'web';

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

	/**
	 * @var Ytnuk\Message\Control\Factory
	 */
	private $messageControl;

	public function __construct(
		Entity $web,
		Repository $repository,
		Form\Control\Factory $formControl,
		Ytnuk\Orm\Grid\Control\Factory $gridControl,
		Ytnuk\Menu\Control\Factory $menuControl,
		Ytnuk\Message\Control\Factory $messageControl
	) {
		parent::__construct($web);
		$this->web = $web;
		$this->repository = $repository;
		$this->formControl = $formControl;
		$this->gridControl = $gridControl;
		$this->menuControl = $menuControl; //TODO: should not be here
		$this->messageControl = $messageControl;
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
		] + parent::getViews();
	}

	public function redrawControl(
		string $snippet = NULL,
		bool $redraw = TRUE
	) {
		parent::redrawControl(
			$snippet,
			$redraw
		);
		$this[Ytnuk\Menu\Control::NAME]->redrawControl(); //TODO: should not be here
		$this[Ytnuk\Message\Control::NAME]->redrawControl();
	}

	protected function createComponentMenu() : Ytnuk\Menu\Control //TODO: should not be here
	{
		return $this->menuControl->create($this->web->menu);
	}

	protected function createComponentForm() : Form\Control
	{
		return $this->formControl->create($this->web);
	}

	protected function createComponentGrid() : Ytnuk\Orm\Grid\Control
	{
		return $this->gridControl->create($this->repository);
	}

	protected function createComponentMessage()
	{
		return $this->messageControl->create();
	}

	protected function createComponentTracy()
	{
		return new Tracy\Control;
	}
}
