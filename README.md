CircuitBreaker
==============

This implementation of the CircuitBreaker pattern provide a mechanism for resilience
against a cascade of failures which could adversely affect an application's performance.
The CurcuitBreaker monitors successful and non-successful transactions then provides
feedback, by way of tripping the circuit breaker if the failures reach a certain threshold.

The circuit breaker is used with the code:

```php
if ($app['breaker']->isOpen()) {
    try {
        // YOUR EXPENSIVE CALL HERE
        $app['breaker']->success();

    } catch (Exception $e) {
        // log a failure
        $app['breaker']->failure();
    }
} else {
    // FALLBACK TO SOMETHING ELSE HERE
}
```

If the system reaches a threshold of failures (i.e. a back-end is not
responding) then the system can 'trip' (open) the circuit so the request is no
longer performed. This can prevent systems from timing out and causing more issues
by continually attempting requests on services which are experiencing issues.

TODO
----

* Complete impementation
* Add 'half-open' impl
