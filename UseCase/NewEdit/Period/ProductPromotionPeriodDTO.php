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

namespace BaksDev\Products\Promotion\UseCase\NewEdit\Period;

use BaksDev\Products\Promotion\Entity\Event\Period\ProductPromotionPeriodInterface;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductPromotionPeriod */
final class ProductPromotionPeriodDTO implements ProductPromotionPeriodInterface
{
    /** Дата начала */
    #[Assert\NotBlank]
    private DateTimeImmutable $dateStart;

    /** Дата окончания */
    #[Assert\NotBlank]
    private ?DateTimeImmutable $dateEnd;

    public function getDateStart(): DateTimeImmutable
    {
        /** Если была установлена дата окончания и она прошла
         * - устанавливаем дату начала
         * - обнуляем дату окончания
         */
        if(null !== $this->dateEnd && $this->dateEnd <= new DateTimeImmutable('now'))
        {
            $this->dateEnd = null;
            return new DateTimeImmutable('now');
        }

        return $this->dateStart;
    }

    public function setDateStart(DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): ?DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTimeImmutable $date): self
    {
        $this->dateEnd = $date;

        /** Если дата окончания была установлена - она не может быть меньше или равна дате начала
         */
        if(null !== $this->dateEnd && $this->dateEnd <= $this->dateStart)
        {
            throw new InvalidArgumentException('Дата окончания не может быть меньше или равна даты начала');
        }

        return $this;
    }
}