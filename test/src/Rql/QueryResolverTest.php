<?php

namespace zaboy\test\res\Rql;

use zaboy\res\Rql\QueryResolver;
use zaboy\res\DataStores\DataStoresException;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicOperator;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;
use Xiag\Rql\Parser\Node;
use Xiag\Rql\Parser\Node\Query\ScalarOperator;
use Xiag\Rql\Parser\Node\Query\LogicOperator;
use Xiag\Rql\Parser\Token;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-29 at 18:23:51.
 */
class QueryResolverTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Returner
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new QueryResolver();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testRqlEncode_Null() {
        $query = new Query();
        $this->assertEquals(
            '',
            $this->object->rqlEncode($query)
        );
    }

    public function testRqlEncode_Eq() {
        $query = new Query();
        $eqNode = new ScalarOperator\EqNode(
            'fString', 'val1'
        );
        $query->setQuery($eqNode);
        $this->assertEquals(
            'eq(fString,val1)',
            $this->object->rqlEncode($query)
        );
    }

    public function testRqlEncode_And() {
        $query = new Query();
        $eqNode1 = new ScalarOperator\EqNode(
            'fString', 'val2'
        );
        $eqNode2 = new ScalarOperator\NeNode(
            'fFloat', 300.003
        ); 
        $endNode = new LogicOperator\AndNode([$eqNode1, $eqNode2]);
        $query->setQuery($endNode);   
        $this->assertEquals(
            'and(eq(fString,val2),ne(fFloat,300.003))',
            $this->object->rqlEncode($query)
        );
        $notNode = new LogicOperator\NotNode([$eqNode2]);
        $query->setQuery($notNode);   
        $this->assertEquals(
            'not(ne(fFloat,300.003))',
            $this->object->rqlEncode($query)
        );
        $orNode = new LogicOperator\OrNode([$eqNode1, $endNode, $eqNode2, $notNode]);
        $query->setQuery($orNode);   
        $this->assertEquals(
            'or(eq(fString,val2),and(eq(fString,val2),ne(fFloat,300.003)),ne(fFloat,300.003),not(ne(fFloat,300.003)))',
            $this->object->rqlEncode($query)
        );
    }
    
    public function testRqlEncode_Limit() {
        $query = new Query();
        $limitNode = new Node\LimitNode('Infinity',0);
        $query->setLimit($limitNode);  
        $this->assertEquals(
            '',
            $this->object->rqlEncode($query)
        );
        $limitNode = new Node\LimitNode(1);
        $query->setLimit($limitNode);  
        $this->assertEquals(
            'limit(1,0)',
            $this->object->rqlEncode($query)
        );
        $limitNode = new Node\LimitNode('Infinity',2);
        $query->setLimit($limitNode);  
        $this->assertEquals(
            'limit(Infinity,2)',
            $this->object->rqlEncode($query)
        );
    }
    
    public function testRqlEncode_Select() {
        $query = new Query();
        $selectNode = new Node\SelectNode([]);
        $query->setSelect($selectNode);  
        $this->assertEquals(
            '',
            $this->object->rqlEncode($query)
        );
        $selectNode = new Node\SelectNode(['fFloat']);
        $query->setSelect($selectNode);  
        $this->assertEquals(
            'select(fFloat)',
            $this->object->rqlEncode($query)
        );
        $selectNode = new Node\SelectNode(['fFloat', 'fString']);
        $query->setSelect($selectNode);  
        $this->assertEquals(
            'select(fFloat,fString)',
            $this->object->rqlEncode($query)
        );
    }
    
    public function testRqlEncode_Sort() {
        $query = new Query();
        $sortNode = new Node\SortNode([]);
        $query->setSort($sortNode);  
        $this->assertEquals(
            '',
            $this->object->rqlEncode($query)
        );
        $sortNode = new Node\SortNode(['id' => '1']);
        $query->setSort($sortNode);  
        $this->assertEquals(
            'sort(+id)',
            $this->object->rqlEncode($query)
        );
        $sortNode = new Node\SortNode(['id' => '1', 'fFloat'=> '-1']);
        $query->setSort($sortNode);  
        $this->assertEquals(
            'sort(+id,-fFloat)',
            $this->object->rqlEncode($query)
        );
    } 
    
    public function testRqlEncode_Combo() {
        $query = new Query();
        
        $eqNode1 = new ScalarOperator\EqNode(
            'fString', 'val2'
        );
        $eqNode2 = new ScalarOperator\NeNode(
            'fFloat', 300.003
        ); 
        $endNode = new LogicOperator\AndNode([$eqNode1, $eqNode2]);
        $query->setQuery($endNode); 
        
        $sortNode = new Node\SortNode(['id' => '-1']);
        $query->setSort($sortNode);  
        
        $selectNode = new Node\SelectNode(['fFloat']);
        $query->setSelect($selectNode);  
        
        $limitNode = new Node\LimitNode(50,2);
        $query->setLimit($limitNode); 
        
        $this->assertEquals(
            'and(eq(fString,val2),ne(fFloat,300.003))&limit(50,2)&sort(-id)&select(fFloat)',
            $this->object->rqlEncode($query)
        );
    } 
    
}
