<?php
namespace App\Mail;
use App\Models\ArtisanProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArtisanRejectedNotification extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public ArtisanProfile $profile) {}

    public function envelope(): Envelope {
        return new Envelope(subject: 'Update on Your Kainara Artisan Application');
    }
    public function content(): Content {
        return new Content(view: 'emails.artisans.rejected');
    }
}