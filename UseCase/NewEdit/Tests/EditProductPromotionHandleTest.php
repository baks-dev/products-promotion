<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 */

declare(strict_types=1);

namespace BaksDev\Products\Promotion\UseCase\NewEdit\Tests;

use BaksDev\Products\Promotion\Controller\Admin\Tests\IndexControllerTest;
use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEvent;
use BaksDev\Products\Promotion\Entity\ProductPromotion;
use BaksDev\Products\Promotion\Type\ProductPromotionUid;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionHandler;
use BaksDev\Reference\Measurement\Type\Measurements\Collection\MeasurementCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group product-promotion
 *
 * @depends BaksDev\Products\Promotion\Controller\Admin\Tests\IndexControllerTest::class
 */
#[Group('product-promotion')]
#[When(env: 'test')]
final class EditProductPromotionHandleTest extends KernelTestCase
{
    private static ProductPromotionEvent|false $event = false;

    public static function setUpBeforeClass(): void
    {
        /**
         * Инициализируем инстансы
         * @var MeasurementCollection $MeasurementCollection
         */
        $OzonSupplyStatus = self::getContainer()->get(MeasurementCollection::class);
        $OzonSupplyStatus->cases();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        /** Активное событие */
        $ProductPromotionEvent = $em->getRepository(ProductPromotionEvent::class)
            ->findOneBy(['main' => ProductPromotionUid::TEST]);

        self::$event = $ProductPromotionEvent ?? false;
    }

    #[DependsOnClass(IndexControllerTest::class)]
    public function testUseCase(): void
    {
        /** @var ProductPromotionEvent $ProductPromotionEvent */
        $ProductPromotionEvent = self::$event;

        $EditProductPromotionDTO = new ProductPromotionDTO;
        $ProductPromotionEvent->getDto($EditProductPromotionDTO);

        /** Price */
        $EditProductPromotionPriceDTO = $EditProductPromotionDTO->getPrice();
        $EditProductPromotionPriceDTO->setValue('-100%');

        $EditProductPromotionDTO->setPrice($EditProductPromotionPriceDTO);

        /** Period */
        $ProductPromotionPeriodDTO = $EditProductPromotionDTO->getPeriod();
        $ProductPromotionPeriodDTO->setDateStart(new \DateTimeImmutable('+2 day'));
        $ProductPromotionPeriodDTO->setDateEnd(new \DateTimeImmutable('+3 day'));

        /** @var ProductPromotionHandler $ProductPromotionHandler */
        $ProductPromotionHandler = self::getContainer()->get(ProductPromotionHandler::class);

        $handle = $ProductPromotionHandler->handle($EditProductPromotionDTO);

        self::assertTrue(($handle instanceof ProductPromotion), $handle.': Ошибка ProductPromotion');
    }
}