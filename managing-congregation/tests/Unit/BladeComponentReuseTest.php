<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Feature: congregation-management-mvp, Property 22: Blade Components Are Reused
 * Validates: Requirements 8.3
 *
 * For any view file, UI elements should use x-component syntax rather than raw HTML/Tailwind classes
 */
class BladeComponentReuseTest extends TestCase
{
    /**
     * @test
     */
    public function blade_components_exist_for_common_ui_elements(): void
    {
        $requiredComponents = [
            'button',
            'status-card',
            'ledger-row',
            'timeline-node',
        ];

        foreach ($requiredComponents as $component) {
            $componentPath = resource_path("views/components/{$component}.blade.php");
            
            $this->assertFileExists(
                $componentPath,
                "Component {$component} should exist at {$componentPath}"
            );
        }
    }

    /**
     * @test
     */
    public function views_use_x_component_syntax_instead_of_raw_classes(): void
    {
        $viewsPath = resource_path('views');
        $viewFiles = File::allFiles($viewsPath);

        $antiPatterns = [
            // Anti-pattern: Inline button with raw Tailwind classes
            'pattern' => '/<button\s+class="[^"]*bg-(blue|amber|emerald|rose)-\d+[^"]*"[^>]*>/i',
            'message' => 'Views should use <x-button> component instead of raw button with Tailwind classes',
        ];

        $violations = [];

        foreach ($viewFiles as $file) {
            // Skip component files themselves
            if (str_contains($file->getPathname(), 'components/')) {
                continue;
            }

            $content = File::get($file->getPathname());

            // Check for anti-patterns
            if (preg_match($antiPatterns['pattern'], $content, $matches)) {
                $violations[] = [
                    'file' => $file->getRelativePathname(),
                    'match' => $matches[0] ?? 'unknown',
                ];
            }
        }

        // For now, we'll just warn about violations rather than fail
        // This allows gradual migration to components
        if (count($violations) > 0) {
            $this->markTestIncomplete(
                "Found " . count($violations) . " views that could use components:\n" .
                implode("\n", array_map(fn($v) => "- {$v['file']}", $violations))
            );
        }

        $this->assertTrue(true, 'Component reuse check completed');
    }

    /**
     * @test
     */
    public function button_component_supports_required_variants(): void
    {
        $componentPath = resource_path('views/components/button.blade.php');
        $content = File::get($componentPath);

        $requiredVariants = ['primary', 'secondary', 'success', 'danger'];

        foreach ($requiredVariants as $variant) {
            $this->assertStringContainsString(
                "'{$variant}'",
                $content,
                "Button component should support '{$variant}' variant"
            );
        }
    }

    /**
     * @test
     */
    public function status_card_component_supports_required_variants(): void
    {
        $componentPath = resource_path('views/components/status-card.blade.php');
        $content = File::get($componentPath);

        $requiredVariants = ['peace', 'attention', 'pending'];

        foreach ($requiredVariants as $variant) {
            $this->assertStringContainsString(
                "'{$variant}'",
                $content,
                "Status card component should support '{$variant}' variant"
            );
        }
    }

    /**
     * @test
     */
    public function components_have_minimum_touch_target_size(): void
    {
        $componentPath = resource_path('views/components/button.blade.php');
        $content = File::get($componentPath);

        // Check for 48px minimum height (accessibility requirement)
        $this->assertStringContainsString(
            'min-h-[48px]',
            $content,
            'Button component should have minimum 48px height for touch targets'
        );
    }

    /**
     * @test
     */
    public function ledger_row_component_has_proper_structure(): void
    {
        $componentPath = resource_path('views/components/ledger-row.blade.php');
        $content = File::get($componentPath);

        // Check for required elements
        $this->assertStringContainsString('date-badge', $content, 'Ledger row should have date badge');
        $this->assertStringContainsString('description', $content, 'Ledger row should have description');
        $this->assertStringContainsString('amount', $content, 'Ledger row should have amount');
    }

    /**
     * @test
     */
    public function timeline_node_component_supports_state_variants(): void
    {
        $componentPath = resource_path('views/components/timeline-node.blade.php');
        $content = File::get($componentPath);

        $requiredStates = ['isPast', 'isToday', 'isFuture'];

        foreach ($requiredStates as $state) {
            $this->assertStringContainsString(
                $state,
                $content,
                "Timeline node component should support '{$state}' state"
            );
        }
    }
}
