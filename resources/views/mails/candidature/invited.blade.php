<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Invitation à entretien — {{ $companyName ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; background:#f6f7fb; margin:0; padding:24px; color:#111827; }
        .card { max-width:680px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(16,24,40,0.06); overflow:hidden; }
        .header { background:#0b5ed7; color:#fff; padding:20px 24px; }
        .header h1 { margin:0; font-size:18px; font-weight:600; }
        .body { padding:22px 24px; font-size:15px; line-height:1.6; color:#1f2937; }
        .muted { color:#6b7280; font-size:14px; }
        .meta { background:#f3f7fb; border-radius:6px; padding:12px 14px; margin-top:12px; font-size:14px; color:#111827; }
        .btn { display:inline-block; margin-top:16px; padding:10px 16px; background:#0b5ed7; color:#fff; text-decoration:none; border-radius:6px; font-weight:600; }
        .footer { background:#fafbfd; padding:14px 24px; color:#6b7280; font-size:13px; }
        .kv { color:#374151; font-weight:600; display:inline-block; width:140px; }
    </style>
</head>
<body>
    <div class="card" role="article" aria-label="Invitation à entretien">
        <div class="header">
            <h1>Invitation à Entretien</h1>
        </div>

        <div class="body">
            <p class="muted">Bonjour {{ $applicantName ? e($applicantName) : 'Madame, Monsieur' }},</p>

            <p>Nous vous remercions pour votre candidature au poste <strong>{{ $positionTitle ? e($positionTitle) : '—' }}</strong>.<br> Après étude de votre dossier, le recruteur serait ravis d'échanger avec vous lors d'un entretien programmé ainsi qu'il suit:</p>

            <div class="meta" aria-hidden="false">
                <div style="margin-bottom:8px;"><span class="kv">Entreprise :</span> {{ $companyName ?? config('app.name') }}</div>
                <div style="margin-bottom:8px;"><span class="kv">Poste :</span> {{ $positionTitle ?? '—' }}</div>
                <div style="margin-bottom:8px;"><span class="kv">Date & heure :</span> {{ $interviewDateTime ?? ($interviewDate ?? '—') . ' ' . ($interviewTime ?? '') }}</div>
                <div style="margin-bottom:8px;"><span class="kv">Mode :</span> {{ $interviewMode ?? 'Présentiel / Visio' }}</div>
                <div><span class="kv">Lieu / lien :</span> {{ $interviewLocation ?? ($interviewLink ?? '—') }}</div>
            </div>

            @if(!empty($instructions))
                <div style="margin-top:16px;">
                    <p class="muted">Informations complémentaires :</p>
                    <div class="meta">{!! nl2br(e($instructions)) !!}</div>
                </div>
            @endif

            @if(!empty($actionUrl))
                <div style="margin-top:16px;">
                    <p>Merci de confirmer votre disponibilité en cliquant sur le bouton ci‑dessous. Si l'horaire ne vous convient pas, contactez-nous afin de proposer un créneau alternatif.</p>

                        <a class="btn" href="{{ $actionUrl }}">Confirmer ma présence</a>
                </div>
            @endif

            <p class="muted" style="margin-top:18px;">
                <!-- Pour toute question, vous pouvez joindre {{ $contactPerson ?? 'notre équipe' }} à <a href="mailto:{{ $supportEmail ?? 'support@' . parse_url(config('app.url'), PHP_URL_HOST) }}">{{ $supportEmail ?? 'support@' . parse_url(config('app.url'), PHP_URL_HOST) }}</a>. -->
                Pour toute question, vous pouvez joindre directement le recruteur via la messagerie de votre espace candidat.
            </p>
        </div>

        <div class="footer">
            <div><strong>{{ $companyName ?? config('app.name') }}</strong></div>
            <div>Nous vous remercions pour l'intérêt porté à notre entreprise et restons à votre disposition.</div>
        </div>
    </div>
</body>
</html>