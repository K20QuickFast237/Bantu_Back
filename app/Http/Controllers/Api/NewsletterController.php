<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Response;

class NewsletterController extends Controller
{
    // S'abonner
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Abonnement réussi ! Merci de vous être inscrit à notre newsletter.',
            'data' => $subscriber,
        ], Response::HTTP_CREATED);
    }

    // Se désabonner
    public function unsubscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:newsletter_subscribers,email']);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();
        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Vous avez été désabonné avec succès.',
        ], Response::HTTP_OK);
    }

    // Lister les abonnés
    public function index()
    {
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();

        return response()->json([
            'data' => $subscribers,
        ]);
    }
}
