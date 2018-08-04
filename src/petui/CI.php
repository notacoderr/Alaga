<?php

namespace petui;

use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{TextFormat, Config};
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\utils\TextFormat as TF;

class CI extends PluginBase implements Listener
{   
    
    public $formapi;
    public $blockpets;
	public $pipol;
    private $typeCache = [];
    /**
    private $mobs = array(
    'bat',
    'blaze',
    'cavespider',
    'chicken',
    'cow',
    'creeper',
    'donkey',
    'enderman',
    'endermite',
    'evoker',
    'ghast',
    'horse',
    'husk',
    'irongolem',
    'llma',
    'mooshroom',
    'ocelot',
    'pig',
    'polarbear',
    'rabbit',
    'sheep',
    'skeletonhorse',
    'skeleton',
    'slime',
    'spider',
    'vex',
    'wolf',
    'zombie',
    'zombiehorse',
    'zombiepigman',
    'zombievillager',
    'elderguardian',
    'wither',
    'enderdragon'

    );
    */

    public function onEnable()
    {

        $this->formapi = $this->getServer()->getPluginManager()->getPlugin('FormAPI');
        $this->blockpets = $this->getServer()->getPluginManager()->getPlugin('BlockPets');
        
        $this->ui = new petUI($this);

        $this->getLogger()->info("Pets are leashed..");

        $this->saveResource('main.yml');
        $this->settings = new Config($this->getDataFolder() . "main.yml", CONFIG::YAML);

        $this->saveResource('petcount.yml');
        $this->pcount = new Config($this->getDataFolder() . "petcount.yml", CONFIG::YAML);
    }
	
    public function runCMD(string $c) : void
    {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $c);
    }

    public function sendNormalMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
            if (isset($data[0])){
                #$type = $this->mobs[ $data[0] ];
		switch ($data[0])
		{
			case 0:
				$this->storeTypeCache($player, "wolf");
                        	$this->ui->normalForm($player, "wolf");
			break;
				
			case 1:
				$this->storeTypeCache($player, "ocelot");
                        	$this->ui->normalForm($player, "ocelot");
			break;
			
			case 2:
				$this->storeTypeCache($player, "pig");
                        	$this->ui->normalForm($player, "pig");
			break;
				
			case 3:
				$this->storeTypeCache($player, "rabbit");
                        	$this->ui->normalForm($player, "rabbit");
			break;
			
			case 4:
				$this->storeTypeCache($player, "cavespider");
                        	$this->ui->normalForm($player, "cavespider");
			break
				
		}
               	return true;
            }
        });
        $form->setTitle('§l§fPet Store');
	    
	$form->addButton('§l§0Dog : §c$' . $this->getPrice("wolf")); //data[0]
	$form->addButton('§l§0Cat : §c$' . $this->getPrice("ocelot")); //data[1]
	$form->addButton('§l§0Pig : §c$' . $this->getPrice("pig")); //data[2]
	$form->addButton('§l§0Bunny : §c$' . $this->getPrice("rabbit")); //data[3]
	$form->addButton('§l§0Cave Spider : §c$' . $this->getPrice("cavespider")); //data[4]

        $form->sendToPlayer($player);
    }
	
    public function sendVIPMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
            if (isset($data[0])){
		switch ($data[0])
		{
			case 0:
				$this->storeTypeCache($player, "vex");
                        	$this->ui->customForm($player, "vex");
			break;
				
			case 1:
				$this->storeTypeCache($player, "ghast");
                        	$this->ui->customForm($player, "ghast");
			break;
			
			case 2:
				$this->storeTypeCache($player, "wither");
                        	$this->ui->customForm($player, "wither");
			break;
				
			case 3:
				$this->storeTypeCache($player, "enderdragon");
                        	$this->ui->customForm($player, "enderdragon");
			break;
				
		}
               	return true;
            }
        });
        $form->setTitle('§l§fPet Store');
	    
	$form->addButton('§l§0Vex : §c$' . $this->getPrice("vex")); //data[0]
	$form->addButton('§l§0Ghast : §c$' . $this->getPrice("ghast")); //data[1]
	$form->addButton('§l§0Wither : §c$' . $this->getPrice("wither")); //data[2]
	$form->addButton('§l§0Dragon : §c$' . $this->getPrice("enderdragon")); //data[2]

        $form->sendToPlayer($player);
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

			#case 1:
                        	#$player->sendMessage("Feature is in progress");//$this->sendExoMenu($player);
			#break;

			case 1:
				if($player->hasPermission("shs.vip.pet"))
				{
					$this->sendVIPMenu($player);
				} else {
					$player->sendMessage("§cPlease visit the store and purchase VIP");
				}
			break;		
		}
		return true;
            }
        });
        $form->setTitle('§l§fPet Store');
	    
        $form->addButton('§l§0Normal PetStore'); //data[0]
	$form->addButton('§l§cV§fI§cP §0PetStore'); //data[1]
	
        $form->sendToPlayer($player);
		
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool
    {
	  	if(!$sender instanceof Player){
		  	$sender->sendMessage("Command must be run ingame!");
		 	return true;
	  	}

	  	switch(strtolower($cmd->getName())){
		case "buypet":
                $petcount = count($this->blockpets->getPetsFrom($sender));
		$this->sendMainMenu($sender);
		$this->removeType($sender);
            break;
      }
        return true;
	}

    private function storeTypeCache(Player $player, $type): void
    {
        $this->typeCache[ $player->getName() ] = $type;
    }

    public function getType(Player $player): string
    {
        if(array_key_exists($player->getName(), $this->typeCache))
        {
            return $this->typeCache[ $player->getName() ];
        }
    }

    public function removeType(Player $player): void
    {
        if(array_key_exists($player->getName(), $this->typeCache))
        {
            unset( $this->typeCache[$player->getName()] );
        }
    }

    public function getPrice($type): int
    {
       return $this->settings->getNested("price.pets.". $type);
    }

    public function applyPetRequest(Player $player, string $petname, float $size,string $baby, string $target = null)
    {
        $eco = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
        $type = $this->getType($player);
        $petprice = $this->getPrice($type);
        $pmoney = $eco->mymoney($player->getName());
        //player = Player sender , human = Player target, target = string target, plname = string sender

        if($target !== null)
        {
            $human = $this->getServer()->getPlayer($target);
            if(!$human instanceof Player) //check if the target is a Player / Online
            {
                $player->addTitle("§l§fPlayer§c Offline", "§f§lFailed! please check the name: " . $target);
                    $this->removeType($player);
                        return true;
            }

            if($pmoney < $petprice) //checks if player can buy
            {
                $need = (int) $petprice - $pmoney;
                    $player->addTitle("§l§fTransact§c Failed", "§f§lYou need §c$" . $need);
                        $this->removeType($player);
                            return true;
            }
            
            $petcount = count( $this->blockpets->getPetsFrom($human) );

            if( $petcount >= $this->settings->get('maxpets')) //checks if the player reached the max
            {
                $player->addTitle("§l§fTransact§c Failed", "§f§lThat player has max owned pet");
                    $this->removeType($player);
                        return true;
            }

            $target = '"'. $target . '"';
            $this->runCMD("spawnpet " .$type. " " .$petname. " " .$size. " " .$baby." ".$target);
            $human->addTitle("§l§fPet Delivery", "§f§l".$player->getName()." bought a(n) $type for you");
            $player->addTitle("§l§bSuccess!", "§f§lYou bought a(n) $type for§b $target");
            $eco->reducemoney($player->getName(), $petprice);
            $this->removeType($player);

            return true;

        } else {
            $petcount = count( $this->blockpets->getPetsFrom($player) );
            if( $petcount >= $this->settings->get('maxpets') && !$player->hasPermission('bypass.maxpet'))
            {
                $player->addTitle("§l§fTransact§c Failed", "§f§lYou already reached pet limit");
                    $this->removeType($player);
                        return true;
            }

            if($pmoney < $petprice)
            {
                $need = (int) $petprice - $pmoney;
                    $player->addTitle("§l§dKawaii§f Pets", "§f§lYou need §c$" . $need);
                        $this->removeType($player);
                            return true;
            }

            $plname = '"'. $player->getName() . '"';
            $this->runCMD("spawnpet " .$type. " " .$petname. " " .$size. " " .$baby." ".$plname);
            $player->addTitle("§l§bSuccess!", "§f§lYou bought a(n) $type named§b $petname");
            $eco->reducemoney($player->getName(), $petprice);
            $this->removeType($player);

            return true;
        }
    }
}
