<?php
namespace Ytnuk\Web\Form;

use Ytnuk;

final class Control
	extends Ytnuk\Orm\Form\Control
{

	public function __construct(
		Ytnuk\Web\Entity $web,
		Ytnuk\Orm\Form\Factory $form
	) {
		parent::__construct($web, $form);
	}
}
