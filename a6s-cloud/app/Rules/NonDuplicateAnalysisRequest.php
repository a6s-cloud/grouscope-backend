<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use \DateTime;
use Illuminate\Support\Facades\DB;
use AnalysisRequestService;
use App\AnalysisResults;

class NonDuplicateAnalysisRequest implements Rule
{
    private $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $analysis_date = AnalysisRequestService::formatDate($this->request->start_date);

        return !AnalysisResults
            ::where('analysis_start_date', '=', $analysis_date['target_start_date'])
            ->where('analysis_word', '=', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
