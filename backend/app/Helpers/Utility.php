<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Constants;
use Log;
use DB;
use Config;
use Input;
use Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class Utility {

    //put your code here

    public static function log($data) {
        Log::debug(json_encode($data));
    }

    public static function percent($value, $percent) {
        return ($value * ($percent / 100));
    }

    public static function validation_err($validator) {
        $messages = $validator->messages()->all();
        $validErr['status'] = 'ERROR';
        foreach ($messages as $msg) {
            $validErr['messages'][] = $msg;
        }
        $json = json_encode($validErr);
        $remove = array("\\r\\n", "\\n", "\\r", "\\");
        $jsonstr = str_replace($remove, "", trim($json));
        Log::debug("--------------------Validation Messages----------------------");
        Log::debug(json_encode($validErr));
        return $jsonstr;
    }

    public static function parseMe($number) {
//        return floatval($number);
        return doubleval($number);
    }

    public static function isValidRet($value, $key = null, $ret = null) {
        if ($key == null)
            return (($value == '0' || trim($value) == '') ? $ret : trim($value));
        return ((!isset($value[$key]) || $value[$key] == '0' || (!is_array($value[$key]) && trim($value[$key]) == '')) ? $ret : ((!is_array($value[$key])) ? trim($value[$key]) : $value[$key]));
    }

    public static function calcPercentAmnt($totalAmmount, $percent) {
        return doubleval((($totalAmmount * $percent) / 100));
    }

    public static function validatePan($pan) {
        $regExpe = '/^([A-Z]){3}(C|P|H|F|A|T|B|L|J|G){1}([A-Z]){1}([0-9]){4}([A-Z]){1}?$/';
        return preg_match($regExpe, $pan);
    }

    public static function validateGSTN($gstn, $state_code) {
        $regExpe = '/^([0-9]){2}([A-Z]){3}(C|P|H|F|A|T|B|L|J|G){1}([A-Z]){1}([0-9]){4}([A-Z]){1}([1-9A-Z]){1}Z([0-9A-Z]){1}$/';
        $stateRegExp = '/^([0-9]){2}$/';
        if (self::parseMe($state_code) !== self::parseMe(substr($gstn, 0, 2))) {
            return false;
        }
        return preg_match($regExpe, $gstn);
    }

    public static function uniqueDocumentName($sep = '-') {
        $document_name = time() . $sep . rand(11111, 99999);
        return $document_name;
    }

    public static function dateFormat($date = '', $format = 'd/m/Y') {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            $date_aux = date_create_from_format($format, $date);
            return date_format($date_aux, 'Y-m-d');
        }
        return $date;
    }

    public static function dateTimeFormat($date = '', $format = 'd/m/Y H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            $date_aux = date_create_from_format($format, $date);
            return date_format($date_aux, 'Y-m-d H:i:s');
        }
        return $date;
    }

    public static function dateTimeFormatRev($date = '', $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            $date_aux = date_create_from_format($format, $date);
            return date_format($date_aux, 'd/m/Y H:i:s');
        }
        return $date;
    }

    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function uniqueUserID($max = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $random_user_id = substr(str_shuffle($chars), 0, $max);
        return $random_user_id;
    }

    public static function slug($text) {
        return strtolower(substr(str_replace(' ', '', $text), 0, 100));
    }

    public static function logException($ex, $print = true) {
        try {
            Log::debug("Exception found :-------->>>>");
            if ($print)
                Log::debug($ex);
            else
                Log::debug($ex->getMessage());
        } catch (Exception $exception) {
            Log::debug("Exception found in logException :-" . $exception);
        }
    }

    public static function convertNumberToWord($num, $currency = array("currency" => "Rupee", "substance" => "Paisa")) {
        $p = 0;
//$currency = array_map("strtolower", $currency);
        if (empty($num))
            return "zero " . $currency['currency'];
        $num = (preg_replace("/[^\d-.]+/", "", $num));
        $num = (floor($num * 100) / 100);
        list($baseNum, $decimal) = sscanf($num, '%d.%d');
        $no = $baseNum;
        list($decimal) = sscanf(($decimal * 10), "%2d");
        $baseDecimal = $decimal;

        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = $decimals = array();
        $words = array('0' => '', '1' => 'One', '2' => 'Two',
            '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
            '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
            '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
            '13' => 'Thirteen', '14' => 'Fourteen',
            '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
            '60' => 'Sixty', '70' => 'Seventy',
            '80' => 'Eighty', '90' => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore', 'Trillion', 'Quadrillion');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0] && (empty($baseDecimal) || $baseDecimal <= 0)) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
            } else
                $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $result = $result . $currency['currency'];

        if ($baseNum > 1 && $currency['currency'] != 'Rupees')
            $result .= "s";
        while ($p < strlen($decimal)) {
            $divider = ($p == 2) ? 10 : 100;
            $number = floor($decimal % $divider);
            $decimal = floor($decimal / $divider);
            $p += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($decimals)) && $number > 9) ? '' : null;
                $hundred = ($counter == 1 && $decimals[0]) ? ' and ' : null;
                $decimals[] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
            } else
                $decimals[] = null;
        }
        $decimals = array_reverse($decimals);
        $decimalResult = implode('', $decimals);

        if (!empty($decimalResult))
            $result .= " and " . $decimalResult . " " . ((($baseDecimal) > 1) ? $currency['substance'] . "" : $currency['substance']);
        $result = preg_replace("#\s+#", " ", $result);
        return $result;
    }

    public static function genErrResp($code = "internal_err", $data = null, $is_format = false, $outer_msg = null) {
        try {
            $msg = array();
            $msg_files = Constants::MSG_FILES;
            if ($outer_msg === null) {
                if (is_array($code)) {
                    foreach ($code as $index => $single_code) {
                        if (strpos($single_code, ".") !== false) {
                            $single_msg = config($single_code);
                            continue;
                        }
                        foreach ($msg_files as $msg_file) {
                            $single_msg = config($msg_file . "." . $single_code);
                            if ($single_msg != '') {
                                break;
                            }
                        }
                        $msg[$index] = isset($single_msg) ? $single_msg : $single_code;
                    }
                } else {
                    if (strpos($code, ".") !== false) {
                        $single_msg = config($code);
                    } else {
                        foreach ($msg_files as $msg_file) {
                            $single_msg = config($msg_file . "." . $code);
                            if ($single_msg != '') {
                                break;
                            }
                        }
                    }
                    $msg[0] = isset($single_msg) ? $single_msg : $code;
                }
            } else {
                $msg[0] = $outer_msg;
            }
            $respData[Constants::STATUS] = Constants::ERR;
            $respData[Constants::MESSAGES] = array_values($msg);
            $respData[Constants::CODE] = $code;
            $respData[Constants::DATA] = $data;
        } catch (Exception $ex) {
            Log::debug("exception: " . $ex);
        }
//        Log::debug("genErrResp respData:-->>>>>>>>>");
//        Log::debug(json_encode($respData));
        if ($is_format)
            return json_encode($respData, JSON_FORCE_OBJECT);
        return json_encode($respData);
    }

    public static function genSuccessResp($code = "internal_err", $data = null, $is_format = true) {
        try {
            $msg_files = Constants::MSG_FILES;
            if (is_array($code)) {
                foreach ($code as $index => $single_code) {
                    if (strpos($single_code, ".") !== false) {
                        $single_msg = config($single_code);
                    } else {
                        foreach ($msg_files as $msg_file) {
                            $single_msg = config($msg_file . "." . $single_code);
                            if ($single_msg != '') {
                                break;
                            }
                        }
                    }
//                    $single_msg = config((strpos($single_code, ".") === false) ? "validation_message." . $single_code : $single_code);
                    $intErr = "Validation message file doesn't contain code:- " . $single_code;
                    $msg[$index] = isset($single_msg) ? $single_msg : $intErr;
                }
            } else {
                if (strpos($code, ".") !== false) {
                    $single_msg = config($code);
                } else {
                    foreach ($msg_files as $msg_file) {
                        $single_msg = config($msg_file . "." . $code);
                        if ($single_msg != '') {
                            break;
                        }
                    }
                }
                $intErr = "Validation message file doesn't contain code:- " . $code;
                $msg[0] = isset($single_msg) ? $single_msg : $intErr;
            }
            $respData[Constants::STATUS] = Constants::SUCCESS;
            $respData[Constants::MESSAGES] = $msg;
            $respData[Constants::DATA] = $data;
            $respData[Constants::CODE] = $code;
        } catch (Exception $ex) {
            Log::debug("exception: " . $ex);
        }
//        Log::debug("genSuccessResp respData:-->>>>>>>>>");
//        Log::debug(json_encode($respData));
        return $respData;
        if ($is_format)
            return json_encode($respData, JSON_FORCE_OBJECT);
        return json_encode($respData, JSON_PRETTY_PRINT);
    }

    public static function filterList($return_data) {
        unset($return_data['config'][Constants::COLUMN_SEQUENCE]);
        unset($return_data['config'][Constants::EXPORT_COLUMN_SEQUENCE]);
        return $return_data;
    }

    public static function setPrecision($value) {
        return round($value, (config('precision') ? config('precision') : 3));
    }

    public static function user($obj, $table = '') {
        $table = ($table == '') ? '' : $table . '.';
        return $obj->where($table . 'user_id', config('user_id'));
    }

    public static function orderBy($return_data, $sort_selection = false, $in_order = 'false') {
        if (isset($return_data['config'][Constants::SORT_COLUMNS][$sort_selection])) {
            $in_order = ($in_order == 'asc') ? $in_order : 'desc';
            $return_data['data'] = $return_data['data']->orderBy($sort_selection, $in_order);
        } else {
            if (count($return_data['config'][Constants::ORDER_BY]) > 0) {
                foreach ($return_data['config'][Constants::ORDER_BY] as $key => $value) {
                    $return_data['data'] = $return_data['data']->orderBy($key, $value);
                }
            }
        }
        return $return_data;
    }

    public static function query($obj, $query, $column = 'name') {
        if ($query != null) {
            $query = (config('config.search_prefix') ? '%' : '')
                    . $query
                    . (config('config.search_postfix') ? '%' : '');
            if (is_array($column)) {
                $obj = $obj->where(function ($q) use ($query, $column) {
                    foreach ($column as $key => $value) {
                        $q->orWhere($value, Constants::LIKE, $query);
                    }
                });
            } else {
                $obj = $obj->where(function ($q) use ($query, $column) {
                    $q->where($column, Constants::LIKE, $query);
                });
            }
        }
        return $obj;
    }

    public static function dDQuery($obj, $query, $id, $to_array = true, $query_column = 'name', $id_column = 'id', $ids = []) {
        if ($query != '') {
            if (is_array($query_column)) {
                $query = (config('config.search_prefix') ? '%' : '')
                        . $query
                        . (config('config.search_postfix') ? '%' : '');
                $obj = $obj->where(function ($q) use ($query, $query_column, $ids, $id_column) {
                    if (count($ids))
                        $q = $q->whereIn($id_column, $ids);
                    foreach ($query_column as $key => $value) {
                        $q = $q->orWhere($value, Constants::LIKE, $query);
                    }
                });
            } else {
                $query = (config('config.search_prefix') ? '%' : '')
                        . $query
                        . (config('config.search_postfix') ? '%' : '');
                $obj = $obj->where(function ($q) use ($query, $query_column, $ids, $id_column) {
                    if (count($ids))
                        $q = $q->whereIn($id_column, $ids);
                    $q = $q->orWhere($query_column, Constants::LIKE, $query);
                });
            }
        } else if ($id != null) {
            $obj = $obj->where($id_column, $id);
        }
        if ($to_array)
            return $obj->get()->toArray();
        return $obj;
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function allDateFormat($date, $format) {
        if ($date == '') {
            return $date;
        } else {
            $date = date_create($date);
            switch ($format) {
                case 'mmyyyy':
                    $date = date_format($date, "m/Y");
                    break;
                case 'mmddyyyy':
                    $date = date_format($date, "m/d/Y");
                    break;
                case 'ddmmyyyy':
                    $date = date_format($date, "d/m/Y");
                    break;
                case 'mmyy':
                    $date = date_format($date, "m/y");
                    break;
                case 'mmmyyyy':
                    $date = date_format($date, 'M/Y');
                    break;
                case 'ddmmmyyyy':
                    $date = date_format($date, "d-M-Y");
                    break;
                case 'ddmmyyy':
                    $date = date_format($date, "d-m-Y");
                    break;
                case 'yyyyddmm':
                    $date = date_format($date, "Y/d/m");
                    break;
                case 'yyyymmdd':
                    $date = date_format($date, "Y/m/d");
                    break;
                case 'yyyymm':
                    $date = date_format($date, "Y/m");
                    break;
                default :
                    $date = date_format($date, "m/y");
            }
            return $date;
        }
    }

    public static function genTableFormatData($code = "information", $file = 'general') {
        $file = 'tables/' . $file . ".";
        $return_data = array();
        $return_data[Constants::SORT_COLUMNS] = array('updated_at' => 'Updated At', 'created_at' => 'Created At');
        $return_data[Constants::ORDER_BY] = array();
        if (config($file . $code) === NULL) {
            $return_data[Constants::COLUMNS] = array('id');
            $return_data[Constants::OPERATIONS] = array('_');
            $return_data[Constants::COLUMNS_CLASSES] = array('');
            $return_data[Constants::COLUMN_SEQUENCE] = array('id');
            $return_data[Constants::EXPORT_COLUMN_SEQUENCE] = array('id');
            $return_data[Constants::LIST_CONST['avail_columns']] = [];
            $return_data[Constants::LIST_CONST['avail_formats']] = [];
            $return_data[Constants::LIST_CONST['avail_filters']] = [];
            $return_data[Constants::LIST_CONST['column_width']] = [];
            $return_data[Constants::LIST_CONST['aggregates']] = [];
            return $return_data;
        }
        $return_data[Constants::COLUMNS] = config($file . $code)[Constants::COLUMNS];
        $return_data[Constants::FORMATS] = self::isValidRet(config($file . $code), Constants::FORMATS);
        $return_data[Constants::FILTERS] = self::isValidRet(config($file . $code), Constants::FILTERS);
        if (isset(config($file . $code)[Constants::OPERATIONS])) {
            $return_data[Constants::OPERATIONS] = config($file . $code)[Constants::OPERATIONS];
        }
        $return_data[Constants::COLUMNS_CLASSES] = array_fill(0, count($return_data[Constants::COLUMNS]), '');
        $return_data[Constants::EXPORT_COLUMN_SEQUENCE] = self::isValidRet(config($file . $code), Constants::EXPORT_COLUMN_SEQUENCE);
        if (isset(config($file . $code)[Constants::COLUMNS_CLASSES])) {
            $return_data[Constants::COLUMNS_CLASSES] = config($file . $code)[Constants::COLUMNS_CLASSES];
        }
        if (isset(config($file . $code)[Constants::COLUMN_SEQUENCE])) {
            $return_data[Constants::COLUMN_SEQUENCE] = config($file . $code)[Constants::COLUMN_SEQUENCE];
        }
        if (isset(config($file . $code)[Constants::EXPORT_COLUMN_SEQUENCE])) {
            $return_data[Constants::EXPORT_COLUMN_SEQUENCE] = config($file . $code)[Constants::EXPORT_COLUMN_SEQUENCE];
        }
        if (isset(config($file . $code)[Constants::SORT_COLUMNS])) {
            $return_data[Constants::SORT_COLUMNS] = config($file . $code)[Constants::SORT_COLUMNS];
        }
        if (isset(config($file . $code)[Constants::ORDER_BY])) {
            $return_data[Constants::ORDER_BY] = config($file . $code)[Constants::ORDER_BY];
        }
        if (isset(config($file . $code)[Constants::CUBE_COLUMN]))
            $return_data[Constants::CUBE_COLUMN] = config($file . $code)[Constants::CUBE_COLUMN];
        if (isset(config($file . $code)[Constants::CUBE_VALUES]))
            $return_data[Constants::CUBE_VALUES] = config($file . $code)[Constants::CUBE_VALUES];
        if (isset(config($file . $code)[Constants::CUBE]))
            $return_data[Constants::CUBE] = config($file . $code)[Constants::CUBE];
        $const_key = Constants::LIST_CONST['avail_columns'];
        $return_data[$const_key] = isset(config($file . $code)[$const_key]) ? config($file . $code)[$const_key] : [];
        if (isset(config($file . $code)[$const_key])) {
            $column_list = ColumnList::select('column')->where('list_key', $code)->where('user_id', config('user_id'))->get()->toArray();
            if (count($column_list)) {
                $return_data[Constants::COLUMNS] = array_intersect_key($column_list[0]['column'], $return_data[Constants::LIST_CONST['avail_columns']]);
                $return_data[Constants::LIST_CONST['avail_columns']] = array_merge($return_data[Constants::COLUMNS], $return_data[Constants::LIST_CONST['avail_columns']]);
            }
        }
        $const_key = Constants::LIST_CONST['avail_formats'];
        $return_data[$const_key] = isset(config($file . $code)[$const_key]) ? config($file . $code)[$const_key] : [];
        $const_key = Constants::LIST_CONST['avail_filters'];
        $return_data[$const_key] = isset(config($file . $code)[$const_key]) ? config($file . $code)[$const_key] : [];
        $const_key = Constants::LIST_CONST['column_width'];
        $return_data[$const_key] = isset(config($file . $code)[$const_key]) ? config($file . $code)[$const_key] : [];
        $const_key = Constants::LIST_CONST['aggregates'];
        $return_data[$const_key] = isset(config($file . $code)[$const_key]) ? config($file . $code)[$const_key] : [];
        return $return_data;
    }

    public static function toJson($param, $from_string = true, $delimiter = ',', $ret = null) {
        if (!$param)
            return $ret;
        if ($from_string) {
            $param = explode($delimiter, $param);
        }
        return json_encode($param);
    }

    public static function toJsonObject($param, $from_string = true, $delimiter = ',') {
        if (!$param)
            return null;
        if ($from_string) {
            $param = explode($delimiter, $param);
        }
        return json_encode($param, JSON_FORCE_OBJECT);
    }

    public static function toString($param, $from_json = true, $delimiter = ',') {
        if (!$param)
            return null;
        if ($from_json) {
            $param = json_decode($param, true);
        }
        return implode($delimiter, $param);
    }

    public static function toArray($param, $from_json_or_string = 'json', $delimeter = ',') {
        if (!$param)
            return null;
        if ($from_json_or_string == 'json') {
            return json_decode($param, true);
        }
        return explode($delimiter, $param);
    }

    public static function db_date($column, $as = null, $format = Constants::DATE_FORMAT) {
        if (!$as)
            $as = $column;
        return DB::raw("to_char($column, '$format') AS $as");
    }

    public static function db_concat($first, $second, $as = null, $delimeter = ' ') {
        if (!$as)
            return DB::raw("CONCAT($first, '$delimeter', $second)");
        return DB::raw("CONCAT($first, '$delimeter', $second) AS $as");
    }

    public static function parseResp($resp) {
        if (isset($resp['data']))
            return $resp['data'];
        return null;
    }

    public static function respToData($resp) {
        if (is_array($resp))
            return $resp;
        return json_decode($resp, true);
    }

    public static function ReqInvalid($req, $validation_rule) {
        $valResp = [];
        foreach ($validation_rule as $key => $value) {
            $rules = $value[0];
            $messages = $value[1];
            foreach ($rules as $rkey => $rvalue) {
                switch ($rvalue) {
                    case 'required':
                        if (!isset($req[$key]) || trim($req[$key]) == '')
                            $valResp[] = (!isset($messages[$rkey]) || trim($messages[$rkey]) == '') ? 'internal_error' : $messages[$rkey];
                        break;
                    default:
                        break;
                }
            }
        }
        if (count($valResp))
            return $valResp;
        return false;
    }

    public static function specView($arr, $key) {
        return implode(',', array_column($arr[$key], 'value'));
    }

    public static function specJoin($arr, $key) {
        $spec_ids = array_column($arr[$key], 'value', 'id');
        $specs = Specification::whereIn('id', array_keys($spec_ids))->get()->toArray();
        foreach ($specs as $key => $value) {
            $specs[$key]['value'] = '';
            if (isset($spec_ids[$value['id']]))
                $specs[$key]['value'] = $spec_ids[$value['id']];
        }
        return $specs;
    }

    public static function roundMe($num, $type = Constants::NUM_TYPE['currency'], $decimal = Constants::NUM_TYPE_DECIMAL['currency']) {
        return round($num, $decimal);
    }

    public static function twoDecimal($num) {
        return round($num, 2);
    }

    public static function numFormat($num) {
        return money_format(config('currency_format'), $num);
    }

    public static function numberFormat($num, $digit = 2, $flag = false) {
        if (!$flag)
            return number_format($num, $digit);
        else
            return number_format($num, $digit, '.', '');
    }

    public static function qtyFormat($num) {
        return number_format($num, config('decimal'), '.', '');
    }

}
