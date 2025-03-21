<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

if (! function_exists('tracker_start')) {
    function tracker_start(string $connection = 'mysql'): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        DB::connection($connection)->enableQueryLog();
    }
}

if (! function_exists('tracker_end')) {
    function tracker_end(string $connection = 'mysql', ?string $userId = null, ?string $ip = null): void
    {
        if (! DB::connection($connection)->logging()) {
            return;
        }

        if (app()->runningUnitTests()) {
            DB::connection($connection)->disableQueryLog();

            return;
        }

        foreach (DB::connection($connection)->getQueryLog() as $log) {
            foreach ($log['bindings'] as $pos => $value) {
                if (is_string($value)) {
                    $log['bindings'][$pos] = "'{$value}'";
                }
            }

            $sql = str($log['query'])
                ->replaceArray('?', $log['bindings'])
                ->value();

            DB::connection('mysql')->table('trackersql')->insert([
                'sqle'       => $sql,
                'usere'      => $userId,
                'ip'         => $ip ?? request()->ip(),
                'connection' => $connection,
            ]);
        }

        DB::connection($connection)->flushQueryLog();
        DB::connection($connection)->disableQueryLog();
    }
}

if (! function_exists('str')) {
    /**
     * @template T of string|null
     *
     * @param  T  $value
     * @return Stringable|string|mixed
     *
     * @psalm-return (func_num_args() is 0 ? object : Stringable)
     */
    function str()
    {
        if (func_num_args() === 0) {
            return new class
            {
                /**
                 * @param  string  $method
                 * @param  mixed  $parameters
                 */
                public function __call($method, $parameters)
                {
                    return Str::$method(...$parameters);
                }

                public function __toString()
                {
                    return '';
                }
            };
        }

        return Str::of((string) func_get_arg(0));
    }
}
