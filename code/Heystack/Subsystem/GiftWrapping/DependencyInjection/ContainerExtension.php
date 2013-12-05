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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Heystack\Subsystem\GiftWrapping\Services;

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

        $this->processConfig($config, $container);
    }

    /**
     * {@inheritdoc}
     *
     * Adds the configuration for the payment handler.
     *
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function processConfig(array $config, ContainerBuilder $container)
    {
        $config = array_pop($config);

        if (isset($config['config']) &&  $container->hasDefinition(Services::GIFT_WRAPPING_HANDLER)) {

            $container->getDefinition(Services::GIFT_WRAPPING_HANDLER)->addMethodCall('setConfig', array($config['config']));

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
