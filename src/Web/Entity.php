<?php
namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property int $id {primary}
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Translation\Entity $name {1:1 Ytnuk\Translation\Entity::$web, primary=true, cascade=[persist, remove]}
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Menu\Entity $menu {1:1 Ytnuk\Menu\Entity::$web, primary=true}
 * @property Nextras\Orm\Relationships\OneHasMany|Domain\Entity[] $domains {1:m Domain\Entity::$web}
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'name';
}
