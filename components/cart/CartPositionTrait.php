<?php
/**
 * CartPositionTrait class file.
 */

namespace app\components\cart;

use yii\base\Component;

/**
 * @property int $quantity
 * @property array $mainIngredients
 * @property array $additionalIngredients
 * @property array $properties
 * @property int $size
 * @property int $cost
 */
trait CartPositionTrait
{
    /**
     * @var int
     */
    protected $quantity;
    
    /**
     * @var array
     */
    protected $mainIngredients = [];
    
    /**
     * @var array
     */
    protected $additionalIngredients = [];

    /**
     * @var array
     */
    protected $productProperties = [];

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $type;


    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getMainIngredients()
    {
        return $this->mainIngredients;
    }

    /**
     * @param array $ingredients
     */
    public function setMainIngredients($ingredients): void
    {
        $this->mainIngredients = $ingredients;
    }

    /**
     * @return mixed
     */
    public function getAdditionalIngredients()
    {
        return $this->additionalIngredients;
    }

    /**
     * @param array $ingredients
     */
    public function setAdditionalIngredients($ingredients): void
    {
        $this->additionalIngredients = $ingredients;
    }

    /**
     * @return mixed
     */
    public function getProductProperties()
    {
        return $this->productProperties;
    }

    /**
     * @param array $properties
     */
    public function setProductProperties($properties): void
    {
        $this->productProperties = $properties;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param bool $withDiscount
     * @return int
     */
    public function getCost($withDiscount = true): int
    {
        /** @var Component|CartPositionInterface|self $this */
        $cost = $this->getQuantity() * $this->getPrice();

        if($this->type !== 'classic' && !$this->isPizza()){
            $cost += $this->getIngredientsTotalValue();
        }

        $costEvent = new CostCalculationEvent([
            'baseCost' => $cost,
        ]);

        if ($this instanceof Component) {
            $this->trigger(CartPositionInterface::EVENT_COST_CALCULATION, $costEvent);
        }

        if ($withDiscount) {
            $cost = max(0, $cost - $costEvent->discountValue);
        }

        return $cost;
    }
}
