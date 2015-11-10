<?php
namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property string $id {primary}
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Translation\Entity $name {1:1 Ytnuk\Translation\Entity::$web, primary=true}
 * @property Nextras\Orm\Relationships\OneHasOne|Ytnuk\Menu\Entity $menu {1:1 Ytnuk\Menu\Entity::$web, primary=true}
 * @property Nextras\Orm\Relationships\OneHasMany|Locale\Entity[] $localeNodes {1:m Locale\Entity::$web}
 * @property-read Ytnuk\Translation\Locale\Entity|NULL $locale {virtual}
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'name';

	public function getterLocale()
	{
		$node = $this->localeNodes->get()->findBy(['primary' => TRUE])->fetch();

		return $node instanceof Locale\Entity ? $node->locale : NULL;
	}
}
