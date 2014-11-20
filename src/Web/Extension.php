<?php

namespace WebEdit\Web;

use Nette;
use WebEdit;

/**
 * Class Extension
 *
 * @package WebEdit\Web
 */
final class Extension extends Nette\DI\CompilerExtension implements WebEdit\Config\Provider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'web' => NULL,
		'module' => 'Home',
		'presenter' => 'Presenter',
		'action' => 'view',
		'locale' => 'en_US',
	];

	/**
	 * @return array
	 */
	public function getConfigResources()
	{
		$config = $this->getConfig($this->defaults);
		$configResources = [
			Nette\Bridges\ApplicationDI\RoutingExtension::class => [
				'routes' => [
					'//' . $this->name . '/[<locale [a-z]{2}_[A-Z]{2}?>/]<module>[/<action>][/<id [0-9]+>]' => $config
				]
			]
		];
		if ($_SERVER['SERVER_NAME'] === $this->name) {
			$configResources[Nette\Bridges\ApplicationDI\ApplicationExtension::class] = [
				'mapping' => [
					'*' => ucfirst($config['web']) . '\*\*'
				]
			];
			$configResources[WebEdit\Alias\Extension::class] = [
				'pattern' => [
					'WebEdit\*' => ucfirst($config['web']) . '\\$1'
				]
			];
		}

		return $configResources;
	}
}
