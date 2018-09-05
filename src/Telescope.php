<?php

namespace Laravel\Telescope;

use Closure;
use Illuminate\Support\Str;
use Laravel\Telescope\Contracts\EntriesRepository;

class Telescope
{
    /**
     * The callback that filters the entries that should be recorded.
     *
     * @var \Closure
     */
    public static $filterUsing;

    /**
     * The callback that adds tags to the record.
     *
     * @var \Closure
     */
    public static $tagUsing;

    /**
     * The list of queued entries to be stored.
     *
     * @var array
     */
    public static $entriesQueue;

    /**
     * Indicates if Telescope should ignore events fired by Laravel.
     *
     * @var bool
     */
    public static $ignoreFrameworkEvents = false;

    /**
     * Record the given entry.
     *
     * @param  int $type
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    protected static function record($type, $entry, $tags = [])
    {
        if (static::$filterUsing && ! call_user_func(static::$filterUsing, $type, $entry)) {
            return;
        }

        if (static::$tagUsing) {
            $tags = array_unique(
                array_merge($tags, call_user_func(static::$tagUsing, $type, $entry))
            );
        }

        static::$entriesQueue[] = [
            'type' => $type,
            'content' => $entry,
            'tags' => $tags,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Record the given entry.
     *
     * @param  array  $entry
     * @param  array  $tags
     * @return void
     */
    public static function recordMail($entry, $tags = [])
    {
        return static::record(EntryType::MAIL, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array  $entry
     * @param  array  $tags
     * @return void
     */
    public static function recordLogEntry($entry, $tags = [])
    {
        return static::record(EntryType::LOG, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordNotification($entry, $tags = [])
    {
        return static::record(EntryType::NOTIFICATION, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordJob($entry, $tags = [])
    {
        return static::record(EntryType::JOB, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordEvent($entry, $tags = [])
    {
        return static::record(EntryType::EVENT, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordCacheEntry($entry, $tags = [])
    {
        return static::record(EntryType::CACHE, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordQuery($entry, $tags = [])
    {
        return static::record(EntryType::QUERY, $entry, $tags);
    }

    /**
     * Record the given entry.
     *
     * @param  array $entry
     * @param  array $tags
     * @return void
     */
    public static function recordRequest($entry, $tags = [])
    {
        return static::record(EntryType::REQUEST, $entry, $tags);
    }

    /**
     * Set the callback that filters the entries that should be recorded.
     *
     * @param  \Closure $callback
     * @return static
     */
    public static function filter(Closure $callback)
    {
        static::$filterUsing = $callback;

        return new static;
    }

    /**
     * Set the callback that adds tags to the record.
     *
     * @param  \Closure $callback
     * @return static
     */
    public static function tag(Closure $callback)
    {
        static::$tagUsing = $callback;

        return new static;
    }

    /**
     * Store the queued entries and flush the queue.
     *
     * @param  Contracts\EntriesRepository $storage
     * @param  array $entry
     */
    public static function store(EntriesRepository $storage)
    {
        $storage->store(Str::uuid(), static::$entriesQueue);

        static::$entriesQueue = [];
    }

    /**
     * Determines if Telescope is ignoring events fired by Laravel.
     *
     * @return bool
     */
    public static function ignoresFrameworkEvents()
    {
        return static::$ignoreFrameworkEvents;
    }

    /**
     * Specifies that Telescope should ignore events fired by Laravel.
     *
     * @return static
     */
    public static function ignoreFrameworkEvents()
    {
        static::$ignoreFrameworkEvents = true;

        return new static;
    }
}