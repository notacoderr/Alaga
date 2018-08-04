<?php

namespace petui;

use petui\CI;
use pocketmine\Player;

class petUI
{
    public $main;
	
	public function __construct(CI $pg) {
        	$this->main = $pg;
    	}
	
	public function sendMainMenu(Player $player)
    	{
		$form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
		    if (isset($data[0])){
			switch ($data[0])
			{
				case 0:
					$this->sendNormalMenu($player);
				break;

				case 1:
					$player->sendMessage("Feature is in progress");//$this->sendExoMenu($player);
				break;

				case 2:
					if($player->hasPermission("shs.vip.pet"))
					{
						$this->sendVIPMenu($player);
					} else {
						$player->sendMessage("§cPlease visit the store and purchase VIP");
					}
				break;
				default:
					$this->mgt->sendUI($player);			
			}
			return true;
		    }
		});
		$form->setTitle('§l§fPet Store');

		$form->addButton('§l§0Toggle All Pets'); //data[0]
		$form->addButton('§l§0Release A Pet'); //data[1]
		$form->addButton('§l§0Change-name'); //data[2]
		$form->addButton('§l§0Top Pets'); //data[2]

		$form->sendToPlayer($player);
	}
}
