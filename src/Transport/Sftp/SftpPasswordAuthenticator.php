<?php

declare(strict_types=1);

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

namespace Gtlogistics\EdiClient\Transport\Sftp;

use Webmozart\Assert\Assert;

use function Safe\ssh2_auth_password;

final class SftpPasswordAuthenticator implements SftpAuthenticatorInterface
{
    public function __construct(
        private readonly string $username,
        private readonly string $password,
    ) {
    }

    public function authenticate($connection): void
    {
        Assert::resource($connection);

        ssh2_auth_password($connection, $this->username, $this->password);
    }
}
