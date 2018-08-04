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

    public function mainForm(Player $player, $wooosh, $skadoossh)
    {
        $form = $this->main->formapi->createCustomForm(function (Player $player, array $data) {
            $price = 0;
            if( isset($data[1]) ) {
				$pref = $data[1]; //pet name
                $nn = $data[2]; //baby
                $poor = $data[3]; //target name

				if(strlen($pref) <= 0 or strlen($pref) >= 9 ) {
                    $player->sendMessage('<•> Error! 1 - 8 characters are allowed');
                    return true;
				}

                $baby = 'false';
                if($nn) {
                    $price = $price + $this->main->settings->getNested('price.others.baby');
                    $baby = 'true';
                }

                $target = null;
                if($poor !== "") {
                    $target = $poor;
                }

                $this->main->applyPetRequest($player, $pref, (float) 1.0, $baby, $price, $target);
            }
			return true;
			
        });
        
        $form->setTitle('§l§dKawaii §fPet Store');
        //data[0]
        $form->addLabel("§fPet type: $wooosh (§a $skadoossh §f)§r\n§fFields with §c*§f are required.\n§eType the exact name of the player, dont replace spaces or add special characters\n§cNote: some pets need u to relogin to be turned into baby, and some just stay as adult (no baby form).");

        //$data[1]
		$form->addInput("§oPetName §8(max of 8 chars.)§c*");

        //$data[2]
        $form->addToggle("§oBaby Pet? add:§a $" . $this->main->settings->getNested('price.others.baby'), false);

        //$data[3]
        $form->addInput("§oGift to a player (optional | auto-complete)");
        $form->sendToPlayer($player);
    }
}