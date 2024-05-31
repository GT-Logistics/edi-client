<?php

namespace Gtlogistics\EdiClient\Serializer\X12;

use Gtlogistics\EdiClient\Model\EdiInterface;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Uhin\X12Parser\Parser\X12Parser;
use Uhin\X12Parser\Serializer\X12Serializer;

use function \Safe\sprintf;

class AnsiX12Serializer implements SerializerInterface
{
    /**
     * @var array<string, AnsiX12EncoderInterface>
     */
    private array $encoders;

    /**
     * @param array<string, AnsiX12EncoderInterface> $encoders
     */
    public function __construct(array $encoders)
    {
        if (!class_exists(X12Parser::class)) {
            throw new \RuntimeException('Can not detect an X12 Parser, please execute "composer require uhin/x12-parser"');
        }

        $this->encoders = $encoders;
    }

    public function serialize(EdiInterface $edi): string
    {
        $encoder = $this->getEncoder($edi->getCode());
        $x12 = $encoder->encode($edi);

        return (new X12Serializer($x12))->serialize();
    }

    public function deserialize(string $content): EdiInterface
    {
        $parser = new X12Parser($content);
        $x12 = $parser->parse();

        return $this->getEncoder($x12->ISA[0]->GS[0]->GS01)->decode($x12);
    }

    private function getEncoder(string $code): AnsiX12EncoderInterface
    {
        if (!isset($this->encoders[$code])) {
            throw new \RuntimeException(sprintf('Unsupported EDI document %s, supported documents: %s', $code, implode(', ', array_keys($this->encoders))));
        }

        return $this->encoders[$code];
    }
}
