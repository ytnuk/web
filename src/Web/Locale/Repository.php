<?php
namespace Ytnuk\Web\Locale;

use Ytnuk;

final class Repository
	extends Ytnuk\Orm\Repository
{

	public static function getEntityClassNames() : array
	{
		return [
			Entity::class,
		];
	}
}
