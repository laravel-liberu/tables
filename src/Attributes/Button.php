<?php

namespace LaravelEnso\Tables\Attributes;

class Button
{
    final public const Mandatory = ['type', 'icon'];

    final public const Types = ['global', 'row', 'dropdown'];

    final public const Optional = [
        'action', 'confirmation', 'event', 'fullRoute', 'label',  'message',
        'method', 'params', 'postEvent', 'routeSuffix', 'tooltip', 'slot',
        'class', 'name', 'selection',
    ];

    final public const Actions = ['ajax', 'export', 'href', 'router'];

    final public const Methods = ['DELETE', 'GET', 'PATCH', 'POST', 'PUT'];
}
