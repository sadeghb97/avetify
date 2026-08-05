<?php
namespace Avetify\Fields\DateFields;

enum DateValueType {
    case UNIX;
    case MYSQL_DATE;
    case MYSQL_DATETIME;
}