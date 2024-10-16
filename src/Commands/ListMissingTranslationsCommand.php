<?php

namespace Spatie\Translatable\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ListMissingTranslationsCommand extends Command
{
    protected $signature = 'translatable:missing
                                {model : The model class to check (e.g., "User" or "App\\Models\\User")}
                                {--locales= : Comma-separated list of locales to check}
                                {--attributes= : Comma-separated list of attributes to check}';

    protected $description = 'List missing translations for a specific translatable model';

    public function handle(): void
    {
        $modelInput = $this->argument('model');

        $modelClass = $this->resolveModelClass($modelInput);

        if (!class_exists($modelClass)) {
            throw new InvalidArgumentException("Model class {$modelClass} does not exist.");
        }

        $locales = $this->option('locales')
            ? explode(',', $this->option('locales'))
            : config('app.locales', [config('app.locale')]);

        $model = new $modelClass;

        if (!method_exists($model, 'getTranslatableAttributes')) {
            throw new InvalidArgumentException(
                "Model {$modelClass} does not use HasTranslations trait."
            );
        }

        $attributes = $this->option('attributes')
            ? explode(',', $this->option('attributes'))
            : $model->getTranslatableAttributes();

        $query = $modelClass::query();

        $total = $query->count();

        if ($total ==    0) {
            throw new InvalidArgumentException(
                "No records found for model $modelClass."
            );
        }

        $this->info("Checking model: $modelClass");

        $bar = $this->output->createProgressBar($total);

        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $missingTranslations = collect();

        $query->chunk(100, function ($models) use ($locales, $attributes, &$missingTranslations, $bar) {
            foreach ($models as $model) {
                foreach ($attributes as $attribute) {
                    $missingLocales = $this->findMissingLocales($model, $attribute, $locales);

                    if (!empty($missingLocales)) {
                        $missingTranslations->push([
                            'model' => get_class($model),
                            'id' => $model->getKey(),
                            'attribute' => $attribute,
                            'missing_locales' => implode(', ', $missingLocales),
                        ]);
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();

        $this->line('');

        if ($missingTranslations->isEmpty()) {
            $this->info('All translations are complete for model!');
            return;
        }

        $this->info('Missing Translations');

        $this->table(
            ['Model', 'ID', 'Attribute', 'Missing Locales'],
            $missingTranslations
        );

        return;
    }

    private function findMissingLocales(Model $model, string $attribute, array $locales): array
    {
        return array_filter($locales, function ($locale) use ($model, $attribute) {
            return !$model->hasTranslation($attribute, $locale);
        });
    }

    private function resolveModelClass(string $modelInput): string
    {
        $possiblePaths = [
            $modelInput,
            "App\\Models\\$modelInput",
            "App\\$modelInput"
        ];

        foreach ($possiblePaths as $path) {
            if (class_exists($path)) {
                return $path;
            }
        }

        return $modelInput;
    }
}
