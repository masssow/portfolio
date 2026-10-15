<?php

namespace App\Tests\Content;

use App\Content\AccompanimentCatalog;
use PHPUnit\Framework\TestCase;

final class AccompanimentCatalogTest extends TestCase
{
    public function testEveryServiceLinkCanPreselectTheQuoteForm(): void
    {
        foreach (AccompanimentCatalog::SERVICES as $slug => $service) {
            self::assertSame($service['key'], AccompanimentCatalog::normalize($slug));
            self::assertArrayHasKey($service['key'], AccompanimentCatalog::choices());
        }
    }

    public function testPacksAndHistoricalValuesRemainValid(): void
    {
        foreach (['starter', 'pro', 'business', 'web', 'tools', 'automation', 'terrain', 'support'] as $value) {
            self::assertSame($value, AccompanimentCatalog::normalize($value));
        }
        self::assertNull(AccompanimentCatalog::normalize(null));
        self::assertNull(AccompanimentCatalog::normalize('unknown'));
    }
}
