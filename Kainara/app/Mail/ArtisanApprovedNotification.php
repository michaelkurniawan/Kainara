<?php
namespace App\Mail;
use App\Models\ArtisanProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArtisanApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(public ArtisanProfile $profile) {} // Gunakan PHP 8 property promotion

    public function envelope(): Envelope {
        return new Envelope(subject: 'Congratulations! Your Kainara Artisan Application is Approved');
    }
    public function content(): Content {
        return new Content(view: 'emails.artisans.approved');
    }
}