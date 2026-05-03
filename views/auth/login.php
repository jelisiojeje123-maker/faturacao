<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Entrar — FaturaMZ Pro</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { line-height:1; font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        .gradient-bg { background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e3a5f 100%); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float-anim { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- Left panel – branding -->
    <div class="hidden lg:flex lg:w-1/2 gradient-bg flex-col items-center justify-center p-12 relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute top-[-80px] left-[-80px] w-64 h-64 rounded-full bg-blue-600/10 blur-2xl"></div>
        <div class="absolute bottom-[-60px] right-[-60px] w-80 h-80 rounded-full bg-blue-500/10 blur-3xl"></div>

        <div class="relative z-10 text-center">
            <!-- Icon -->
            <div class="float-anim inline-flex items-center justify-center w-24 h-24 bg-blue-600 rounded-3xl shadow-2xl shadow-blue-600/40 mb-8">
                <span class="material-symbols-outlined text-white text-5xl">receipt_long</span>
            </div>
            <h1 class="text-4xl font-black text-white mb-3 tracking-tight">FaturaMZ Pro</h1>
            <p class="text-slate-400 text-lg mb-12 max-w-sm leading-relaxed">
                Sistema completo de faturação para prestadores de serviços em Moçambique
            </p>

            <!-- Feature bullets -->
            <div class="space-y-4 text-left max-w-xs mx-auto">
                <?php $features = [
                    ['check_circle','Faturas com IVA (16%) automático'],
                    ['group','Gestão completa de clientes'],
                    ['payments','Registo de pagamentos'],
                    ['bar_chart','Dashboard financeiro em tempo real'],
                ]; ?>
                <?php foreach ($features as [$icon, $text]): ?>
                <div class="flex items-center gap-3 text-slate-300 text-sm">
                    <span class="material-symbols-outlined text-blue-400 text-[20px]"><?= $icon ?></span>
                    <span><?= $text ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right panel – form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <!-- Mobile logo -->
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg mb-3">
                    <span class="material-symbols-outlined text-white text-2xl">receipt_long</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900">FaturaMZ Pro</h1>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-black text-slate-900 mb-1">Bem-vindo de volta!</h2>
                <p class="text-slate-500 text-sm">Introduza as suas credenciais para aceder ao sistema.</p>
            </div>

            <!-- Flash error -->
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-3
                        <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                <span class="material-symbols-outlined text-[18px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
            <?php endif; ?>

            <!-- Login form -->
            <form method="POST" action="/faturacao/login.php" class="space-y-5" id="login-form">
                <?= csrfField() ?>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">mail</span>
                        <input type="email" id="email" name="email" required autocomplete="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="utilizador@empresa.mz"
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="password">Senha</label>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-10 pr-12 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white">
                        <button type="button" id="toggle-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="text-sm text-slate-600">Manter sessão iniciada</label>
                </div>

                <!-- Submit -->
                <button type="submit" id="btn-login"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl
                               transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 mt-2">
                    <span id="btn-text">Entrar no Sistema</span>
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </form>

            <!-- Demo credentials hint -->
            <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-500">
                <p class="font-semibold text-slate-700 mb-1">Credenciais padrão (após setup):</p>
                <p>Email: <code class="text-blue-600">admin@empresa.mz</code></p>
                <p>Senha: <code class="text-blue-600">admin123</code></p>
                <p class="mt-2 pt-2 border-t border-slate-200">
                    Primeira vez? <a href="/faturacao/setup.php" class="text-blue-600 font-semibold hover:underline">Executar setup.php →</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    // Toggle password visibility
    document.getElementById('toggle-password').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    });

    // Loading state on submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn  = document.getElementById('btn-login');
        const text = document.getElementById('btn-text');
        btn.disabled = true;
        text.textContent = 'A verificar...';
    });
    </script>
</body>
</html>
