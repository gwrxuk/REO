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

        $propertyModel = PropertyModel::where('propertyId', $id)->first();
        if ($propertyModel === null) {
            return ["data" => []];
        }
        $fields = PropertyFieldsModel::where('propertyId', $id)->get();
        if ($fields->count() === 0) {
            return ["data" => []];
        }
        $property = new Property($propertyModel->name, $propertyModel->address, $propertyModel->propertyType, $fields);

        $searchProfilesModelList = SearchProfileModel::where("propertyType", $property->getPropertyType())->get();
        $searchProfileList = [];
        foreach ($searchProfilesModelList as $searchProfileModel) {
            $searchProfileFieldsModel = SearchProfileFieldsModel::where("searchProfileId",
                $searchProfileModel->searchProfileId)->get();
            $searchProfile = new SearchProfile($searchProfileModel->name, $searchProfileModel->propertyType,
                $searchProfileFieldsModel);
            $result = $this->calculateScore($searchProfileModel->searchProfileId, $property->getPropertyFields(),
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
            if ($value === null) {
                continue;
            }
            if (isset($searchProfileFields[$key])) {
                $strict = (int)$this->calculateStrict($value, $searchProfileFields[$key]);
                $result["strictMatchesCount"] += $strict;
                if ($strict === 0) {
                    $result["looseMatchesCount"] += (int)$this->calculateLoose($value, $searchProfileFields[$key]);
                }
            }
        }
        $result["score"] = $result["strictMatchesCount"] + $result["looseMatchesCount"] * 0.5;

        return $result;
    }

    private function calculateStrict($target, $range)
    {

        if (is_array($range)) {
            if ($range[0] !== null && $range[1] !== null) {
                return ($range[0] <= $target && $range[1] >= $target);
            }

            if ($range[0] === null && $range[1] !== null) {
                return $range[1] >= $target;
            }

            if ($range[0] !== null && $range[1] === null) {
                return $range[0] <= $target;
            }
            return true;
        } else {
            return $target === $range;
        }
    }

    private function calculateLoose($target, $range)
    {
        if (is_array($range)) {
            if ($range[0] !== null && $range[1] !== null) {
                return ($range[0] * 0.75 <= $target && $range[1] * 1.25 >= $target);
            }

            if ($range[0] === null && $range[1] !== null) {
                return $range[1] * 1.25 >= $target;
            }

            if ($range[0] !== null && $range[1] === null) {
                return $range[0] * 0.75 <= $target;
            }
            return true;
        } else {
            return $target === $range;
        }
    }

    private function cmp($a, $b)
    {
        if ($a["score"] === $b["score"]) {
            return 0;
        }
        return ($a["score"] < $b["score"]) ? 1 : -1;
    }

}
