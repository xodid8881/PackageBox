<?php
declare(strict_types=1);

namespace PackageBox\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use PackageBox\PackageBox;
use pocketmine\permission\DefaultPermissions;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class PackageBoxSettingCommand extends Command
{

  protected $plugin;
  private $chat;
  public function __construct(PackageBox $plugin)
  {
    $this->plugin = $plugin;
    parent::__construct('패키지상자', '패키지상자를 관리하는 명령어 합니다.', '/패키지상자');
  }

  public function execute(CommandSender $sender, string $commandLabel, array $args)
  {
    $name = $sender->getName ();
    if (!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
      $sender->sendMessage($this->plugin->tag()."권한이 없습니다.");
      return true;
    }
    if( ! isset($args[0] )){
      $sender->sendMessage ($this->plugin->tag());
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 생성 ( 패키지상자이름 ) < 패키지상자를 생성합니다. >");
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 목록 < 패키지상자를 목록을 불러옵니다. >");
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 세팅 ( 패키지상자이름 ) < 패키지상자를 세팅합니다. >");
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 세팅정보 ( 패키지상자이름 ) < 패키지상자에 세팅 정보를 불러옵니다. >");
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 삭제 ( 패키지상자이름 ) < 패키지상자를 삭제합니다. >");
      $sender->sendMessage ($this->plugin->tag()."/패키지상자 불러오기 ( 패키지상자이름 ) < 패키지상자를 불러옵니다. >");
      return true;
    }
    switch ($args [0]) {
      case "생성" :
      if (isset($args[1])) {
        if (isset($this->plugin->boxdb [$args[1]])){
          $sender->sendMessage ($this->plugin->tag()."이미 해당 이름으로 패키지상자가 만들어져 있습니다.");
        }
        $this->plugin->boxdb [$args[1]] = [];
        $this->plugin->save ();
        $sender->sendMessage ($this->plugin->tag(). $args[1] ."패키지상자가 생성되었습니다.");
        return true;
      } else {
        $sender->sendMessage ($this->plugin->tag()."/패키지상자 생성 ( 패키지상자이름 ) < 패키지상자를 생성합니다. >");
        return true;
      }
      break;
      case "목록" :
      $sender->sendMessage ($this->tag());
      foreach ( $this->plugin->boxdb as $boxname => $tp ) {
        $sender->sendMessage ($this->tag() . $boxname);
      }
      break;
      case "세팅" :
      if (isset($args[1])) {
        if (isset($this->plugin->boxdb [$args[1]])){
          if (! isset ( $this->chat [$name] )) {
            $this->plugin->pldb [strtolower($name)] ["BoxName"] = $args[1];
            $this->plugin->save ();
            $this->plugin->BoxSettingGUI($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
          }
          if (date("YmdHis") - $this->chat [$name] < 3) {
            $sender->sendMessage ( $this->plugin->tag() . "이용 쿨타임이 지나지 않아 불가능합니다." );
            return true;
          } else {
            $this->plugin->pldb [strtolower($name)] ["BoxName"] = $args[1];
            $this->plugin->save ();
            $this->plugin->BoxSettingGUI($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
          }
        } else {
          $sender->sendMessage ($this->plugin->tag(). $args[1] . "라는 패키지상자가 존재하지 않습니다.");
          return true;
        }
      } else {
        $sender->sendMessage ($this->plugin->tag()."/패키지상자 세팅 ( 패키지상자이름 ) < 패키지상자를 세팅합니다. >");
        return true;
      }
      break;
      case "세팅정보" :
      if (isset($args[1])) {
        if (isset($this->plugin->boxdb [$args[1]])){
          if (! isset ( $this->chat [$name] )) {
            $this->plugin->pldb [strtolower($name)] ["BoxName"] = $args[1];
            $this->plugin->save ();
            $this->plugin->BoxSeeSettingGUI($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
          }
          if (date("YmdHis") - $this->chat [$name] < 3) {
            $sender->sendMessage ( $this->plugin->tag() . "이용 쿨타임이 지나지 않아 불가능합니다." );
            return true;
          } else {
            $this->plugin->pldb [strtolower($name)] ["BoxName"] = $args[1];
            $this->plugin->save ();
            $this->plugin->BoxSeeSettingGUI($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
          }
        } else {
          $sender->sendMessage ($this->plugin->tag(). $args[1] . "라는 패키지상자가 존재하지 않습니다.");
          return true;
        }
      } else {
        $sender->sendMessage ($this->plugin->tag()."/패키지상자 세팅정보 ( 패키지상자이름 ) < 패키지상자에 세팅 정보를 불러옵니다. >");
        return true;
      }
      break;
      case "삭제" :
      if (isset($args[1])) {
        if (!isset($this->plugin->boxdb [$args[1]])){
          $sender->sendMessage ($this->plugin->tag(). $args[1] . "라는 패키지상자가 존재하지 않습니다.");
        }
        unset($this->plugin->boxdb [$args[1]]);
        $this->plugin->save ();
        $sender->sendMessage ($this->plugin->tag(). $args[1] ."패키지상자가 삭제되었습니다.");
        return true;
      } else {
        $sender->sendMessage ($this->plugin->tag()."/패키지상자 삭제 ( 패키지상자이름 ) < 패키지상자를 삭제합니다. >");
        return true;
      }
      break;
      case "불러오기" :
      if (isset($args[1])) {
        if (!isset($this->plugin->boxdb [$args[1]])){
          $sender->sendMessage ($this->plugin->tag(). $args[1] . "라는 패키지상자가 존재하지 않습니다.");
          return true;
        }
        $item = ItemFactory::getInstance()->get(54, 0, 1)->setCustomName("$args[1]|패키지상자");
        $sender->getInventory()->addItem($item);
        $sender->sendMessage ($this->plugin->tag(). $args[1] ."패키지상자를 불러왔습니다.");
        return true;
      } else {
        $sender->sendMessage ($this->plugin->tag()."/패키지상자 불러오기 ( 패키지상자이름 ) < 패키지상자를 불러옵니다. >");
        return true;
      }
      break;
    }
  }

}
