<?php
namespace Ytnuk\Web\Domain;

use Nextras;
use Ytnuk;

//TODO: id => int
/**
 * @property string $id {primary}
 * @property string $host
 * @property Nextras\Orm\Relationships\ManyHasOne|Ytnuk\Web\Entity $web {m:1 Ytnuk\Web\Entity::$domains}
 * @property bool|NULL $secured
 * @property Nextras\Orm\Relationships\OneHasMany|Locale\Entity[] $localeNodes {1:m Locale\Entity::$domain}
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
