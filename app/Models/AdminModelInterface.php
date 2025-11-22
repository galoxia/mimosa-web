<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

interface AdminModelInterface
{
    static function getClassSlug(): string;

    function getIndexFields(): array;

    static function getIndexDefinitions(): array;

    static function getIndexOrderBy(): array;

    static function getSingularName(): string;

    static function getPluralName(): string;

    function isIndexable(): bool;

    static function getIndexTable( array $filters = [] ): array;

    static function getIndexBuilder( array $filters = [] ): Builder;

    static function filterIndexBuilder( array &$filters, $builder ): Builder;

    static function isCreatable(): bool;

    function isUpdatable(): bool;

    function isDeletable(): bool;

    static function getFilterFields(): array;

    static function getMassiveAssignmentFields(): array;

//    static function getDefaultFilters(): array;

    static function getCreateFormFields(): array;

    function getUpdateFormFields(): array;

    static function getCreateFormDefinitions(): array;

    function getUpdateFormDefinitions(): array;
}
