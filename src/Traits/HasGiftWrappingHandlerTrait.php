<?php

namespace Heystack\GiftWrapping\Traits;

use Heystack\GiftWrapping\GiftWrappingHandler;

/**
 * Class HasGiftWrappingHandlerTrait
 * @package Heystack\GiftWrapping\Traits
 */
trait HasGiftWrappingHandlerTrait
{
    /**
     * @var \Heystack\GiftWrapping\GiftWrappingHandler
     */
    protected $giftWrappingHandler;

    /**
     * @param \Heystack\GiftWrapping\GiftWrappingHandler $giftWrappingHandler
     */
    public function setGiftWrappingHandler(GiftWrappingHandler $giftWrappingHandler)
    {
        $this->giftWrappingHandler = $giftWrappingHandler;
    }

    /**
     * @return \Heystack\GiftWrapping\GiftWrappingHandler
     */
    public function getGiftWrappingHandler()
    {
        return $this->giftWrappingHandler;
    }
} 