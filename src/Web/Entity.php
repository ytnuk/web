<?php
namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property int $id {primary}
 * @property string $alias
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Translation\Entity $name {1:1 Ytnuk\Translation\Entity, oneSided=true, isMain=true, cascade=[persist, remove]}
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Menu\Entity $menu {1:1 Ytnuk\Menu\Entity, oneSided=true, isMain=true}
 * @property Nextras\Orm\Relationships\OneHasMany|Domain\Entity[] $domains {1:m Domain\Entity::$web, cascade=[persist, remove]}
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'name';
}
