<?php

namespace LaravelLiberu\Tables\Attributes;

class Filter
{
    final public const Mandatory = ['label', 'data', 'value', 'type'];

    final public const Optional = [
        'slot', 'multiple', 'route', 'translated', 'params',
        'pivotParams', 'custom', 'selectLabel',
    ];
}
