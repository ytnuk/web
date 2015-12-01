<?php
namespace Ytnuk\Web;

use Ytnuk;

final class Presenter
	extends Ytnuk\Web\Application\Presenter
{

	/**
	 * @var Repository
	 */
	private $repository;

	/**
	 * @var Control\Factory
	 */
	private $control;

	/**
	 * @var Entity
	 */
	private $entity;

	public function __construct(
		Repository $repository,
		Control\Factory $control
	) {
		parent::__construct();
		$this->repository = $repository;
		$this->control = $control;
	}

	public function actionEdit(string $id)
	{
		if ( ! $this->entity = $this->repository->getById($id)) {
			$this->error();
		}
	}

	public function renderEdit()
	{
		$this['web']['menu'][] = 'web.presenter.action.edit';
	}

	protected function createComponentWeb() : Control
	{
		$control = parent::createComponentWeb();
		$control->setEntity($this->entity ? : new Entity);

		return $control;
	}
}
