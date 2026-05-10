<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

use WP_REST_Response;

/**
 * Minimal but solid validator: supports rules like:
 *  - required, string, int, boolean, numeric
 *  - min:x, max:x
 *  - in:a,b,c
 *  - regex:/^...$/
 *  - slug
 */
final class RequestValidator
{
    private $errors = [];
    private $rules = [];
    private $messages = [];

    public function __construct(array $rules, array $messages = [])
    {
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public function passes(\WP_REST_Request $req): bool
    {
        $params = Helpers::allParams($req);

        foreach ($this->rules as $field => $ruleLine) {
            $rules = is_array($ruleLine) ? $ruleLine : array_map('trim', explode('|', (string) $ruleLine));
            $value = isset($params[$field]) ? $params[$field] : null;

            $isRequired = in_array('required', $rules, true);
            if ($isRequired && ($value === null || $value === '')) {
                $this->fail($field, 'required');
                continue;
            }

            // Skip other rules if not provided and not required
            if ($value === null || $value === '') {
                continue;
            }

            foreach ($rules as $rule) {
                if ($rule === 'required') {
                    continue;
                }

                if ($rule === 'string' && !is_string($value)) {
                    $this->fail($field, 'string');
                }

                if ($rule === 'int' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->fail($field, 'int');
                }

                if ($rule === 'boolean' && !in_array($value, [true, false, 'true', 'false', '1', '0', 1, 0], true)) {
                    $this->fail($field, 'boolean');
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $this->fail($field, 'numeric');
                }

                if ($rule === 'slug') {
                    $norm = \Rehub\Rest\normalize_slug((string) $value);
                    if ($norm === '') {
                        $this->fail($field, 'slug');
                    }
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (is_numeric($value) && $value < $min) {
                        $this->fail($field, "min:$min");
                    }
                    if (is_string($value) && mb_strlen($value) < $min) {
                        $this->fail($field, "min:$min");
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (is_numeric($value) && $value > $max) {
                        $this->fail($field, "max:$max");
                    }
                    if (is_string($value) && mb_strlen($value) > $max) {
                        $this->fail($field, "max:$max");
                    }
                }

                if (strpos($rule, 'in:') === 0) {
                    $opts = array_map('trim', explode(',', substr($rule, 3)));
                    if (!in_array((string) $value, $opts, true)) {
                        $this->fail($field, 'in');
                    }
                }

                if (strpos($rule, 'regex:') === 0) {
                    $pattern = substr($rule, 6);
                    if (@preg_match($pattern, '') === false || !preg_match($pattern, (string) $value)) {
                        $this->fail($field, 'regex');
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function fail(string $field, string $rule): void
    {
        $key = $field . '.' . $rule;
        $this->errors[$field][] = isset($this->messages[$key])
            ? $this->messages[$key]
            : "The $field field failed the $rule rule.";
    }

    public function toErrorResponse(): WP_REST_Response
    {
        return Response::error('اعتبارسنجی نامعتبر است.', 422, ['errors' => $this->errors]);
    }
}
