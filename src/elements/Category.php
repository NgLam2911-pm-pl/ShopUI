<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\elements;

class Category implements Element{

	/** @var Element[] */
	private array $items = [];

	public function __construct(
		private readonly string $name,
		private readonly string $icon
	){}

	public function getName(): string{
		return $this->name;
	}

	public function getIcon(): string{
		return $this->icon;
	}

	/**
	 * @return Element[]
	 */
	public function getItems(): array{
		return $this->items;
	}

	public function addItem(Element $item): void{
		$this->items[] = $item;
	}

	public function addItems(Element ...$items): void{
		foreach($items as $item){
			$this->addItem($item);
		}
	}

	public function removeItem(Element $item): void{
		$key = array_search($item, $this->items);
		if($key !== false){
			unset($this->items[$key]);
		}
	}
}