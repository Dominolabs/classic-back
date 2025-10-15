<?php
/**
 * DiscountBehavior class file.
 */

namespace app\components\cart;

use yii\base\Behavior;

/**
 * Class DiscountBehavior.
 *
 * @package app\components\cart
 */
class DiscountBehavior extends Behavior
{
    public function events()
    {
        return [
            ShoppingCart::EVENT_COST_CALCULATION => 'onCostCalculation',
            CartPositionInterface::EVENT_COST_CALCULATION => 'onCostCalculation',
        ];
    }
    /**
     * @param CostCalculationEvent $event
     */
    public function onCostCalculation($event)
    {
    }
}
