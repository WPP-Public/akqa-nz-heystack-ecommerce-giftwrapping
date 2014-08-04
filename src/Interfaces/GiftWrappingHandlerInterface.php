<?php

namespace Heystack\GiftWrapping\Interfaces;

use Heystack\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

/**
 * Interface GiftWrappingHandlerInterface
 * @package Heystack\GiftWrapping\Interfaces
 */
interface GiftWrappingHandlerInterface extends TransactionModifierInterface
{
    /**
     *
     */
    const CONFIG_PRICE_KEY = 'config-price';
    /**
     *
     */
    const CONFIG_MESSAGE_KEY = 'config-message';

    /**
     * @param bool $active
     * @return void
     */
    public function setActive($active);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return void
     */
    public function updateTotal();

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getCost();

    /**
     * @return string
     */
    public function getMessage();
}