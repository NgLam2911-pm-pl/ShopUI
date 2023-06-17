<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\utils;

use InvalidArgumentException;
use NgLam2911\ShopUI\elements\Category;
use NgLam2911\ShopUI\elements\Element;
use NgLam2911\ShopUI\elements\ShopItem;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;

class ShopConfigParser{

	private Config $shopConfig;

	public function __construct(string $path){
		$this->shopConfig = new Config($path, Config::YAML);
	}

	/**
	 * @return Element[]
	 */
	public function parse(): array{
		$elements = [];
		$data = $this->shopConfig->getAll();
		foreach($data as $element){
			$elements[] = $this->parseElement($element);
		}
		return $elements;
	}

	private function parseElement(array $data): Element{
		if (isset($data["category"])){
			return $this->parseCategory($data);
		}elseif(isset($data["item"])){
			return $this->parseShopItem($data);
		}else{
			throw new InvalidArgumentException("Invalid element data at config");
		}
	}

	private function parseCategory(array $data): Category{
		if (isset($data["category"], $data["icon"], $data["items"])){
			throw new InvalidArgumentException("Invalid category data at category {$data["name"]}");
		}
		$category = new Category($data["category"], $data["icon"]);
		foreach($data["items"] as $item){
			$category->addItem($this->parseElement($item));
		}
		return $category;
	}

	private function parseShopItem(array $data): ShopItem{
		if (isset($data["item"], $data["buy_price"], $data["sell_price"], $data["icon"])){
			throw new InvalidArgumentException("Invalid shop item data at item {$data["item"]}");
		}
		$item = StringToItemParser::getInstance()->parse($data["item"]);
		if (is_null($item)){
			throw new InvalidArgumentException("Invalid item {$data["item"]}");
		}
		if (is_numeric($data["buy_price"])){
			throw new InvalidArgumentException("Invalid buy price {$data["buy_price"]} at item {$data["item"]}");
		}
		$buy_price = (float) $data["buy_price"];
		if ($buy_price < 0){
			throw new InvalidArgumentException("Buy price must be greater than 0 at item {$data["item"]}");
		}
		if (is_numeric($data["sell_price"])){
			throw new InvalidArgumentException("Invalid sell price {$data["sell_price"]} at item {$data["item"]}");
		}
		$sell_price = (float) $data["sell_price"];
		if ($sell_price < 0){
			throw new InvalidArgumentException("Sell price must be greater than 0 at item {$data["item"]}");
		}
		return new ShopItem($item, $buy_price, $sell_price, $data["icon"]);
	}
}