<?php

namespace LaravelLiberu\Tables\Attributes;

class Column
{
    final public const Mandatory = ['data', 'label', 'name'];

    final public const Optional = [
        'align', 'class', 'dateFormat', 'enum', 'meta', 'money', 'number', 'tooltip', 'resource',
    ];

    final public const Meta = [
        'average', 'boolean', 'clickable', 'cents', 'customTotal', 'date', 'datetime',
        'filterable', 'icon', 'method', 'notExportable', 'nullLast', 'searchable',
        'rawTotal', 'rogue', 'slot', 'sortable', 'sort:ASC', 'sort:DESC', 'translatable',
        'total', 'notVisible',
    ];
}
