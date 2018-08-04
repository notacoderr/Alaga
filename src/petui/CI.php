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

    public function sendMainMenu(Player $player)
    {
        $form = $this->formapi->createSimpleForm(function (Player $player, array $data) {
            if (isset($data[0])){
                $type = $this->mobs[ $data[0] ];
                if (is_string($type))
                {
                    $this->storeTypeCache($player, strtolower($type));
                    $this->ui->mainForm($player, $type, $this->getPrice($type));
                } else {
                    $player->sendMessage("§4§lAn ERROR has OCCURED, please report to an Admin ASAP");
                }
                
				return true;
            }
        });
        $form->setTitle('§l§dKawaii §fPet Store');

        foreach ($this->mobs as $x) {
            $form->addButton('§l§0'. strtoupper($x) .' : §c$' . $this->getPrice($x)); //data[0]
        }

        $this->removeType($player);
        $form->sendToPlayer($player);
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool
    {
	  	if(!$sender instanceof Player){
		  	$sender->sendMessage("Command must be run ingame!");
		 	return true;
	  	}

	  	switch(strtolower($cmd->getName())){
            case "buypetui":
                $petcount = count($this->blockpets->getPetsFrom($sender));
                //if( $petcount >= $this->settings->get('maxpets'))
                //{
                //    $sender->addTitle("§l§cError", "§f§lYou already reached pet owned limit");
                //    return true;
                //}//two way check if the player has reached the max owned pet

				$this->sendMainMenu($sender);
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

    public function applyPetRequest(Player $player, string $petname, float $size,string $baby,int $price, string $target = null)
    {
        $eco = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
        $type = $this->getType($player);
        $petprice = $this->getPrice($type) + $price;
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
                    $player->addTitle("§l§dKawaii§f Pets", "§f§lYou need §c$" . $need);
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
            $human->addTitle("§l§fPet Gift", "§f§l".$player->getName()." bought a(n) $type for you");
            $player->addTitle("§l§dKawaii§f Pets", "§f§lYou bought a(n) $type for§b $target");
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
            $player->addTitle("§l§dKawaii§f Pets", "§f§lYou bought a(n) $type named§b $petname");
            $eco->reducemoney($player->getName(), $petprice);
            $this->removeType($player);

            return true;
        }
    }
}
