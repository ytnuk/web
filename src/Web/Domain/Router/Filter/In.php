<?php
namespace Ytnuk\Web\Domain\Router\Filter;

interface In
{

	public function filterIn(
		array $params,
		array & $dependencies = []
	) : array;
}
