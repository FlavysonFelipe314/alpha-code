<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ef4444; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .credentials { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #ef4444; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AlphaCode - Modo Caverna</h1>
        </div>
        <div class="content">
            <h2>Bem-vindo, {{ $user->name }}!</h2>
            <p>Suas credenciais de acesso foram geradas com sucesso.</p>
            
            @if($senha)
            <div class="credentials">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Senha:</strong> {{ $senha }}</p>
            </div>
            
            <p>Use essas credenciais para acessar sua conta:</p>
            <a href="{{ $loginUrl }}" class="button">Acessar Plataforma</a>
            
            <p><strong>Importante:</strong> Por segurança, recomendamos que você altere sua senha após o primeiro login.</p>
            @else
            <p>Sua assinatura foi ativada com sucesso!</p>
            <p>Use sua senha atual para acessar:</p>
            <a href="{{ $loginUrl }}" class="button">Acessar Plataforma</a>
            @endif
        </div>
        <div class="footer">
            <p>© 2025 AlphaCode. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
