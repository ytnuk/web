<?php

namespace Ytnuk\Web;

use Kdyby;
use Nette;
use VojtechDobes;
use Ytnuk;

/**
 * Class Extension
 *
 * @package Ytnuk\Web
 */
final class Extension extends Nette\DI\CompilerExtension implements Ytnuk\Config\Provider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'web' => NULL,
		'module' => 'Home',
		'presenter' => 'Presenter',
		'action' => 'view',
		'locale' => 'en_US'
	];

	/**
	 * @inheritdoc
	 */
	public function getConfigResources()
	{
		$config = $this->getConfig($this->defaults);
		if ($_SERVER['SERVER_NAME'] === $this->name) {
			$configResources = [
				Ytnuk\Orm\Extension::class => [
					'repositories' => [
						$this->prefix('repository') => Repository::class
					]
				],
				'services' => [
					Control\Factory::class,
				],
				Ytnuk\Alias\Extension::class => [
					'pattern' => [
						'Ytnuk\*' => ucfirst($config['web']) . '\\$1'
					]
				],
				Kdyby\Translation\DI\TranslationExtension::class => [
					'dirs' => [
						__DIR__ . '/../../locale',
					]
				]
			];
		} else {
			$configResources = [];
		}
		if (is_array($config['locale'])) {
			$locales = $config['locale'];
			$config['locale'] = reset($locales);
		} else {
			$locales = [$config['locale']];
		}
		$configResources[Nette\Bridges\ApplicationDI\RoutingExtension::class] = [
			'routes' => [
				'//' . $this->name . '/[<locale (' . implode('|', $locales) . ')?>/]<module>[/<action>][/<id [0-9]+>]' => $config
			]
		];

		return $configResources;
	}
}
