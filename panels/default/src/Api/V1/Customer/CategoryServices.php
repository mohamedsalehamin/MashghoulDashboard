<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\ContentModule\Models\Category;
use App\DefaultPanel\Resources\Api\Customer\CategoryResource;
use App\DefaultPanel\Resources\Api\Customer\CategoryWithChildrenResource;
use Tasawk\Api\Facade\Api;


class CategoryServices {
    public function list() {
        //Todo:Fetch categories where has products in current selected branch
        $categories = Category::parent()
            ->enabled()
            ->get();
        return Api::isOk(__("List of categories"), CategoryResource::collection($categories));
    }

    public function show(Category $category) {
        return Api::isOk(__("Category information"), new CategoryWithChildrenResource($category));
    }


}
