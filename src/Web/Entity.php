<?php
namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property string $id
 * @property Nextras\Orm\Relationships\OneHasOneDirected|Ytnuk\Translation\Entity $name {1:1d Ytnuk\Translation\Entity::$web primary}
 * @property Nextras\Orm\Relationships\OneHasOneDirected|Ytnuk\Menu\Entity $menu {1:1d Ytnuk\Menu\Entity::$web primary}
 * @property Nextras\Orm\Relationships\OneHasMany|Locale\Entity[] $localeNodes {1:m Locale\Entity::$web}
 * @property-read array $locales {virtual}
 * @property-read Ytnuk\Translation\Locale\Entity|NULL $locale {virtual}
 */
final class Entity
	extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'name';

	public function getterLocales() : array
	{
		$locales = [];
		foreach ($this->localeNodes as $node) {
			$locales[] = $node->getRawValue('locale');
		}

		return $locales;
	}

	public function getterLocale()
	{
		$node = $this->localeNodes->get()->findBy(['primary' => TRUE])->fetch();

		return $node instanceof Locale\Entity ? $node->locale : NULL;
	}
}
