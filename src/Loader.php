<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI;

use NgLam2911\libasyneco\exceptions\DependencyMissingException;
use NgLam2911\libasyneco\exceptions\InvalidProviderException;
use NgLam2911\libasyneco\libasyneco;
use NgLam2911\libasyneco\providers\EcoProvider;
use NgLam2911\ShopUI\command\ShopCommand;
use NgLam2911\ShopUI\elements\Category;
use NgLam2911\ShopUI\forms\CategoryForm;
use NgLam2911\ShopUI\utils\ShopConfigParser;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase{
	use SingletonTrait;

	private Category $mainCategory;

	public EcoProvider $ecoProvider;

	public function onLoad() : void{
		self::setInstance($this);
	}

	/**
	 * @throws InvalidProviderException
	 * @throws DependencyMissingException
	 */
	public function onEnable(): void{
		$this->saveResource("shop.yml");
		$this->saveResource("config.yml");
		$configParser = new ShopConfigParser($this->getDataFolder() . "shop.yml");
		$this->mainCategory = new Category("Shop", "");
		foreach($configParser->parse() as $element){
			$this->mainCategory->addItem($element);
		}
		$this->ecoProvider = libasyneco::init($this->getConfig()->get("economy-provider")); //TODO: get from config

		$this->getServer()->getCommandMap()->register("shop", new ShopCommand());
	}

	public function openShop(Player $player) : void{
		new CategoryForm($this->mainCategory, $player);
	}
}