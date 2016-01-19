<?php
/**
 * Zaboy lib (http://zaboy.org/lib/)
 * 
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\res\DataStores;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\res\DataStores\DataStoresAbstractFactoryTrait;

/**
 * Create and return an instance of the DataStore which based on DbTable
 * 
 *  This Factory depends on Container (which should return an 'config' as array)
 *
 * The configuration can contain:
 * <code>
 * 'DataStore' => [
 * 	'db' => array(
		'driver' => 'Pdo_Mysql',
		'host' => 'localhost',
		'database' => '',
	)
 *     'DbTable' => [
 * 
 *         'driver' => 'Pdo_Mysql',
 *         'database' => 'mydatabase',
 *         'tableName' => 'mytableName',
 *         'username' => 'root',
 *         'password' => 'mypassword'
 *     ]
 * ]
 * </code>
 * 
 * If Container is not provided, will use 
 * .././../congig/autoload/dataStore.local.php 
 * (BaseURL//congig/autoload/dataStore.local.php)
 * 
 * @category   DataStores
 * @package    DataStores
 * @uses zend-db
 * @see https://github.com/zendframework/zend-db
 */
class DataStoresAbstractFactory implements AbstractFactoryInterface
{
    use DataStoresAbstractFactoryTrait;
    
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canMakeDataStore($serviceLocator, $requestedName);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->makeDataStore( $serviceLocator, $requestedName);
    }
    
    
}    