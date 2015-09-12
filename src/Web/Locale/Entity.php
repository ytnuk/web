<?php
namespace Ytnuk\Web\Locale;

use Nextras;
use Ytnuk;

/**
 * @property Nextras\Orm\Relationships\ManyHasOne|Ytnuk\Web\Entity $web {m:1 Ytnuk\Web\Entity::$localeNodes}
 * @property Nextras\Orm\Relationships\ManyHasOne|Ytnuk\Translation\Locale\Entity $locale {m:1 Ytnuk\Translation\Locale\Entity::$webNodes}
 * @property bool|NULL $primary
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'locale';
}
