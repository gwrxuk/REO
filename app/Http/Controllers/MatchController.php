<?php

namespace App\Http\Controllers;

use App\Components\Property;
use App\Components\SearchProfile;
use App\Models\PropertyFieldsModel;
use App\Models\PropertyModel;
use App\Models\SearchProfileFieldsModel;
use App\Models\SearchProfileModel;

class MatchController extends Controller
{
    public function __construct()
    {

    }

    public function index($id)
    {

        $propertyModelList = PropertyModel::where('propertyType', $id)->get();
        if ($propertyModelList->count() == 0) {
            return json_encode([]);
        }
        $fields = PropertyFieldsModel::where('propertyType', $id)->get();
        $propertyModel = $propertyModelList[0];
        $property = new Property($propertyModel->name, $propertyModel->address, $propertyModel->propertyType, $fields);

        $searchProfilesModelList = SearchProfileModel::where("propertyType", $id)->get();
        $searchProfileList = [];
        foreach ($searchProfilesModelList as $searchProfileModel) {
            $searchProfileFieldsModel = SearchProfileFieldsModel::where("searchProfileId",
                $searchProfileModel->id)->get();
            $searchProfile = new SearchProfile($searchProfileModel->name, $searchProfileModel->propertyType,
                $searchProfileFieldsModel);
            $result = $this->calculateScore($searchProfileModel->id, $property->getPropertyFields(),
                $searchProfile->getSearchProfileFields());
            $searchProfileList[] = $result;
        }
        usort($searchProfileList, array($this, 'cmp'));

        return ["data" => $searchProfileList];
    }

    function calculateScore($id, $propertyFields, $searchProfileFields)
    {
        $result = ["searchProfileId" => $id, "score" => 0, "strictMatchesCount" => 0, "looseMatchesCount" => 0];
        foreach ($propertyFields as $key => $value) {
            if (isset($searchProfileFields[$key])) {
                $result["strictMatchesCount"] += (int)$this->calculateStrict($value, $searchProfileFields[$key]);
                $result["looseMatchesCount"] += (int)$this->calculateLoose($value, $searchProfileFields[$key]);
            }
        }
        $result["score"] = $result["strictMatchesCount"] + $result["looseMatchesCount"] * 0.5;

        return $result;
    }

    private function calculateStrict($target, $range)
    {

        if (is_array($range)) {
            if ($range[0] != null && $range[1] != null) {
                return ($range[0] <= $target && $range[1] >= $target);
            }

            if ($range[0] == null && $range[1] != null) {
                return $range[1] >= $target;
            }

            if ($range[0] != null && $range[1] == null) {
                return $range[0] <= $target;
            }
            return false;
        } else {
            return $target === $range;
        }
    }

    private function calculateLoose($target, $range)
    {
        if (is_array($range)) {
            if ($range[0] != null && $range[1] != null) {
                return ($range[0] * 0.75 <= $target && $range[1] * 1.25 >= $target);
            }

            if ($range[0] == null && $range[1] != null) {
                return $range[1] * 1.25 >= $target;
            }

            if ($range[0] != null && $range[1] == null) {
                return $range[0] * 0.75 <= $target;
            }
            return false;
        } else {
            return $target === $range;
        }
    }

    private function cmp($a, $b)
    {
        if ($a["score"] == $b["score"]) {
            return 0;
        }
        return ($a["score"] < $b["score"]) ? 1 : -1;
    }

}
