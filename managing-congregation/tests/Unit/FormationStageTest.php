<?php

namespace Tests\Unit;

use App\Enums\FormationStage;
use PHPUnit\Framework\TestCase;

class FormationStageTest extends TestCase
{
    public function test_it_has_aspirant_stage()
    {
        $this->assertEquals('aspirant', FormationStage::Aspirant->value);
        $this->assertEquals('Aspirant', FormationStage::Aspirant->label());
    }
}
