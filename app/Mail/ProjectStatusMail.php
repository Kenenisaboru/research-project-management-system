<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProjectStatusMail
 *
 * Queued Mailable for project status changes and governance alerts.
 */
class ProjectStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  string  $notificationType
     * @param  string  $notificationMessage
     * @param  string|null  $recipientName
     */
    public function __construct(
        public string $notificationType,
        public string $notificationMessage,
        public ?string $recipientName = null
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $subject = 'RPMS Notification: ' . ucwords(strtolower(str_replace('_', ' ', $this->notificationType)));

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            htmlString: '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="background-color: #1a56db; color: #ffffff; padding: 15px; border-radius: 6px 6px 0 0; text-align: center;">
                        <h2 style="margin: 0; font-size: 20px;">Gambella University — RPMS</h2>
                        <p style="margin: 5px 0 0; font-size: 13px; opacity: 0.9;">Research Project Management System</p>
                    </div>
                    <div style="padding: 20px; color: #334155; line-height: 1.6;">
                        <p>Dear ' . e($this->recipientName ?? 'Researcher') . ',</p>
                        <div style="background: #f8fafc; border-left: 4px solid #1a56db; padding: 12px 16px; margin: 15px 0; border-radius: 4px;">
                            <p style="margin: 0; font-size: 15px; color: #0f172a;">' . nl2br(e($this->notificationMessage)) . '</p>
                        </div>
                        <p style="font-size: 13px; color: #64748b; margin-top: 25px;">
                            Please sign in to your RPMS portal account to review or take action on this item.
                        </p>
                    </div>
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 15px; text-align: center; font-size: 12px; color: #94a3b8;">
                        Research & Community Service Directorate &bull; Gambella University, Ethiopia
                    </div>
                </div>
            ',
        );
    }
}
