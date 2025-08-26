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

namespace BaksDev\Products\Promotion\Repository\CurrentProductPromotionEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Products\Product\Entity\ProductInvariable;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEvent;
use BaksDev\Products\Promotion\Entity\ProductPromotion;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

final class CurrentProductPromotionEventRepository implements CurrentProductPromotionEventInterface
{
    private ProductInvariableUid|false $invariable = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function byInvariable(ProductInvariable|ProductInvariableUid|string $invariable): self
    {
        if(is_string($invariable))
        {
            $invariable = new ProductInvariableUid($invariable);
        }

        if($invariable instanceof ProductInvariable)
        {
            $invariable = $invariable->getId();
        }

        $this->invariable = $invariable;

        return $this;
    }

    /**
     * Метод возвращает текущее активное событие заказа
     */
    public function find(): ProductPromotionEvent|false
    {
        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(ProductPromotion::class, 'product_promotion')
            ->where('product_promotion.id = :invariable')
            ->setParameter(
                key: 'invariable',
                value: $this->invariable,
                type: ProductInvariableUid::TYPE
            );

        $orm
            ->select('event')
            ->join(
                ProductPromotionEvent::class,
                'event',
                'WITH',
                'event.id = product_promotion.event'
            );

        return $orm->getOneOrNullResult() ?: false;
    }
}
