<?php

namespace Kutny\Web;

use Nette;
use Kutny;

/**
 * Class Extension
 *
 * @package Kutny\Web
 */
final class Extension extends Nette\DI\CompilerExtension implements Kutny\Config\Provider
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
	 * @return array
	 */
	public function getConfigResources()
	{
		$config = $this->getConfig($this->defaults);
		$configResources = [];
		if ($_SERVER['SERVER_NAME'] === $this->name) {
			$configResources[Nette\Bridges\ApplicationDI\ApplicationExtension::class] = [
				'mapping' => [
					'*' => ucfirst($config['web']) . '\*\*'
				]
			];
			$configResources[Kutny\Alias\Extension::class] = [
				'pattern' => [
					'Kutny\*' => ucfirst($config['web']) . '\\$1'
				]
			];
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
