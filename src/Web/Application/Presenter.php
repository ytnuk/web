<?php
namespace Ytnuk\Web\Application;

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
	 * @var string
	 * @persistent
	 */
	public $web;

	/**
	 * @var Ytnuk\Web\Entity
	 */
	private $entity;

	/**
	 * @var Ytnuk\Web\Repository
	 */
	private $repository;

	/**
	 * @var Ytnuk\Web\Control\Factory
	 */
	private $control;

	public function injectWeb(
		Ytnuk\Web\Repository $repository,
		Ytnuk\Web\Control\Factory $control
	) {
		$this->repository = $repository;
		$this->control = $control;
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		$template = $this->getTemplate();
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->add(
				'web',
				$this->entity
			);
		}
	}

	public function processSignal()
	{
		$this[Ytnuk\Web\Control::NAME][Ytnuk\Web\Tracy\Control::NAME]->redrawControl();
		parent::processSignal();
		$this->redrawControl();
		$this[Ytnuk\Web\Control::NAME]->redrawControl();
	}

	protected function startup()
	{
		parent::startup();
		if ( ! $this->entity = $this->repository->getById($this->web)) {
			$this->error();
		}
	}

	//TODO: should not be used for accessing menu, create directly menu control using multiplier and access directly using an identifier

	protected function createComponentWeb() : Ytnuk\Web\Control
	{
		return $this->control->create($this->entity);
	}
}
