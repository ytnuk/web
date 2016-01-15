<?php
namespace Ytnuk\Web\Domain\Router;

use Nette;
use Nextras;
use Tracy;
use VitKutny;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	const FILE_MASK = '//<domain>[/web/<web>[/domain/<webDomain>]]/<file>';
	const WEB_MASK = '//<domain>[/<locale>][[/<slug>]/<id>]/<module>[/<action>]';

	/**
	 * @var array|Filter\In[]
	 */
	private $filterIn = [];

	/**
	 * @var Nette\Caching\Cache
	 */
	private $filterInCache;

	/**
	 * @var array|Filter\Out[]
	 */
	private $filterOut = [];

	/**
	 * @var Nette\Caching\Cache
	 */
	private $filterOutCache;

	/**
	 * @var Ytnuk\Web\Domain\Repository
	 */
	private $repository;

	/**
	 * @var Tracy\ILogger
	 */
	private $logger;

	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	/**
	 * @var string
	 */
	private $wwwDir;

	/**
	 * @var VitKutny\Version\Filter
	 */
	private $versionFilter;

	public function __construct(
		string $wwwDir,
		Ytnuk\Web\Domain\Repository $repository,
		VitKutny\Version\Filter $versionFilter,
		Tracy\ILogger $logger,
		Nette\Caching\IStorage $storage
	) {
		parent::__construct();
		$this->wwwDir = $wwwDir;
		$this->repository = $repository;
		$this->logger = $logger;
		$this->cache = new Nette\Caching\Cache($storage, strtr(self::class, '\\', Nette\Caching\Cache::NAMESPACE_SEPARATOR));
		$this->filterInCache = $this->cache->derive('filterIn');
		$this->filterOutCache = $this->cache->derive('filterOut');
		$this->versionFilter = $versionFilter;
	}

	public function addFilterIn(Filter\In $filter)
	{
		$this->filterIn[] = $filter;
	}

	public function addFilterOut(Filter\Out $filter)
	{
		$this->filterOut[] = $filter;
	}

	public function create()
	{
		try {
			$this->repository->mapper->getStorageReflection();
		} catch (Nextras\Dbal\QueryException $ex) {
			$this->logger->log($ex, Tracy\ILogger::EXCEPTION);

			return;
		}
		$routes = array_filter(array_map([
			$this->cache,
			'load',
		], $domains = $this->getDomains()));
		$domains = array_diff_key($domains, $routes);
		if ($domains) {
			$routes = array_merge($routes, array_map([
				$this,
				'getRouteForDomain',
			], iterator_to_array($this->repository->findById($domains))));
		}
		array_walk($routes, function (array $route) {
			$this[] = $this->createFileRoute($route);
			$this[] = $this->createWebRoute($route);
		});
	}

	private function createWebRoute(
		array $route
	) {
		return new Nette\Application\Routers\Route(...
			array_values(array_merge_recursive(['mask' => self::WEB_MASK] + $route, [
				'metadata' => [
					'slug' => [
						Nette\Application\Routers\Route::PATTERN => '[a-z0-9-]+',
					],
					'id' => [
						Nette\Application\Routers\Route::PATTERN => '[0-9]+',
					],
					NULL => [
						Nette\Application\Routers\Route::FILTER_IN => function (array $params) {
							return array_diff_key($this->filterIn && array_filter($params, 'is_scalar') === $params ? $this->filterInCache->load($params, function (& $dependencies) use
							(
								$params
							) {
								$dependencies = (array) $dependencies;
								array_walk($this->filterIn, function (Filter\In $filter) use
								(
									& $params,
									& $dependencies
								) {
									$params = $filter->filterIn($params, $dependencies);
								});

								return $params;
							}) : $params, array_flip(['slug']));
						},
						Nette\Application\Routers\Route::FILTER_OUT => function (array $params) {
							return $this->filterOut && array_filter($params, 'is_scalar') === $params ? $this->filterOutCache->load($params, function (& $dependencies) use
							(
								$params
							) {
								$dependencies = (array) $dependencies;
								array_walk($this->filterOut, function (Filter\Out $filter) use
								(
									& $params,
									& $dependencies
								) {
									$params = $filter->filterOut($params, $dependencies);
								});

								return array_filter($params);
							}) : $params;
						},
					],
				],
			])));
	}

	private function createFileRoute(array $route)
	{
		$web = $route['metadata']['web'][Nette\Application\Routers\Route::VALUE];
		unset($route['metadata']['web']);

		return new Nette\Application\Routers\Route(...
			array_values(array_merge_recursive(['mask' => self::FILE_MASK] + $route, [
				'metadata' => [
					'file' => [
						Nette\Application\Routers\Route::PATTERN => '[a-z0-9.-/]+',
					],
					NULL => [
						Nette\Application\Routers\Route::FILTER_IN => function (array $params) {
							$webDir = implode(DIRECTORY_SEPARATOR, [
								$this->wwwDir,
								'web',
								$params['web'],
							]);
							$domainDir = implode(DIRECTORY_SEPARATOR, [
								$webDir,
								'domain',
								$domain = $params['domain'],
							]);
							$webFile = implode(DIRECTORY_SEPARATOR, [
								$webDir,
								$file = $params['file'],
							]);
							$domainFile = implode(DIRECTORY_SEPARATOR, [
								$domainDir,
								$file,
							]);
							if (is_file($domainFile)) {
								$params['webDomain'] = $domain;
							} elseif ( ! is_file($webFile)) {
								return NULL;
							}

							return $params;
						},
						Nette\Application\Routers\Route::FILTER_OUT => function (array $params) use
						(
							$web
						) {
							if ( ! isset($params['file']) || ! $file = $params['file']) {
								return NULL;
							}
							$webDir = implode(DIRECTORY_SEPARATOR, [
								$this->wwwDir,
								'web',
								$params['web'] = $web,
							]);
							$webFile = implode(DIRECTORY_SEPARATOR, [
								$webDir,
								$file,
							]);
							$domainDir = implode(DIRECTORY_SEPARATOR, [
								$webDir,
								'domain',
								$domain = $params['domain'],
							]);
							$domainFile = implode(DIRECTORY_SEPARATOR, [
								$domainDir,
								$file,
							]);
							$directory = NULL;
							if (is_file($domainFile)) {
								$params['webDomain'] = $domain;
								$directory = $domainDir;
							} elseif (is_file($webFile)) {
								$directory = $webDir;
							} else {
								unset($params['web']);
								$directory = $this->wwwDir;
							}
							if (( ! isset($params['version']) || $params['version']) && $directory) {
								$url = new Nette\Http\Url(call_user_func($this->versionFilter, $file, $directory, $parameter = is_string($params['version'] ?? NULL) ? $params['version'] : 'version'));
								$params['version'] = $url->getQueryParameter($parameter);
							} else {
								unset($params['version']);
							}

							return array_intersect_key($params, array_flip([
								'domain',
								'web',
								'webDomain',
								'file',
								'version',
							]));
						},
					],
				],
			])));
	}

	private function getDomains() : array
	{
		$this->repository->onAfterInsert[] = function () {
			$this->cache->remove(NULL);
		};

		return $this->cache->load(NULL, function (& $dependencies) {
			$dependencies[Nette\Caching\Cache::TAGS] = [];

			return array_map(function (Ytnuk\Web\Domain\Entity $entity) use
			(
				$dependencies
			) {
				$dependencies[Nette\Caching\Cache::TAGS] = array_merge($dependencies[Nette\Caching\Cache::TAGS], $entity->getCacheTags());

				return $entity->id;
			}, iterator_to_array($this->repository->findAll()));
		});
	}

	private function getRouteForDomain(Ytnuk\Web\Domain\Entity $entity) : array
	{
		return $this->cache->load($entity->id, function (& $dependencies) use
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

			return array_filter([
				'metadata' => [
					'domain' => [
						Nette\Application\Routers\Route::PATTERN => $entity->host,
					],
					'web' => [
						Nette\Application\Routers\Route::VALUE => $entity->web->alias,
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
						Nette\Application\Routers\Route::PATTERN => implode($glue = '|', array_map(function (string $locale) use
						(
							$glue
						) {
							return addcslashes($locale, $glue);
						}, $locales)),
					],
				],
				'secured' => $entity->secured ? Nette\Application\IRouter::SECURED : NULL,
			]);
		});
	}
}
