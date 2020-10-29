<?php

/**
 *        888    d8P   d8b  888              888
 *        888   d8P    Y8P  888              888
 *        888  d8P          888              888
 *        888d88K      888  888888  .d8888b  88888b.    .d88b.   88888b.
 *        8888888b     888  888    d88P"     888 "88b  d8P  Y8b  888 "88b
 *        888  Y88b    888  888    888       888  888  88888888  888  888
 *        888   Y88b   888  Y88b.  Y88b.     888  888  Y8b.      888  888
 *        888    Y88b  888   "Y888  "Y8888P  888  888   "Y8888   888  888
 *
 *          This program is to get recipes from item in "PocketMine-MP"
 *
 *          author: boymelancholy
 *          contact: https://twitter.com/b0ymelancholy/
 *          repository: https://github.com/boymelancholy/Kitchen
 */

declare(strict_types=1);

namespace boymelancholy\kitchen;

use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Class Tray
 * @package boymelancholy\kitchen
 * @author boymelancholy
 */
class Tray {

    public const PAYMENT = 0;
    public const SERVING = 1;

    private static $trayService;

    /**
     * オーダーの設定
     *
     * @param Player $player
     * @param Item $item
     * @param array $items
     */
    public static function setOrder(Player $player, Item $item, array $items) : void {
        $id = $player->getUniqueId()->__toString();
        self::$trayService[$id][] = $item;
        self::$trayService[$id][] = $items;
    }

    /**
     * オーダーがあるか
     *
     * @param Player $player
     * @return bool
     */
    public static function isOrdered(Player $player) : bool {
        $id = $player->getUniqueId()->__toString();
        return isset(self::$trayService[$id]);
    }

    /**
     * 消費するアイテムを取得
     *
     * @param Player $player
     * @return Item|null
     */
    public static function getPayment(Player $player) : ?Item {
        $id = $player->getUniqueId()->__toString();
        try {
            return self::$trayService[$id][self::PAYMENT];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 受け取る素材の取得
     *
     * @param Player $player
     * @return Item[]
     */
    public static function getServing(Player $player) : array {
        $id = $player->getUniqueId()->__toString();
        try {
            return self::$trayService[$id][self::SERVING];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * オーダーの削除
     *
     * @param Player $player
     * @return void
     */
    public static function cancel(Player $player) : void {
        $id = $player->getUniqueId()->__toString();
        unset(self::$trayService[$id]);
    }

}