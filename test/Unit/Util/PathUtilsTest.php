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

namespace Gtlogistics\EdiClient\Test\Unit\Util;

use Gtlogistics\EdiClient\Util\PathUtils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(PathUtils::class)]
class PathUtilsTest extends TestCase
{
    #[TestWith(['/path/to/dir/', 'path/to/dir/'])]
    #[TestWith(['/path/to/dir/', '/path/to/dir/'])]
    #[TestWith(['/path/to/dir/', '/path/to/dir'])]
    #[TestWith(['/path/to/dir/', 'path/to/dir'])]
    #[TestWith(['/path/', 'path/'])]
    #[TestWith(['/path/', '/path/'])]
    #[TestWith(['/path/', '/path'])]
    #[TestWith(['/path/', 'path'])]
    public function testNormalizeDirPath(string $expected, string $path): void
    {
        $this->assertEquals($expected, PathUtils::normalizeDirPath($path));
    }

    #[TestWith(['test.edi', 'path/to/dir/', 'path/to/dir/test.edi'])]
    #[TestWith(['test.edi', '/path/to/dir/', 'path/to/dir/test.edi'])]
    #[TestWith(['test.edi', '/path/to/dir', 'path/to/dir/test.edi'])]
    #[TestWith(['test.edi', 'path/to/dir', 'path/to/dir/test.edi'])]
    #[TestWith(['dir/test.edi', 'path/', 'path/dir/test.edi'])]
    #[TestWith(['dir/test.edi', '/path/', 'path/dir/test.edi'])]
    #[TestWith(['dir/test.edi', '/path', 'path/dir/test.edi'])]
    #[TestWith(['dir/test.edi', 'path', 'path/dir/test.edi'])]
    public function testNormalizeFilePath(string $expected, string $path, string $filename): void
    {
        $this->assertEquals($expected, PathUtils::normalizeFilePath($path, $filename));
    }
}
