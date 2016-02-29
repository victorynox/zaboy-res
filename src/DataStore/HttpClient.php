<?php
/**
 * Zaboy lib (http://zaboy.org/lib/)
 * 
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\res\DataStore;

use zaboy\res\DataStores\DataStoresAbstract;
use zaboy\res\DataStores\DataStoresException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Zend\Http\Client;

/**
 * DataStores as http Client
 * 
 * @category   DataStores
 * @package    DataStores
 * @uses Zend\Http\Client
 * @see https://github.com/zendframework/zend-db
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
class HttpClient extends DataStoresAbstract
{    
    /**
     * @var string 'http://example.org'
     */
    protected $url;
    
    /**
     * @var string 'mylogin'
     * @see https://en.wikipedia.org/wiki/Basic_access_authentication
     */
    protected $login;    
    
     
    /**
     * @var string 'kjfgn&56Ykjfnd'
     * @see https://en.wikipedia.org/wiki/Basic_access_authentication
     */
    protected $password;      
    
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * 
     * @param string $url  'http://example.org'
     * @param array $options
     */
    public function __construct($url, $options = null )
    {
        parent::__construct($options);
        if (isset($options['login']) && isset($options['password'])) {
            $this->login = options['login'];
            $this->password = $options['password'];
        }
        $this->url = rtrim(trim($url),'/');
        $supportedKeys = [
            'maxredirects',
            'useragent',
            'timeout',
        ];
        $this->options = array_intersect_key($options, array_flip($supportedKeys));
    }        
            
            
    /**
     * Return Item by id
     * 
     * Method return null if item with that id is absent.
     * Format of Item - Array("id"=>123, "fild1"=value1, ...)
     * 
     * @param int|string|float $id PrimaryKey
     * @return array|null
     */
    public function read($id)
    {
        $this->_checkIdentifierType($id);
        $identifier = $this->getIdentifier();


        if (isset($row) ) {
           return $row->getArrayCopy(); 
        }else{
            return null;
        }        
    }

    /**
     * 
     * @return array array of keys or empty array
     */
    public function  getKeys() 
    {
        $identifier = $this->getIdentifier();
        $select = $this->_dbTable->getSql()->select();
        $select->columns(array($identifier));
        $rowset = $this->_dbTable->selectWith($select);
        $keysArrays = $rowset->toArray();
        if(PHP_VERSION_ID >= 50500) {
            $keys = array_column($keysArrays, $identifier);
        }else{
            $keys = array();
            foreach ($keysArrays as $key => $value) {
                $keys[] = $value[$identifier];
            }
        }
        return $keys;
    }
    
    /**
     * Return items by criteria with mapping, sorting and paging
     * 
     * Example:
     * <code>
     * find(
     *    array('fild2' => 2, 'fild5' => 'something'), // 'fild2' === 2 && 'fild5 === 'something' 
     *    array(self::DEF_ID), // return only identifiers
     *    array(self::DEF_ID => self::DESC),  // Sorting in reverse order by 'id" fild
     *    10, // not more then 10 items
     *    5 // from 6th items in result set (offset of the first item is 0)
     * ) 
     * </code>
     * 
     * ORDER
     * http://www.simplecoding.org/sortirovka-v-mysql-neskolko-redko-ispolzuemyx-vozmozhnostej.html
     * http://ru.php.net/manual/ru/function.usort.php
     * 
     * @see ASC
     * @see DESC
     * @param Array|null $where   
     * @param array|null $filds What filds will be included in result set. All by default 
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array    Empty array or array of arrays
     */
    public function find(
        $where = null,             
        $filds = null, 
        $order = null,            
        $limit = null, 
        $offset = null 
    ) {
        $select = $this->_dbTable->getSql()->select();
        
        // ***********************   where   *********************** 
        if (!empty($where)) {
            $select->where($where);
        }    

        // ***********************   order   *********************** 
        if (!empty($order)) {
            foreach ($order as $ordKey => $ordVal) {
                if ((int) $ordVal === self::SORT_DESC) {
                    $select->order($ordKey . ' ' . self::DESC);
                }else{
                    $select->order($ordKey . ' ' . self::ASC);
                }
            }
        }else{
            $select->order($this->getIdentifier() . ' ' . self::ASC);
        }
        
        // *********************  limit, offset   *********************** 
        if (isset($limit)) { 
            $select->limit($limit);
        }    
        if (isset($offset)) { 
            $select->offset($offset);
        }            
        
        // *********************  filds  *********************** 
        if (!empty($filds)) {
            $select->columns($filds);
        }            

        // ***********************   return   *********************** 
        $rowset = $this->_dbTable->selectWith($select);
        return $rowset->toArray();
    } 
    
  
    /**
     * By default, insert new (by create) Item. 
     * 
     * It can't overwrite existing item by default. 
     * You can get item "id" for creatad item us result this function.
     * 
     * If  $item["id"] !== null, item set with that id. 
     * If item with same id already exist - method will throw exception, 
     * but if $rewriteIfExist = true item will be rewrited.<br>
     * 
     * If $item["id"] is not set or $item["id"]===null, 
     * item will be insert with autoincrement PrimryKey.<br>
     * 
     * @param array $itemData associated array with or without PrimaryKey
     * @return int|string|null  "id" for creatad item
     */
    public function create($itemData, $rewriteIfExist = false) {
        $identifier = $this->getIdentifier();
        $adapter = $this->_dbTable->getAdapter();
        // begin Transaction
        $adapter->getDriver()->getConnection()->beginTransaction();
        
$client->setRawBody($xml);
$client->setEncType('text/xml');

        $id = $this->_dbTable->getLastInsertValue();
        return $id;
    }

    /**
     * By default, update existing Item.
     * 
     * If item with PrimaryKey == $item["id"] is existing in store, item will updete.
     * Filds wich don't present in $item will not change in item in store.<br>
     * Method will return updated item<br>
     * <br>
     * If $item["id"] isn't set - method will throw exception.<br>
     * <br>
     * If item with PrimaryKey == $item["id"] is absent - method  will throw exception,<br>
     * but if $createIfAbsent = true item will be created and method return inserted item<br>
     * <br>
     * 
     * @param array $itemData associated array with PrimaryKey
     * @return array updated item or inserted item
     */
    public function update($itemData, $createIfAbsent = false) {
        $identifier = $this->getIdentifier();
        if (!isset($itemData[$identifier])) {
            throw new DataStoresException( 'Item must has primary key'); 
        }
        $id = $itemData[$identifier];
        $this->_checkIdentifierType($id);
        $adapter = $this->_dbTable->getAdapter();
        $errorMsg = 'Cann\'t update item with "id" = ' . $id; 
        $queryStr = 'SELECT ' . Select::SQL_STAR 
            . ' FROM ' . $adapter->platform->quoteIdentifier($this->_dbTable->getTable()) 
            . ' WHERE ' . $adapter->platform->quoteIdentifier($identifier)  . ' = ?'  
            . ' FOR UPDATE'; 
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            //is row with this index exist?
            $rowset = $adapter->query($queryStr, array($id));
            $isExist = !is_null($rowset->current());
            switch (true) {
                 case !$isExist && !$createIfAbsent:
                    throw new DataStoresException($errorMsg);
                case !$isExist && $createIfAbsent:
                    $this->_dbTable->insert($itemData);
                    $result = $itemData;
                    break;
                case $isExist:
                    unset($itemData[$identifier]);
                    $this->_dbTable->update($itemData, array('id' => $id));
                    $rowset = $adapter->query($queryStr, array($id));
                    $result = $rowset->current()->getArrayCopy();
                    break;
            }
            $adapter->getDriver()->getConnection()->commit();            
        }    
        catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new DataStoresException($errorMsg, 0, $e);
        }
        return $result;
    }

     /**
      * Delete Item by id. Method do nothing if item with that id is absent.
      * 
      * @param int|string $id PrimaryKey
      * @return int number of deleted items: 0 or 1
      */
    public function delete($id) {
        $identifier = $this->getIdentifier();
        $this->_checkIdentifierType($id);       
        $deletedItemsCount = $this->_dbTable->delete(array($identifier => $id));
        return $deletedItemsCount;
    }  
    
     /**
      * Delete all Items.
      * 
      * @return int number of deleted items or null if object doesn't support it
      */
    public function deleteAll() {
        $where = '1=1';
        $deletedItemsCount = $this->_dbTable->delete( $where);
        return $deletedItemsCount;
    }
    
    
    /**
     * @see coutable
     * @return int
     */
    public function count() {
        $adapter = $this->_dbTable->getAdapter();
        /* @var $rowset Zend\Db\ResultSet\ResultSet */
        $rowset = $adapter->query(
            'SELECT COUNT(*) AS count FROM ' 
            . $adapter->platform->quoteIdentifier($this->_dbTable->getTable())
            , $adapter::QUERY_MODE_EXECUTE);
        return $rowset->current()['count'];
    }    

    public function query(Query $query) 
    {
        $limits = $query->getLimit();
        $limit = !$limits ? 'Infinity' : $query->getLimit()->getLimit();
        $offset =  !$limits ? 0 : $query->getLimit()->getOffset();
        $sort = $query->getSort();
        $sortFilds = !$sort ? [$this->getIdentifier()=>self::ASC] : $sort->getFields();
        $select = $query->getSelect();  //What filds will return
        $selectFilds = !$select ? [] : $select->getFields();
        $selectSQL = $this->_dbTable->getSql()->select();
        // ***********************   where   *********************** 
        $where = $this->getQueryWhereConditioon($query->getQuery());
        $selectSQL->where($where);
        // ***********************   order   *********************** 
        foreach ($sortFilds as $ordKey => $ordVal) {
            if ((int) $ordVal === self::SORT_DESC) {
                $selectSQL->order($ordKey . ' ' . self::DESC);
            }else{
                $selectSQL->order($ordKey . ' ' . self::ASC);
            }
        }
        // *********************  limit, offset   *********************** 
        if ($limit<>'Infinity') { 
            $selectSQL->limit($limit);
        }    
        if ($offset<>0) { 
            $selectSQL->offset($offset);
        }            
        // *********************  filds  *********************** 
        if (!empty($selectFilds)) {
            $selectSQL->columns($selectFilds);
        }            
        // ***********************   return   *********************** 
        $rowset = $this->_dbTable->selectWith($selectSQL);
        return $rowset->toArray();
    }
    
    protected function getQueryWhereConditioon(AbstractQueryNode $queryNode = null)
    {
        $db = $this->_dbTable->getAdapter();
        $qi = function($name) use ($db) { return $db->platform->quoteIdentifier($name); };
        $qv = function($name) use ($db) { return $db->platform->quoteValue($name); };
        
        switch (true) {
            case is_null($queryNode):
                $conditioon = $qv(1) . ' = ' . $qv(1);
                break;
            case is_a($queryNode, '\Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode', true):
                /* @var $queryNode LogicOperator\AndNode */
                $subNodes = $queryNode->getQueries();
                $conditioon = '';
                foreach ($subNodes as $subNode) {
                    $conditioon = $conditioon .   
                        '(' 
                        . $this->getQueryWhereConditioon($subNode)
                        . ')' . PHP_EOL . ' AND ';
                }
                $conditioon = rtrim($conditioon, ' AND ');
                break;
            case is_a($queryNode, '\Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode', true):
                /* @var $queryNode ScalarOperator\EqNode */
                $field = $queryNode->getField();
                $value = $queryNode->getValue();  
                $conditioon =  $qi($field) . ' = ' . $qv($value);
                break;
            default:
                throw new DataStoresException( 
                    'The logical condition not suppoted' . $queryNode->getNodeName()
                ); 
        }
        return $conditioon;
    }    
    
    protected function initHttpClient( $method, $rqlQuery, $id = null)
    {
        $url = !$id ? $this->url : $this->url . '/' . $id;
        $httpClient = new Client($url, $this->options);
        if (isset($this->login) && isset($this->password)) {
            $httpClient->setAuth($this->login, $this->password);
        }
        $httpClient->setMethod($method);
        
    }
}    