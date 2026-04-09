<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\AdminBulkEmail;
use App\Models\AdminEmailLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminEmailController extends Controller
{
    public function index(): View
    {
        return view('backend.emails.index', [
            'emails' => AdminEmailLog::query()->latest()->paginate(10),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'to' => ['required', 'string', 'max:5000'],
            'cc' => ['nullable', 'string', 'max:5000'],
            'bcc' => ['nullable', 'string', 'max:5000'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ]);

        [$toRecipients, $invalidToRecipients] = $this->parseRecipientList($data['to']);

        if ($toRecipients === [] || $invalidToRecipients !== []) {
            return back()
                ->withInput()
                ->withErrors(['to' => 'Please enter at least one valid recipient email address.']);
        }

        [$ccRecipients, $invalidCcRecipients] = $this->parseRecipientList($data['cc'] ?? '');
        [$bccRecipients, $invalidBccRecipients] = $this->parseRecipientList($data['bcc'] ?? '');

        if ($invalidCcRecipients !== []) {
            return back()
                ->withInput()
                ->withErrors(['cc' => 'One or more CC email addresses are invalid.']);
        }

        if ($invalidBccRecipients !== []) {
            return back()
                ->withInput()
                ->withErrors(['bcc' => 'One or more BCC email addresses are invalid.']);
        }

        $attachments = $this->storeAttachments($request);

        try {
            $pendingMail = Mail::to($toRecipients);

            if ($ccRecipients !== []) {
                $pendingMail->cc($ccRecipients);
            }

            if ($bccRecipients !== []) {
                $pendingMail->bcc($bccRecipients);
            }

            $pendingMail->send(new AdminBulkEmail(
                    subjectLine: $data['subject'],
                    body: $data['body'],
                    fileAttachments: $attachments,
                ));

            AdminEmailLog::query()->create([
                'subject' => $data['subject'],
                'body' => $data['body'],
                'to_recipients' => $toRecipients,
                'cc_recipients' => $ccRecipients,
                'bcc_recipients' => $bccRecipients,
                'attachments' => $attachments,
                'status' => 'sent',
                'sent_at' => now(),
                'sent_by' => auth()->id(),
            ]);
        } catch (Throwable $throwable) {
            Log::error('Admin email send failed', [
                'message' => $throwable->getMessage(),
                'to' => $toRecipients,
                'cc' => $ccRecipients,
                'bcc' => $bccRecipients,
            ]);

            AdminEmailLog::query()->create([
                'subject' => $data['subject'],
                'body' => $data['body'],
                'to_recipients' => $toRecipients,
                'cc_recipients' => $ccRecipients,
                'bcc_recipients' => $bccRecipients,
                'attachments' => $attachments,
                'status' => 'failed',
                'error_message' => Str::limit($throwable->getMessage(), 2000),
                'sent_by' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Email could not be sent. Please check mail settings and try again.');
        }

        return back()->with('success', 'Email sent successfully.');
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function parseRecipientList(?string $value): array
    {
        $emails = preg_split('/[,\n;]+/', (string) $value) ?: [];
        $emails = array_filter(array_map(static fn (string $email): string => trim($email), $emails));
        $emails = array_values(array_unique($emails));

        $valid = [];
        $invalid = [];

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                $valid[] = $email;
                continue;
            }

            $invalid[] = $email;
        }

        return [$valid, $invalid];
    }

    /**
     * @return array<int, array{path:string,name:string,mime:string}>
     */
    private function storeAttachments(Request $request): array
    {
        $files = $request->file('attachments', []);
        $stored = [];

        foreach ($files as $file) {
            $directory = 'admin-email-attachments/'.now()->format('Y/m/d');
            $name = Str::uuid()->toString().'_'.$file->getClientOriginalName();
            $path = $file->storeAs($directory, $name);

            $stored[] = [
                'path' => Storage::path($path),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
            ];
        }

        return $stored;
    }
}
