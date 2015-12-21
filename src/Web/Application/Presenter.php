<?php
namespace Ytnuk\Web\Application;

use Nette;
use ReflectionClass;
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
	public $web;

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

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	public function injectWeb(
		Ytnuk\Web\Repository $repository,
		Ytnuk\Web\Control\Factory $control,
		Ytnuk\Message\Control\Factory $messageControl,
		Nette\DI\Container $container
	) {
		$this->repository = $repository;
		$this->control = $control;
		$this->messageControl = $messageControl;
		$this->container = $container;
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		$this->redrawControl();
		$this['web']->redrawControl();
		$this['message']->redrawControl();
	}

	public function checkRequirements($element)
	{
		if ($element instanceof ReflectionClass) {
			if ($this->getParameter('id') !== NULL) {
				$action = $this->formatActionMethod($this->getAction());
				if ( ! $element->hasMethod($action) || ! in_array(
						'id',
						array_column(
							$element->getMethod($action)->getParameters(),
							'name'
						)
					)
				) {
					$this->error();
				}
			}
		}
		parent::checkRequirements($element);
	}

	protected function createComponentTemplating()
	{
		$control = parent::createComponentTemplating();
		array_unshift(
			$control->templates,
			implode(
				DIRECTORY_SEPARATOR,
				[
					$appDir = $this->container->getParameters()['appDir'],
					'web',
					$this->web->id,
					'domain',
					$this->domain,
					'src',
				]
			),
			implode(
				DIRECTORY_SEPARATOR,
				[
					$appDir,
					'web',
					$this->web->id,
					'src',
				]
			),
			implode(
				DIRECTORY_SEPARATOR,
				[
					$appDir,
					'src',
				]
			)
		);

		return $control;
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

	public function loadState(array $params)
	{
		parent::loadState($params);
		if ( ! isset($params['web']) || ! $this->web = $this->repository->getById($params['web'])) {
			$this->error();
		}
	}

	protected function createComponentWeb() : Ytnuk\Web\Control
	{
		return $this->control->create($this->web);
	}

	protected function createComponentMessage()
	{
		return $this->messageControl->create();
	}
}
