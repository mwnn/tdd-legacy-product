<?php

/*
 * TODO:
 * - refactor the class to be able to test the public methods
 * - write the tests with an sqlite database, but do not touch the production database!
 * - find the bug in the class with your tests and fix it!
 * - check the class characteristic and introduce an exception if it would be a better practice
 * - check if the class have code duplication, and refactor it
 */

/*
-- schema

CREATE TABLE product (
    id INTEGER PRIMARY KEY,
    ean varchar(64) default '',
    name text default ''
);

*/
define('PRODUCTION_DATABASE_FILE', './product.db');

require_once("ProductDao.php");
require_once("Product.php");


try {
    //- add my product
    $product = new Product();
    $product->ean = '1234';
    $product->name = 'Chicken';

    $result = ProductDao::create($product);
    var_export($result);

    //- add my product - will delete
    $product = new Product();
    $product->ean = '878789';
    $product->name = 'Turkey';

    $result = ProductDao::create($product);
    var_export($result);

//    $result = ProductDao::getByEan('1234');
//    var_export($result);
//
//    $result = ProductDao::getById(9);
//    var_export($result);
//
//    $result = ProductDao::getById(1);
//    var_export($result);
//
//    $productToDelete = ProductDao::getByEan('878789');
//    $result = ProductDao::delete($productToDelete);
//    var_export($result);
//
//    $result = ProductDao::getByEan('878789');
//    var_export($result);


}
catch (\Exception $e) {
    echo "Exception: " . $e->getMessage()."\n";
}




