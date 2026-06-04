<?php

// TRY-CATCH
class WalletService
{
    public function transferMoney(int $senderId, int $recieverId, float $amount): string
    {
        try {
            if ($amount <= 0) {
                throw new Exception("Transfer amount must be positive");
            }

            $senderBal = 50000.00;

            if ($senderBal < $amount) {
                throw new Exception("Balance not enough");
            }

            $senderBal = $senderBal - $amount;

            return "Transfer successful. Amount sent from {$senderId} to {$recieverId}: " . number_format($amount, 2) . ". Remaining balance: " . number_format($senderBal, 2);

        } catch (Exception $e) {
            return "Transfer failed: " . $e->getMessage() . PHP_EOL;
        }
    }
}

$walletService = new WalletService();
echo $walletService->transferMoney(1234, 2002, 4500.75);


echo '' . PHP_EOL; //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


// NULL Safety & Objects

$userInfo = (object) [
    "name" => "Aaditya",
    "address" => (object) [
        "perm_addr" => "Solapur",
        "curr_addr" => "Pune"
    ],
    "nicknames" => [
        "1" => "Yash",
    ],
];

// just like JS
echo $userInfo?->nickname['2'] ?? "Adi" . PHP_EOL; // for arrays
echo 'Current city: ' . $userInfo?->address?->curr_addr . PHP_EOL; // for objects

// Match Expression: better switch
$status = 'pending';

$statusLabel = match ($status) {
    'pending' => 'Awaiting approval',
    'approved' => 'Transaction approved',
    'success' => 'Success!',
    default => 'Unknown...',
};

echo 'Current status: ' . $statusLabel . PHP_EOL;
