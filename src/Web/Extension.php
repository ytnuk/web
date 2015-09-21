<?php
namespace Ytnuk\Web;

use Kdyby;
use Nette;
use Ytnuk;

final class Extension
	extends Nette\DI\CompilerExtension
	implements Ytnuk\Config\Provider
{

	/**
	 * @var string
	 */
	private $wwwDir;

	function __construct(string $wwwDir)
	{
		$this->wwwDir = $wwwDir;
	}

	public function beforeCompile()
	{
		parent::beforeCompile();
		$builder = $this->getContainerBuilder();
		$router = $builder->getDefinition($builder->getByType(Nette\Application\IRouter::class));
		$router->setFactory(Router\Factory::class);
	}

	public function getConfigResources() : array
	{
		return [
			Ytnuk\Orm\Extension::class => [
				'repositories' => [
					$this->prefix('repository') => Repository::class,
					$this->prefix('localeRepository') => Locale\Repository::class,
				],
			],
			Ytnuk\Alias\Extension::class => [
				'pattern' => [
					'Ytnuk\*' => basename(dirname($this->wwwDir)) . '\\$1',
				],
			],
			Kdyby\Translation\DI\TranslationExtension::class => [
				'dirs' => [
					__DIR__ . '/../../locale',
				],
			],
			'services' => [
				Control\Factory::class,
				Form\Control\Factory::class,
			],
			Nette\Bridges\ApplicationDI\ApplicationExtension::class => [
				'errorPresenter' => 'Web:Error:Presenter',
			],
		];
	}
}
