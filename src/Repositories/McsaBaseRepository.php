<?php

namespace Bishopm\Connexion\Repositories;

use Bishopm\Connexion\Repositories\BaseRepository;
use Bishopm\Connexion\Repositories\SettingsRepository;
use Bishopm\Connexion\Models\Setting;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

/**
 * Class MCSARepository
 *
 */
abstract class McsaBaseRepository implements BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->api_url = Setting::where('setting_key','church_api_url')->first()->setting_value;
        $this->token = Setting::where('setting_key','church_api_token')->first()->setting_value;
        $this->client = new Client();
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        $url = $this->api_url . '/' . $this->model . '/' . $id;
        $res = $this->client->request('GET', $url);
        return json_decode($res->getBody()->getContents());
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        $url = $this->api_url . '/' . $this->model;
        $res = $this->client->request('GET', $url);
        return $res->getBody()->getContents();
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = 15)
    {
        return $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update($id,$data)
    {
        $url = $this->api_url . '/' . $this->model . '/' . $id . '?token=' . $this->token;
        try {
            $res = $this->client->request('POST', $url, ['form_params' => $data]);
            return $res->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return json_decode($responseBodyAsString);
        }
    }

    /**
     * @inheritdoc
     */
    public function destroy($model)
    {
        return $model->delete();
    }


    /**
     * @inheritdoc
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByAttributes(array $attributes)
    {
        $query = $this->buildQueryByAttributes($attributes);

        return $query->first();
    }

    /**
     * @inheritdoc
     */
    public function getByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->buildQueryByAttributes($attributes, $orderBy, $sortOrder);

        return $query->get();
    }

    /**
     * Build Query to catch resources by an array of attributes and params
     * @param  array $attributes
     * @param  null|string $orderBy
     * @param  string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildQueryByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->model->query();
        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }
        if (null !== $orderBy) {
            $query->orderBy($orderBy, $sortOrder);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function findByMany(array $ids)
    {
        $query = $this->model->query();
        return $query->whereIn("id", $ids)->get();
    }

}
