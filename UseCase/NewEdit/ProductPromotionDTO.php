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

namespace BaksDev\Products\Promotion\UseCase\NewEdit;

use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEventInterface;
use BaksDev\Products\Promotion\Type\Event\ProductPromotionEventUid;
use BaksDev\Products\Promotion\UseCase\NewEdit\Invariable\ProductPromotionInvariableDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\Period\ProductPromotionPeriodDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\Price\ProductPromotionPriceDTO;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductPromotionEvent */
final class ProductPromotionDTO implements ProductPromotionEventInterface
{
    /** ID События  */
    #[Assert\Uuid]
    private ?ProductPromotionEventUid $id = null;

    /** Постоянная величина продукта */
    #[Assert\NotBlank]
    private ProductPromotionInvariableDTO $invariable;

    /** Значение кастомный скидки (надбавки) */
    #[Assert\NotBlank]
    private ProductPromotionPriceDTO $price;

    /** Период действия кастомной скидки (надбавки) */
    #[Assert\NotBlank]
    private ProductPromotionPeriodDTO $period;

    public function getEvent(): ?ProductPromotionEventUid
    {
        return $this->id;
    }

    public function getPrice(): ProductPromotionPriceDTO
    {
        return $this->price;
    }

    public function setPrice(ProductPromotionPriceDTO $price): void
    {
        $this->price = $price;
    }

    public function getPeriod(): ProductPromotionPeriodDTO
    {
        return $this->period;
    }

    public function setPeriod(ProductPromotionPeriodDTO $period): void
    {
        $this->period = $period;
    }

    public function getInvariable(): ProductPromotionInvariableDTO
    {
        return $this->invariable;
    }

    public function setInvariable(ProductPromotionInvariableDTO $invariable): void
    {
        $this->invariable = $invariable;
    }
}