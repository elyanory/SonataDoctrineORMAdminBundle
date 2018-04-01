<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineORMAdminBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\BooleanFilter;

class BooleanFilterTest extends TestCase
{
    public function testFilterEmpty(): void
    {
        $filter = new BooleanFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', null);
        $filter->filter($builder, 'alias', 'field', '');
        $filter->filter($builder, 'alias', 'field', 'test');
        $filter->filter($builder, 'alias', 'field', false);

        $filter->filter($builder, 'alias', 'field', []);
        $filter->filter($builder, 'alias', 'field', [null, 'test']);

        $this->assertEquals([], $builder->query);
        $this->assertFalse($filter->isActive());
    }

    public function testFilterNo(): void
    {
        $filter = new BooleanFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', ['type' => null, 'value' => BooleanType::TYPE_NO]);

        $this->assertEquals(['alias.field = :field_name_0'], $builder->query);
        $this->assertEquals(['field_name_0' => 0], $builder->parameters);
        $this->assertTrue($filter->isActive());
    }

    public function testFilterYes(): void
    {
        $filter = new BooleanFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', ['type' => null, 'value' => BooleanType::TYPE_YES]);

        $this->assertEquals(['alias.field = :field_name_0'], $builder->query);
        $this->assertEquals(['field_name_0' => 1], $builder->parameters);
        $this->assertTrue($filter->isActive());
    }

    public function testFilterArray(): void
    {
        $filter = new BooleanFilter();
        $filter->initialize('field_name', ['field_options' => ['class' => 'FooBar']]);

        $builder = new ProxyQuery(new QueryBuilder());

        $filter->filter($builder, 'alias', 'field', ['type' => null, 'value' => [BooleanType::TYPE_NO]]);

        $this->assertEquals(['in_alias.field', 'alias.field IN ("0")'], $builder->query);
        $this->assertTrue($filter->isActive());
    }
}