<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Assignment;
use App\Models\Community;
use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Feature: congregation-management-mvp, Property 1: Model Relationships Have Correct Return Types
 * Validates: Requirements 1.3
 *
 * For any model with relationships, all relationship methods should have proper return type declarations
 */
class ModelRelationshipsTest extends TestCase
{
    /**
     * @test
     * @dataProvider modelRelationshipProvider
     */
    public function model_relationships_have_correct_return_types(string $modelClass, string $method, string $expectedReturnType): void
    {
        $reflection = new ReflectionClass($modelClass);

        $this->assertTrue(
            $reflection->hasMethod($method),
            "Model {$modelClass} should have method {$method}"
        );

        $methodReflection = $reflection->getMethod($method);
        $returnType = $methodReflection->getReturnType();

        $this->assertNotNull(
            $returnType,
            "Method {$modelClass}::{$method}() should have a return type declaration"
        );

        $returnTypeName = $returnType->getName();

        $this->assertEquals(
            $expectedReturnType,
            $returnTypeName,
            "Method {$modelClass}::{$method}() should return {$expectedReturnType}, got {$returnTypeName}"
        );
    }

    public static function modelRelationshipProvider(): array
    {
        return [
            // User model
            [User::class, 'community', BelongsTo::class],

            // Member model
            [Member::class, 'community', BelongsTo::class],
            [Member::class, 'formationEvents', HasMany::class],
            [Member::class, 'assignments', HasMany::class],
            [Member::class, 'currentAssignment', HasOne::class],
            [Member::class, 'healthRecords', HasMany::class],
            [Member::class, 'skills', HasMany::class],

            // Community model
            [Community::class, 'members', HasMany::class],

            // FormationEvent model
            [FormationEvent::class, 'member', BelongsTo::class],
            [FormationEvent::class, 'documents', HasMany::class],

            // FormationDocument model
            [FormationDocument::class, 'formationEvent', BelongsTo::class],
            [FormationDocument::class, 'uploadedBy', BelongsTo::class],

            // Assignment model
            [Assignment::class, 'member', BelongsTo::class],
            [Assignment::class, 'community', BelongsTo::class],
        ];
    }

    /**
     * @test
     */
    public function all_relationship_methods_are_tested(): void
    {
        $models = [
            User::class,
            Member::class,
            Community::class,
            FormationEvent::class,
            FormationDocument::class,
            Assignment::class,
        ];

        $testedRelationships = collect(self::modelRelationshipProvider())
            ->map(fn ($item) => $item[0].'::'.$item[1])
            ->toArray();

        foreach ($models as $modelClass) {
            $reflection = new ReflectionClass($modelClass);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                // Skip non-relationship methods
                if ($method->class !== $modelClass) {
                    continue;
                }

                $returnType = $method->getReturnType();
                if (! $returnType) {
                    continue;
                }

                $returnTypeName = $returnType->getName();

                // Check if it's a relationship method
                if (in_array($returnTypeName, [
                    BelongsTo::class,
                    HasMany::class,
                    HasOne::class,
                    'Illuminate\Database\Eloquent\Relations\BelongsTo',
                    'Illuminate\Database\Eloquent\Relations\HasMany',
                    'Illuminate\Database\Eloquent\Relations\HasOne',
                ])) {
                    $methodSignature = $modelClass.'::'.$method->getName();

                    $this->assertContains(
                        $methodSignature,
                        $testedRelationships,
                        "Relationship method {$methodSignature} should be included in test data provider"
                    );
                }
            }
        }
    }
}
