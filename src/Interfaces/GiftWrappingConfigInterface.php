<?php

namespace Heystack\GiftWrapping\Interfaces;

/**
 * @package Heystack\GiftWrapping\Interfaces
 */
interface GiftWrappingConfigInterface
{
    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getMessage();
}