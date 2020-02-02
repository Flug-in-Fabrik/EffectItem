<?php

/**
 * @name EffectItem
 * @main Securti\effectitem\EffectItem
 * @author ["Flug-in-Fabrik", "Securti"]
 * @version 0.1
 * @api 3.10.0
 * @description 버프 아이템을 추가합니다.
 * 해당 플러그인 (EffectItem)은 Fabrik-EULA에 의해 보호됩니다
 * Fabrik-EULA : https://github.com/Flug-in-Fabrik/Fabrik-EULA
 */
 
 namespace Securti\effectitem;
 
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\item\Item;

use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\NetworkLittleEndianNBTStream;

class EffectItem extends PluginBase implements Listener{

  public $prefix = "§l§b[알림] §7";
  
  public function onEnable(){

    $this->getServer()->getPluginManager()->registerEvents($this,$this);
 
    $a = new PluginCommand("버프", $this);
    $a->setPermission("op");
    $a->setUsage("/버프");
    $a->setDescription("버프 관리 명령어입니다");
    $this->getServer()->getCommandMap()->register($this->getDescription()->getName(), $a);
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $array) :bool{
  
    $prefix = $this->prefix;
    
    $player = $sender;
    $name = strtolower($player->getName());
    $inventory = $player->getInventory();
    $item = $inventory->getItemInHand();
    
    $command = $command->getName();
    
    if(!$player instanceof Player) return true;
    
    if($command === "버프"){
    
      if(count($array) == 3){
      
        if(is_numeric($array[0]) and is_numeric($array[1]) and is_numeric($array[2])){
        
          if((int) $array[0] > 0 and (int) $array[1] > 0 and (int) $array[2] > 0){
          
            if($item->getNamedTagEntry("effectitem") == null){
            
              $item->setNamedTagEntry(new StringTag("effectitem", -1));
            }
            
            $item->setNamedTagEntry(new StringTag("effectitem", $item->getNamedTagEntry("effectitem")->getValue() + 1));
            $item->setNamedTagEntry(new StringTag("code".$item->getNamedTagEntry("effectitem")->getValue(), (int) $array[0]));
            $item->setNamedTagEntry(new StringTag("amplification".$item->getNamedTagEntry("effectitem")->getValue(), (int) $array[1]));
            $item->setNamedTagEntry(new StringTag("duration".$item->getNamedTagEntry("effectitem")->getValue(), (int) $array[2]));
            
            $inventory->setItemInHand($item);
            
            $player->sendMessage($prefix."버프 효과를 추가하였습니다");
          }
          else{
          
            $player->sendMessage($prefix."/버프 <포션 코드> <포션 시간> <포션 강도>");
          }
        }
        else{
        
          $player->sendMessage($prefix."/버프 <포션 코드> <포션 강도> <포션 시간>");
        }
      }
      else{
      
        $player->sendMessage($prefix."/버프 <포션 코드> <포션 강도> <포션 시간>");
      }
    }
    
    return true;
  }
  public function onInteract(PlayerInteractEvent $e){
  
    $player = $e->getPlayer();
    
    $item = $e->getItem();
    
    if($item->getNamedTagEntry("effectitem") !== null){
    
      for($i = 0; $i <= $item->getNamedTagEntry("effectitem")->getValue(); $i++){
      
        $effect = $item->getNamedTagEntry("code".$i)->getValue();
        $duration = $item->getNamedTagEntry("duration".$i)->getValue();
        $amplification = $item->getNamedTagEntry("amplification".$i)->getValue();
        
        $instance = new EffectInstance(Effect::getEffect($effect), $duration * 20, $amplification - 1, false);
        $player->addEffect($instance);
      }
    }
  }
}