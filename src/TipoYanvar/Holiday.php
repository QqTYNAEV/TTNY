<?php

// VK -> @qq_tynaev
// GitHub -> QqTYNAEV

namespace TipoYanvar;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;
use pocketmine\event\Listener;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Holiday extends PluginBase implements Listener{
	
	public function onEnable(){
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    @mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->c = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$this->task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask(array($this, "timeChek")), 20);
		//$this->task = $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask(array($this, "timeChek")), 20);
	}
	
	public function timeChek(){
	    if(count($this->getServer()->getOnlinePlayers()) <= 0){
	        return;
	    }
        $timer = strtotime("1 January 2023") - time();
        $days = floor($timer / (24 * 60 * 60));
        $hours = floor(($timer % (24 * 60 * 60)) / (60 * 60));
        $minutes = floor(($timer % (60 * 60)) / 60);
        $seconds = $timer % 60;
	    //$this->getLogger()->info("{$days} дней, {$hours} часов, {$minutes} минут, {$seconds} секунд\n{$timer}");
	    $xyz = explode(" ", $this->c->get("xyz"));
	    $coords = new Vector3($xyz[0], $xyz[1], $xyz[2]);
        if($timer <= -1){
            $this->task->remove();
            $text = new FloatingTextParticle($coords, "", $this->c->get("holiday"));
            $this->getServer()->getDefaultLevel()->addParticle($text);
            return;
        }
	    $text = new FloatingTextParticle($coords, "", str_replace(["{days}", "{hours}", "{minutes}", "{seconds}"], [$days, $hours, $minutes, $seconds], $this->c->get("text")));
	    $this->getServer()->getDefaultLevel()->addParticle($text);
	    $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask(array($this, "disableText"), array($text, $this->getServer()->getDefaultLevel())), 20);
	}
	
	public function disableText($text, $level){
	    $text->setInvisible();
	    $level->addParticle($text);
	}
	
	/*public function DvaPoRusskomu($msg){
	    // Всё блин, я сдаюсь
	}*/
	
	public function onCommand(CommandSender $s, Command $cmd, $label, array $args){
	    if($cmd->getName() == "timer-ny"){
	        if($s instanceof Player){
	            $x = $s->getFloorX();
	            $y = $s->getFloorY() + 2;
	            $z = $s->getFloorZ();
	            $this->c->set("xyz", "{$x} {$y} {$z}");
	            $this->c->save();
	            $s->sendMessage("§a»§f Координаты таймера успешно изменены");
	        }else{
	            $s->sendMessage("§c»§f Ввод доступен только в игре");
	        }
	    }
	}
}