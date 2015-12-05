<?php
namespace Ytnuk\Web\Domain\Router\Filter;

interface Out
{

	public function filterOut(
		array $params,
		array & $dependencies = []
	) : array;
}
