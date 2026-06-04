<?php

// CLASSES
class Car
{
    public $brand; // public: property can be accessed anywhere
    public $color; // private: can only be accessed in same class
    public $capacity;
    public function build() // method (function)
    {
        return "Car manufactured!" . PHP_EOL;
    }
}

$createCar = new Car(); // creating object
$qualities = new Car(); // for values

$brand1 = $qualities->brand = "Car brand 1"; // setting up values
$brand2 = $qualities->brand = "Car brand 2"; // setting up values
$qualities->color = "black";                 // setting up values
$qualities->capacity = "4";                  // setting up values

echo $createCar->build(); // calling method
echo $brand1 . PHP_EOL;
echo $brand2 . PHP_EOL;
echo "{$brand1} of {$createCar->color} color manufactured which is {$createCar->capacity} seats capacity." . PHP_EOL;


echo "" . PHP_EOL; //------------------------------------------------------------------------------------------------------


// CONSTRUCTOR
class Bus
{
    public $capacity;
    public function __construct($capacity)
    {
        $this->capacity = $capacity; // $this means current object
    }
}

$bus1 = new Bus("20 people");
$bus2 = new Bus("40 people");
$bus3 = new Bus("30 people");

echo $bus1->capacity . PHP_EOL;
echo $bus2->capacity . PHP_EOL;
echo $bus3->capacity . PHP_EOL;


echo "" . PHP_EOL; //--------------------------------------------------------------------------------------------------------


// EXAMPLE
class BankAccount
{
    private float $balance;
    private string $accountNumber;
    private string $currency;

    // constructor
    public function __construct(string $accountNumber, float $initialBal = 0.0)
    {
        $this->accountNumber = $accountNumber;
        $this->balance = $initialBal;
        $this->currency = 'INR';
    }

    // methods
    public function deposit(float $amount): float
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit amount must be positive');
        }
        $this->balance += $amount;
        return $this->balance;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
    public function withdraw(float $amount): float
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Balance must be positive');
        }
        $this->balance -= $amount;
        return $this->balance;
    }
    public static function formatCurrency(float $amount): string
    {
        return number_format($amount, 2); // N,NNN.00 -> 2
    }
}

$account = new BankAccount('ACC-2002202', 28533.13);
$account->deposit(2341.00);
// static fns are called on classes directly
echo "New balance: " . BankAccount::formatCurrency($account->getBalance());
