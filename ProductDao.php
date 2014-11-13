<?php

/**
 * Class ProductDao
 */
class ProductDao
{
    /**
     * @var \PDO Database resource.
     */
    private $pdo;

	/**
	 * Constructor.
	 *
	 * @param PDO $pdo
	 */
	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * @param $query
	 * @param $data
	 * @return PDOStatement
	 * @throw PDOException
	 */
	private function exec($query, $data)
	{
		$status = null;
		$sth    = $this->getPdo()->prepare($query);

		if (false === $sth->execute($data))
		{
			throw new PDOException(sprintf("Failed to execute command. ErrorMessage: %s, ErrorCode: %d", $sth->errorInfo(), $sth->errorCode()));
		}

		return $sth;
	}

	/**
	 * Get product by field.
	 *
	 * @return NullProduct|Product
	 * @throw PDOException
	 */
	public function getByField($name, $value)
	{
		$product = new NullProduct;

		$query   = sprintf("SELECT * FROM product WHERE %s = :%s", $name, $name);
		$data    = array(":$name" => $value);

		$sth     = $this->exec($query, $data);

		$rows = $sth->fetchAll();

		if (count($rows) > 0)
		{
			$row = $rows[0];
			$product = new Product($row['ean'], $row['name']);
			$product->id = $row['id'];
		}

		return $product;
	}

    /**
     * Get product by EAN.
     *
     * @param $ean
     * @return NullProduct|Product
	 * @throw PDOException
     */
    public function getByEan($ean)
    {
		return $this->getByField('ean', $ean);
    }

    /**
     * Get product by id.
     *
     * @param $id
     * @return NullProduct|Product
	 * @throw PDOException
     */
    public function getById($id)
    {
		return $this->getByField('id', $id);
    }

    /**
     * Create product in database if the EAN is not existing.
     *
     * @param Product $product
     * @return bool
	 * @throw PDOException
     */
    public function create(Product $product)
	{
		$result = false;

		if ($this->checkUnique($product->ean))
		{
			$sth = $this->exec("INSERT INTO product (ean, name) VALUES (:ean, :name)", array(
				':ean' => $product->ean,
				':name' => $product->name,
			));

			$result = true;
		}

		return $result;
    }

    /**
     * Modify the product name and ean in database by id.
     * It checks if the EAN already exists by another product, and does not overwrite.
     *
     * @param Product $product
     * @return int|null
     */
    public function modify(Product $product)
    {
		$sth = $this->exec("UPDATE product SET ean = :ean, name = :name WHERE id = :id ", array(
			':id'   => $product->id,
			':ean'  => $product->ean,
			':name' => $product->name,
		));

		$result = $sth->rowCount() ? true : false;

		return $result;
    }

    /**
     * Delete product from database
     *
     * @param Product $product
     * @return null|int
     */
    public function delete(Product $product)
    {
		$result = null;

		$sth = $this->exec("DELETE FROM product WHERE id = :id", array(
			':id' => $product->id,
		));

		if ($sth instanceof PDOStatement)
		{
			$result = $sth->rowCount();
		}

		return $result;
    }

    /**
     * Internal PDO getter
     *
     * @return PDO
     */
    private function getPdo()
    {
/*		if (!($this->pdo !== null && $this->pdo instanceof \PDO))
		{
			$dsn = sprintf("sqlite:%s", PRODUCTION_DATABASE_FILE);
			$this->$pdo = new PDO($dsn);
			$this->$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
//*/
		return $this->pdo;
	}

    /**
     * Check if the product will be unique by EAN
     *
     * @param $ean
     * @return bool
     */
    private function checkUnique($ean)
    {
		$result = true;

		$sth = $this->exec("SELECT COUNT(1) FROM product WHERE ean = :ean", array(
			':ean' => $ean,
		));

		$countRow = $sth->fetch();

		if ((int)$countRow[0] > 0)
		{
			$result = false;
		}

		return $result;
    }
}
