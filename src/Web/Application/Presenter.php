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
	public $domain;

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

	/**
	 * @var Ytnuk\Message\Control\Factory
	 */
	private $messageControl;

	public function injectWeb(
		Ytnuk\Web\Repository $repository,
		Ytnuk\Web\Control\Factory $control,
		Ytnuk\Message\Control\Factory $messageControl
	) {
		$this->repository = $repository;
		$this->control = $control;
		$this->messageControl = $messageControl;
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		$this->redrawControl();
		$this[Ytnuk\Web\Control::NAME]->redrawControl();
		$this[Ytnuk\Message\Control::NAME]->redrawControl();
		$template = $this->getTemplate();
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->add(
				'web',
				$this->entity
			);
		}
	}

	protected function createRequest(
		$component,
		$destination,
		array $args,
		$mode
	) {
		return parent::createRequest(
			$component,
			$destination instanceof Ytnuk\Web\Entity ? $destination->menu->link : $destination,
			$args,
			$mode
		);
	}

	protected function startup()
	{
		parent::startup();
		if ( ! $this->entity = $this->repository->getById($this->getParameter('web'))) {
			$this->error();
		}
	}

	protected function createComponentWeb() : Ytnuk\Web\Control
	{
		return $this->control->create($this->entity);
	}

	protected function createComponentMessage()
	{
		return $this->messageControl->create();
	}
}
