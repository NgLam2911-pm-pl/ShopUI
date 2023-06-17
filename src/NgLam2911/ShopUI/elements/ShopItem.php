<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\elements;

use pocketmine\item\Item;

readonly class ShopItem implements Element{

	public function __construct(
		private Item $item,
		private float $buy_price,
		private float $sell_price,
		private string $icon
	){}

	public function getBuyPrice() : float{
		return $this->buy_price;
	}

	public function getSellPrice() : float{
		return $this->sell_price;
	}

	public function getItem() : Item{
		return $this->item;
	}

	public function getIcon() : string{
		return $this->icon;
	}
}