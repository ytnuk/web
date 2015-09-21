<?php
namespace Ytnuk\Web\Router;

use Nette;
use Nextras;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	/**
	 * @var Ytnuk\Web\Entity[]
	 */
	private $webs = [];

	/**
	 * @var Ytnuk\Translation\Locale\Entity[]
	 */
	private $locales = [];

	public function __construct(Ytnuk\Web\Repository $repository)
	{
		parent::__construct();
		foreach ($this->webs = $repository->findAll()->fetchPairs(current($repository->getEntityMetadata()->getPrimaryKey())) as $web) {
			$this[] = new Nette\Application\Routers\Route(
				'//[!<web>][!:8080][/<locale>]/<module>[/<action>][/<id>]',
				[
					'module' => $web->menu->link->module,
					'presenter' => $web->menu->link->presenter,
					'action' => $web->menu->link->action,
					//TODO: maybe web should not rely on Menu/Entity at all? should have own Link/Entity
					'web' => [
						Nette\Application\Routers\Route::VALUE => $web,
						Nette\Application\Routers\Route::PATTERN => $web->id,
						Nette\Application\Routers\Route::FILTER_IN => function ($web) {
							return $web instanceof Ytnuk\Web\Entity ? $web : isset($this->webs[$web]) ? $this->webs[$web] : NULL;
						},
						Nette\Application\Routers\Route::FILTER_OUT => function ($web) : string {
							return $web instanceof Ytnuk\Web\Entity ? $web->id : $web;
						},
					],
					'locale' => [
						Nette\Application\Routers\Route::VALUE => $web->locale,
						Nette\Application\Routers\Route::PATTERN => implode(
							'|',
							array_keys($this->locales += $web->locales)
						),
						Nette\Application\Routers\Route::FILTER_IN => function ($locale) {
							return $locale instanceof Ytnuk\Web\Entity ? $locale : isset($this->locales[$locale]) ? $this->locales[$locale] : NULL;
						},
						Nette\Application\Routers\Route::FILTER_OUT => function ($locale) {
							return $locale instanceof Ytnuk\Translation\Locale\Entity ? $locale->id : $locale;
						},
					],
				]
			);
		}
	}
}
