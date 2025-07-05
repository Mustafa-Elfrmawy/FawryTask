<?php

declare(strict_types=1);
class Product
{
    public $name;
    public $price;
    public $quantity;
    public $weight;
    public $expiryDate;

    public function __construct($name, $price, $quantity, $weight = 0, $expiryDate = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->weight = $weight;
        $this->expiryDate = $expiryDate;
    }

    public function isExpired()
    {
        if (!$this->expiryDate) {
            return false;
        }
        return date('Y-m-d') > $this->expiryDate;
    }

    public function needsShipping()
    {
        return $this->weight > 0;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}

class Customer
{
    public $name;
    public $balance;

    public function __construct($name, $balance)
    {
        $this->name = $name;
        $this->balance = $balance;
    }

    public function canAfford($amount)
    {
        return $this->balance >= $amount;
    }

    public function pay($amount)
    {
        if (!$this->canAfford($amount)) {
            throw new Exception("Insufficient balance");
        }
        $this->balance -= $amount;
    }
}

class CartItem
{
    public $product;
    public $quantity;

    public function __construct(Product $product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getTotal()
    {
        return $this->product->price * $this->quantity;
    }

    public function getTotalWeight()
    {
        return $this->product->weight * $this->quantity;
    }
}

class Cart
{
    private $items = [];

    public function addProduct(Product $product, int $quantity)
    {
        if ($quantity > $product->quantity) {
            throw new Exception("Requested quantity exceeds available stock");
        }

        if ($product->isExpired()) {
            throw new Exception("Product is expired");
        }

        $this->items[] = new CartItem($product, $quantity);
    }

    public function getSubtotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function getTotalWeight()
    {
        $weight = 0;
        foreach ($this->items as $item) {
            if ($item->product->needsShipping()) {
                $weight += $item->getTotalWeight();
            }
        }
        return $weight;
    }

    public function getShippingFee()
    {
        $totalWeight = $this->getTotalWeight();
        if ($totalWeight == 0) {
            return 0;
        }
        return ceil($totalWeight / 1000) * 30;
    }

    public function getTotal()
    {
        return $this->getSubtotal() + $this->getShippingFee();
    }

    public function getItems()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return empty($this->items);
    }
}

class ShippingService
{
    public static function processShipment(Cart $cart)
    {
        $totalWeight = $cart->getTotalWeight();

        if ($totalWeight == 0) {
            return;
        }

        echo "\n=== Shipping Notice ===\n";

        foreach ($cart->getItems() as $item) {
            if ($item->product->needsShipping()) {
                echo sprintf(
                    "%dx %s - Weight: %dg\n",
                    $item->quantity,
                    $item->product->name,
                    $item->getTotalWeight()
                );
            }
        }

        echo sprintf("Total Weight: %.3fkg\n", $totalWeight / 1000);
    }
}

class CheckoutService
{
    public static function processOrder(Customer $customer, Cart $cart)
    {
        if ($cart->isEmpty()) {
            throw new Exception("Cart is empty");
        }

        $total = $cart->getTotal();

        if (!$customer->canAfford($total)) {
            throw new Exception("Customer balance is insufficient");
        }

        $customer->pay($total);

        self::processShipment($cart);

        self::printReceipt($customer, $cart);
    }

    private static function processShipment(Cart $cart)
    {
        if ($cart->getTotalWeight() > 0) {
            ShippingService::processShipment($cart);
        }
    }

    private static function printReceipt(Customer $customer, Cart $cart)
    {
        echo "\n=== Checkout Receipt ===\n";

        foreach ($cart->getItems() as $item) {
            echo sprintf(
                "%d x %s - %.2f\n",
                $item->quantity,
                $item->product->name,
                $item->getTotal()
            );
        }

        echo "\n";
        echo sprintf("Subtotal: %.2f\n", $cart->getSubtotal());
        echo sprintf("Shipping: %.2f\n", $cart->getShippingFee());
        echo sprintf("Total: %.2f\n", $cart->getTotal());
        echo sprintf("Customer Balance: %.2f\n", $customer->balance);
    }
}

try {
    // Enter the weight in grams, resulting in kilograms
    // name, price, quantity, weight = 0, expiryDate = null
    $laptop = new Product("Laptop", 1500, 5, 1000);
    $ebook = new Product("E-book", 25, 10);
    $milk = new Product("Milk", 20.5, 20, 500, "2025-07-6");
    // $chess = new Product("Chess", 8, 7, 1000, "2025-07-20");



    $customer = new Customer("Mostafa Elfaramawy", 2000);
    $cart = new Cart();

    $cart->addProduct($laptop, 1);
    $cart->addProduct($ebook, 2);
    $cart->addProduct($milk, 1);
    // Shipping default value per kilo 30
    CheckoutService::processOrder($customer, $cart);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

/*
 * Author: Mostafa Elfaramawy
 * Language: PHP
 *
 * While I have a solid understanding of Java, I chose to implement this challenge using PHP — 
 * the language in which I currently have deeper expertise — to deliver a clean, complete, and well-structured solution.
 * I'm actively strengthening my Java skills to align fully with the internship's technical requirements.
 * Porting this solution to Java would primarily involve syntax translation, as the overall logic and structure remain consistent.
 */