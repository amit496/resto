<?php

namespace Tests\Feature\Admin;

use App\Mail\AdminBulkEmail;
use App\Models\AdminEmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminEmailCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_email_with_multiple_recipients_cc_bcc_and_attachment(): void
    {
        Mail::fake();
        Storage::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.emails.send'), [
            'to' => "alice@example.com, bob@example.com\ncharlie@example.com",
            'cc' => 'cc@example.com',
            'bcc' => 'bcc@example.com',
            'subject' => 'Launch Update',
            'body' => 'Hello team, this is the launch update.',
            'attachments' => [
                UploadedFile::fake()->create('brief.pdf', 200, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Email sent successfully.');

        Mail::assertSent(AdminBulkEmail::class, function (AdminBulkEmail $mail): bool {
            return $mail->subjectLine === 'Launch Update'
                && $mail->body === 'Hello team, this is the launch update.'
                && count($mail->fileAttachments) === 1;
        });

        $this->assertDatabaseHas('admin_email_logs', [
            'subject' => 'Launch Update',
            'status' => 'sent',
            'sent_by' => $user->id,
        ]);

        $log = AdminEmailLog::query()->first();

        $this->assertSame(['alice@example.com', 'bob@example.com', 'charlie@example.com'], $log?->to_recipients);
        $this->assertSame(['cc@example.com'], $log?->cc_recipients);
        $this->assertSame(['bcc@example.com'], $log?->bcc_recipients);
        $this->assertCount(1, $log?->attachments ?? []);
    }
}
