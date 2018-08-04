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

    	public function customForm(Player $player, $wooosh)
    	{
		$form = $this->main->formapi->createCustomForm(function (Player $player, array $data) {
		    if( isset($data[1]) )
		    {
			$pref = $data[1]; //pet name
			$nn = $data[2]; //baby
			$poor = $data[3]; //target name

			if(strlen($pref) <= 0 or strlen($pref) >= 9 ) {
				$player->sendMessage('<•> Error! 1 - 8 characters are allowed');
				return true;
			}

			$baby = 'false';
			if($nn) {
				$baby = 'true';
			}

			$target = null;
			if($poor !== "") {
				$target = $poor;
			}

				$this->main->applyPetRequest($player, $pref, (float) 1.0, $baby, $target);
		    }
			return true;
		});

		$form->setTitle('§l§cPet§f Config');
		//data[0]
		$skadoossh = $this->main->getPrice($wooosh);
		$form->addLabel("§fPet type: $wooosh (§a $skadoossh §f)§r\n§cNote: some pets need you to relogin to be turned into baby, and some has no baby form.");
		//$data[1]
		$form->addInput("§oPetName §8(max of 8 chars.)");
		//$data[2]
		$form->addToggle("§oBaby Form?", false);
		//$data[3]
		$form->addInput("§oGift to a player (optional | auto-complete)");
		$form->sendToPlayer($player);
    	}
	
	public function normalForm(Player $player, $wooosh)
    	{
		$form = $this->main->formapi->createCustomForm(function (Player $player, array $data) {
		    if( isset($data[1]) ) {

			$pref = $data[1]; //pet name
			$poor = $data[2]; //target name

			if(strlen($pref) <= 0 or strlen($pref) >= 9 ) {
			    $player->sendMessage('<•> Error! Only 1 - 8 characters are allowed');
			    return true;
			}

			$target = null;
			if($poor !== "") {
			    $target = $poor;
			}

			$this->main->applyPetRequest($player, $pref, (float) 1.0, "false", $target);
		    }
			return true;	
		});
        
		$form->setTitle('§l§cPet§f Config');
		//data[0]
		$skadoossh = $this->main->getPrice($wooosh);
		$form->addLabel("§fPet type: $wooosh (§a $skadoossh §f)");
		//$data[1]
		$form->addInput("§oPetName §8(max of 8 chars.)§c*");
		//$data[2]
		$form->addInput("§oGift to a player (optional | auto-complete)");
		$form->sendToPlayer($player);
    	}
}
