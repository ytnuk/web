<?php
namespace Ytnuk\Web\Domain\Locale;

use Nextras;
use Ytnuk;

/**
 * @property int $id {primary}
 * @property Nextras\Orm\Relationships\ManyHasOne|Ytnuk\Web\Domain\Entity $domain {m:1 Ytnuk\Web\Domain\Entity::$localeNodes}
 * @property Nextras\Orm\Relationships\ManyHasOne|Ytnuk\Translation\Locale\Entity $locale {m:1 Ytnuk\Translation\Locale\Entity, oneSided=true}
 * @property bool|NULL $primary
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'locale';
}
