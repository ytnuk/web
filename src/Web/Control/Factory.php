<?php
namespace Ytnuk\Web\Control;

use Ytnuk;

interface Factory
{

	public function create(Ytnuk\Web\Entity $web) : Ytnuk\Web\Control;
}
