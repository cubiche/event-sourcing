<?php

/**
 * This file is part of the Cubiche/EventSourcing component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cubiche\Domain\EventSourcing\Tests\Units\Versioning;

use Cubiche\Domain\EventSourcing\Tests\Units\TestCase;
use Cubiche\Domain\EventSourcing\Versioning\Version;
use Cubiche\Domain\EventSourcing\Versioning\VersionIncrementType;

/**
 * VersionTests class.
 *
 * Generated by TestGenerator on 2016-06-28 at 14:36:54.
 */
class VersionTests extends TestCase
{
    /**
     * Test version.
     */
    public function testVersion()
    {
        $this
            ->given($version = Version::fromString('1.0'))
            ->then()
                ->integer($version->major())
                    ->isEqualTo(1)
                ->integer($version->minor())
                    ->isEqualTo(0)
                ->integer($version->patch())
                    ->isEqualTo(0)
        ;

        $this
            ->given($version = Version::fromString('3.0.9.1'))
            ->then()
                ->integer($version->major())
                    ->isEqualTo(3)
                ->integer($version->minor())
                    ->isEqualTo(0)
                ->integer($version->patch())
                    ->isEqualTo(9)
                ->string($version->__toString())
                    ->isEqualTo('3.0.9')
        ;

        $this
            ->given($version = new Version(0, 145, 324))
            ->then()
                ->integer($version->major())
                    ->isEqualTo(0)
                ->integer($version->minor())
                    ->isEqualTo(145)
                ->integer($version->patch())
                    ->isEqualTo(324)
                ->and()
                ->when($version->increment(VersionIncrementType::MAJOR()))
                ->then()
                    ->integer($version->major())
                        ->isEqualTo(1)
                ->and()
                ->when($version->increment(VersionIncrementType::MINOR()))
                ->then()
                    ->integer($version->minor())
                        ->isEqualTo(146)
                ->and()
                ->when($version->increment(VersionIncrementType::PATCH()))
                ->then()
                    ->integer($version->patch())
                        ->isEqualTo(325)
                ->and()
                ->when($version->setMinor(657))
                ->then()
                    ->integer($version->minor())
                        ->isEqualTo(657)
                ->and()
                ->when($version->setPatch(54))
                ->then()
                    ->integer($version->patch())
                        ->isEqualTo(54)
        ;
    }
}
