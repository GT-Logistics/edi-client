<?php

/*
 * Copyright (C) 2024 GT+ Logistics.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 * USA
 */

namespace Gtlogistics\EdiClient\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsType;
use Safe\Exceptions\StreamException;

use function Safe\stream_get_contents;

final class IsStreamContentIdentical extends Constraint
{
    private Constraint $resourceConstraint;

    private Constraint $identicalConstraint;

    private string $contents;

    public function __construct(string $contents)
    {
        $this->resourceConstraint = new IsType(IsType::TYPE_RESOURCE);
        $this->identicalConstraint = new IsIdentical($contents);
        $this->contents = $contents;
    }

    /**
     * @param resource $other
     */
    public function matches($other): bool
    {
        if (!$this->resourceConstraint->evaluate($other, '', true)) {
            return false;
        }

        try {
            $data = stream_get_contents($other);

            return $this->identicalConstraint->evaluate($data, '', true);
        } catch (StreamException) {
            return false;
        }
    }

    public function toString(): string
    {
        return sprintf('is identical to %s', $this->contents);
    }
}
