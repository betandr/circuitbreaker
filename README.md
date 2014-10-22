CircuitBreaker
==============

This implementation of the CircuitBreaker pattern provide a mechanism for resilience
against a cascade of failures which could adversely affect an application's performance.
The CurcuitBreaker monitors successful and non-successful transactions then provides
feedback, by way of tripping the circuit breaker if the failures reach a certain threshold.

The circuit breaker is used with the code:

```php
if ($app['breaker']->isClosed()) {
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

If the circuit breaker closes, then your logical test will fail in your mainline
code. However if you subsequently register a successful transaction then it will
re-close the circuit breaker and your code will be able to execute. This is so
systems are able to recover if the upstream problem is resolved. This will be
re-factored to use a 'half-open' method which will change the breaker into a
half-state where exploratory transactions can be completed to test to see if the
situation has resolved.

Tests
----

To run all tests, use:

```
phpunit --bootstrap src/autoload.php tests
```

TODO
----

* Complete impementation
* Add 'half-open' impl
