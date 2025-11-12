<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmation de candidature — {{ $companyName ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial; background:#f6f7fb; margin:0; padding:24px; color:#1f2937; }
        .card { max-width:680px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(16,24,40,0.06); overflow:hidden; }
        .header { background:#0b5ed7; color:#fff; padding:20px 24px; }
        .header h1 { margin:0; font-size:18px; font-weight:600; }
        .body { padding:22px 24px; font-size:15px; line-height:1.6; }
        .muted { color:#6b7280; font-size:14px; }
        .meta { background:#f3f7fb; border-radius:6px; padding:12px 14px; margin-top:12px; font-size:14px; color:#111827; }
        .btn { display:inline-block; margin-top:16px; padding:10px 16px; background:#0b5ed7; color:#fff; text-decoration:none; border-radius:6px; font-weight:600; }
        .footer { background:#fafbfd; padding:14px 24px; color:#6b7280; font-size:13px; }
        .kv { color:#374151; font-weight:600; display:inline-block; width:120px; }
    </style>
</head>
<body>
    <div class="card" role="article" aria-label="Confirmation de réception de candidature">
        <div class="header">
            <h1>Confirmation de candidature</h1>
        </div>

        <div class="body">
            <p class="muted">Bonjour {{ $applicantName ? e($applicantName) : 'Candidat' }},</p>

            <p>Nous confirmons la bonne transmission de votre candidature pour le poste <strong>{{ $positionTitle ? e($positionTitle) : '—' }}</strong> au sein de <strong>{{ $companyName ?? config('app.name') }}</strong>.</p>

            <div class="meta" aria-hidden="false">
                <div style="margin-bottom:8px;"><span class="kv">Candidat :</span> {{ $applicantName ?? '—' }}</div>
                <div style="margin-bottom:8px;"><span class="kv">Poste :</span> {{ $positionTitle ?? '—' }}</div>
                <div><span class="kv">Soumis le :</span> {{ $submittedAt ?? now()->format('d/m/Y H:i') }}</div>
            </div>

            @if(!empty($notes))
                <div style="margin-top:16px;">
                    <p class="muted">Remarques :</p>
                    <div class="meta">{!! nl2br(e($notes)) !!}</div>
                </div>
            @endif

            <div style="margin-top:16px;">
                <p>Votre candidature est maintenant enregistrée. L'équipe de recrutement examinera votre profil et, si votre candidature correspond aux besoins du poste, vous serez contacté(e) pour la suite du processus.</p>

                @if(!empty($actionUrl))
                    <a class="btn" href="{{ $actionUrl }}">Consulter ma candidature</a>
                @endif
            </div>

            <p class="muted" style="margin-top:18px;">
                Pour toute question, écrivez dans la fenêtre de Chat ouverte dans votre espace candidat
                <!-- contactez-nous à <a href="mailto:{{ $supportEmail ?? 'support@' . parse_url(config('app.url'), PHP_URL_HOST) }}">{{ $supportEmail ?? 'support@' . parse_url(config('app.url'), PHP_URL_HOST) }}</a>. -->
            </p>
        </div>

        <div class="footer">
            <div><strong>{{ $companyName ?? config('app.name') }}</strong></div>
            <div>Nous vous remercions de votre intérêt — Équipe {{ $companyName ?? config('app.name') }}</div>
        </div>
    </div>
</body>
</html>