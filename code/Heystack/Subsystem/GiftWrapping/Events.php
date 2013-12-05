<?php
/**
 * This file is part of the Ecommerce-GiftWrapping package
 *
 * @package Ecommerce-GiftWrapping
 */

/**
 * GiftWrapping namespace
 */
namespace Heystack\Subsystem\GiftWrapping;

/**
 * Events holds constant references to triggerable dispatch events.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-GiftWrapping
 * @see Symfony\Component\EventDispatcher
 *
 */
final class Events
{
    /**
     * Indicates that the GiftWrappingHandler's total has been updated
     */
    const TOTAL_UPDATED       = 'giftwrapping.totalupdated';

}
