<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\command;

use NgLam2911\ShopUI\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class ShopCommand extends Command implements PluginOwned{
	use PluginOwnedTrait;

	public function __construct(){
		parent::__construct("shop", "OpenShop", null, ["shopui"]);
		$this->setPermission("shopui.command.shop");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if (!$sender instanceof Player){
			$sender->sendMessage("§cPlease use this command in-game");
			return;
		}
		Loader::getInstance()->openShop($sender);
	}
}