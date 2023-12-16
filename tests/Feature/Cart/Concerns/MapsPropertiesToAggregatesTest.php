<?php

beforeEach(function() {

    if(!class_exists(B::class)) {
        class B {
            public int $value = 5;

            public function sayHello()
            {
                return "hello";
            }

            public function sayHelloWithArgs(array $args)
            {
                return $args[0];
            }
        }

    }

    if(!class_exists(A::class)) {
        class A {
            use \Antidote\LaravelCart\Concerns\MapsPropertiesToAggregates;

            private B $b;

            public function __construct()
            {
                $this->b = new B;
            }

            public function getValue(): int
            {
                return $this->mapToAggregate($this->b, 'value', 3);
            }

            public function getValueByClass(): int
            {
                return $this->mapToAggregate(B::class, 'value', 5);
            }

            public function sayHelloWithArgs(array $args): string
            {
                return $this->mapToAggregate(B::class, 'sayHelloWithArgs', 'default', $args);
            }

            public function sayHelloWithArgsWithInstance(string $arg): string
            {
                return $this->mapToAggregate($this->b, 'sayHelloWithArgs', 'default', [$arg]);
            }

            public function getAnotherValue(): int
            {
                return $this->mapToAggregate($this->b, 'anotherValue', 10);
            }

            public function sayHello(): string
            {
                return $this->mapToAggregate($this->b, 'sayHello', 'not saying hello');
            }

            public function sayHelloAgain(): string
            {
                return $this->mapToAggregate(B::class, 'sayHello', 'not saying hello');
            }

            public function doesNotExist(): string
            {
                return $this->mapToAggregate(B::class, 'doesNotExist', 'does not exist');
            }
        };
    }
});

it('will defer a property to an aggregate', function() {

    expect((new A)->getValue())->toBe(5);
})
->covers(\Antidote\LaravelCart\Concerns\MapsPropertiesToAggregates::class);

it('will supply a default if the property does not exist', function () {

    expect((new A)->getAnotherValue())->toBe(10);
})
->covers(\Antidote\LaravelCart\Concerns\MapsPropertiesToAggregates::class);;

it('will defer a method to an aggregate', function () {

    expect((new A)->sayHello())->toBe('hello');
    expect((new A)->sayHelloAgain())->toBe('hello');
    expect((new A)->getValueByClass())->toBe(5);
})
->covers(\Antidote\LaravelCart\Concerns\MapsPropertiesToAggregates::class);

it('will defer a method to an aggregate with arguments', function () {
    expect((new A)->sayHelloWithArgs(['hello']))->toBe('hello');
    expect((new A)->sayHelloWithArgsWithInstance('hello'))->toBe('hello');
})
->covers(\Antidote\LaravelCart\Concerns\MapsPropertiesToAggregates::class);

it('returns the default if the method or property does not exist', function () {
    expect((new A)->doesNotExist())->toBe('does not exist');
});
