<?php
/**
 * Zaboy lib (http://zaboy.org/lib/)
 * 
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\test\res\DataStore;

use  zaboy\test\res\DataStore\AbstractTest;
use  Zend\Db\TableGateway\TableGateway;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-11 at 16:19:25.
 */
class DbTableTest extends AbstractTest {

    /**
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $object;

    /**
     * @var Zend\Db\Adapter\Adapter
     */
    protected $adapter;   
    
    protected $dbTableName;
    
    protected $configTableDefault = array(
        'id' => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
        'anotherId' => 'INT NOT NULL',
        'fString' => 'CHAR(20)',
        'fInt' => 'INT'
    );
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        /**
        $this->dbTableName = $this->config['testDbTable']['tableName'];
        $this->adapter = $this->container->get('db');
        $dbTableFactory = new DbTableFactory();
        $this->object = $dbTableFactory->makeDbTableDataStore($this->container, $this->dbTableName);
         * 
         */
        $this->dbTableName = $this->config['testDbTable']['tableName'];
        $this->adapter = $this->container->get('db');
        $this->object = $this->container->get('testDbTable');    
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * 
     * @param array $data
     */
    protected function _getDbTableFilds($data) {
        $record = array_shift($data);
        reset($record);
        $firstKey = key($record);
        $firstValue = array_shift($record);
        $dbTableFilds = '';
        if (is_string($firstValue)) {
            $dbTableFilds = '`' . $firstKey . '` CHAR(80) PRIMARY KEY';
        } elseif (is_integer($firstValue)) {
            $dbTableFilds = '`' . $firstKey . '` INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
        } else {
            trigger_error("Type of primary key must be int or string", E_USER_ERROR);
        }
        foreach ($record as $key => $value) {
            if (is_string($value)) {
                $fildType = ', `' . $key . '` CHAR(80)';
            } elseif (is_integer($value)) {
                $fildType = ', `' . $key . '` INT';
            } elseif (is_float($value)) {
                $fildType = ', `' . $key . '` DOUBLE PRECISION';
            } else {
                trigger_error("Type of fild of array isn't supported.", E_USER_ERROR);
            }
            $dbTableFilds = $dbTableFilds . $fildType;
        }
        return $dbTableFilds;
    }

    /**
     * This method init $this->object
     */
    protected function _prepareTable($data) {

        $quoteTableName = $this->adapter->platform->quoteIdentifier($this->dbTableName) ;
       
        $deleteStatementStr = "DROP TABLE IF EXISTS " .  $quoteTableName;
        $deleteStatement = $this->adapter->query($deleteStatementStr);
        $deleteStatement->execute();


        $createStr = "CREATE TABLE  " . $quoteTableName;
        $filds = $this->_getDbTableFilds($data);
        $createStatementStr = $createStr . '(' . $filds . ') ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';

        $createStatement = $this->adapter->query($createStatementStr);
        $createStatement->execute();
    }

    /**
     * This method init $this->object
     */
    protected function _initObject($data = null) {
        
        if (is_null($data)) {
            $data = $this->_itemsArrayDelault;
        }
        
        $this->_prepareTable($data);
        $dbTable = new TableGateway($this->dbTableName, $this->adapter);

        foreach ($data as $record) {
            $dbTable->insert($record);
        }
    }

    /*     * ************************** Identifier *********************** */
}
