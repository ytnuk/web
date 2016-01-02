<?php
namespace Ytnuk\Web;

use Kdyby;
use Nette;
use VitKutny;
use Ytnuk;

final class Extension
	extends Nette\DI\CompilerExtension
	implements Kdyby\Translation\DI\ITranslationProvider, Ytnuk\Orm\Provider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'error' => [
			'presenter' => 'Web:Error:Presenter',
		],
	];

	public function beforeCompile()
	{
		parent::beforeCompile();
		$builder = $this->getContainerBuilder();
		$router = $builder->getDefinition($builder->getByType(Nette\Application\IRouter::class));
		$router->setFactory(Domain\Router\Factory::class);
		$router->setArguments([$builder->parameters['wwwDir']]);
		foreach ($builder->findByType(Domain\Router\Filter\In::class) as $filterIn) {
			$router->addSetup('addFilterIn', [$filterIn]);
		}
		foreach ($builder->findByType(Domain\Router\Filter\Out::class) as $filterOut) {
			$router->addSetup('addFilterOut', [$filterOut]);
		}
		$router->addSetup('create');
		$application = $builder->getDefinition($builder->getByType(Nette\Application\Application::class));
		$application->addSetup('$errorPresenter', [$this->config['error']['presenter']]);
		$application->addSetup('$service->onError[] = ?', [
			Ytnuk\Web\Error\Presenter::class . '::onError',
		]);
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();
		$this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('control'))->setImplement(Control\Factory::class);
		$builder->addDefinition($this->prefix('form.control'))->setImplement(Form\Control\Factory::class);
	}

	public function getTranslationResources() : array
	{
		return [
			__DIR__ . '/../../locale',
		];
	}

	public function getOrmResources() : array
	{
		return [
			'repositories' => [
				$this->prefix('repository') => Repository::class,
				$this->prefix('domainRepository') => Domain\Repository::class,
				$this->prefix('domainLocaleRepository') => Domain\Locale\Repository::class,
			],
		];
	}
}
