<?php

namespace App\Helpers;

use App\Models\Activity;
use App\Models\Assignment;
use App\Models\Examination;
use App\Models\File as ModelsFile;
use App\Models\Question;
use App\Models\Topic;
use DateInterval;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AppHelper
{
    public static function splitDateTime($datetime)
    {
        if (!$datetime) {
            return null;
        }

        [$date, $time] = explode(' ', $datetime);
        [$hour, $minute, $second] = explode(':', $time);

        return [
            'date' => $date,
            'hour' => $hour,
            'minute' => $minute,
            'second' => $second
        ];
    }

    public static function isTodayInRange($date_start, $date_end)
    {
        if (!$date_start && !$date_end) {
            return true;
        }

        $now = Carbon::now()->setTimezone('Asia/Bangkok');

        if (!$date_start) {
            return $now->lte(Carbon::parse($date_end));
        } elseif (!$date_end) {
            return $now->gte(Carbon::parse($date_start));
        } else {
            return $now->gte(Carbon::parse($date_start)) && $now->lte(Carbon::parse($date_end));
        }
    }

    public static function isTodayGreaterThan($date)
    {
        if (is_null($date)) {
            return false;
        }

        $now = Carbon::now()->setTimezone('Asia/Bangkok');

        return $now->gte(Carbon::parse($date, 'Asia/Bangkok'));
    }

    public static function saveImageOnServerAndRename(UploadedFile $image)
    {
        $uniqName = uniqid() . '_' . $image->getClientOriginalName();
        $fileContents = file_get_contents($image->getRealPath());
        $path = 'image/' . $uniqName;
        File::put(public_path($path), $fileContents);

        return $path;
    }

    public static function storeFileOnServer(UploadedFile $file = null, $path)
    {
        if (is_null($file)) {
            return null;
        }
        $name = $file->getClientOriginalName();
        $fileContents = file_get_contents($file->getRealPath());

        if (!File::isDirectory(public_path($path))) {
            File::makeDirectory(public_path($path), 0755, true, true);
        }

        File::put(public_path($path . $name), $fileContents);

        return $path . $name;
    }

    public static function getCurrentTime()
    {
        return Carbon::now()->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s');
    }

    public static function calcTimeDiff(string $dateGiven = null, string $dateCompare = null)
    {
        if (is_null($dateCompare)) {
            $dateCompare = AppHelper::getCurrentTime();
        }

        if (is_null($dateGiven)) {
            return null;
        }

        try {
            $timeDiff = Carbon::parse($dateCompare)->diff(Carbon::parse($dateGiven));
        } catch (\Throwable $th) {
            return null;
        }

        return $timeDiff;
    }

    public static function formatDateTime($date, string $format)
    {
        if (is_null($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $th) {
            return $date;
        }
    }

    public static function getCourseIdFromUrl()
    {
        $path = request()->path();

        switch ($path) {
            case strpos($path, 'course/quiz') === 0:
                try {
                    $exam = Examination::find(request()->query('id'));

                    $courseId = $exam->activity->topic->course->id;
                } catch (\Throwable $th) {
                    $courseId = null;
                }

                break;

            case strpos($path, 'course/assign') === 0:
                try {
                    $assign = Assignment::find(request()->query('id'));
                    $courseId = $assign->activity->topic->course->id;
                } catch (\Throwable $th) {
                    $courseId = null;
                }

                break;

            case strpos($path, 'course/file') === 0:
                try {
                    $file = ModelsFile::find(request()->query('id'));
                    $courseId = $file->activity->topic->course->id;
                } catch (\Throwable $th) {
                    $courseId = null;
                }

                break;
            case strpos($path, 'course/activity/edit') === 0:
            case strpos($path, 'course/activity/update') === 0:
                $type = request()->query('type');
                switch ($type) {
                    case 'assign':
                        try {
                            $assign = Assignment::find(request()->query('id'));
                            $courseId = $assign->activity->topic->course->id;
                        } catch (\Throwable $th) {
                            $courseId = null;
                        }
                        break;

                    case 'quiz':
                        try {
                            $exam = Examination::find(request()->query('id'));
                            $courseId = $exam->activity->topic->course->id;
                        } catch (\Throwable $th) {
                            $courseId = null;
                        }
                        break;

                    case 'question':
                        try {
                            $question = Question::find(request()->query('id'));
                            $courseId = $question->exam->activity->topic->course->id;
                        } catch (\Throwable $th) {
                            $courseId = null;
                        }
                        break;

                    case 'file':
                        try {
                            $file = ModelsFile::find(request()->query('id'));
                            $courseId = $file->activity->topic->course->id;
                        } catch (\Throwable $th) {
                            $courseId = null;
                        }
                        break;
                    default:
                        $courseId = null;
                        break;
                }

                break;

            case strpos($path, 'course/add') === 0:
            case strpos($path, 'course/store') === 0:
            case strpos($path, 'course/topic/edit') === 0:
            case strpos($path, 'course/topic/delete') === 0:
            case strpos($path, 'course/topic/update') === 0:
                $topicId = request()->query('id');
                $courseId = Topic::find($topicId)->course->id;
                break;
            case strpos($path, 'course/process/toggle') === 0:
                $activityId = request()->query('id');
                $courseId = Activity::find($activityId)->topic->course->id;
                break;

            default:
                $courseId = request()->query('id');
                break;
        }
        return $courseId;
    }

    public static function randomArrayInt($length, $random)
    {
        $arr = collect([]);
        $arrRandom = collect([]);

        for ($i = 1; $i <= $length; $i++) {
            $arr->push($i);
        }

        if (!$random) {
            return $arr;
        }

        while (!$arr->isEmpty()) {
            $randomNumber = random_int(1, count($arr));
            $arrRandom->push($arr[$randomNumber - 1]);
            $arr->splice($randomNumber - 1, 1);
        }

        return $arrRandom;
    }

    public static function intToRoman($number)
    {
        $map = [
            1 => 'I',
            4 => 'IV',
            5 => 'V',
            9 => 'IX',
            10 => 'X',
            40 => 'XL',
            50 => 'L',
            90 => 'XC',
            100 => 'C',
            400 => 'CD',
            500 => 'D',
            900 => 'CM',
            1000 => 'M'
        ];

        $result = '';

        foreach (array_reverse($map, true) as $key => $value) {
            while ($number >= $key) {
                $result .= $value;
                $number -= $key;
            }
        }

        return $result;
    }

    public static function intToAlphabet($number)
    {
        return chr(65 + $number - 1);
    }

    public static function discoverFileType($filePath)
    {
        $extension = File::extension($filePath);
        $fileTypes = [
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'mp3' => 'audio',
            'wav' => 'audio',
            'ogg' => 'audio',
            'mp4' => 'video',
            'avi' => 'video',
            'mov' => 'video',
            'wmv' => 'video',
            'pdf' => 'document',
            'doc' => 'document',
            'docx' => 'document',
            'txt' => 'document',
        ];

        foreach ($fileTypes as $extensions => $type) {
            if (in_array($extension, (array)$extensions)) {
                return $type;
            }
        }

        return 'unknown';
    }
}
