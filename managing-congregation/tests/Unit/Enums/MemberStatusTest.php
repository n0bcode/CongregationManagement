<?php

namespace Tests\Unit\Enums;

use App\Enums\MemberStatus;
use PHPUnit\Framework\TestCase;

class MemberStatusTest extends TestCase
{
    public function test_member_status_enum_cases()
    {
        $cases = MemberStatus::cases();
        $values = array_column($cases, 'value');

        $this->assertContains('active', $values);
        $this->assertContains('deceased', $values);
        $this->assertContains('exited', $values);
        $this->assertContains('transferred', $values);

        $this->assertCount(4, $cases);
    }

    public function test_member_status_enum_labels()
    {
        $this->assertEquals('Active', MemberStatus::Active->label());
        $this->assertEquals('Deceased', MemberStatus::Deceased->label());
        $this->assertEquals('Exited', MemberStatus::Exited->label());
        $this->assertEquals('Transferred', MemberStatus::Transferred->label());
    }
}
