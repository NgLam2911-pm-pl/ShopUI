<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\forms;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use Generator;
use NgLam2911\libasyneco\exceptions\EcoException;
use NgLam2911\ShopUI\elements\ShopItem;
use NgLam2911\ShopUI\Loader;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

readonly class ShopItemForm extends AsyncForm{

	public function __construct(
		private ShopItem $shopItem,
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
		$form = new CustomForm(
			$this->shopItem->getItem()->getName(),
			[
				new Label("buy_price", "Buy: " . $this->shopItem->getBuyPrice()),
				new Label("sell_price", "Sell: " . $this->shopItem->getSellPrice()),
				new Label("balance", "Your balance: " . $balance . "$"),
				new Toggle("option", "Buy/Sell", false),
				new Input("amount", "Amount", "123456789")
			],
			function(Player $player, CustomFormResponse $response): void{
				Await::g2c($this->handleResponse($player, $response));
			},
			function(Player $player): void{
				$this->callback?->send();
			}
		);
		$this->player->sendForm($form);
	}

	/**
	 * @param Player             $player
	 * @param CustomFormResponse $response
	 *
	 * @return Generator<void>
	 */
	public function handleResponse(Player $player, CustomFormResponse $response) : Generator{
		$option = $response->getBool("option");
		$amount = $response->getString("amount");
		if (!is_numeric($amount)){
			$player->sendMessage("Amount must be a number");
			return;
		}
		$amount = (int) $amount;
		if ($amount <= 0){
			$player->sendMessage("Amount must be greater than 0");
			return;
		}
		if (!$option){ //Buy
			if ($player->getInventory()->canAddItem($this->shopItem->getItem()->setCount($amount)) === false){
				$player->sendMessage("You dont have enough space in your inventory");
				return;
			}
			try{
				yield from Loader::getInstance()->ecoProvider->takeMoney($player, $this->shopItem->getBuyPrice() * $amount);
			}catch(EcoException){
				$player->sendMessage("You dont have enough money to buy this item");
				return;
			}
			$player->getInventory()->addItem($this->shopItem->getItem()->setCount($amount));
			$player->sendMessage("You have bought x" . $amount . " " . $this->shopItem->getItem()->getName());
			return;
		}
		//Sell
		$inventory = $player->getInventory();
		//Check if player have enough item in inventory
		if ($inventory->contains($this->shopItem->getItem()->setCount($amount)) === false){
			$player->sendMessage("You dont have enough item to sell");
			return;
		}
		//Add money to player
		try{
			yield from Loader::getInstance()->ecoProvider->addMoney($player, $this->shopItem->getSellPrice() * $amount);
		}catch(EcoException){
			$player->sendMessage("Failed to give money to your account");
			return;
		}
		//Remove item from inventory
		$inventory->removeItem($this->shopItem->getItem()->setCount($amount));
		$player->sendMessage("You have sold x" . $amount . " " . $this->shopItem->getItem()->getName());
	}
}