<?php

namespace App\Http\Controllers;

use App\User;
use App\Model\Job;
use App\Model\City;
use App\Model\Company;
use App\Model\Country;
use App\Model\JobType;
use App\Model\Category;
use App\Model\Province;
use App\Model\Tag;
use App\Model\TagType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use DB;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('jobsList.settingPage');
    }

    public function getSettingData() 
    {

        try {
            $users = User::all();
            $data = ["users" => $users];
        } catch(Exception $e) {
            $data = "error";
        }
        return $data;

    }

    public function getPlaceData() 
    {
        $country = Country::with('provinces.cities')
        // ->where('city_status','!=','D')
        // ->where('province_status','!=','D')
            ->whereHas('provinces.cities', function ($query) {
                return $query->where('city_status','!=','D');
            })
            ->whereHas('provinces', function ($query) {
                return $query->where('province_status','!=','D')
                ->orderBy('id', 'DESC');
            })
            ->where('country_status','!=','D')
        ->orderBy('created_at', 'DESC')
        ->get();

        return $country;
    }

    public function getPlaceForShowList(){
        $country = \DB::table('countries')
        ->join('provinces','countries.id','=','provinces.country_id')
        ->join('cities','provinces.id','=','cities.province_id')
        ->where('city_status','!=','D')
        ->where('province_status','!=','D')
        ->where('city_status','!=','D')
        ->orderBy('countries.id', 'DESC')
        ->orderBy('cities.created_at', 'DESC')
        ->get();

        return $country;

    }


    public function getCountries() 
    {

        $countries = Country::all()->where('country_status','!=','D');

        return $countries;

    }
    public function getProvinces(Request $request) 
    {
        $id = $request->input('id');
        return Province::where('country_id',$id)->where('province_status','!=','D')->get();
    }
    
    public function getCities(Request $request) 
    {
        $id = $request->input('id');
        return City::where('province_id',$id)->where('city_status','!=','D')->get();
    }
    public function getJobType() 
    {
        return JobType::where('id','<=','3')->get();
    }
    public function getTagList() 
    {
        return TagType::with('tags')->get();
        // return Tag::All();
    }

    public function deleteCity($id){
        
        $jobs = Job::where('city_id',$id)->get();
        
        if(count($jobs) > 0){
            return response()->json([
                'message' => 'すでに使用されている都市は削除できません。',
            ], 503);
        }
        
        $targetCity = City::find($id);
        $targetCity->city_status = "D";

        DB::transaction(function () use ($targetCity) {
            $targetCity->save();
        });

            return $targetCity;
    }

    public function registerCountry(Request $request){
        $countryName = $request->input('countryName');
        $countryImage = $request->input('countryImage');
        $countryId = $request->input('countryId');
        if($countryId != null){
            $targetCountry = Country::find($countryId);
        }else{
            $checkCountry = Country::where('country_name',$countryName)->get();
            if(count($checkCountry) > 0){
                return response()->json([
                    'message' => 'すでに登録されています',
                ], 503);
                }
            $targetCountry = new Country();
        }

        $targetCountry->country_name = $countryName;


        if($request->countryImage){
            $countryBgImage = $request->countryImage->getClientOriginalName();
            \Log::info($countryBgImage);
            
            $targetCountry->country_image = $countryBgImage != "" ? '/storage/images/countryBgImages/' . $countryBgImage : "/images/search.jpg";
            if(config('app.file_system') == "s3"){
                
                Storage::putFileAs('/storage/images/countryBgImages/', request()->countryImage, $countryBgImage);
                $targetCountry->country_image = Storage::cloud()->url($targetCountry->country_image);

            }else if(config('app.file_system') == "local"){

                request()->countryImage->storeAs('/public/images/countryBgImages/',$countryBgImage);        
                
            }

            \Log::info("--- ------------------");
            \Log::info(config('app.file_system'));
            \Log::info("--- ------------------");


        }
        DB::transaction(function () use ($targetCountry) {
            $targetCountry->save();
            \Log::info($targetCountry);        
        });
    }

    public function registerProvince(Request $request){
        $provinceName = $request->input('provinceName');
        $countryId = $request->input('countryId');

        $newProvince = new Province();
        $newProvince->province_name = $provinceName;
        $newProvince->country_id = $countryId;

        DB::transaction(function () use ($newProvince) {
            $newProvince->save();
        });
    }

    public function getTargetProvinces($country_id){     

        $target = Province::where('country_id',$country_id)->get();

        return $target;

    }

    public function registerCity(Request $request){
        $cityName = $request->input('cityName');
        $provinceId = $request->input('provinceId');

        $newCity = new City();
        $newCity->city_name = $cityName;
        $newCity->province_id = $provinceId;

        DB::transaction(function () use ($newCity) {

            $newCity->save();
        });
    }

    public function userDelete(Request $request){
        $userId = $request->input('userId');
        \Log::info($userId);
        $targetUser = User::find($userId);
        if($targetUser->user_status == "D"){
            $targetUser->user_status = "A";
        }else{
            $targetUser->user_status = "D";
        }
        DB::transaction(function () use ($targetUser) {

            $targetUser->save();
        });
    }


    public function getCategories()
    {
        //
        $categories = Category::where('category_status',"!=","D")->get();

        return $categories;

    }
    

    public function deleteCategory(Request $request)
    {

        $jobs = Job::where('category_id',$request->id)->get();

        if(count($jobs) > 0){
            return response()->json([
                'message' => 'すでに使用されているカテゴリーは削除できません。',
            ], 503);
        }
        
        $category = Category::find($request->id);
        $category->category_status = "D";

        DB::transaction(function () use ($category) {

            $category->save();

        });

        return $category;

    }
    public function storeCategory(Request $request)
    {
        //
        $category = new Category();
        $category->category_name = $request->category;

        DB::transaction(function () use ($category) {

            $category->save();
            return "category saved";

        });
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request,[
            'password' => 'required|confirmed|min:8',
        ]);
        $auth = Auth::User();

        $form = $request->all();
        
                
        if (isset($form['password'])) {
        
            $form['password'] = Hash::make($form['password']);
        
        }
        DB::transaction(function () use ($auth, $form) {

            $auth->fill($form)->save();
            
        });
    }

    public function getInitData(){
        $categories = $this->getCategories();
        $countries = $this->getCountries();
        $placeData = $this->getPlaceData();
        $jobTypes = $this->getJobType();
        $tagTypeList = $this->getTagList();

        return [
            "categories" => $categories,
            "countries" => $countries,
            "placeData" => $placeData,
            "jobTypes" => $jobTypes,
            "tagTypeList" => $tagTypeList
        ];

    }    
}
