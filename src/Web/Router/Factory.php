<?php
namespace Ytnuk\Web\Router;

use Nette;
use Nextras;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	const ROUTE_MASK = '//[!v.][!<web>][/<locale>]/<module>[/<action>][/<id>]';

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
		$metadata = array_filter(
			array_map(
				[
					$this->cache,
					'load',
				],
				$webs = $this->getWebs()
			)
		);
		$webs = array_diff_key(
			$webs,
			$metadata
		);
		if ($webs) {
			$metadata = array_merge(
				$metadata,
				array_map(
					[
						$this,
						'getMetadataForWeb',
					],
					$this->repository->findById($webs)
				)
			);
		}
		array_walk(
			$metadata,
			function (array $metadata) {
				$this[] = new Nette\Application\Routers\Route(
					self::ROUTE_MASK,
					$metadata,
					Nette\Application\Routers\Route::SECURED
				);
			}
		);
	}

	private function getWebs() : array
	{
		$this->repository->onAfterInsert[] = function () {
			$this->cache->remove(get_class($this->repository));
		};

		return $this->cache->load(
			get_class($this->repository),
			function (& $dependencies) {
				$dependencies[Nette\Caching\Cache::TAGS] = [];

				return array_map(
					function (Ytnuk\Web\Entity $entity) use
					(
						$dependencies
					) {
						$dependencies[Nette\Caching\Cache::TAGS] = array_merge(
							$dependencies[Nette\Caching\Cache::TAGS],
							$entity->getCacheTags()
						);

						return $entity->id;
					},
					iterator_to_array($this->repository->findAll())
				);
			}
		);
	}

	private function getMetadataForWeb(Ytnuk\Web\Entity $entity) : array
	{
		return $this->cache->load(
			$entity->id,
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
					'web' => [
						Nette\Application\Routers\Route::VALUE => $entity->id,
						Nette\Application\Routers\Route::PATTERN => $entity->id,
					],
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
