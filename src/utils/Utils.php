<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\utils;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuOption;
use InvalidArgumentException;
use NgLam2911\ShopUI\elements\Category;
use NgLam2911\ShopUI\elements\Element;
use NgLam2911\ShopUI\elements\ShopItem;

class Utils{

	public static function getIconType(string $icon): string{
		if (str_contains($icon, "http")) {
			return FormIcon::IMAGE_TYPE_URL;
		} else {
			return FormIcon::IMAGE_TYPE_PATH;
		}
	}

	public static function parseElement2MenuOption(Element $element): MenuOption{
		if ($element instanceof Category){
			return new MenuOption(
				$element->getName(),
				new FormIcon($element->getIcon(), Utils::getIconType($element->getIcon()))
			);
		}
		if ($element instanceof ShopItem){
			return new MenuOption(
				implode("\n", [
					$element->getItem()->getName(),
					"Buy: " . $element->getBuyPrice() . "|Sell: " . $element->getSellPrice()
				]),
				new FormIcon($element->getIcon(), Utils::getIconType($element->getIcon()))
			);
		}
		throw new InvalidArgumentException("Invalid element");
	}
}