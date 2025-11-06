<?php

namespace Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;

function assertModelTable(Model $model, string $expectedTable): void
{
    expect($model->getTable())->toBe($expectedTable);
}

function assertModelFillable(Model $model, array $expectedFillable): void
{
    expect($model->getFillable())->toBe($expectedFillable);
}

function assertModelUsesUuid(Model $model): void
{
    expect($model->getKeyType())->toBe('string')
        ->and($model->getIncrementing())->toBeFalse();
}

function assertModelHasTimestamps(Model $model, bool $enabled = true): void
{
    expect($model->usesTimestamps())->toBe($enabled);
}

function assertModelHasMethod(Model $model, string $methodName): void
{
    expect(method_exists($model, $methodName))->toBeTrue();
}

function assertModelHasCasts(Model $model, array $castKeys): void
{
    foreach ($castKeys as $key) {
        expect($model->getCasts())->toHaveKey($key);
    }
}

function assertModelPivot(Model $model, string $table, array $fillable): void
{
    assertModelTable($model, $table);
    assertModelFillable($model, $fillable);
    expect($model->getIncrementing())->toBeFalse();
    assertModelHasTimestamps($model, false);
}

function assertModelUsesUuidWithTimestamps(Model $model, string $table, array $fillable): void
{
    assertModelTable($model, $table);
    assertModelFillable($model, $fillable);
    assertModelUsesUuid($model);
    assertModelHasTimestamps($model, true);
}

