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

namespace Kitchen\boymelancholy\kitchen;

use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\Server;

/**
 * 負のIDのアイテムを除いたレシピへ
 * Convert to easily-using recipes without including these items: (id < 0)
 *
 * Class Kitchen
 * @package Kitchen\boymelancholy\kitchen
 * @author boymelancholy
 */
class Kitchen {

    /** @var Item */
    private $baseItem;

    /** @var ShapedRecipe[] */
    private $recipeNotes = [];

    /** @var array */
    private $validIngredients = [];

    /** @var Item[] */
    private $validResults = [];

    public function __construct(Item $item) {
        $this->baseItem = $item;
        $craft = Server::getInstance()->getCraftingManager();
        $recipe = $craft->matchRecipeByOutputs([$item]);
        while (true) {
            $recipes = $recipe->current();
            if ($recipes == null) {
                break;
            }
            $this->recipeNotes[] = $recipes;
            $recipe->next();
        }
    }

    /**
     * レシピノートを返却
     * Return recipe note
     *
     * @return ShapedRecipe[]
     */
    public function getRecipeNote() : array {
        return $this->recipeNotes;
    }

    /**
     * 有効なアイテムたちの提供
     * Get valid items
     *
     * @return Item[]
     */
    public function getAllValidResults() : array {
        return $this->validResults;
    }

    /**
     * 有効な素材たちの提示
     * Get valid ingredients
     *
     * @return Item[]
     */
    public function getAllValidIngredients() : array {
        return $this->validIngredients;
    }

    /**
     * $index番目の素材を取得。ない場合はnull
     * Get the item of $index (If it's empty, return null)
     *
     * @param int $index
     * @return Item|null
     */
    public function getValidResult(int $index = 0) : ?Item {
        try {
            return $this->validResults[$index];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * $index番目の素材を取得。ない場合はnull
     * Get the ingredient of $index (If it's empty, return null)
     *
     * @param int $index
     * @return Item[]|null
     */
    public function getValidIngredient(int $index = 0) : ?array {
        try {
            return $this->validIngredients[$index];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * アイテムの調理
     * Cook the item!!
     *
     * @return void
     */
    public function cooking() : void {
        foreach ($this->recipeNotes as $note) {
            if ($note instanceof ShapedRecipe) {
                $results = $note->getResults();
                $result = array_shift($results);
                $materials = $note->getIngredientList();
                foreach ($materials as $material) {
                    if ($material->getId() < 0) {
                        continue 2;
                    }
                    if ($material->getName() === 'Unknown') {
                        continue 2;
                    }
                }
                $this->validResults[] = $result;
                $this->validIngredients[] = $materials;
            }
        }
    }

    /**
     * 素材たちを綺麗な状態にする
     * Convert to flesh items
     *
     * @param Item[] $items
     * @return Item[]
     */
    public function convertFleshIngredients(array $items) : array {
        $fleshMaterials = [];
        $materialCount = [];
        foreach ($items as $dirty) {
            if (isset($materialCount[$dirty->getId()])) {
                $materialCount[$dirty->getId()] += 1;
            } else {
                $materialCount[$dirty->getId()] = 1;
            }
        }
        foreach ($materialCount as $key => $val) {
            $fleshMaterials[] = Item::get($key, 0, $val);
        }
        return $fleshMaterials;
    }
}