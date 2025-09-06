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

namespace BaksDev\Products\Promotion\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Products\Product\Repository\ProductDetail\ProductDetailByInvariableInterface;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Promotion\Entity\Event\ProductPromotionEvent;
use BaksDev\Products\Promotion\Entity\ProductPromotion;
use BaksDev\Products\Promotion\Repository\CurrentProductPromotionEvent\CurrentProductPromotionEventInterface;
use BaksDev\Products\Promotion\UseCase\NewEdit\Invariable\ProductPromotionInvariableDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionDTO;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionForm;
use BaksDev\Products\Promotion\UseCase\NewEdit\ProductPromotionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCT_PROMOTION_NEW_EDIT')]
class NewEditController extends AbstractController
{
    #[Route('/admin/products/promotion/{invariable}', name: 'admin.promotion.edit', methods: ['GET', 'POST'])]
    public function index(
        #[ParamConverter(ProductInvariableUid::class, key: 'invariable')] ProductInvariableUid $ProductInvariableUid,
        Request $request,
        ProductPromotionHandler $productPromotionHandler,
        CurrentProductPromotionEventInterface $currentProductPromotionEventRepository,
        ProductDetailByInvariableInterface $productDetailByInvariableRepository,
    ): Response
    {
        $NewProductPromotionDTO = new ProductPromotionDTO;

        $productPromotion = $currentProductPromotionEventRepository
            ->byInvariable($ProductInvariableUid)
            ->find();

        $ProductPromotionInvariableDTO = new ProductPromotionInvariableDTO();
        $ProductPromotionInvariableDTO->setProduct($ProductInvariableUid);

        $NewProductPromotionDTO->setInvariable($ProductPromotionInvariableDTO);

        if($productPromotion instanceof ProductPromotionEvent)
        {
            $productPromotion->getDto($NewProductPromotionDTO);
        }

        $form = $this
            ->createForm(
                ProductPromotionForm::class,
                $NewProductPromotionDTO,
                ['action' => $this->generateUrl(
                    'products-promotion:admin.promotion.edit', ['invariable' => $ProductInvariableUid,],
                )],
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('product_promotion'))
        {

            if(
                false === $this->isGranted('ROLE_ADMIN') &&
                false === $this->isGranted('ROLE_PRODUCT_PROMOTION_PRICE_DECREMENT') &&
                str_starts_with($NewProductPromotionDTO->getPrice()->getValue(), '-')
            )
            {
                $this->addFlash(
                    'page.edit',
                    'notice.edit',
                    'products-promotion.admin.promotion',
                );

                return $this->redirectToRoute('products-promotion:admin.promotion.index');
            }


            $this->refreshTokenForm($form);

            $handle = $productPromotionHandler->handle($NewProductPromotionDTO);

            $this->addFlash(
                'page.edit',
                $handle instanceof ProductPromotion ? 'success.edit' : 'danger.edit',
                'products-promotion.admin.promotion',
                $handle,
            );

            return $this->redirectToRoute('products-promotion:admin.promotion.index');
        }

        $productPromotion = $productDetailByInvariableRepository
            ->invariable($ProductInvariableUid)
            ->find();

        return $this->render([
            'form' => $form->createView(),
            'product' => $productPromotion,
        ]);
    }
}