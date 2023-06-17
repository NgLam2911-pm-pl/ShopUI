<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\forms;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use Exception;
use Generator;
use NgLam2911\libasyneco\exceptions\EcoException;
use NgLam2911\ShopUI\elements\Category;
use NgLam2911\ShopUI\elements\ShopItem;
use NgLam2911\ShopUI\Loader;
use NgLam2911\ShopUI\utils\Utils;
use pocketmine\player\Player;

readonly class CategoryForm extends AsyncForm{

	public function __construct(
		private Category $category,
		private Player $player,
		private ?CategoryForm $callback = null
	){
		$this->send();
	}

	protected function asyncSend() : Generator{
		try{
			$balance = yield from Loader::getInstance()->ecoProvider->myMoney($this->player);
		}catch(EcoException){
			Loader::getInstance()->getLogger()->error("Cant get balance infomation from user: " . $this->player->getName());
			$this->player->sendMessage("Something went wrong...");
			return;
		}
		$menuOptions = array_map(fn($element) => Utils::parseElement2MenuOption($element), $this->category->getItems());
		$exitButton = is_null($this->callback)?(new MenuOption("Exit")):(new MenuOption("Back"));
		$menuOptions = array_merge([$exitButton], $menuOptions);
		$form = new MenuForm(
			$this->category->getName(),
			"Your balance: " . $balance . "$",
			$menuOptions,
			function(Player $player, int $selectedOption) : void{
				$this->handleSelection($player, $selectedOption);
			},
			null
		);
		$this->player->sendForm($form);
	}

	/**
	 * @throws Exception
	 */
	public function handleSelection(Player $player, int $selectedOption) : void{
		if ($selectedOption === 0){ //Exit
			if (!is_null($this->callback)){
				$this->callback->send();
			}
			return;
		}
		$element = $this->category->getItems()[$selectedOption - 1];
		if ($element instanceof Category)
			new CategoryForm($element, $player, $this);
		if ($element instanceof ShopItem)
			new ShopItemForm($element, $player, $this);
		else
			throw new Exception("Unknown element type");
	}



}