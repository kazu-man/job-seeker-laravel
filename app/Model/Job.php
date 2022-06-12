<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Notifications\Notifiable;

class Job extends Model
{
    use Searchable;
    use Notifiable;

    /**
     * モデルのインデックス名取得
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'jobs';
    }

    public function toSearchableArray()
    {
        //検索に必要な値だけを取得
        $array = $this->toArray();
        unset($array["id"]);

        $tags = [];
        if(count($this->jobTagRelations) > 0){
            foreach($this->jobTagRelations as $tag){
                array_push($tags,$tag->Tag->tag_name);
            }
        }

        $description = $this->jobDescription;
        unset($description["id"],$description["created_at"],$description["updated_at"]);

        $address = $this->address;
        if($this->address != null){
            unset($address["id"],$address["created_at"],$address["updated_at"],$address["job_id"],$address["lat"],$address["lng"]);

        }

        $array = [];
        $array['job_title'] = $this->job_title;
        $array['status'] = $this->job_status;
        $array['annual_salary'] = $this->annual_salary;
        $array['job_description'] = $description;
        $array['address'] = $address;
        $array['company'] = $this->company->company_name;
        $array['category'] = $this->category->category_name;
        $array['tags'] = $tags;
        $array['city'] = $this->city->city_name;
        $array['province'] = $this->city->province->province_name;
        $array['country'] = $this->city->province->country->country_name;

        $array['city_id'] = $this->city->id;
        $array['province_id'] = $this->city->province->id;
        $array['country_id'] = $this->city->province->country->id;
        $array['category_id'] = $this->category->id;
        $array['job_type_id'] = $this->jobType->id;
        $array['company_id'] = $this->company->id;


        return $array;

        
    }

    public function jobDescription()
    {
        return $this->belongsTo('App\Model\JobDescription');
    }
    public function company()
    {
        return $this->belongsTo('App\Model\Company');
    }
    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }
    public function city()
    {
        return $this->belongsTo('App\Model\City');
    }

    public function jobType()
    {
        return $this->belongsTo('App\Model\JobType');
    }

    public function likes()
    {
        return $this->hasMany('App\Model\Like');
    }
    public function applyRecords()
    {   
        return $this->hasMany('App\Model\ApplyRecord');
    }
    public function JobTagRelations()
    {
        return $this->hasMany('App\Model\JobTagRelation');
    }
    public function address()
    {
        return $this->hasOne('App\Model\Address');
    }

}
