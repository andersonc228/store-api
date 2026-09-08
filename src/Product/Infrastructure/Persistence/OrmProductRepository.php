<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Domain\Exception\ProductAlreadyExistsException;
use App\Product\Domain\Model\Product;
use App\Product\Domain\Model\ProductRepository;
use App\Shared\Common\Functional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class OrmProductRepository extends ServiceEntityRepository implements ProductRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByReference(string $reference): ?Product
    {
        $product = $this->findOneBy([
            'reference' => strtoupper($reference),
        ]);
        return $product ?? null;
    }

    /** @throws ProductAlreadyExistsException */
    public function save(Product ...$products): void
    {
        $em = $this->getEntityManager();

        try {
            Functional::each(static function (Product $product) use ($em): void {
                $em->persist($product);
            }, $products);

            $em->flush();
        } catch (UniqueConstraintViolationException) {
            $references = array_map(
                static fn (Product $product): string => $product->reference(),
                $products
            );

            $referenceList = implode(', ', $references);

            throw ProductAlreadyExistsException::fromReference($referenceList);
        }
    }
}
