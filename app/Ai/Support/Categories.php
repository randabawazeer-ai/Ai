<?php

namespace App\Ai\Support;

use App\Models\Category;
use App\Models\User;

class Categories
{
    /**
     * Get the categories available to a user (built-in defaults + the user's own).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function availableFor(User $user): array
    {
        return Category::query()
            ->whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'type'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
            ])
            ->all();
    }

    /**
     * Resolve a category by name or id, restricted to categories the user may use.
     *
     * @return array{0: int|null, 1: string|null} the resolved id and an error message (or null)
     */
    public static function resolve(User $user, ?string $name, int|string|null $id = null): array
    {
        if ($name !== null && trim($name) !== '') {
            $category = Category::query()
                ->where('name', trim($name))
                ->where(fn ($query) => $query->whereNull('user_id')->orWhere('user_id', $user->id))
                ->orderByRaw('CASE WHEN user_id IS NULL THEN 1 ELSE 0 END')
                ->first();

            return $category ? [$category->id, null] : [null, "التصنيف \"{$name}\" غير موجود. اختر من القائمة المتاحة."];
        }

        if ($id !== null) {
            $category = Category::query()
                ->where('id', $id)
                ->where(fn ($query) => $query->whereNull('user_id')->orWhere('user_id', $user->id))
                ->first();

            return $category ? [$category->id, null] : [null, 'التصنيف غير موجود أو غير متاح لك.'];
        }

        return [null, null];
    }
}
