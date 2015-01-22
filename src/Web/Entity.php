<?php

namespace Ytnuk\Web;

use Ytnuk;

/**
 * @property Ytnuk\Menu\Entity $menu {1:1d Ytnuk\Menu\Repository $page primary}
 * @property string $project
 * @property string $name
 */
final class Entity extends Ytnuk\Orm\Entity
{

	const PROPERTY_NAME = 'project';
}
