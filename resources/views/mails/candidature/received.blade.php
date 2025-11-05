<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Candidature reçue</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#222; background:#f6f6f6; margin:0; padding:0; }
        .container { max-width:600px; margin:32px auto; background:#ffffff; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.06); overflow:hidden; }
        .header { padding:20px 28px; background:#0b5ed7; color:#fff; }
        .header h1 { margin:0; font-size:18px; font-weight:600; }
        .body { padding:24px 28px; }
        .muted { color:#6c757d; font-size:14px; }
        .section { margin-top:16px; }
        .footer { padding:18px 28px; background:#fafafa; color:#6c757d; font-size:13px; }
        .btn { display:inline-block; padding:10px 14px; background:#0b5ed7; color:#fff; text-decoration:none; border-radius:4px; margin-top:12px; }
        .meta { background:#f1f5fb; padding:12px; border-radius:4px; font-size:13px; color:#333; margin-top:12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Candidature reçue</h1>
        </div>

        <div class="body">
            <p class="muted">Bonjour {{ $recipientName ?? 'Recruteur' }},</p>

            <p>Nous vous confirmons la bonne réception d'une nouvelle candidature pour le poste <strong>{{ $positionTitle ?? '—' }}</strong>.</p>

            <div class="section">
                <p class="muted">Détails de la candidature :</p>
                <div class="meta">
                    <div><strong>Candidat :</strong> {{ $applicantName ?? 'Nom non fourni' }}</div>
                    <div><strong>Email :</strong> {{ $applicantEmail ?? '—' }}</div>
                    <div><strong>Soumis le :</strong> {{ $submittedAt ?? now()->toDateTimeString() }}</div>
                </div>
            </div>

            @if(!empty($coverLetter))
            <div class="section">
                <p class="muted">Message du candidat :</p>
                <div class="meta">
                    {!! nl2br(e($coverLetter)) !!}
                </div>
            </div>
            @endif

            <div class="section">
                <p>La candidature a été ajoutée à votre tableau de bord. Vous pouvez consulter le profil complet du candidat, les pièces jointes et poursuivre le processus de recrutement depuis l'espace dédié.</p>

                @if(!empty($actionUrl))
                <a class="btn" href="{{ $actionUrl }}">Voir la candidature</a>
                @endif
            </div>

            <p class="muted" style="margin-top:18px">Si vous n'êtes pas responsable du recrutement pour ce poste, veuillez ignorer ce message ou contacter l'administrateur.</p>
        </div>

        <div class="footer">
            <div><strong>{{ $companyName ?? config('app.name') }}</strong></div>
            <div>Merci de votre confiance — Équipe {{ $companyName ?? config('app.name') }}</div>
        </div>
    </div>
</body>
</html>