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

namespace BaksDev\Products\Promotion\Entity;

use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEvent;
use BaksDev\Products\Promotion\Type\Event\ProductPromotionEventUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Корневая сущность
 *
 * @see ProductPromotionEvent
 */
#[ORM\Entity]
#[ORM\Table(name: 'product_promotion')]
class ProductPromotion
{
    /**
     * Уникальный продукт
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductInvariableUid::TYPE)]
    private ProductInvariableUid $id;

    /** ID События  */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductPromotionEventUid::TYPE)]
    private ProductPromotionEventUid $event;

    public function __construct(ProductInvariable|ProductInvariableUid $invariable)
    {
        $this->id = $invariable instanceof ProductInvariable ? $invariable->getId() : $invariable;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): ProductInvariableUid
    {
        return $this->id;
    }

    public function getEvent(): ProductPromotionEventUid
    {
        return $this->event;
    }

    public function setEvent(ProductPromotionEventUid|ProductPromotionEvent $event): void
    {
        $this->event = $event instanceof ProductPromotionEvent ? $event->getId() : $event;
    }
}