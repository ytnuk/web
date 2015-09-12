<?php
namespace Ytnuk\Web\Locale\Form;

use Nette;
use Nextras;
use Ytnuk;

final class Container
	extends Ytnuk\Orm\Form\Container
{

	protected function addProperty(Nextras\Orm\Entity\Reflection\PropertyMetadata $metadata)
	{
		$component = parent::addProperty($metadata);
		if ($component instanceof Nette\Forms\Controls\BaseControl) {
			switch ($metadata->name) {
				case 'web':
				case 'locale':
					$component->setOption(
						'unique',
						TRUE
					);
					break;
				case 'primary':
					$component->setOption(
						'unique',
						'menu'
					);
					break;
			}
		}

		return $component;
	}
}
