<?php

namespace App\Enums;

enum SettingType : string
{
    case BOOLEAN = "boolean";
    case INTEGER = "integer";
    case FLOAT = "float";
    case STRING = "string";
    case ARRAY = "array";
}
