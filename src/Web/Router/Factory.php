<?php
namespace Ytnuk\Web\Router;

use Nette;
use Ytnuk;

final class Factory
	extends Nette\Application\Routers\RouteList
{

	public function __construct(Ytnuk\Web\Repository $repository)
	{
		parent::__construct();
		foreach ($repository->findAll() as $web) {
			$this[] = new Nette\Application\Routers\Route(
				'//' . $web->id . '/[<locale (' . implode(
					'|',
					$web->locales
				) . ')?>/]<module>[/<action>][/<id [0-9]+>]',
				[
					'module' => $web->menu->link->module,
					'presenter' => $web->menu->link->presenter,
					'action' => $web->menu->link->action,
					//TODO: maybe web should not rely on Menu/Entity at all? should have own Link/Entity
					'web' => $web,
					'locale' => $web->locale ? $web->locale->getPersistedId() : NULL,
				]
			);
		}
	}
}
