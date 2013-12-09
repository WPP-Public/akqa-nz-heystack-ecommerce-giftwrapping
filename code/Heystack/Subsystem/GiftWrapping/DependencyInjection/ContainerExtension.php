<?php
/**
 * This file is part of the Ecommerce-GiftWrapping package
 *
 * @package Ecommerce-GiftWrapping
 */

/**
 * GiftWrapping namespace
 */
namespace Heystack\Subsystem\GiftWrapping\DependencyInjection;

use Heystack\Subsystem\Core\Loader\DBClosureLoader;
use Heystack\Subsystem\GiftWrapping\Config\ContainerConfig;
use Heystack\Subsystem\GiftWrapping\Interfaces\GiftWrappingConfigInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Heystack\Subsystem\GiftWrapping\Services;
use Symfony\Component\Config\Definition\Processor;

/**
 *
 * @copyright  Heyday
 * @package Ecommerce-GiftWrapping
 *
 */
class ContainerExtension implements ExtensionInterface
{

    /**
     * Loads a services.yml file into a fresh container, ready to me merged
     * back into the main container
     *
     * @param  array            $config
     * @param  ContainerBuilder $container
     * @return null
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_GIFT_WRAPPING_BASE_PATH . '/config')
        );

        $loader->load('services.yml');

        $validatedConfig = (new Processor())->processConfiguration(
            new ContainerConfig(),
            $config
        );

        if ( (isset($validatedConfig['config']) || isset($validatedConfig['config_db'])) && $container->hasDefinition(Services::GIFT_WRAPPING_HANDLER) ) {

            $priceConfig = array();

            if( isset($validatedConfig['config']) && count($validatedConfig['config']) ) {

                foreach ($validatedConfig['config'] as $currencyCodeConfig ) {

                    $priceConfig[$currencyCodeConfig['code']] = $currencyCodeConfig['price'];

                }

            } else if ( isset($validatedConfig['config_db']) ) {

                $query = new \SQLQuery(
                    $validatedConfig['config_db']['select'],
                    $validatedConfig['config_db']['from'],
                    $validatedConfig['config_db']['where']
                );
                (new DBClosureLoader(
                    function (GiftWrappingConfigInterface $record) use (&$priceConfig) {

                        $priceConfig[$record->getCurrencyCode()] = $record->getPrice();

                    }
                ))->load($query);

            }

            $container->getDefinition(Services::GIFT_WRAPPING_HANDLER)->addMethodCall('setConfig', [$priceConfig]);

        } else {
            throw new ConfigurationException('Please configure the gift wrapping subsystem on your /mysite/config/services.yml file');
        }
    }

    /**
     * Returns the namespace of the container extension
     * @return type
     */
    public function getNamespace()
    {
        return 'gift-wrapping';
    }

    /**
     * Returns Xsd Validation Base Path, which is not used, so false
     * @return boolean
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the container extensions alias
     * @return type
     */
    public function getAlias()
    {
        return 'gift-wrapping';
    }

}
