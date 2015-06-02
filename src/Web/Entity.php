<?php

namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property string $project
 * @property Nextras\Orm\Relationships\OneHasOneDirected|Ytnuk\Translation\Entity $name {1:1d Ytnuk\Translation\Repository $web primary}
 * @property Nextras\Orm\Relationships\OneHasOneDirected|Ytnuk\Menu\Entity $menu {1:1d Ytnuk\Menu\Repository $web primary}
 */
final class Entity extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'project';
}
