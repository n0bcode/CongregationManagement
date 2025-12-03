<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\FormationStage;
use PHPUnit\Framework\TestCase;

class FormationStageTest extends TestCase
{
    public function test_it_has_expected_cases(): void
    {
        $cases = FormationStage::cases();
        $values = array_column($cases, 'value');

        $this->assertContains('postulancy', $values);
        $this->assertContains('novitiate', $values);
        $this->assertContains('first_vows', $values);
        $this->assertContains('final_vows', $values);
    }

    public function test_it_has_expected_labels(): void
    {
        $this->assertEquals('Postulancy', FormationStage::Postulancy->label());
        $this->assertEquals('Novitiate', FormationStage::Novitiate->label());
        $this->assertEquals('First Vows', FormationStage::FirstVows->label());
        $this->assertEquals('Final Vows', FormationStage::FinalVows->label());
    }
}
