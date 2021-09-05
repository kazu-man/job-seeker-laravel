<?php

namespace App\Scout;

use Elasticsearch\Client as Elastic;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class ElasticsearchEngine extends Engine
{
    /**
     * @var string
     */
    protected $index;

    /**
     * @var Elastic
     */
    protected $elastic;

    /**
     * ElasticsearchEngine constructor.
     *
     * @param string $index
     * @param \Elasticsearch\Client $elastic
     */
    public function __construct($index, Elastic $elastic)
    {
        $this->index = $index;
        $this->elastic = $elastic;
    }

    public function flush($model)
    {
        //
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function update($models)
    {
        $params['body'] = [];
        $models->each(function ($model) use (&$params) {
            
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getKey(),
                    '_index' => $model->searchableAs(),
                    ]
                ];
                $params['body'][] = [
                    'doc' => $model->toSearchableArray(),
                    'doc_as_upsert' => true
                ];
            });
        $this->elastic->bulk($params);
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function delete($models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getKey(),
                    '_index' => $model->searchableAs(),
                ]
            ];
        });
        $this->elastic->bulk($params);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->performSearch($builder, array_filter([
            'filters' => $this->filters($builder),
            'limit' => $builder->limit,
        ]));
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder $builder
     * @param  int $perPage
     * @param  int $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        $result = $this->performSearch($builder, [
            'filters' => $this->filters($builder),
            'from' => (($page * $perPage) - $perPage),
            'limit' => $perPage
        ]);

        // $result['nbPages'] = $result['hits']['total'] / $perPage;
        $result['hits']['total'] = $result['hits']['total']['value'];
        return $result;
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder $builder
     * @param  mixed $results
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        if ($results['hits']['total'] === 0) {
            return collect();
        }

        $keys = collect($results['hits']['hits'])
            ->pluck('_id')->values()->all();

        $models = $model->whereIn(
            $model->getKeyName(), $keys
        )->get()->keyBy($model->getKeyName());

        return collect($results['hits']['hits'])->map(function ($hit) use ($model, $models) {
            return isset($models[$hit['_id']]) ? $models[$hit['_id']] : null;
        })->filter()->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['hits']['total'];
    }

    /**
     * @param \Laravel\Scout\Builder $builder
     * @param array $options
     * @return array|mixed
     */
    protected function performSearch(Builder $builder, $options = [])
    {
        $bool = [
            "bool" => [
                'must' => [
                    [
                        'query_string' => [
                            'query' => "{$builder->query}"
                        ]
                    ]
                ]
            ]
        ];
        //空白の場合は全て
        if($builder->query == ""){
            $bool = [
                    "bool" => [
                        "match_all" => []
                ]
            ];
        }

        $params = [
            'index' => $builder->model->searchableAs(),
            'body' => [
                'query' => $bool
            ]
        ];

        if ($sort = $this->sort($builder)) {
            $params['body']['sort'] = $sort;
        }
        if (isset($options['filters']) && count($options['filters'])) {
            $params['body']['query']['bool']['filter'] = $options['filters'];
        }
        if (isset($options['from'])) {
            $params['body']['from'] = $options['from'];
        }
        if (isset($options['limit'])) {
            $params['body']['size'] = $options['limit'];
        }
        
        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $this->elastic,
                $builder->query,
                $params
            );
        }

        return $this->elastic->search($params);
    }

    public function filters(Builder $builder)
    {
        $filter = collect($builder->wheres)->map(function ($value, $key) {
            return [
                'match' => [
                    $key => $value
                ]
            ];

        })->values()->all();
        return $filter;
    }

    protected function sort(Builder $builder)
    {
        if (count($builder->orders) == 0) {
            return null;
        }

        return collect($builder->orders)->map(function ($order) {
            return [$order['column'] => $order['direction']];
        })->toArray();
    }
}