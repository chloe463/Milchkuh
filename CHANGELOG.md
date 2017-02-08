# CHANGELOG

## v1.2.2 (4040425)

## Improvement

* Make it possible to set charset

## v1.2.1 (a1a6c21)

### Code Refactor

* Change logger variable name (`$logger` -> `$query_logger`)

## v1.2.0

### New Feature

* Add `chloe463\Milchkuh\BindParamBuilder`

## v1.1.0

### New Feature

* Add `chloe463\Milchkuh\Logger`
    * `Logger` logs queries and bind parameters

## v1.0.1

### Refactor

* Allow $bind_param to be omitted

## v1.0.0

### THE 1ST RELEASE

* Add LISENCE.txt

## v0.3.1

### Document Update

* Update README

## v0.3.0

### CI

* Add `.travis.yml` and make it possible to run unit test on Travis CI

## v0.2.0

### BREAKING CHANGE

* Add namesapce `chloe463` as vendor name
* Change namespace `Milchkuh\Milchkuh` -> `chloe463\Milchkuh\Milchkuh`

## v0.1.2

### Refactor

* Refactor exception class
    * Make Exception::$message contain previous exception message

## v0.1.1

### New Feature

* Add new class QueryBuilder

## v0.1.0

* `chloe463\Milchkuh\Milchkuh` is a wrapper class of PDO
* It helps you to access MySQL databases with simple APIs

