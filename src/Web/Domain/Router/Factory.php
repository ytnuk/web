<?php
namespace Ytnuk\Web\Domain\Router;

use Nette;
use Nextras;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	const ROUTE_MASK = '//<domain>[/<locale>]/<module>[/<action>[/<id>]]';

	/**
	 * @var Ytnuk\Web\Domain\Repository
	 */
	private $repository;

	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	/**
	 * @var array
	 */
	private $moduleIn = [];

	/**
	 * @var array
	 */
	private $actionIn = [];

	/**
	 * @var array
	 */
	private $aliasOut = [];

	/**
	 * @var Ytnuk\Link\Alias\Repository
	 */
	private $linkAliasRepository;

	public function __construct(
		Ytnuk\Web\Domain\Repository $repository,
		Ytnuk\Link\Alias\Repository $linkAliasRepository,
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
		$this->linkAliasRepository = $linkAliasRepository;
	}

	public function constructUrl(
		Nette\Application\Request $appRequest,
		Nette\Http\Url $refUrl
	) {
		$this->aliasOut = [];

		return parent::constructUrl(
			$appRequest,
			$refUrl
		);
	}

	public function match(Nette\Http\IRequest $httpRequest)
	{
		$this->moduleIn = $this->actionIn = [];

		return parent::match($httpRequest);
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
				$key = count($this);
				$this[] = new Nette\Application\Routers\Route(
					...
					array_values(
						array_merge_recursive(
							$route,
							[
								'metadata' => [
									'module' => [
										Nette\Application\Routers\Route::FILTER_IN => function (string $module) use
										(
											$key
										) {
											return implode(
												':',
												array_map(
													'ucfirst',
													explode(
														'.',
														$this->moduleIn[$key] = $module
													)
												)
											);
										},
										Nette\Application\Routers\Route::FILTER_OUT => function (string $module) use
										(
											$key
										) {
											$alias = $this->aliasOut[$key] ?? NULL;
											if ($alias instanceof Ytnuk\Link\Alias\Entity) {
												return $alias->value;
											}

											return implode(
												'.',
												array_map(
													'lcfirst',
													explode(
														':',
														$module
													)
												)
											);
										},
									],
									'action' => [
										Nette\Application\Routers\Route::FILTER_IN => function (string $action) use
										(
											$key
										) {
											return $this->actionIn[$key] = $action;
										},
									],
									NULL => [
										Nette\Application\Routers\Route::FILTER_IN => function (array $params) use
										(
											$key
										) {
											if (isset($this->moduleIn[$key]) && ! isset($this->actionIn[$key]) && $locale = $params['locale'] ?? NULL) {
												if (isset($params['id'])) {
													return NULL;
												}
												$linkAlias = $this->linkAliasRepository->getBy(
													[
														'locale' => $locale,
														'value' => $this->moduleIn[$key],
													]
												);
												if ($linkAlias instanceof Ytnuk\Link\Alias\Entity && $link = $linkAlias->link) {
													return [
														'link' => $link,
														'module' => $link->module,
														'presenter' => $link->presenter,
														'action' => $link->action,
													] + $link->parameters->get()->fetchPairs(
														'key',
														'value'
													) + $params;
												}
											}

											return $params;
										},
										Nette\Application\Routers\Route::FILTER_OUT => function (array $params) use
										(
											$key
										) {
											$link = $params['link'] ?? NULL;
											if ($link instanceof Ytnuk\Link\Entity && $locale = $params['locale'] ?? NULL) {
												if ($this->aliasOut[$key] = $link->getterAlias($locale)) {
													unset($params['action']);
													unset($params['id']);
												}
											}

											return $params;
										},
									],
								],
							]
						)
					)
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

				return array_filter(
					[
						'mask' => self::ROUTE_MASK,
						'metadata' => [
							'domain' => [
								Nette\Application\Routers\Route::PATTERN => $entity->id,
							],
							'web' => [
								Nette\Application\Routers\Route::VALUE => $entity->web->id,
							],
							'module' => [
								Nette\Application\Routers\Route::VALUE => $entity->web->menu->link->module,
							],
							'presenter' => [
								Nette\Application\Routers\Route::VALUE => $entity->web->menu->link->presenter,
							],
							'action' => [
								Nette\Application\Routers\Route::VALUE => $entity->web->menu->link->action,
							],
							'locale' => [
								Nette\Application\Routers\Route::VALUE => $locale,
								Nette\Application\Routers\Route::PATTERN => implode(
									$glue = '|',
									array_map(
										function (string $locale) use
										(
											$glue
										) {
											return addcslashes(
												$locale,
												$glue
											);
										},
										$locales
									)
								),
							],
						],
						'secured' => $entity->secured ? Nette\Application\IRouter::SECURED : NULL,
					]
				);
			}
		);
	}
}
