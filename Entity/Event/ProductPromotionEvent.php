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

namespace BaksDev\Products\Promotion\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Promotion\Entity\Event\Invariable\ProductPromotionInvariable;
use BaksDev\Products\Promotion\Entity\Event\Modify\ProductPromotionModify;
use BaksDev\Products\Promotion\Entity\Event\Period\ProductPromotionPeriod;
use BaksDev\Products\Promotion\Entity\Event\Price\ProductPromotionPrice;
use BaksDev\Products\Promotion\Entity\ProductPromotion;
use BaksDev\Products\Promotion\Type\Event\ProductPromotionEventUid;
use BaksDev\Products\Promotion\Type\ProductPromotionUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/** Событие */
#[ORM\Entity]
#[ORM\Table(name: 'product_promotion_event')]
class ProductPromotionEvent extends EntityEvent
{
    /** ID События  */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductPromotionEventUid::TYPE)]
    private ProductPromotionEventUid $id;

    /** ID Корня */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductPromotionUid::TYPE, nullable: false)]
    private ProductPromotionUid $main;

    /** Постоянная величина продукта */
    #[ORM\OneToOne(targetEntity: ProductPromotionInvariable::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?ProductPromotionInvariable $invariable = null;

    /** Значение кастомной скидки (надбавки) */
    #[ORM\OneToOne(targetEntity: ProductPromotionPrice::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductPromotionPrice $price;

    /** Период действия кастомной скидки (надбавки) */
    #[ORM\OneToOne(targetEntity: ProductPromotionPeriod::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductPromotionPeriod $period;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: ProductPromotionModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductPromotionModify $modify;

    public function __construct()
    {
        $this->id = new ProductPromotionEventUid();
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /** Гидрирует переданную DTO, вызывая ее сеттеры */
    public function getDto($dto): mixed
    {
        if($dto instanceof ProductPromotionEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    /** Гидрирует сущность переданной DTO */
    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductPromotionEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getId(): ProductPromotionEventUid
    {
        return $this->id;
    }

    public function getMain(): ?ProductPromotionUid
    {
        return $this->main;
    }

    public function setMain(ProductPromotion|ProductPromotionUid $main): void
    {
        $this->main = $main instanceof ProductPromotion ? $main->getId() : $main;
    }
}