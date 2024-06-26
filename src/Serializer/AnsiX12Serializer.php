<?php

namespace Gtlogistics\EdiClient\Serializer;

use Gtlogistics\X12Parser\Edi;
use Gtlogistics\X12Parser\Model\ReleaseInterface;
use Gtlogistics\X12Parser\Parser\X12Parser;
use Gtlogistics\X12Parser\Serializer\X12Serializer;
use Webmozart\Assert\Assert;

/**
 * @implements SerializerInterface<Edi>
 */
class AnsiX12Serializer implements SerializerInterface
{
    private X12Serializer $serializer;

    private X12Parser $parser;

    /**
     * @param ReleaseInterface[] $releases
     */
    public function __construct(
        array $releases,
        string $elementDelimiter,
        string $segmentDelimiter,
    ) {
        if (!class_exists(X12Serializer::class)) {
            throw new \RuntimeException('Can not detect an X12 Serializer, please execute "composer require gtlogistics/edi-x12"');
        }

        $this->serializer = new X12Serializer($elementDelimiter, $segmentDelimiter);
        $this->parser = new X12Parser($releases);
    }

    public function serialize(mixed $edi): string
    {
        Assert::isInstanceOf($edi, Edi::class);

        return $this->serializer->serialize($edi);
    }

    public function deserialize(string $content): mixed
    {
        return $this->parser->parse($content);
    }
}
