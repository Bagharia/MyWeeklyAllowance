<?php
require_once 'vendor/autoload.php';

use Alexis\MyWeeklyAllowance\Wallet;

session_start();

if (!isset($_SESSION['wallet'])) {
    $_SESSION['wallet'] = new Wallet();
}

$wallet = $_SESSION['wallet'];
$message = null;
$messageType = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'deposit':
                    $amount = floatval($_POST['amount']);
                    if ($amount > 0) {
                        $wallet->addMoney($amount);
                        $message = "Dépôt de {$amount}€ effectué ! ";
                        $messageType = 'success';
                    }
                    break;
                
                case 'withdraw':
                    $amount = floatval($_POST['amount']);
                    if ($amount > 0) {
                        $wallet->removeMoney($amount);
                        $message = "Retrait de {$amount}€ effectué. 💸";
                        $messageType = 'success';
                    }
                    break;

                case 'set_allowance':
                    $amount = floatval($_POST['amount']);
                    if ($amount >= 0) {
                        $wallet->setAllowance($amount);
                        $message = "Allocation fixée à {$amount}€ / semaine. ";
                        $messageType = 'success';
                    }
                    break;

                case 'process_allowance':
                    $wallet->processAllowance();
                    $message = "C'est jour de paie ! Allocation reçue. ";
                    $messageType = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage() . " ";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWeeklyAllowance </title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <header>
        <h1>Mon Porte-Monnaie</h1>
        <div class="balance-card">
            <span class="balance-amount"><?php echo number_format($wallet->getBalance(), 2, ',', ' '); ?></span>
            <span class="currency">€</span>
        </div>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <div class="card">
                <h2>Déposer</h2>
                <form method="POST" class="form-group">
                    <input type="hidden" name="action" value="deposit">
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="Montant" required>
                    <button type="submit" class="btn-deposit">Ajouter</button>
                </form>
            </div>

            <div class="card">
                <h2>Dépenser</h2>
                <form method="POST" class="form-group">
                    <input type="hidden" name="action" value="withdraw">
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="Montant" required>
                    <button type="submit" class="btn-withdraw">Retirer</button>
                </form>
            </div>

            <div class="card allowance-section">
                <h2>Allocation Hebdo</h2>
                <form method="POST" class="form-group" style="margin-bottom: 15px;">
                    <input type="hidden" name="action" value="set_allowance">
                    <input type="number" name="amount" step="0.01" min="0" placeholder="Montant hebdo" required>
                    <button type="submit" class="btn-allowance" style="width: auto;">Définir</button>
                </form>
                
                <form method="POST">
                    <input type="hidden" name="action" value="process_allowance">
                    <button type="submit" class="btn-allowance">Recevoir l'allocation</button>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>
