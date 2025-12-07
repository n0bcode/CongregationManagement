<?php

namespace App\Livewire\Forms;

use Livewire\Component;

abstract class MultiStepForm extends Component
{
    public int $currentStep = 1;
    public int $totalSteps = 1;
    public array $steps = [];

    public function mount()
    {
        $this->steps = $this->getSteps();
        $this->totalSteps = count($this->steps);
    }

    abstract protected function getSteps(): array;

    public function nextStep()
    {
        $this->validateStep($this->currentStep);
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateStep($step)
    {
        // Override in child component to add validation logic per step
    }

    public function render()
    {
        return view('livewire.forms.multi-step-form');
    }
}
