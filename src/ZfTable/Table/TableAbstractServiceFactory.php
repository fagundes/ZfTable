<?php

/**
 * ZfTable ( Module for Zend Framework 2)
 *
 * @copyright Copyright (c) 2013 Piotr Duda dudapiotrek@gmail.com
 * @license   MIT License
 */

namespace ZfTable\Table;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Can creates any children from AbstractTable, invokes a new instance, but
 * injects the main service locator object into it.
 */
class TableAbstractServiceFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (class_exists($requestedName)) {
            $reflect = new \ReflectionClass($requestedName);
            if ($reflect->isSubclassOf('ZfTable\AbstractTable')) {
                return true;
            }
        }
        return false;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if ($this->canCreateServiceWithName($serviceLocator, $name, $requestedName)) {
            /**
             * @var \ZfTable\AbstractTable $table
             */
            $table = new $requestedName;
            
            //inject the decorator factory
            $table->setDecoratorFactory($serviceLocator->get('ZfTable\Decorator\DecoratorFactory'));
            
            $config = $serviceLocator->get('Config');
            $zftableConfig = isset($config['zftable'])?$config['zftable']:array();
            
            $table->setOptions($zftableConfig);

            $form   = $table->getForm();
            $filter = $table->getFilter();
            $form->setInputFilter($filter);

            return $table;
        }
        return null;
    }
}
