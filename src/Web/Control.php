<?php
namespace Ytnuk\Web;

use Ytnuk;

final class Control
	extends Ytnuk\Orm\Control //TODO: provide form and grid for administration
{

	/**
	 * @var Entity
	 */
	private $web;

	/**
	 * @var Ytnuk\Menu\Control\Factory
	 */
	private $menuControl;

	public function __construct(
		Entity $web,
		Ytnuk\Menu\Control\Factory $menuControl
	) {
		parent::__construct($web);
		$this->web = $web;
		$this->menuControl = $menuControl;
	}

	public function redrawControl(
		string $snippet = NULL,
		bool $redraw = TRUE
	) {
		parent::redrawControl(
			$snippet,
			$redraw
		);
		$this[Ytnuk\Menu\Control::class]->redrawControl();
	}

	protected function createComponentYtnukMenuControl() : Ytnuk\Menu\Control
	{
		return $this->menuControl->create($this->web->menu);
	}
}
