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
		'mask' => '[<locale [a-z]{2}_[A-Z]{2}?>/]<module>[/<action>][/<id [0-9]+>]',
		'metadata' => [
			'module' => 'Home',
			'presenter' => 'Presenter',
			'action' => 'view',
			'locale' => 'en_US',
		],
	];

	/**
	 * @return array
	 */
	public function getConfigResources()
	{
		$config = $this->getConfig($this->defaults);
		$config['metadata']['web'] = $this->name;

		return [
			Nette\Bridges\ApplicationDI\ApplicationExtension::class => [
				'mapping' => [
					'*' => ucfirst($this->name) . '\*\*'
				]
			],
			Nette\Bridges\ApplicationDI\RoutingExtension::class => [
				'routes' => [
					$config['mask'] => $config['metadata']
				]
			],
			WebEdit\Alias\Extension::class => [
				'pattern' => [
					'WebEdit\*' => ucfirst($this->name) . '\\$1'
				]
			]
		];
	}
}
