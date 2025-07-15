<?php

namespace Tests\Helpers\TestingTraits;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Provides a set of generic tests and assertion helpers for Eloquent models.
 * This trait can be used in your test classes to reduce boilerplate code
 * for common model testing scenarios like creation, column attributes, and relationships.
 */
trait ModelsTestTrait
{
    use RefreshDatabase;

    /**
     * The Eloquent model class that should be tested.
     * This method must be implemented by the class using this trait.
     *
     * @return string The fully qualified class name of the model.
     */
    abstract protected function model(): string;

    /**
     * It tests that a model instance can be created using its factory and saved to the database.
     *
     * @return void
     */
    public function test_model_insertion(): void
    {
        $factoryClass = $this->getFactoryClass();

        try {
            // Create and persist a new model record using its factory.
            $model = $factoryClass::new()->create();

            // Assert that the created record exists in the database.
            $this->assertDatabaseHas($model->getTable(), [$model->getKeyName() => $model->getKey()]);
        } catch (\Exception $e) {
            report($e);
            $this->fail("The model insertion test failed: " . $e->getMessage());
        }
    }

    /**
     * Get the factory instance for the current model.
     *
     * @return object
     */
    private function getFactoryClass(): object
    {
        return $this->model()::factory();
    }

    /**
     * Get the table name for the current model.
     *
     * @return string
     */
    public function getTableName(): string
    {
        return (new ($this->model()))->getTable();
    }

    //================= Assertion Methods ==============//

    /**
     * Assert that a given column on the model is a valid UUID.
     *
     * @param string $column The name of the column to check.
     * @return void
     */
    public function assertColumnIsUuid(string $column): void
    {
        // Create a new model instance.
        $data = $this->model()::factory()->create();

        // Assert that the column's value is not empty.
        $this->assertNotEmpty($data->{$column});

        // Assert that the column's value is a valid UUID.
        $this->assertTrue(Str::isUuid($data->{$column}), "The {$column} is not a valid UUID.");
    }

    /**
     * Assert that a given column's value is encrypted in the database.
     *
     * @param string $column The name of the column to check.
     * @return void
     */
    public function assertColumnIsEncrypted(string $column): void
    {
        $plainText = Str::random(5);

        $insertedData = $this->model()::factory()->create([
            "{$column}" => $plainText,
        ]);

        $data = DB::table($this->getTableName())->find($insertedData->id);

        // Assert that the stored value does not match the original plain text.
        $this->assertNotEquals($plainText, $data->{$column}, "The encrypted value should not match the plain text.");

        // Assert that the stored value is a string.
        $this->assertIsString($data->{$column}, "The encrypted value should be a string.");

        // Decrypt the value and assert that it matches the original plain text.
        $decryptedColumnData = Crypt::decryptString($data->{$column});
        $this->assertEquals($plainText, $decryptedColumnData, 'The column was not correctly encrypted and decrypted.');
    }

    /**
     * Assert that a column has a specific default value when not provided.
     *
     * @param string $column The name of the column to check.
     * @param mixed $expectedValue The expected default value.
     * @return void
     */
    public function assertColumnHasDefaultValue(string $column, $expectedValue): void
    {
        // 1. Generate factory attributes without saving to DB.
        $attributes = $this->getFactoryClass()->make()->getAttributes();

        // 2. Unset the column we want the DB to set a default for.
        unset($attributes[$column]);

        // 3. Create the model instance with the modified attributes.
        // The specified column will be omitted from the INSERT query.
        $modelInstance = $this->getFactoryClass()->create($attributes);

        // 4. Refresh the model to get the values actually stored in the DB.
        $modelInstance->refresh();

        // 5. Assert the column's value matches the expected default value.
        $this->assertEquals($expectedValue, $modelInstance->{$column}, "The default value for {$column} does not match the expected value.");
    }
    /**
     * Assert that a `belongsTo` relationship is defined correctly.
     *
     * @param string $relatedModel The class name of the related model.
     * @param string $relationshipMethod The name of the relationship method on the main model.
     * @return void
     */
    public function assertBelongsToRelationship(string $relatedModel, string $relationshipMethod): void
    {
        // Create an instance of the related model.
        $relatedInstance = $relatedModel::factory()->create();

        // Create the main model instance and associate it with the related model.
        $modelInstance = $this->model()::factory()->for($relatedInstance)->create();

        // Access the relationship.
        $relationship = $modelInstance->{$relationshipMethod};

        $this->assertInstanceOf($relatedModel, $relationship, "{$relationshipMethod} is not an instance of {$relatedModel}.");
        $this->assertEquals($relatedInstance->id, $relationship->id, "{$relationshipMethod} relationship does not match the related model instance.");
    }

    /**
     * Assert that a `hasMany` relationship is defined correctly.
     *
     * @param string $relatedModel The class name of the related model.
     * @param string $relationshipMethod The name of the relationship method on the main model.
     * @return void
     */
    public function assertHasManyRelationship(string $relatedModel, string $relationshipMethod): void
    {
        // Create the main model instance.
        $modelInstance = $this->model()::factory()->create();
        $count = rand(3, 5);

        // Create several instances of the related model and associate them.
        $relatedInstances = $relatedModel::factory()->count($count)->for($modelInstance)->create();

        // Access the relationship collection.
        $relationship = $modelInstance->{$relationshipMethod};

        // Assert the relationship returns an Eloquent Collection.
        $this->assertInstanceOf(EloquentCollection::class, $relationship, "{$relationshipMethod} is not returning a Collection.");

        // Assert the count of related items is correct.
        $this->assertCount($relatedInstances->count(), $relationship, "{$relationshipMethod} relationship count does not match.");
        $this->assertEquals($count, $relationship->count(), "{$relationshipMethod} relationship count does not match.");

        // Assert that each item in the collection is an instance of the related model.
        foreach ($relationship as $relatedItem) {
            $this->assertInstanceOf($relatedModel, $relatedItem, "{$relationshipMethod} contains an item that is not an instance of {$relatedModel}.");
        }
    }

    /**
     * Assert that a `belongsToMany` relationship is defined correctly.
     *
     * @param string $relatedModel The class name of the related model.
     * @param string $relationshipMethod The name of the relationship method on the main model.
     * @return void
     */
    public function assertBelongsToManyRelationship(string $relatedModel, string $relationshipMethod): void
    {
        $count = rand(3, 5);

        // Create the main model instance and attach several related models.
        $modelInstance = $this->model()::factory()
            ->has($relatedModel::factory()->count($count), $relationshipMethod)
            ->create();

        // Access the relationship collection.
        $relationship = $modelInstance->{$relationshipMethod};

        // Assert the relationship returns an Eloquent Collection.
        $this->assertInstanceOf(EloquentCollection::class, $relationship, "{$relationshipMethod} is not returning a Collection.");

        // Assert the count of related items is correct.
        $this->assertCount($count, $relationship, "{$relationshipMethod} relationship count does not match.");

        // Assert that each item in the collection is an instance of the related model.
        foreach ($relationship as $relatedItem) {
            $this->assertInstanceOf($relatedModel, $relatedItem, "{$relationshipMethod} contains an item that is not an instance of {$relatedModel}.");
        }
    }
}
