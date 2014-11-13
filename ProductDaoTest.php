<?php

include_once "./Product.php";
include_once "./ProductDao.php";

/**
 * Class ProductDaoTest
 */
class ProductDaoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    private $sqlLite;

    /**
     * @var ProductDao
     */
    private $productDAO;

    public function setUp()
    {
        $this->sqlLite = new PDO('sqlite::memory:', null, null, array(
//            PDO::ATTR_PERSISTENT => true,
//            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));

        $this->sqlLite->query(file_get_contents("testSchema.sql"));
        $this->productDAO = new ProductDao($this->sqlLite);
    }

    public function tearDown()
    {
        unset($this->sqlLite);
        unset($this->productDAO);
    }

    /**
     * @param $arrayProduct
     * @param $objProduct
     *
     * @dataProvider dataForTestCreate
     */
    public function testGetProductByEanPositiveCase($arrayProduct, $objProduct)
    {
        $q_tpl = "INSERT INTO product (id, ean, name) VALUES ( %d, %d, '%s');";
        $this->sqlLite->query(sprintf($q_tpl, $objProduct->id, $objProduct->ean, $objProduct->name));

        $result = $this->productDAO->getByEan($objProduct->ean);
        $this->assertInstanceOf("Product", $result);

        $this->assertEquals($objProduct, $result);
    }

    public function testGetProductByEanNegativeCase()
    {
        $nullResult = $this->productDAO->getByEan(22);
        $this->assertInstanceOf("NullProduct", $nullResult);
    }

    /**
     * @dataProvider dataForTestCreate
     */
    public function testGetByIdPositiveCase($arrayProduct, $objProduct)
    {
        $q_tpl = "INSERT INTO product (id, ean, name) VALUES ( %d, %d, '%s');";
        $this->sqlLite->query(sprintf($q_tpl, $objProduct->id, $objProduct->ean, $objProduct->name));

        $result = $this->productDAO->getById($objProduct->id);
        $this->assertInstanceOf("Product", $result);
        $this->assertEquals($objProduct, $result);
    }

    public function testGetByIdNegativeCase()
    {
        $nullResult = $this->productDAO->getById(1);
        $this->assertInstanceOf("NullProduct", $nullResult);
    }

    /**
     * @dataProvider dataForTestCreate
     */
    public function testCreateProduct($arrayProduct, $objProduct)
    {
        $result = $this->productDAO->create($objProduct);

        $this->assertTrue($result);

        $q = "SELECT * FROM product WHERE ean = '".$objProduct->ean."';";
        $smt = $this->sqlLite->query($q);
        $data = $smt->fetch();

        $this->assertEquals($arrayProduct, $data);
    }

    /**
     * @param $arrayProduct
     * @param $objProductű
     *
     * @dataProvider dataForTestCreate
     */
    public function testModifyPositiveCase($arrayProduct, $objProduct)
    {
        $this->productDAO->create($objProduct);

        $q_tpl = "INSERT INTO product (id, ean, name) VALUES ( %d, %d, '%s');";
        $this->sqlLite->query(sprintf($q_tpl, $objProduct->id, $objProduct->ean, $objProduct->name));

        $result = $this->productDAO->getByEan($objProduct->ean);

        // set new values
        $newEan = $objProduct->ean + 10000;
        $newName = 'Updated ' . $objProduct->name;
        $result->ean = $newEan;
        $result->name = $newName;

        // check affected rows count
        $updateResult = $this->productDAO->modify($result);
        $this->assertEquals(1, $updateResult);

        // double-check values
        $q = "SELECT * FROM product WHERE ean = '" . $newEan . "';";
        $smt = $this->sqlLite->query($q);
        $data = $smt->fetch();

        // update initial data array
        $arrayProduct[1] = $newEan;
        $arrayProduct[2] = $newName;
        $arrayProduct['ean'] = $newEan;
        $arrayProduct['name'] = $newName;

        $this->assertEquals($arrayProduct, $data);
    }
    /**
     *
     * @dataProvider dataForTestCreate
     */
    public function testModifyNegativeCase($arrayProduct, $objProduct)
    {
        $this->productDAO->create($objProduct);

        $objProduct->id = $objProduct->id + 1;  // set id to wrong/non existent value

        $updateResult = $this->productDAO->modify($objProduct);
        $this->assertEquals(0, $updateResult);

        $result = $this->productDAO->getByEan($objProduct->ean);

        $arrayFromProduct = array(
            0 => $result->id,
            1 => $result->ean,
            2 => $result->name,
            'id'   => $result->id,
            'ean'  => $result->ean,
            'name' => $result->name,
        );

        $this->assertEquals($arrayProduct, $arrayFromProduct);
    }

    /**
     * @dataProvider dataForTestCreate
     */
    public function testDeletePositiveCase($arrayProduct, $objProduct)
    {
        $this->productDAO->create($objProduct);

        $productToDelete = $this->productDAO->getByEan($objProduct->ean);
        $delResult = $this->productDAO->delete($productToDelete);

        $this->assertEquals(1, $delResult);

        // double check
        $q = "SELECT * FROM product WHERE ean = '".$objProduct->ean."';";
        $smt = $this->sqlLite->query($q);
        $data = $smt->fetchAll();

        $this->assertEmpty($data);
    }

    /**
     * @dataProvider dataForTestCreate
     */
    public function testDeleteNegativeCase($arrayProduct, $objProduct)
    {
        // creation skipped
        $delResult = $this->productDAO->delete($objProduct);

        $this->assertEquals(0, $delResult);
    }


    public function dataForTestCreate()
    {
        return array(
            array(
                array('id' => 1, 0 => 1, 'ean'=>2000, 1 =>'2000', 'name' => 'Chicken 2000', 2 => 'Chicken 2000'),
                new Product(2000, 'Chicken 2000')),

            array(
                array('id' => 1, 0 => 1, 'ean'=>3000, 1 =>'3000', 'name' => 'Chicken 3000', 2 => 'Chicken 3000'),
                new Product(3000, 'Chicken 3000')),
        );
    }
}
