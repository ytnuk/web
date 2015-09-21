<?php
namespace Ytnuk\Web\Form\Control;

use Ytnuk;

interface Factory
{

	public function create(Ytnuk\Web\Entity $web) : Ytnuk\Web\Form\Control;
}
