<?php
namespace Ytnuk\Web\Domain\Router;

use Nette;
use Nextras;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	const ROUTE_MASK = '//<domain>[/<locale>]/<module>[/<action>][/<id>]';

	/**
	 * @var Ytnuk\Web\Domain\Repository
	 */
	private $repository;

	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	public function __construct(
		Ytnuk\Web\Domain\Repository $repository,
		Nette\Caching\IStorage $storage
	) {
		parent::__construct();
		$this->repository = $repository;
		$this->cache = new Nette\Caching\Cache(
			$storage,
			strtr(
				self::class,
				'\\',
				Nette\Caching\Cache::NAMESPACE_SEPARATOR
			)
		);
	}

	public function create()
	{
		$routes = array_filter(
			array_map(
				[
					$this->cache,
					'load',
				],
				$domains = $this->getDomains()
			)
		);
		$domains = array_diff_key(
			$domains,
			$routes
		);
		if ($domains) {
			$routes = array_merge(
				$routes,
				array_map(
					[
						$this,
						'getRouteForDomain',
					],
					iterator_to_array($this->repository->findById($domains))
				)
			);
		}
		array_walk(
			$routes,
			function (array $route) {
				$this[] = new Nette\Application\Routers\Route(
					...
					$route
				);
			}
		);
	}

	private function getDomains() : array
	{
		$this->repository->onAfterInsert[] = function () {
			$this->cache->remove(NULL);
		};

		return $this->cache->load(
			NULL,
			function (& $dependencies) {
				$dependencies[Nette\Caching\Cache::TAGS] = [];

				return array_map(
					function (Ytnuk\Web\Domain\Entity $entity) use
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

	private function getRouteForDomain(Ytnuk\Web\Domain\Entity $entity) : array
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
				$route = [
					self::ROUTE_MASK,
					[
						'domain' => [
							Nette\Application\Routers\Route::PATTERN => $entity->id,
						],
						'web' => $entity->web->id,
						'module' => $entity->web->menu->link->module,
						'presenter' => $entity->web->menu->link->presenter,
						'action' => $entity->web->menu->link->action,
						'locale' => [
							Nette\Application\Routers\Route::VALUE => $locale,
							Nette\Application\Routers\Route::PATTERN => implode(
								'|',
								array_map(
									function (string $locale) {
										return addcslashes(
											$locale,
											'|'
										);
									},
									$locales
								)
							),
						],
					],
				];
				if ($entity->secured) {
					$route[] = Nette\Application\IRouter::SECURED;
				}

				return $route;
			}
		);
	}
}
