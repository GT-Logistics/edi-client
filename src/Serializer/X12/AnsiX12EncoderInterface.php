<?php

namespace Gtlogistics\EdiClient\Serializer\X12;

use Gtlogistics\EdiClient\Model\EdiInterface;
use Uhin\X12Parser\EDI\X12;

interface AnsiX12EncoderInterface
{
    public function decode(X12 $edi): EdiInterface;

    public function encode(EdiInterface $edi): X12;
}
