<?php

namespace App\Http\Controllers;

class Constants {

    const DATA = 'data';
    const RESP = 'resp';
    const MESSAGES = 'messages';
    const STATUS = 'status';
    const ERRORS = 'errors';
    const CODE = 'code';
    const ERR_MSG = 'err_msg';
    const ERR = 'ERROR';
    const SUCCESS = 'SUCCESS';
    const UNAUTHORISED = 'UNAUTHORISED';
    const LIKE = 'ilike';
    const PAGINATION_LIMIT = 10;
    const MAX_SIZE_UPLOAD = 10000;
    const COLUMNS = 'columns';
    const FORMATS = 'formats';
    const FILTERS = 'filters';
    const COLUMNS_CLASSES = 'columns_classes';
    const OPERATIONS = 'operations';
    const COLUMN_SEQUENCE = 'column_sequence';
    const EXPORT_COLUMN_SEQUENCE = 'export_column_sequence';
    const SORT_COLUMNS = 'sort_columns';
    const ORDER_BY = 'order_by';
    const MSG_FILES = [
        'validation_message',
        'user_message',
        'tax_message',
    ];
    const DATE_FORMATS = [
        'Y-m-d',
        'd-m-Y',
        'Y/m/d',
        'd/m/Y',
        'Y/M/D',
        'D/M/Y',
        'Y-M-D',
        'D-M-Y',
        'Y/M/d',
        'd/M/Y',
        'Y-M-d',
        'd-M-Y',
    ];
    const LIST_CONST = [
        'avail_columns' => 'avail_columns',
        'avail_formats' => 'avail_formats',
        'avail_filters' => 'avail_filters',
        'column_width' => 'column_width',
        'aggregates' => 'aggregates',
    ];
    const CUBE_COLUMN = 'cube_columns';
    const CUBE_VALUES = 'cube_values';
    const CUBE = 'cube';
    const NUM_TYPE_DECIMAL = [
        'currency' => 4,
        'qty' => 3
    ];
    const NUM_TYPE = [
        'currency' => 'currency',
        'qty' => 'qty'
    ];
    const LOGGED_IN_STATUS = [
        'successfully_logged_in' => 'S',
        'log_in_fail' => 'F',
        'other_system_logged_in' => 'O',
        'unknown' => 'U',
        'token_expired' => 'T',
    ];
    const AUDIT_ACTION = [
        'logged_in' => 'logged_in',
        'logged_out' => 'logged_out'
    ];
    const DEV_ROUTE_LOC = 4;
    const LOCAL_ROUTE_LOC = 3;

}
