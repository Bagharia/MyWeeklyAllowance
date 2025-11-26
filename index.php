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
                        $message = "Dépôt de {$amount}€ effectué !";
                        $messageType = 'success';
                    }
                    break;
                
                case 'withdraw':
                    $amount = floatval($_POST['amount']);
                    if ($amount > 0) {
                        $wallet->removeMoney($amount);
                        $message = "Retrait de {$amount}€ effectué.";
                        $messageType = 'success';
                    }
                    break;

                case 'set_allowance':
                    $amount = floatval($_POST['amount']);
                    if ($amount >= 0) {
                        $wallet->setAllowance($amount);
                        $message = "Allocation fixée à {$amount}€ / semaine.";
                        $messageType = 'success';
                    }
                    break;

                case 'process_allowance':
                    $wallet->processAllowance();
                    $message = "C'est jour de paie ! Allocation reçue.";
                    $messageType = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWeeklyAllowance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-8 px-4">

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <header class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
            Mon Porte-Monnaie
        </h1>
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition-transform duration-300">
            <p class="text-indigo-100 text-sm font-medium mb-2">Solde actuel</p>
            <div class="flex items-center justify-center gap-2">
                <span class="text-5xl md:text-6xl font-extrabold text-white">
                    <?php echo number_format($wallet->getBalance(), 2, ',', ' '); ?>
                </span>
                <span class="text-3xl md:text-4xl font-bold text-indigo-200">€</span>
            </div>
        </div>
    </header>

    <main>
        <!-- Message de notification -->
        <?php if ($message): ?>
            <div class="mb-6 animate-fade-in">
                <?php if ($messageType === 'success'): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-md">
                        <p class="font-medium"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-md">
                        <p class="font-medium"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Carte Déposer -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Déposer</h2>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="deposit">
                    <input 
                        type="number" 
                        name="amount" 
                        step="0.01" 
                        min="0.01" 
                        placeholder="Montant à déposer"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all"
                        required
                    >
                    <button 
                        type="submit" 
                        class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        Ajouter
                    </button>
                </form>
            </div>

            <!-- Carte Dépenser -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Dépenser</h2>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="withdraw">
                    <input 
                        type="number" 
                        name="amount" 
                        step="0.01" 
                        min="0.01" 
                        placeholder="Montant à retirer"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all"
                        required
                    >
                    <button 
                        type="submit" 
                        class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        Retirer
                    </button>
                </form>
            </div>

            <!-- Carte Allocation -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100 md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Allocation Hebdo</h2>
                </div>
                
                <form method="POST" class="space-y-4 mb-4">
                    <input type="hidden" name="action" value="set_allowance">
                    <input 
                        type="number" 
                        name="amount" 
                        step="0.01" 
                        min="0" 
                        placeholder="Montant hebdomadaire"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                        required
                    >
                    <button 
                        type="submit" 
                        class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                    >
                        Définir
                    </button>
                </form>
                
                <div class="border-t-2 border-gray-100 pt-4">
                    <form method="POST">
                        <input type="hidden" name="action" value="process_allowance">
                        <button 
                            type="submit" 
                            class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Recevoir l'allocation
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-12 text-gray-500 text-sm">
        <p>MyWeeklyAllowance - Gérez votre argent simplement</p>
    </footer>
</div>

</body>
</html>