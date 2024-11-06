<?php

namespace App\Http\Traits;

trait CommonFunctionTrait
{
    /**
     * Get the list of organization types.
     *
     * This function returns an array of possible organization types
     * that can be used in various contexts within the application.
     *
     * @return array List of organization types.
     */
    public function organisationType($typeIndex) {
        switch ($typeIndex) {
            case 'pc':
                return 'Public Company';
            case 'se':
                return 'Self Employed';
            case 'ga':
                return 'Government Agency';
            case 'np':
                return 'Nonprofit';
            case 'sp':
                return 'Sole Proprietorship';
            case 'ph':
                return 'Privately Held';
            case 'ps':
                return 'Partnership';
            default:
                return 'Unknown Type'; // Optional: Handle invalid index
        }
    }
    
}