<?php

namespace Ytnuk\Web;

use Ytnuk;

/**
 * Class Repository
 *
 * @package Ytnuk\Web
 */
final class Repository extends Ytnuk\Orm\Repository
{

	/**
	 * @param string $project
	 *
	 * @return Entity
	 */
	public function get($project)
	{
		return $this->findBy(['project' => $project])
			->fetch();
	}
}
