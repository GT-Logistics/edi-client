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

use Safe\Exceptions\Ssh2Exception;
use Webmozart\Assert\Assert;

final class SftpNoneAuthenticator implements SftpAuthenticatorInterface
{
    public function __construct(
        private readonly string $username,
    ) {
    }

    public function authenticate($connection): void
    {
        Assert::resource($connection);

        $result = ssh2_auth_none($connection, $this->username);

        if ($result === true) {
            return;
        }
        if (is_array($result)) {
            throw new Ssh2Exception(sprintf('None authentication for user %s failed, available authentications: %s', $this->username, implode(', ', $result)));
        }

        throw new Ssh2Exception(sprintf('None authentication for user %s failed', $this->username));
    }
}
