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

use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEvent;
use BaksDev\Products\Promotion\Entity\ProductPromotion;
use BaksDev\Products\Promotion\Type\ProductPromotionUid;
use BaksDev\Products\Promotion\UseCase\NewEdit\Invariable\ProductPromotionInvariableDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\Period\ProductPromotionPeriodDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\Price\ProductPromotionPriceDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionHandler;
use BaksDev\Reference\Measurement\Type\Measurements\Collection\MeasurementCollection;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group product-promotion
 */
#[Group('product-promotion')]
#[When(env: 'test')]
final class NewProductPromotionHandleTest extends KernelTestCase
{
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

        /** Корень */
        $ProductPromotion = $em->getRepository(ProductPromotion::class)
            ->find(ProductPromotionUid::TEST);

        if($ProductPromotion)
        {
            $em->remove($ProductPromotion);
        }

        /** События */
        $ProductPromotionEvents = $em->getRepository(ProductPromotionEvent::class)
            ->findBy(['main' => ProductPromotionUid::TEST]);

        foreach($ProductPromotionEvents as $event)
        {
            $em->remove($event);
        }

        $em->flush();
    }

    public function testUseCase(): void
    {
        $NewProductPromotionDTO = new ProductPromotionDTO;

        /** Invariable */
        $ProductPromotionInvariableDTO = new ProductPromotionInvariableDTO();
        $ProductPromotionInvariableDTO
            ->setProduct(new ProductInvariableUid(ProductInvariableUid::TEST))
            ->setProfile(new UserProfileUid(UserProfileUid::TEST));

        $NewProductPromotionDTO->setInvariable($ProductPromotionInvariableDTO);

        /** Price */
        $ProductPromotionPriceDTO = new ProductPromotionPriceDTO;
        $ProductPromotionPriceDTO->setValue('+100%');

        $NewProductPromotionDTO->setPrice($ProductPromotionPriceDTO);

        /** Period */
        $ProductPromotionPeriodDTO = new ProductPromotionPeriodDTO();
        $ProductPromotionPeriodDTO->setDateStart(new \DateTimeImmutable('now'));
        $ProductPromotionPeriodDTO->setDateEnd(new \DateTimeImmutable('+1 day'));

        $NewProductPromotionDTO->setPeriod($ProductPromotionPeriodDTO);

        /** @var ProductPromotionHandler $NewProductPromotionHandler */
        $NewProductPromotionHandler = self::getContainer()->get(ProductPromotionHandler::class);

        $handle = $NewProductPromotionHandler->handle($NewProductPromotionDTO);

        self::assertTrue(($handle instanceof ProductPromotion), $handle.': Ошибка ProductPromotion');
    }
}