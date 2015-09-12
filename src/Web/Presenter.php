<?php
namespace Ytnuk\Web;

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
	 * @var Entity
	 * @persistent
	 */
	public $web;

	/**
	 * @var Control\Factory
	 */
	private $control;

	/**
	 * @var Repository
	 */
	private $repository;

	public function inject(
		Control\Factory $control,
		Repository $repository
	) {
		$this->control = $control;
		$this->repository = $repository;
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		$template = $this->getTemplate();
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->add(
				'web',
				$this->web
			);
		}
	}

	public function redrawControl(
		string $snippet = NULL,
		bool $redraw = TRUE
	) {
		parent::redrawControl(
			$snippet,
			$redraw
		);
		$this[Ytnuk\Message\Control::class]->redrawControl();
	}

	protected function startup()
	{
		parent::startup();
		if ( ! $this->web instanceof Entity) {
			$this->error();
		}
	}

	protected function createComponentYtnukWebControl() : Control
	{
		return $this->control->create($this->web);
	}
}
