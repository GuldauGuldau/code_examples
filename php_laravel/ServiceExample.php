<?php
namespace App\Services;

use App\Models\Company;
use App\Models\AdvertPosition;
use Session;

class CompanyService
{
    /**
    * @param \App\Models\Category $category
    * @param integer $city_id
    * @param boolean $is_website
    * @param boolean $is_sale
    * @param string $order ('name', 'project_asc', 'project_desc', 'view_desc', 'sale_desc')
    * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
    */
    public function getCompanyForCategory($category, $city_id, $is_website = false, $is_sale = false, $order = null)
    {
        // Получаем список id дочерних категории и присоеднияем к нему
        // текущую категорию
        $category_ids = $category->children()->pluck('id')->merge($category->id);

        // Получаем компании с заданным городом и для заданных категории
        $query = Company::select('id', 'alias', 'name','name_ex', 'logo', 'image', 'short_description')
                          ->whereHas('cities', function ($query) use($city_id) {
                              $query->where('id', $city_id);
                          })->whereHas('categories', function ($query) use($category_ids) {
                              $query->whereIn('id', $category_ids);
                          });

        if($is_website) {
            // Отбираем те компании, у которых в контактах есть website
            $query->whereHas('contacts', function ($query) {
                $query->where('contact_type', 'website');
            });
        }

        if($is_sale) {
            // Отбираем те компании, у которых есть активные акции
            $query->has('sales');
        }

        if($order)
        {
            // Сортируем в заданном порядке
            $companies = $this->sortCompanies($query, $order);
        } else {
            // Сортируем в случайном порядке для каждой сессии
            $sort = $this->randomKeyList();
            $companies = $query->get()->sortBy(function ($company, $key) use($sort) {
                return (isset($sort[$company['id']])) ? $sort[$company['id']] : $company['id'];
            });
        }

        // Добавляем рекламируемые компании
        if($category->isTop()) {
            $promoCategoryId = $category->id;
        } else {
            $promoCategoryId = $category->parent_id;
        }
        $advertList = AdvertPosition::active($city_id, $promoCategoryId)->get();

        if(count($advertList) > 0) {
            foreach($advertList as $advertItem) {
                $companies->prepend($advertItem->company);
            }
        }

        return $companies;
    }

    private function randomKeyList()
    {
        $custom_sort = [];
        if (Session::has('cm_sort')) {
            $custom_sort = Session::get('cm_sort');
        } else {
          $count = Company::all()->count();
          foreach(Company::all() as $item) {
            $custom_sort[$item->id] = rand(1, $count);
          }
          Session::put('cm_sort', $custom_sort);
          Session::save();
        }
        return $custom_sort;
    }

    private function sortCompanies($query, $order) {
      switch ($order) {
          case 'name':
              // сортируем от А до Я
              return $query->get()->sortBy('name');
              break;
          case 'project_asc':
              // по числу проектов по возрастанию
              return $query->get()->sortBy(function ($company, $key) {
                  return $company->projects->count();
              });
              $query->get();
              break;
          case 'project_desc':
              // по числу проектов по убыванию
              return $query->get()->sortByDesc(function ($company, $key) {
                //dump($company->projects->count());
                  return $company->projects->count();
              });
              break;
          case 'sale_desc':
              // по числу акции по убыванию
              return $query->get()->sortByDesc(function ($company, $key) {
                  return count($company->sales);
              });
              break;
          case 'view_desc':
              // по числу просмотров по убыванию
              return $query->get()->sortByDesc(function ($company, $key) {
                  $e = $company->events()->where('name', 'view')->get()->pluck('id');
                  return \App\Models\StatisticsLog::whereIn('statistics_event_id', $e)->count();
              });
              break;
      }
    }

}
