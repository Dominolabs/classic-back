<?php

namespace app\commands;

use app\module\admin\module\booking\models\Country;
use yii\console\Controller;

class ParseCountriesController extends Controller
{
    /**
     * This command parse countries from JSON file.
     */
    public function actionIndex()
    {
        $countriesJson = json_decode(file_get_contents(__DIR__ . '/../assets/json/countries.json'), true);

        if (!empty($countriesJson)) {
            foreach ($countriesJson as $countryItem) {
                $country = new Country();

                $country->short_name = $countryItem['short_name'] ?: '';
                $country->name = $countryItem['name'] ?: '';
                $country->phone_code = $countryItem['phone_code'];
                $country->created_at = time();
                $country->updated_at = time();
                $country->save();
            }
        }
    }
}
