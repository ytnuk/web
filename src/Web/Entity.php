<?php

namespace Ytnuk\Web;

use Nextras;
use Ytnuk;

/**
 * @property string $name
 * @property string $project
 * @property Nextras\Orm\Relationships\OneHasOneDirected|Ytnuk\Menu\Entity $menu {1:1d Ytnuk\Menu\Repository $page primary}
 */
final class Entity extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'project';
}
