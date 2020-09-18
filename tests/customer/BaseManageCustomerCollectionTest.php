<?php

namespace Yosmy\Payment\Test;

use PHPUnit\Framework\TestCase;
use Yosmy\Payment;

class BaseManageCustomerCollectionTest extends TestCase
{
    public function testAdd()
    {
        $uri = 'mongodb://mongo-1:27017';
        $db = 'db-1';
        $collection = 'customers';

        $manageCollection = new Payment\BaseManageCustomerCollection(
            $uri,
            $db,
            $collection,
            Payment\BaseCustomer::class
        );

        $this->assertEquals(
            'customers',
            $manageCollection->getName()
        );

        $this->assertEquals(
            [
                'root' => Payment\BaseCustomer::class,
            ],
            $manageCollection->getTypeMap()
        );
    }
}