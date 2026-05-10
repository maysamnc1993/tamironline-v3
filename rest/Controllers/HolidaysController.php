<?php

/**
 * Holidays REST Controller
 *
 * Endpoints:
 *   GET /wp-json/tamir/v1/holidays?from=YYYY-MM-DD&to=YYYY-MM-DD
 *
 * Response:
 *   {
 *     "success": true,
 *     "data": [
 *       { "date": "1405-01-13", "title": "روز طبیعت", "open_slots": ["12-14","15-17"] }
 *     ],
 *     "meta": { "from":"...", "to":"...", "count":N, "version":N, "all_slots":[...] }
 *   }
 *
 * @package Tamir\Holidays\Controllers
 */

namespace Tamir\Holidays\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use Tamir\Holidays\HolidayRepository;
use Tamir\Holidays\HolidayCacheBuster;
use Tamir\Holidays\TimeSlotsConfig;
use WP_REST_Request;

defined('ABSPATH') || exit;

final class HolidaysController extends Controller
{
    private const MAX_RANGE_DAYS = 400;

    public function authorize(WP_REST_Request $r): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'to'   => ['required', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required' => 'پارامتر from الزامی است.',
            'from.regex'    => 'فرمت from باید YYYY-MM-DD باشد.',
            'to.required'   => 'پارامتر to الزامی است.',
            'to.regex'      => 'فرمت to باید YYYY-MM-DD باشد.',
        ];
    }

    public function index(WP_REST_Request $r)
    {
        $validator = new RequestValidator($this->rules(), $this->messages());
        if (! $validator->passes($r)) {
            return Response::error('Validation failed.', 422, [
                'errors' => $validator->toErrorResponse(),
            ]);
        }

        $from = (string) $r->get_param('from');
        $to   = (string) $r->get_param('to');

        if (
            ! HolidayRepository::isValidJalaliDate($from) ||
            ! HolidayRepository::isValidJalaliDate($to)
        ) {
            return Response::error('تاریخ شمسی نامعتبر است.', 422);
        }

        if ($this->rangeInDays($from, $to) > self::MAX_RANGE_DAYS) {
            return Response::error(
                'بازه خیلی بزرگ است. حداکثر ' . self::MAX_RANGE_DAYS . ' روز.',
                422
            );
        }

        $items = HolidayRepository::inRange($from, $to);

        return $this->ok([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'from'      => $from,
                'to'        => $to,
                'count'     => count($items),
                'version'   => HolidayCacheBuster::version(),
                // همه‌ی بازه‌های معتبر — تا فرانت بتواند به‌طور consistent نمایش دهد
                'all_slots' => TimeSlotsConfig::all(),
            ],
        ]);
    }

    private function rangeInDays(string $from, string $to): int
    {
        [$fy, $fm, $fd] = array_map('intval', explode('-', $from));
        [$ty, $tm, $td] = array_map('intval', explode('-', $to));
        $fDays = $fy * 365.25 + $fm * 30.4 + $fd;
        $tDays = $ty * 365.25 + $tm * 30.4 + $td;
        return (int) abs($tDays - $fDays);
    }
}
