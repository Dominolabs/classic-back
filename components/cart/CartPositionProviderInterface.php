<?php
/**
 * CartPositionProviderInterface class file.
 */

namespace app\components\cart;

/**
 * Interface CartPositionProviderInterface.
 *
 * @property CartPositionInterface $cartPosition
 * @package app\components\cart
 */
interface CartPositionProviderInterface
{
    /**
     * @param array $params Parameters for cart position
     * @return CartPositionInterface
     */
    public function getCartPosition($params = []);
}
