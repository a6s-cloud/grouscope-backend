<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use \DateTime;
use Illuminate\Support\Facades\DB;

class NonFrequencyAnalysisRequest implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // 過去の解析依頼の内、最も新しいものを取得する
        $last_record = DB::table('analysis_results')
            ->select('analysis_start_date')
            ->where('analysis_word', '=', $value)
            ->orderBy('analysis_start_date', 'desc')
            ->take(1)
            ->get()->shift();

        if(!$last_record) {
            // 今まで解析依頼したこと無いハッシュタグはパス
            return true;
        }

        // 解析日時、現在日時をエポック秒に変換
        $last_requested_date = $last_record->analysis_start_date;
        $epoch_last_requested_date = strtotime($last_requested_date);
        $epoch_current_date = time();
        $current_date = (new DateTime("@$epoch_current_date"))->format('Y-m-d H:i:s');

        // 30 分以内(30 * 60 * 1)の重複したリクエストは禁止する
        return (($epoch_current_date - $epoch_last_requested_date) > (30 * 60 * 1));
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
