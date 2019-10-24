<?php

namespace Spatie\Translatable\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class WhereScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $newQuery = $this->changeQuery($builder, $model);

        $builder->setQuery($newQuery);
    }

    /**
     * @param Builder $builder
     * @param Model $model
     * @return \Illuminate\Database\Query\Builder
     */
    private function changeQuery(Builder $builder, Model $model)
    {
        /** @var \Spatie\Translatable\HasTranslations $model */
        $translatableColumns = $model->getTranslatableAttributes();
        $query = $builder->getQuery();
        $wheres = $query->wheres;
        $locale = app()->getLocale();

        $newArr = [];
        foreach ($wheres as $key => $where) {
            if (method_exists($this, "iterateOn" . $where['type'])) {
                $newArr[] = $this->{"iterateOn" . $where['type']}($where, $translatableColumns, $locale);
            } else {
                $newArr[] = $where;
            }
        }

        if (!empty($newArr)) {
            $query->wheres = $newArr;
        }

        return $query;
    }

    /**
     * @param $where
     * @param $translatableColumns
     * @param $locale
     * @return mixed
     */
    private function iterateOnBasic($where, $translatableColumns, $locale)
    {
        if (
            array_key_exists('column', $where) &&
            in_array($where['column'], $translatableColumns)
        ) {
            $where['column'] = explode('->', $where['column'])[0] . "->{$locale}";
        }

        return $where;
    }

    /**
     * @param $where
     * @param $translatableColumns
     * @param $locale
     * @return mixed
     */
    private function iterateOnNested($where, $translatableColumns, $locale)
    {
        $nestedQuery = $where['query'];
        $nestedWheres = $nestedQuery->wheres;
        $newNestedArr = [];
        foreach ($nestedWheres as $nestedWhere) {

            if (method_exists($this, "iterateOn" . $nestedWhere['type'])) {
                $newNestedArr [] = $this->{"iterateOn" . $nestedWhere['type']}($nestedWhere, $translatableColumns, $locale);
            }
        }

        $nestedQuery->wheres = $newNestedArr;
        $where['query'] = $nestedQuery;
        return $where;
    }
}
