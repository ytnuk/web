<?php
namespace Ytnuk\Web\Router;

use Nette;
use Nextras;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	/**
	 * @var Ytnuk\Web\Repository
	 */
	private $repository;

	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	public function __construct(
		Ytnuk\Web\Repository $repository,
		Nette\Caching\IStorage $storage
	) {
		parent::__construct();
		$this->repository = $repository;
		$this->cache = new Nette\Caching\Cache(
			$storage,
			self::class
		);
	}

	public function create()
	{
		foreach ($this->repository->findAll() as $web) {
			$this[] = new Nette\Application\Routers\Route(
				'//[!v.][!<web>][/<locale>]/<module>[/<action>][/<id>]',
				$this->getMetadataForWeb($web),
				Nette\Application\Routers\Route::SECURED
			);
		}
	}

	private function getMetadataForWeb(Ytnuk\Web\Entity $entity)
	{
		return [
			'web' => [
				Nette\Application\Routers\Route::VALUE => $entity,
				Nette\Application\Routers\Route::PATTERN => $entity->id,
				Nette\Application\Routers\Route::FILTER_IN => function ($web) {
					return $web instanceof Ytnuk\Web\Entity ? $web : $this->repository->getById($web);
				},
				Nette\Application\Routers\Route::FILTER_OUT => function ($web) : string {
					return $web instanceof Ytnuk\Web\Entity ? $web->id : $web;
				},
			],
		] + $this->cache->load(
			$entity->getCacheKey(),
			function (& $dependencies) use
			(
				$entity
			) {
				$dependencies[Nette\Caching\Cache::TAGS] = $entity->getCacheTags();
				$locale = NULL;
				$locales = [];
				foreach ($entity->localeNodes as $localeNode) {
					$locales[] = $localeNode->getRawValue('locale');
					if ($localeNode->primary) {
						$locale = end($locales);
					}
				}

				return [
					'module' => $entity->menu->link->module,
					'presenter' => $entity->menu->link->presenter,
					'action' => $entity->menu->link->action,
					//TODO: maybe web should not rely on Menu/Entity at all? should have own Link/Entity
					'locale' => [
						Nette\Application\Routers\Route::VALUE => $locale,
						Nette\Application\Routers\Route::PATTERN => implode(
							'|',
							$locales
						),
					],
				];
			}
		);
	}
}
