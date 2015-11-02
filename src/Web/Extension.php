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

	public function setCompiler(
		Nette\DI\Compiler $compiler,
		$name
	) : self
	{
		$extension = parent::setCompiler(
			$compiler,
			$name
		);
		$compiler->addExtension(
			'vitkutny.version',
			new VitKutny\Version\Extension
		);

		return $extension;
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();
		$this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('control'))->setImplement(Control\Factory::class);
		$builder->addDefinition($this->prefix('form.control'))->setImplement(Form\Control\Factory::class);
	}

	public function beforeCompile()
	{
		parent::beforeCompile();
		$builder = $this->getContainerBuilder();
		$router = $builder->getDefinition($builder->getByType(Nette\Application\IRouter::class));
		$router->setFactory(Router\Factory::class);
		$router->addSetup('create');
		$application = $builder->getDefinition($builder->getByType(Nette\Application\Application::class));
		$application->addSetup(
			'$errorPresenter',
			[$this->config['error']['presenter']]
		);
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
				$this->prefix('localeRepository') => Locale\Repository::class,
			],
		];
	}
}
